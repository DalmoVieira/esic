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
}

/**
 * Middleware de Autenticação
 */
class AuthMiddleware {
    
    /**
     * Requer usuário logado
     */
    public static function requireAuth() {
        $auth = Auth::getInstance();
        
        if (!$auth->check()) {
            if (self::isApiRequest()) {
                http_response_code(401);
                echo json_encode(['error' => 'Não autenticado']);
                exit;
            } else {
                header('Location: /admin/login');
                exit;
            }
        }
        
        return $auth->user();
    }
    
    /**
     * Requer nível de acesso específico
     */
    public static function requireRole($roles) {
        $user = self::requireAuth();
        $auth = Auth::getInstance();
        
        if (!$auth->hasRole($roles)) {
            if (self::isApiRequest()) {
                http_response_code(403);
                echo json_encode(['error' => 'Acesso negado']);
                exit;
            } else {
                header('Location: /admin/dashboard?error=access_denied');
                exit;
            }
        }
        
        return $user;
    }
    
    /**
     * Requer admin
     */
    public static function requireAdmin() {
        return self::requireRole(['admin']);
    }
    
    /**
     * Verifica CSRF em formulários
     */
    public static function verifyCSRF() {
        $auth = Auth::getInstance();
        $token = $_POST['_token'] ?? $_GET['_token'] ?? '';
        
        if (!$auth->verifyCSRFToken($token)) {
            if (self::isApiRequest()) {
                http_response_code(403);
                echo json_encode(['error' => 'Token CSRF inválido']);
                exit;
            } else {
                die('Token CSRF inválido');
            }
        }
    }
    
    /**
     * Verifica se é requisição de API
     */
    private static function isApiRequest() {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0 || 
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
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

/**
 * OAuth Handler para Google e Gov.br
 */
class OAuthHandler {
    
    private $providers = [];
    
    public function __construct() {
        $this->setupProviders();
    }
    
    private function setupProviders() {
        // Google OAuth
        $this->providers['google'] = [
            'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
            'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? '',
            'auth_url' => 'https://accounts.google.com/o/oauth2/auth',
            'token_url' => 'https://oauth2.googleapis.com/token',
            'user_info_url' => 'https://www.googleapis.com/oauth2/v2/userinfo'
        ];
        
        // Gov.br OAuth
        $this->providers['govbr'] = [
            'client_id' => $_ENV['GOVBR_CLIENT_ID'] ?? '',
            'client_secret' => $_ENV['GOVBR_CLIENT_SECRET'] ?? '',
            'redirect_uri' => $_ENV['GOVBR_REDIRECT_URI'] ?? '',
            'environment' => $_ENV['GOVBR_ENVIRONMENT'] ?? 'homologacao',
            'auth_url' => ($_ENV['GOVBR_ENVIRONMENT'] ?? 'homologacao') === 'producao' 
                ? 'https://sso.acesso.gov.br/authorize'
                : 'https://sso.staging.acesso.gov.br/authorize',
            'token_url' => ($_ENV['GOVBR_ENVIRONMENT'] ?? 'homologacao') === 'producao'
                ? 'https://sso.acesso.gov.br/token'
                : 'https://sso.staging.acesso.gov.br/token'
        ];
    }
    
    /**
     * Gera URL de autorização OAuth
     */
    public function getAuthUrl($provider, $state = null) {
        if (!isset($this->providers[$provider])) {
            throw new Exception("Provider não suportado: {$provider}");
        }
        
        $config = $this->providers[$provider];
        $state = $state ?: bin2hex(random_bytes(16));
        
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => $provider === 'google' ? 'openid email profile' : 'openid email profile cpf',
            'state' => $state
        ];
        
        return $config['auth_url'] . '?' . http_build_query($params);
    }
    
    /**
     * Processa callback OAuth
     */
    public function handleCallback($provider, $code, $state) {
        if (!isset($this->providers[$provider])) {
            throw new Exception("Provider não suportado: {$provider}");
        }
        
        // Trocar código por token
        $tokenData = $this->exchangeCodeForToken($provider, $code);
        
        // Obter dados do usuário
        $userData = $this->getUserInfo($provider, $tokenData['access_token']);
        
        // Processar login OAuth
        return $this->processOAuthLogin($provider, $userData);
    }
    
    private function exchangeCodeForToken($provider, $code) {
        $config = $this->providers[$provider];
        
        $data = [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config['redirect_uri']
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['token_url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    private function getUserInfo($provider, $accessToken) {
        $config = $this->providers[$provider];
        
        if ($provider === 'google') {
            $url = $config['user_info_url'] . '?access_token=' . $accessToken;
        } else {
            // Gov.br usa endpoint diferente
            $url = str_replace('/token', '/userinfo', $config['token_url']);
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    private function processOAuthLogin($provider, $userData) {
        $db = Database::getInstance();
        $email = $userData['email'] ?? '';
        
        if (!$email) {
            throw new Exception("Email não fornecido pelo provider OAuth");
        }
        
        // Buscar usuário existente
        $user = $db->selectOne("SELECT * FROM usuarios WHERE email = ?", [$email]);
        
        if (!$user) {
            // Criar novo usuário (apenas se permitido)
            throw new Exception("Usuário não cadastrado no sistema");
        }
        
        // Atualizar ID do provider OAuth
        $oauthField = $provider === 'google' ? 'google_id' : 'govbr_id';
        $oauthId = $userData['id'] ?? $userData['sub'] ?? '';
        
        if ($oauthId) {
            $db->execute(
                "UPDATE usuarios SET {$oauthField} = ? WHERE id = ?",
                [$oauthId, $user['id']]
            );
        }
        
        // Fazer login simulando credenciais válidas
        $auth = Auth::getInstance();
        $auth->handleSuccessfulLogin($user, false);
        
        return [
            'success' => true,
            'message' => 'Login OAuth realizado com sucesso',
            'user' => $auth->getUserPublicData($user),
            'token' => $auth->generateJWT($user)
        ];
    }
    
    /**
     * Tentativa de autenticação
     */
    public function attempt($email, $password, $remember = false) {
        $loginResult = $this->login($email, $password, $remember);
        
        if (isset($loginResult['success']) && $loginResult['success']) {
            // Recarregar o usuário para garantir que está na propriedade
            return $this->user;
        }
        
        return false;
    }
    
    /**
     * Login por ID do usuário
     */
    public function loginById($userId) {
        try {
            $db = Database::getInstance();
            $user = $db->selectOne(
                "SELECT * FROM usuarios WHERE id = ? AND ativo = 1",
                [$userId]
            );
            
            if ($user) {
                $this->handleSuccessfulLogin($user, false);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Auth Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter usuário por ID
     */
    public function getUserById($userId) {
        try {
            $db = Database::getInstance();
            return $db->selectOne(
                "SELECT id, nome, email, role, status FROM usuarios WHERE id = ?",
                [$userId]
            );
        } catch (Exception $e) {
            error_log("Auth Error: " . $e->getMessage());
            return null;
        }
    }
}