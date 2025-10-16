<?php
// Script de teste para diagnosticar problemas de redirecionamento

echo "<h1>Teste de Redirecionamento</h1>";

echo "<h2>1. Informações do Sistema:</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "</pre>";

echo "<h2>2. Verificando arquivos:</h2>";
echo "<pre>";

$files_to_check = [
    'index.php',
    'login.php',
    'novo-pedido.php',
    'admin-pedidos.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✓ $file existe\n";
        echo "  Tamanho: " . filesize($full_path) . " bytes\n";
        echo "  Permissões: " . substr(sprintf('%o', fileperms($full_path)), -4) . "\n";
    } else {
        echo "✗ $file NÃO ENCONTRADO\n";
    }
}

echo "</pre>";

echo "<h2>3. Conteúdo do index.php:</h2>";
echo "<pre>";
echo htmlspecialchars(file_get_contents(__DIR__ . '/index.php'));
echo "</pre>";

echo "<h2>4. Headers já enviados?</h2>";
echo "<pre>";
if (headers_sent($file, $line)) {
    echo "❌ SIM - Headers já foram enviados em $file na linha $line\n";
} else {
    echo "✓ NÃO - Headers ainda podem ser enviados\n";
}
echo "</pre>";

echo "<h2>5. Teste de redirecionamento:</h2>";
echo "<a href='index.php' class='btn btn-primary'>Testar Redirecionamento do index.php</a><br><br>";
echo "<a href='login.php' class='btn btn-success'>Acessar login.php diretamente</a>";

echo "<style>
    body { font-family: Arial; padding: 20px; }
    .btn { 
        display: inline-block; 
        padding: 10px 20px; 
        margin: 5px; 
        text-decoration: none; 
        color: white; 
        border-radius: 5px; 
    }
    .btn-primary { background: #0d6efd; }
    .btn-success { background: #198754; }
</style>";
?>
