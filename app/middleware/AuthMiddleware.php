<?php

/**
 * Middleware de Autenticação
 * 
 * Gerencia verificações de autenticação, autorização e segurança
 * para proteger rotas administrativas e APIs
 */
class AuthMiddleware
{
    /**
     * Verificar se o usuário está autenticado
     */
    public static function requireAuth()
    {
        // Verificar se existe sessão ativa
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_authenticated'])) {
            self::redirectToLogin();
            return;
        }
        
        // Verificar se a sessão não expirou
        if (isset($_SESSION['expires_at']) && $_SESSION['expires_at'] < time()) {
            self::destroySession();
            self::redirectToLogin();
            return;
        }
        
        // Renovar tempo de expiração da sessão
        $_SESSION['expires_at'] = time() + (8 * 60 * 60); // 8 horas
        
        // Verificar se usuário ainda existe e está ativo
        try {
            $auth = new Auth();
            $user = $auth->getUserById($_SESSION['user_id']);
            
            if (!$user || $user['status'] !== 'ativo') {
                self::destroySession();
                self::redirectToLogin();
                return;
            }
            
            // Atualizar dados do usuário na sessão
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
        } catch (Exception $e) {
            error_log("Erro na verificação de autenticação: " . $e->getMessage());
            self::destroySession();
            self::redirectToLogin();
        }
    }
    
    /**
     * Verificar se o usuário é administrador
     */
    public static function requireAdmin()
    {
        // Primeiro verificar autenticação
        self::requireAuth();
        
        // Verificar se é administrador
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            
            if (self::isApiRequest()) {
                Response::json(['error' => 'Acesso negado. Privilégios de administrador requeridos.'], 403);
            } else {
                include '../app/views/errors/403.php';
                exit;
            }
        }
    }
    
    /**
     * Verificar token CSRF
     */
    public static function verifyCSRF()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(419);
                
                if (self::isApiRequest()) {
                    Response::json(['error' => 'Token CSRF inválido'], 419);
                } else {
                    include '../app/views/errors/419.php';
                    exit;
                }
            }
        }
    }
    
    /**
     * Verificar autenticação via JWT (para APIs)
     */
    public static function requireJWT()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            Response::json(['error' => 'Token de acesso requerido'], 401);
        }
        
        $jwt = $matches[1];
        
        try {
            $auth = new Auth();
            $payload = $auth->verifyJWT($jwt);
            
            // Definir dados do usuário na requisição
            $_SESSION['api_user_id'] = $payload['user_id'];
            $_SESSION['api_user_role'] = $payload['role'];
            
        } catch (Exception $e) {
            Response::json(['error' => 'Token inválido: ' . $e->getMessage()], 401);
        }
    }
    
    /**
     * Verificar rate limiting
     */
    public static function rateLimit($maxRequests = 60, $windowMinutes = 1)
    {
        $ip = Request::ip();
        
        // Usar sessão para rate limiting
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $now = time();
        $windowStart = $now - ($windowMinutes * 60);
        
        // Limpar requests antigos
        $_SESSION['rate_limit'] = array_filter($_SESSION['rate_limit'], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        if (count($_SESSION['rate_limit']) >= $maxRequests) {
            http_response_code(429);
            
            if (self::isApiRequest()) {
                Response::json(['error' => 'Muitas requisições. Tente novamente em alguns minutos.'], 429);
            } else {
                include '../app/views/errors/429.php';
                exit;
            }
        }
        
        $_SESSION['rate_limit'][] = $now;
    }
    
    /**
     * Verificar se o usuário pode acessar o recurso
     */
    public static function canAccess($resource, $action = 'read')
    {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        $role = $_SESSION['user_role'];
        
        // Administradores têm acesso total
        if ($role === 'admin') {
            return true;
        }
        
        // Matriz de permissões por role
        $permissions = [
            'operador' => [
                'pedidos' => ['read', 'update'],
                'recursos' => ['read', 'update'],
                'dashboard' => ['read'],
                'relatorios' => ['read']
            ],
            'visualizador' => [
                'pedidos' => ['read'],
                'recursos' => ['read'],
                'dashboard' => ['read'],
                'relatorios' => ['read']
            ]
        ];
        
        return isset($permissions[$role][$resource]) && 
               in_array($action, $permissions[$role][$resource]);
    }
    
    /**
     * Verificar se é uma requisição da API
     */
    private static function isApiRequest()
    {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0 || 
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
    
    /**
     * Redirecionar para login
     */
    private static function redirectToLogin()
    {
        if (self::isApiRequest()) {
            Response::json(['error' => 'Autenticação requerida'], 401);
        }
        
        $currentUrl = $_SERVER['REQUEST_URI'];
        $loginUrl = '/auth/login';
        
        if ($currentUrl !== '/auth/login') {
            $loginUrl .= '?redirect=' . urlencode($currentUrl);
        }
        
        Response::redirect($loginUrl);
    }
    
    /**
     * Destruir sessão
     */
    private static function destroySession()
    {
        // Remover dados específicos de autenticação
        $keysToRemove = [
            'user_id', 'user_authenticated', 'user_name', 
            'user_email', 'user_role', 'expires_at'
        ];
        
        foreach ($keysToRemove as $key) {
            unset($_SESSION[$key]);
        }
        
        // Se não há mais dados importantes na sessão, destruir completamente
        if (empty(array_diff_key($_SESSION, array_flip(['csrf_token'])))) {
            session_destroy();
            session_start(); // Reiniciar para manter CSRF token
        }
    }
    
    /**
     * Log de evento de segurança
     */
    private static function logSecurityEvent($event, $details = [])
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => Request::ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_id' => $_SESSION['user_id'] ?? null,
            'details' => $details
        ];
        
        error_log("Security Event: " . json_encode($logData));
        
        // Aqui você pode implementar log para banco de dados se necessário
    }
    
    /**
     * Verificar força da senha
     */
    public static function checkPasswordStrength($password)
    {
        $score = 0;
        
        if (strlen($password) >= 8) $score++;
        if (strlen($password) >= 12) $score++;
        if (preg_match('/[a-z]/', $password)) $score++;
        if (preg_match('/[A-Z]/', $password)) $score++;
        if (preg_match('/[0-9]/', $password)) $score++;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score++;
        
        return $score;
    }
    
    /**
     * Gerar token CSRF
     */
    public static function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}