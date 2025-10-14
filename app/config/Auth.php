<?php

/**
 * Sistema E-SIC - Configurações de Autenticação e Segurança
 * 
 * Gerenciamento de JWT, OAuth, sessões e middleware de autenticação
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

class Auth {
    
    private static $instance = null;
    private $user = null;
    private $jwtSecret;
    private $jwtExpiration;
    
    private function __construct() {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'fallback-secret-key-change-in-production';
        $this->jwtExpiration = intval($_ENV['JWT_EXPIRATION'] ?? 3600);
        
        // Iniciar sessão se não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Carregar usuário da sessão ou token
        $this->loadUserFromSession();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Carrega usuário da sessão ou token JWT
     */
    private function loadUserFromSession() {
        // Verificar se existe usuário na sessão
        if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
            $this->loadUser($_SESSION['user_id']);
        }
        
        // Verificar token JWT no header Authorization
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $userData = $this->verifyJWT($token);
            if ($userData) {
                $this->loadUser($userData['user_id']);
            }
        }
    }
    
    /**
     * Carrega dados do usuário do banco
     */
    private function loadUser($userId) {
        try {
            $db = Database::getInstance();
            $user = $db->selectOne(
                "SELECT id, nome, email, nivel_acesso, ativo, ultimo_login, unidade, cargo FROM usuarios WHERE id = ? AND ativo = 1",
                [$userId]
            );
            
            if ($user) {
                $this->user = $user;
            }
        } catch (Exception $e) {
            error_log("Auth Error: " . $e->getMessage());
        }
    }
    
    /**
     * Realiza login do usuário
     */
    public function login($email, $senha, $lembrar = false) {
        try {
            $db = Database::getInstance();
            
            // Buscar usuário por email
            $user = $db->selectOne(
                "SELECT * FROM usuarios WHERE email = ? AND ativo = 1",
                [$email]
            );
            
            if (!$user) {
                $this->logAuthEvent(null, $email, 'tentativa_falha', false, 'Usuário não encontrado');
                return ['success' => false, 'message' => 'Credenciais inválidas'];
            }
            
            // Verificar se usuário está bloqueado
            if ($user['bloqueado_ate'] && new DateTime($user['bloqueado_ate']) > new DateTime()) {
                $this->logAuthEvent($user['id'], $email, 'tentativa_falha', false, 'Usuário bloqueado');
                return ['success' => false, 'message' => 'Usuário temporariamente bloqueado'];
            }
            
            // Verificar senha
            if (!password_verify($senha, $user['senha'])) {
                $this->handleFailedLogin($user['id'], $email);
                return ['success' => false, 'message' => 'Credenciais inválidas'];
            }
            
            // Login bem-sucedido
            $this->handleSuccessfulLogin($user, $lembrar);
            
            return [
                'success' => true, 
                'message' => 'Login realizado com sucesso',
                'user' => $this->getUserPublicData($user),
                'token' => $this->generateJWT($user)
            ];
            
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }
    
    /**
     * Alias para o método login (compatibilidade)
     */
    public function attempt($email, $senha, $lembrar = false) {
        return $this->login($email, $senha, $lembrar);
    }
    
    /**
     * Realiza login direto por ID do usuário (para OAuth)
     */
    public function loginById($userId) {
        try {
            $db = Database::getInstance();
            
            // Buscar usuário por ID
            $user = $db->selectOne(
                "SELECT * FROM usuarios WHERE id = ? AND ativo = 1",
                [$userId]
            );
            
            if (!$user) {
                throw new Exception('Usuário não encontrado ou inativo');
            }
            
            // Fazer login usando dados do usuário
            $this->handleSuccessfulLogin($user, false);
            
            return [
                'success' => true,
                'user' => $this->getUserPublicData($user),
                'token' => $this->generateJWT($user)
            ];
            
        } catch (Exception $e) {
            error_log("LoginById Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Trata login bem-sucedido
     */
    public function handleSuccessfulLogin($user, $lembrar = false) {
        $db = Database::getInstance();
        
        // Atualizar último login e resetar tentativas
        $db->execute(
            "UPDATE usuarios SET ultimo_login = NOW(), tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?",
            [$user['id']]
        );
        
        // Definir sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nivel'] = $user['nivel_acesso'];
        $_SESSION['user_authenticated'] = true;
        $_SESSION['expires_at'] = time() + (8 * 60 * 60); // 8 horas
        
        // Cookie "lembrar-me" (opcional)
        if ($lembrar) {
            $token = $this->generateRememberToken($user['id']);
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 dias
        }
        
        // Carregar usuário na instância
        $this->user = $user;
        
        // Log do evento
        $this->logAuthEvent($user['id'], $user['email'], 'login', true);
    }
    
    /**
     * Trata login com falha
     */
    private function handleFailedLogin($userId, $email) {
        $db = Database::getInstance();
        
        // Incrementar tentativas
        $db->execute(
            "UPDATE usuarios SET tentativas_login = tentativas_login + 1 WHERE id = ?",
            [$userId]
        );
        
        // Verificar se deve bloquear
        $user = $db->selectOne("SELECT tentativas_login FROM usuarios WHERE id = ?", [$userId]);
        
        if ($user['tentativas_login'] >= 5) {
            $bloqueioAte = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $db->execute(
                "UPDATE usuarios SET bloqueado_ate = ? WHERE id = ?",
                [$bloqueioAte, $userId]
            );
            
            $this->logAuthEvent($userId, $email, 'bloqueio', false, 'Bloqueado por excesso de tentativas');
        }
        
        $this->logAuthEvent($userId, $email, 'tentativa_falha', false);
    }
    
    /**
     * Realiza logout
     */
    public function logout() {
        if ($this->user) {
            $this->logAuthEvent($this->user['id'], $this->user['email'], 'logout', true);
        }
        
        // Limpar sessão
        session_unset();
        session_destroy();
        
        // Limpar cookie de lembrar
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        $this->user = null;
    }
    
    /**
     * Verifica se usuário está logado
     */
    public function check() {
        return $this->user !== null;
    }
    
    /**
     * Retorna usuário logado
     */
    public function user() {
        return $this->user;
    }
    
    /**
     * Verifica se usuário tem permissão específica
     */
    public function hasRole($roles) {
        if (!$this->check()) {
            return false;
        }
        
        $userRole = $this->user['nivel_acesso'];
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        return in_array($userRole, $allowedRoles);
    }
    
    /**
     * Verifica se usuário é admin
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    /**
     * Gera token JWT
     */
    public function generateJWT($user) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'nivel' => $user['nivel_acesso'],
            'iat' => time(),
            'exp' => time() + $this->jwtExpiration
        ]);
        
        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->jwtSecret, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }
    
    /**
     * Verifica token JWT
     */
    public function verifyJWT($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        $header = json_decode($this->base64UrlDecode($parts[0]), true);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        $signature = $this->base64UrlDecode($parts[2]);
        
        // Verificar assinatura
        $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], $this->jwtSecret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Gera token para "lembrar-me"
     */
    private function generateRememberToken($userId) {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        
        // Salvar no banco (seria necessário criar tabela remember_tokens)
        // Por simplicidade, vamos usar uma implementação básica
        
        return $token;
    }
    
    /**
     * Registra evento de autenticação
     */
    private function logAuthEvent($userId, $email, $evento, $sucesso, $detalhes = null) {
        try {
            $db = Database::getInstance();
            
            $db->execute(
                "INSERT INTO auth_logs (usuario_id, email, tipo_evento, ip_address, user_agent, sucesso, detalhes) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $userId,
                    $email,
                    $evento,
                    $this->getClientIP(),
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $sucesso ? 1 : 0,
                    $detalhes
                ]
            );
        } catch (Exception $e) {
            error_log("Auth Log Error: " . $e->getMessage());
        }
    }
    
    /**
     * Obtém IP do cliente
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Retorna dados públicos do usuário (sem senha)
     */
    public function getUserPublicData($user) {
        return [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'nivel_acesso' => $user['nivel_acesso'],
            'unidade' => $user['unidade'],
            'cargo' => $user['cargo'],
            'ultimo_login' => $user['ultimo_login']
        ];
    }
    
    /**
     * Codifica base64 URL-safe
     */
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodifica base64 URL-safe
     */
    private function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Gera hash de senha
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Gera token CSRF
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica token CSRF
     */
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Obter usuário por ID
     */
    public function getUserById($userId) {
        try {
            $db = Database::getInstance();
            return $db->selectOne(
                "SELECT id, nome, email, nivel_acesso, ativo, ultimo_login, unidade, cargo FROM usuarios WHERE id = ? AND ativo = 1",
                [$userId]
            );
        } catch (Exception $e) {
            error_log("Auth Error - getUserById: " . $e->getMessage());
            return null;
        }
    }
}



/**
 * Helper para acesso rápido
 */
function auth() {
    return Auth::getInstance();
}

/**
 * Helper para usuário logado
 */
function user() {
    return auth()->user();
}

/**
 * Helper para verificar autenticação
 */
function isLoggedIn() {
    return auth()->check();
}

