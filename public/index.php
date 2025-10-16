<?php

/**
 * Sistema E-SIC - Front Controller
 * 
 * Ponto de entrada único da aplicação com sistema de roteamento
 * Gerencia todas as requisições HTTP e direciona para controllers apropriados
 * 
 * @author Sistema E-SIC
 * @version 1.0
 */

// =======================================================
// CONFIGURAÇÕES INICIAIS
// =======================================================

// Definir timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurar relatório de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar buffer de saída
ob_start();

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =======================================================
// AUTOLOAD E INCLUDES
// =======================================================

// Função de autoload simples
spl_autoload_register(function ($class) {
    $directories = [
        '../app/controllers/',
        '../app/models/',
        '../app/libraries/',
        '../app/middleware/',
        '../app/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Carregar configurações
require_once '../app/config/Database.php';
require_once '../app/config/Auth.php';

// =======================================================
// CLASSE ROUTER
// =======================================================

class Router {
    
    private $routes = [];
    private $middlewares = [];
    
    public function __construct() {
        $this->setupRoutes();
    }
    
    /**
     * Define todas as rotas da aplicação
     */
    private function setupRoutes() {
        
        // =======================================================
        // ROTAS PÚBLICAS
        // =======================================================
        
        // Página inicial - redirecionar para login
        $this->get('/', function() {
            Response::redirect(url('/auth/login'));
        });
        $this->get('/home', function() {
            Response::redirect(url('/auth/login'));
        });
        
        // Novo pedido
        $this->get('/novo-pedido', 'PedidoController@formulario');
        $this->post('/novo-pedido', 'PedidoController@criar');
        
        // Acompanhar pedido
        $this->get('/acompanhar', 'PedidoController@acompanhar');
        $this->post('/acompanhar', 'PedidoController@buscar');
        
        // Visualizar pedido
        $this->get('/pedido/{protocolo}', 'PedidoController@visualizar');
        
        // Recurso
        $this->get('/recurso/{protocolo}', 'RecursoController@formulario');
        $this->post('/recurso/{protocolo}', 'RecursoController@criar');
        
        // Páginas informativas
        $this->get('/sobre', 'HomeController@sobre');
        $this->get('/lei-acesso-informacao', 'HomeController@lai');
        $this->get('/transparencia', 'HomeController@transparencia');
        

        
        // =======================================================
        // ROTAS ADMINISTRATIVAS (PROTEGIDAS)
        // =======================================================
        
        // Dashboard
        $this->get('/admin', 'AdminController@dashboard', ['auth']);
        $this->get('/admin/dashboard', 'AdminController@dashboard', ['auth']);
        
        // Gerenciar pedidos
        $this->get('/admin/pedidos', 'AdminController@pedidos', ['auth']);
        $this->get('/admin/pedidos/{id}', 'AdminController@viewPedido', ['auth']);
        $this->post('/admin/pedidos/{id}/process', 'AdminController@processPedido', ['auth']);
        
        // Recursos
        $this->get('/admin/recursos', 'AdminController@recursos', ['auth']);
        $this->get('/admin/recursos/{id}', 'AdminController@viewRecurso', ['auth']);
        $this->post('/admin/recursos/{id}/process', 'AdminController@processRecurso', ['auth']);
        
        // Usuários (apenas admin)
        $this->get('/admin/usuarios', 'AdminController@usuarios', ['auth', 'admin']);
        $this->get('/admin/usuarios/create', 'AdminController@createUser', ['auth', 'admin']);
        $this->post('/admin/usuarios/create', 'AdminController@createUser', ['auth', 'admin']);
        $this->get('/admin/usuarios/{id}/edit', 'AdminController@editUser', ['auth', 'admin']);
        $this->post('/admin/usuarios/{id}/edit', 'AdminController@editUser', ['auth', 'admin']);
        
        // Relatórios
        $this->get('/admin/relatorios', 'AdminController@relatorios', ['auth']);
        
        // Configurações
        $this->get('/admin/configuracoes', 'AdminController@configuracoes', ['auth', 'admin']);
        $this->post('/admin/configuracoes', 'AdminController@configuracoes', ['auth', 'admin']);
        
        // =======================================================
        // ROTAS DE AUTENTICAÇÃO
        // =======================================================
        
        // Login/Logout
        $this->get('/auth/login', 'AuthController@login');
        $this->post('/auth/login', 'AuthController@login');
        $this->get('/auth/logout', 'AuthController@logout');
        
        // Registro (admin only)
        $this->get('/auth/register', 'AuthController@register', ['auth', 'admin']);
        $this->post('/auth/register', 'AuthController@register', ['auth', 'admin']);
        
        // Recuperação de senha
        $this->get('/auth/forgot-password', 'AuthController@forgotPassword');
        $this->post('/auth/forgot-password', 'AuthController@forgotPassword');
        $this->get('/auth/reset-password/{token}', 'AuthController@resetPassword');
        $this->post('/auth/reset-password/{token}', 'AuthController@resetPassword');
        
        // OAuth
        $this->get('/auth/google', 'AuthController@googleLogin');
        $this->get('/auth/google/callback', 'AuthController@googleCallback');
        $this->get('/auth/govbr', 'AuthController@govbrLogin');
        $this->get('/auth/govbr/callback', 'AuthController@govbrCallback');
        
        // =======================================================
        // API ROUTES
        // =======================================================
        
        // API Pública
        $this->get('/api/config', 'ApiController@config');
        $this->get('/api/pedidos/protocolo/{protocolo}', 'ApiController@pedidoByProtocolo');
        
        // API Autenticação
        $this->post('/api/auth/login', 'ApiController@login');
        $this->post('/api/auth/refresh', 'ApiController@refreshToken', ['auth']);
        
        // API Pedidos (Autenticado)
        $this->get('/api/pedidos', 'ApiController@pedidos', ['auth']);
        $this->get('/api/pedidos/{id}', 'ApiController@pedido', ['auth']);
        $this->post('/api/pedidos', 'ApiController@createPedido', ['auth']);
        $this->put('/api/pedidos/{id}', 'ApiController@updatePedido', ['auth']);
        
        // API Recursos (Autenticado)
        $this->get('/api/recursos', 'ApiController@recursos', ['auth']);
        $this->post('/api/recursos', 'ApiController@createRecurso', ['auth']);
        
        // API Estatísticas
        $this->get('/api/stats', 'ApiController@stats', ['auth']);
        
    }
    
    /**
     * Registra rota GET
     */
    public function get($path, $handler, $middleware = []) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }
    
    /**
     * Registra rota POST
     */
    public function post($path, $handler, $middleware = []) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }
    
    /**
     * Registra rota PUT
     */
    public function put($path, $handler, $middleware = []) {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }
    
    /**
     * Registra rota DELETE
     */
    public function delete($path, $handler, $middleware = []) {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }
    
    /**
     * Adiciona rota ao sistema
     */
    private function addRoute($method, $path, $handler, $middleware = []) {
        // Converter {param} para regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    /**
     * Processa requisição atual
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover /esic do início da URI se presente
        $uri = preg_replace('#^/esic#', '', $uri);
        
        // Normalizar URI
        $uri = rtrim($uri, '/') ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                
                // Extrair parâmetros da URL
                array_shift($matches); // Remove match completo
                $params = $matches;
                
                // Executar middlewares
                foreach ($route['middleware'] as $middlewareName) {
                    $this->runMiddleware($middlewareName);
                }
                
                // Executar handler
                return $this->executeHandler($route['handler'], $params);
            }
        }
        
        // Rota não encontrada
        $this->notFound();
    }
    
    /**
     * Executa middleware
     */
    private function runMiddleware($name) {
        switch ($name) {
            case 'auth':
                AuthMiddleware::requireAuth();
                break;
            case 'admin':
                AuthMiddleware::requireAdmin();
                break;
            case 'csrf':
                AuthMiddleware::verifyCSRF();
                break;
        }
    }
    
    /**
     * Executa handler da rota
     */
    private function executeHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controllerName, $methodName) = explode('@', $handler);
            
            if (!class_exists($controllerName)) {
                throw new Exception("Controller não encontrado: {$controllerName}");
            }
            
            $controller = new $controllerName();
            
            if (!method_exists($controller, $methodName)) {
                throw new Exception("Método não encontrado: {$controllerName}@{$methodName}");
            }
            
            return call_user_func_array([$controller, $methodName], $params);
        }
        
        throw new Exception("Handler inválido");
    }
    
    /**
     * Página 404
     */
    private function notFound() {
        http_response_code(404);
        
        // Se for requisição API, retornar JSON
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Endpoint não encontrado']);
            return;
        }
        
        // Carregar página 404
        include '../app/views/errors/404.php';
    }
}

// =======================================================
// CLASSE DE RESPOSTA HTTP
// =======================================================

class Response {
    
    /**
     * Retorna resposta JSON
     */
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Redireciona para URL
     */
    public static function redirect($url, $status = 302) {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Retorna erro HTTP
     */
    public static function error($message, $status = 500) {
        http_response_code($status);
        
        if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
            self::json(['error' => $message], $status);
        } else {
            echo "<h1>Erro {$status}</h1><p>{$message}</p>";
            exit;
        }
    }
}

// =======================================================
// CLASSE HELPER PARA REQUEST
// =======================================================

class Request {
    
    /**
     * Obtém dados POST
     */
    public static function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Obtém dados GET
     */
    public static function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Obtém dados do corpo da requisição (JSON)
     */
    public static function json() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
    
    /**
     * Verifica se é requisição AJAX
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Obtém IP do cliente
     */
    public static function ip() {
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
}

// =======================================================
// TRATAMENTO DE ERROS
// =======================================================

// Handler de erros customizado
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $error = "Error: {$message} in {$file} on line {$line}";
    error_log($error);
    
    // Em desenvolvimento, mostrar erro
    if (($_ENV['APP_DEBUG'] ?? false) && ($_ENV['APP_ENV'] ?? '') !== 'production') {
        echo "<pre>{$error}</pre>";
    }
    
    return true;
});

// Handler de exceções
set_exception_handler(function($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
    
    if (($_ENV['APP_ENV'] ?? '') === 'production') {
        Response::error('Erro interno do servidor', 500);
    } else {
        echo "<pre>";
        echo "Uncaught Exception: " . $exception->getMessage() . "\n";
        echo "File: " . $exception->getFile() . "\n";
        echo "Line: " . $exception->getLine() . "\n";
        echo "Trace:\n" . $exception->getTraceAsString();
        echo "</pre>";
    }
});

// =======================================================
// EXECUÇÃO DA APLICAÇÃO
// =======================================================

try {
    // Criar e executar router
    $router = new Router();
    $router->dispatch();
    
} catch (Exception $e) {
    error_log("Router Exception: " . $e->getMessage());
    
    if (($_ENV['APP_ENV'] ?? '') === 'production') {
        Response::error('Erro interno do servidor', 500);
    } else {
        throw $e;
    }
} finally {
    // Limpar buffer de saída
    ob_end_flush();
}

// =======================================================
// FUNÇÕES HELPER GLOBAIS
// =======================================================

/**
 * Gera URL da aplicação
 */
function url($path = '') {
    $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/esic';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Gera URL de assets
 */
function asset($path) {
    return url($path);
}

/**
 * Escapa HTML
 */
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Formata data
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (!$date) return '';
    
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formata bytes
 */
function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

/**
 * Debug helper
 */
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Verifica se está em modo de manutenção
 */
function isMaintenanceMode() {
    return file_exists('../maintenance.lock');
}