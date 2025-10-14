<?php
/**
 * Teste de Roteamento - Sistema E-SIC
 */

// Incluir os arquivos necessários
require_once '../app/config/Database.php';
require_once '../app/config/Auth.php';

// Função de autoload
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

echo "<h1>🧪 Teste de Roteamento E-SIC</h1>";

// 1. Testar carregamento de classes
echo "<h2>1. Carregamento de Classes</h2>";

$classes_to_test = [
    'HomeController',
    'PedidoController', 
    'AuthController',
    'Database',
    'Auth'
];

foreach ($classes_to_test as $class) {
    if (class_exists($class)) {
        echo "<p style='color: green;'>✅ $class carregado com sucesso</p>";
    } else {
        echo "<p style='color: red;'>❌ $class NÃO pôde ser carregado</p>";
    }
}

// 2. Testar HomeController
echo "<h2>2. Teste HomeController</h2>";
try {
    if (class_exists('HomeController')) {
        $home = new HomeController();
        if (method_exists($home, 'index')) {
            echo "<p style='color: green;'>✅ Método index() existe no HomeController</p>";
        } else {
            echo "<p style='color: red;'>❌ Método index() NÃO existe no HomeController</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao testar HomeController: " . $e->getMessage() . "</p>";
}

// 3. Testar rota atual
echo "<h2>3. Informações da Requisição</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'não definido') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'não definido') . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . ($_SERVER['PATH_INFO'] ?? 'não definido') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'não definido') . "</p>";

// 4. Simular roteamento
echo "<h2>4. Simulação de Rotas</h2>";

$test_routes = [
    '/' => 'HomeController@index',
    '/home' => 'HomeController@index', 
    '/novo-pedido' => 'PedidoController@formulario',
    '/auth/login' => 'AuthController@showLogin'
];

foreach ($test_routes as $route => $handler) {
    list($controller_name, $method) = explode('@', $handler);
    
    if (class_exists($controller_name)) {
        $controller = new $controller_name();
        if (method_exists($controller, $method)) {
            echo "<p style='color: green;'>✅ Rota '$route' → $handler (OK)</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Rota '$route' → Controller existe mas método '$method' não encontrado</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Rota '$route' → Controller '$controller_name' não encontrado</p>";
    }
}

// 5. Links de teste
echo "<h2>5. Links de Teste</h2>";
echo "<ul>";
echo "<li><a href='/esic/public/'>Página Principal (rota /)</a></li>";
echo "<li><a href='/esic/public/home'>Home (rota /home)</a></li>";
echo "<li><a href='/esic/public/novo-pedido'>Novo Pedido</a></li>";
echo "<li><a href='/esic/public/auth/login'>Login</a></li>";
echo "<li><a href='/esic/public/index.php'>Index direto</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Se você está vendo esta página, o PHP está funcionando!</strong></p>";
echo "<p>O problema pode estar no sistema de roteamento ou na configuração do .htaccess</p>";
?>