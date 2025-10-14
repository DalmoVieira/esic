<?php
// E-SIC - Sistema Eletrônico de Informações ao Cidadão
// Versão de debug/teste

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load bootstrap
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\PublicController;

// Simple routing
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove base path if exists (for subdirectory installations)
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Remove query string and normalize path
$path = '/' . trim($path, '/');

// Debug info
if (isset($_GET['debug'])) {
    echo "<h1>Debug E-SIC</h1>";
    echo "<p>URI: " . htmlspecialchars($uri) . "</p>";
    echo "<p>Path: " . htmlspecialchars($path) . "</p>";
    echo "<p>Base Path: " . htmlspecialchars($basePath) . "</p>";
    exit;
}

try {
    $controller = new PublicController();
    
    // Basic routing
    switch ($path) {
        case '/':
        case '/home':
            $controller->index();
            break;
            
        case '/novo-pedido':
            $controller->novoPedido();
            break;
            
        case '/acompanhar':
            $controller->acompanhar();
            break;
            
        case '/lai':
            $controller->lai();
            break;
            
        case '/sobre':
            $controller->sobre();
            break;
            
        case '/transparencia':
            $controller->transparencia();
            break;
            
        default:
            // Check for dynamic routes
            if (preg_match('/^\/pedido\/([^\/]+)$/', $path, $matches)) {
                $controller->pedidoDetalhes($matches[1]);
            } elseif (preg_match('/^\/recurso\/(\d+)$/', $path, $matches)) {
                $controller->recurso($matches[1]);
            } elseif (preg_match('/^\/download\/(.+)$/', $path, $matches)) {
                $controller->download($matches[1]);
            } else {
                // 404 - Not found
                http_response_code(404);
                echo "Página não encontrada";
            }
            break;
    }
    
} catch (Exception $e) {
    // Handle errors
    if (APP_DEBUG) {
        echo "<h1>Erro:</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        echo "Erro interno do servidor";
    }
    
    // Log error
    error_log("E-SIC Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
}