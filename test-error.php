<?php
// FORÇAR EXIBIÇÃO DE TODOS OS ERROS
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Teste de Erro</title></head><body>";
echo "<h1 style='color: green;'>✅ PHP está funcionando!</h1>";
echo "<p><strong>Versão PHP:</strong> " . phpversion() . "</p>";

echo "<h2>Testando login.php:</h2>";
echo "<p>Tamanho do arquivo: " . filesize(__DIR__ . '/login.php') . " bytes</p>";

echo "<h3>Tentando incluir login.php...</h3>";

// Tentar incluir o login.php
try {
    include(__DIR__ . '/login.php');
    echo "<p style='color: green;'>✅ login.php incluído sem erros!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ ERRO: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
