<?php
echo "<h1>Debug de Rotas E-SIC</h1>";

// Mostrar variáveis de ambiente
echo "<h2>Informações da Request:</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'não definido') . "</p>";

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
echo "<p><strong>Path original:</strong> $path</p>";

// Remove query string and normalize path
$path = '/' . trim($path, '/');
echo "<p><strong>Path normalizado:</strong> $path</p>";

echo "<h2>Teste das Rotas:</h2>";
switch ($path) {
    case '/':
    case '/home':
        echo "<p>✓ Rota HOME detectada</p>";
        break;
        
    case '/novo-pedido':
        echo "<p>✓ Rota NOVO PEDIDO detectada</p>";
        break;
        
    case '/acompanhar':
        echo "<p>✓ Rota ACOMPANHAR detectada</p>";
        break;
        
    case '/lai':
        echo "<p>✓ Rota LAI detectada</p>";
        break;
        
    case '/sobre':
        echo "<p>✓ Rota SOBRE detectada</p>";
        break;
        
    case '/transparencia':
        echo "<p>✓ Rota TRANSPARÊNCIA detectada</p>";
        break;
        
    default:
        echo "<p>❌ Rota não reconhecida: $path</p>";
        
        // Check for dynamic routes
        if (preg_match('/^\/pedido\/([^\/]+)$/', $path, $matches)) {
            echo "<p>✓ Rota dinâmica PEDIDO detectada: " . $matches[1] . "</p>";
        } elseif (preg_match('/^\/recurso\/(\d+)$/', $path, $matches)) {
            echo "<p>✓ Rota dinâmica RECURSO detectada: " . $matches[1] . "</p>";
        } elseif (preg_match('/^\/download\/(.+)$/', $path, $matches)) {
            echo "<p>✓ Rota dinâmica DOWNLOAD detectada: " . $matches[1] . "</p>";
        } else {
            echo "<p>❌ Nenhuma rota dinâmica encontrada</p>";
        }
        break;
}

echo "<h2>Teste do Sistema:</h2>";
echo "<p><a href='/esic/'>Testar página inicial</a></p>";
echo "<p><a href='/esic/novo-pedido'>Testar novo pedido</a></p>";
echo "<p><a href='/esic/acompanhar'>Testar acompanhar</a></p>";
?>