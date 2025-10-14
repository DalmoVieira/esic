<?php
// Simular uma requisição GET para / 
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/esic/public/index.php';

echo "<h1>🔍 Teste Direto do Sistema E-SIC</h1>";
echo "<p>Simulando requisição para rota '/'</p>";

try {
    // Carregar o sistema
    include 'index.php';
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erro encontrado:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>