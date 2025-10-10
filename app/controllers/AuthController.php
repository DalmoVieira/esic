<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/AuthLog.php';
require_once __DIR__ . '/../config/Auth.php';
require_once __DIR__ . '/../config/OAuthHandler.php';

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Página de login
     */
    public function login()
    {
        // Se já está logado, redirecionar
        if ($this->auth->check()) {
            $user = $this->auth->user();
            if (in_array($user['tipo'], ['administrador', 'operador'])) {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
            return;
        }

        if ($this->isPost()) {
            $this->processLogin();
        } else {
            $this->render('auth/login', [
                'title' => 'Login - Sistema E-SIC'
            ]);
        }
    }

    /**
     * Processar login
     */
    private function processLogin()
    {
        try {
            $email = $this->getPost('email');
            $password = $this->getPost('password');
            $remember = $this->getPost('remember') === '1';

            if (empty($email) || empty($password)) {
                throw new Exception('Email e senha são obrigatórios');
            }

            // Verificar rate limiting
            if (!$this->checkLoginRateLimit($email)) {
                throw new Exception('Muitas tentativas de login. Tente novamente em alguns minutos.');
            }

            // Tentar autenticar
            $user = $this->auth->attempt($email, $password, $remember);
            
            if (!$user) {
                $this->logFailedLogin($email);
                throw new Exception('Credenciais inválidas');
            }

            // Verificar se usuário está ativo
            if (!$user['ativo']) {
                throw new Exception('Usuário inativo. Entre em contato com o administrador.');
            }

            // Log de sucesso
            $this->logSuccessfulLogin($user);

            // Redirecionar baseado no tipo de usuário
            if (in_array($user['tipo'], ['administrador', 'operador'])) {
                $redirectUrl = $_SESSION['intended_url'] ?? '/admin/dashboard';
            } else {
                $redirectUrl = $_SESSION['intended_url'] ?? '/';
            }
            
            unset($_SESSION['intended_url']);
            
            $this->success('Login realizado com sucesso');
            $this->redirect($redirectUrl);
            
        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->render('auth/login', [
                'title' => 'Login - Sistema E-SIC',
                'email' => $email ?? ''
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        if ($this->auth->check()) {
            $user = $this->auth->user();
            $this->logLogout($user);
        }
        
        $this->auth->logout();
        $this->success('Logout realizado com sucesso');
        $this->redirect('/');
    }

    /**
     * Página de registro (para administradores)
     */
    public function register()
    {
        $this->requireAuth();
        $this->requireRole(['administrador']);

        if ($this->isPost()) {
            $this->processRegister();
        } else {
            $this->render('auth/register', [
                'title' => 'Novo Usuário - Sistema E-SIC'
            ]);
        }
    }

    /**
     * Processar registro
     */
    private function processRegister()
    {
        try {
            $data = [
                'nome' => $this->getPost('nome'),
                'email' => $this->getPost('email'),
                'password' => $this->getPost('password'),
                'password_confirmation' => $this->getPost('password_confirmation'),
                'tipo' => $this->getPost('tipo', 'operador'),
                'ativo' => 1
            ];

            // Validar dados
            $this->validateRegisterData($data);

            // Criar usuário
            $usuarioModel = new Usuario();
            $userData = [
                'nome' => $data['nome'],
                'email' => $data['email'],
                'senha' => password_hash($data['password'], PASSWORD_DEFAULT),
                'tipo' => $data['tipo'],
                'ativo' => 1
            ];

            $userId = $usuarioModel->create($userData);

            if ($userId) {
                $this->logAction('user_created', [
                    'created_user_id' => $userId,
                    'created_user_email' => $data['email']
                ]);
                
                $this->success('Usuário criado com sucesso');
                $this->redirect('/admin/usuarios');
            } else {
                throw new Exception('Erro ao criar usuário');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->render('auth/register', [
                'title' => 'Novo Usuário - Sistema E-SIC',
                'old_data' => $data ?? []
            ]);
        }
    }

    /**
     * Página de esqueceu a senha
     */
    public function forgotPassword()
    {
        if ($this->isPost()) {
            $this->processForgotPassword();
        } else {
            $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha - Sistema E-SIC'
            ]);
        }
    }

    /**
     * Processar esqueceu a senha
     */
    private function processForgotPassword()
    {
        try {
            $email = $this->getPost('email');

            if (empty($email)) {
                throw new Exception('Email é obrigatório');
            }

            $usuarioModel = new Usuario();
            $user = $usuarioModel->findByEmail($email);

            if (!$user) {
                // Por segurança, não revelar se email existe ou não
                $this->success('Se o email existir em nossa base, você receberá instruções para redefinir sua senha.');
                $this->redirect('/auth/login');
                return;
            }

            // Gerar token de reset
            $token = $this->generateResetToken();
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Salvar token no banco
            $this->db->execute(
                "UPDATE usuarios SET reset_token = ?, reset_token_expires = ? WHERE id = ?",
                [$token, $expires, $user['id']]
            );

            // Enviar email (implementar com PHPMailer)
            $this->sendResetPasswordEmail($user, $token);

            $this->logAction('password_reset_requested', ['user_id' => $user['id']]);

            $this->success('Instruções para redefinir sua senha foram enviadas para seu email.');
            $this->redirect('/auth/login');

        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->render('auth/forgot-password', [
                'title' => 'Recuperar Senha - Sistema E-SIC'
            ]);
        }
    }

    /**
     * Redefinir senha
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            $this->error('Token inválido');
            $this->redirect('/auth/login');
            return;
        }

        // Verificar token
        $user = $this->validateResetToken($token);
        if (!$user) {
            $this->error('Token inválido ou expirado');
            $this->redirect('/auth/forgot-password');
            return;
        }

        if ($this->isPost()) {
            $this->processResetPassword($user, $token);
        } else {
            $this->render('auth/reset-password', [
                'title' => 'Redefinir Senha - Sistema E-SIC',
                'token' => $token
            ]);
        }
    }

    /**
     * Processar redefinição de senha
     */
    private function processResetPassword($user, $token)
    {
        try {
            $password = $this->getPost('password');
            $passwordConfirmation = $this->getPost('password_confirmation');

            if (empty($password)) {
                throw new Exception('Nova senha é obrigatória');
            }

            if (strlen($password) < 6) {
                throw new Exception('A senha deve ter pelo menos 6 caracteres');
            }

            if ($password !== $passwordConfirmation) {
                throw new Exception('As senhas não coincidem');
            }

            // Atualizar senha
            $usuarioModel = new Usuario();
            $success = $usuarioModel->update($user['id'], [
                'senha' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_token_expires' => null
            ]);

            if ($success) {
                $this->logAction('password_reset_completed', ['user_id' => $user['id']]);
                $this->success('Senha redefinida com sucesso. Faça login com sua nova senha.');
                $this->redirect('/auth/login');
            } else {
                throw new Exception('Erro ao redefinir senha');
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
            $this->render('auth/reset-password', [
                'title' => 'Redefinir Senha - Sistema E-SIC',
                'token' => $token
            ]);
        }
    }

    /**
     * OAuth - Google login
     */
    public function googleLogin()
    {
        try {
            $oauth = new OAuthHandler();
            $authUrl = $oauth->getGoogleAuthUrl();
            header('Location: ' . $authUrl);
            exit;
        } catch (Exception $e) {
            $this->error('Erro ao conectar com Google: ' . $e->getMessage());
            $this->redirect('/auth/login');
        }
    }

    /**
     * OAuth - Google callback
     */
    public function googleCallback()
    {
        try {
            $code = $_GET['code'] ?? null;
            
            if (!$code) {
                throw new Exception('Código de autorização não recebido');
            }

            $oauth = new OAuthHandler();
            $userInfo = $oauth->handleGoogleCallback($code);

            // Buscar ou criar usuário
            $usuarioModel = new Usuario();
            $user = $usuarioModel->findByEmail($userInfo['email']);

            if (!$user) {
                // Criar novo usuário
                $userData = [
                    'nome' => $userInfo['name'],
                    'email' => $userInfo['email'],
                    'google_id' => $userInfo['id'],
                    'tipo' => 'cidadao',
                    'ativo' => 1,
                    'email_verificado' => 1
                ];
                
                $userId = $usuarioModel->create($userData);
                $user = $usuarioModel->findById($userId);
            } else {
                // Atualizar Google ID se necessário
                if (empty($user['google_id'])) {
                    $usuarioModel->update($user['id'], ['google_id' => $userInfo['id']]);
                    $user['google_id'] = $userInfo['id'];
                }
            }

            // Fazer login
            $this->auth->loginById($user['id']);
            $this->logSuccessfulLogin($user, 'google');

            $this->success('Login com Google realizado com sucesso');
            $this->redirect('/');

        } catch (Exception $e) {
            $this->error('Erro no login com Google: ' . $e->getMessage());
            $this->redirect('/auth/login');
        }
    }

    /**
     * OAuth - Gov.br login
     */
    public function govbrLogin()
    {
        try {
            $oauth = new OAuthHandler();
            $authUrl = $oauth->getGovBrAuthUrl();
            header('Location: ' . $authUrl);
            exit;
        } catch (Exception $e) {
            $this->error('Erro ao conectar com Gov.br: ' . $e->getMessage());
            $this->redirect('/auth/login');
        }
    }

    /**
     * OAuth - Gov.br callback
     */
    public function govbrCallback()
    {
        try {
            $code = $_GET['code'] ?? null;
            
            if (!$code) {
                throw new Exception('Código de autorização não recebido');
            }

            $oauth = new OAuthHandler();
            $userInfo = $oauth->handleGovBrCallback($code);

            // Buscar ou criar usuário
            $usuarioModel = new Usuario();
            $user = $usuarioModel->findByCpf($userInfo['cpf']);

            if (!$user) {
                // Criar novo usuário
                $userData = [
                    'nome' => $userInfo['name'],
                    'email' => $userInfo['email'] ?? '',
                    'cpf' => $userInfo['cpf'],
                    'govbr_id' => $userInfo['sub'],
                    'tipo' => 'cidadao',
                    'ativo' => 1,
                    'email_verificado' => 1
                ];
                
                $userId = $usuarioModel->create($userData);
                $user = $usuarioModel->findById($userId);
            } else {
                // Atualizar Gov.br ID se necessário
                if (empty($user['govbr_id'])) {
                    $usuarioModel->update($user['id'], ['govbr_id' => $userInfo['sub']]);
                    $user['govbr_id'] = $userInfo['sub'];
                }
            }

            // Fazer login
            $this->auth->loginById($user['id']);
            $this->logSuccessfulLogin($user, 'govbr');

            $this->success('Login com Gov.br realizado com sucesso');
            $this->redirect('/');

        } catch (Exception $e) {
            $this->error('Erro no login com Gov.br: ' . $e->getMessage());
            $this->redirect('/auth/login');
        }
    }

    // Métodos auxiliares privados

    private function validateRegisterData($data)
    {
        $errors = [];

        if (empty($data['nome'])) {
            $errors[] = 'Nome é obrigatório';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email válido é obrigatório';
        }

        if (empty($data['password'])) {
            $errors[] = 'Senha é obrigatória';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Senha deve ter pelo menos 6 caracteres';
        }

        if ($data['password'] !== $data['password_confirmation']) {
            $errors[] = 'Senhas não coincidem';
        }

        if (!in_array($data['tipo'], ['administrador', 'operador'])) {
            $errors[] = 'Tipo de usuário inválido';
        }

        // Verificar se email já existe
        $usuarioModel = new Usuario();
        if ($usuarioModel->findByEmail($data['email'])) {
            $errors[] = 'Email já está em uso';
        }

        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
    }

    private function checkLoginRateLimit($email)
    {
        $authLogModel = new AuthLog();
        return $authLogModel->checkRateLimit($email);
    }

    private function logFailedLogin($email)
    {
        $authLogModel = new AuthLog();
        $authLogModel->logAttempt($email, 'login_failed');
    }

    private function logSuccessfulLogin($user, $method = 'password')
    {
        $authLogModel = new AuthLog();
        $authLogModel->logAttempt($user['email'], 'login_success', $user['id'], [
            'method' => $method,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);
    }

    private function logLogout($user)
    {
        $authLogModel = new AuthLog();
        $authLogModel->logAttempt($user['email'], 'logout', $user['id']);
    }

    private function generateResetToken()
    {
        return bin2hex(random_bytes(32));
    }

    private function validateResetToken($token)
    {
        $user = $this->db->selectOne(
            "SELECT * FROM usuarios WHERE reset_token = ? AND reset_token_expires > NOW()",
            [$token]
        );

        return $user ?: false;
    }

    private function sendResetPasswordEmail($user, $token)
    {
        // Implementar envio de email com PHPMailer
        // Por enquanto, apenas log
        $this->logAction('reset_email_sent', [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'token' => substr($token, 0, 8) . '...' // Log parcial por segurança
        ]);
    }
}