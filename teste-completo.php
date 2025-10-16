<?php
// Forçar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Criar log
$log = "=== TESTE DE ACESSO ===" . PHP_EOL;
$log .= "Data/Hora: " . date('Y-m-d H:i:s') . PHP_EOL;
$log .= "Página: " . $_SERVER['PHP_SELF'] . PHP_EOL;
$log .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
$log .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
$log .= PHP_EOL;

// Salvar log
file_put_contents(__DIR__ . '/logs/teste-acesso.log', $log, FILE_APPEND);

// Mostrar na tela
echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Teste OK</title></head>";
echo "<body style='font-family: Arial; padding: 40px; background: #e8f5e9;'>";
echo "<div style='max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
echo "<h1 style='color: #2e7d32;'>✅ PHP ESTÁ FUNCIONANDO!</h1>";
echo "<p><strong>Data/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Versão PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Arquivo:</strong> " . __FILE__ . "</p>";
echo "<hr>";
echo "<h2>Testes Rápidos:</h2>";
echo "<div style='display: grid; gap: 10px;'>";
echo "<a href='index.php' style='padding: 15px; background: #1976d2; color: white; text-decoration: none; border-radius: 5px; text-align: center;'>🔄 Testar index.php</a>";
echo "<a href='login.php' style='padding: 15px; background: #388e3c; color: white; text-decoration: none; border-radius: 5px; text-align: center;'>📝 Abrir login.php</a>";
echo "<a href='teste1.php' style='padding: 15px; background: #f57c00; color: white; text-decoration: none; border-radius: 5px; text-align: center;'>🧪 Teste 1 (simples)</a>";
echo "<a href='teste2.html' style='padding: 15px; background: #7b1fa2; color: white; text-decoration: none; border-radius: 5px; text-align: center;'>🧪 Teste 2 (HTML)</a>";
echo "</div>";
echo "<hr>";
echo "<p><strong>Log salvo em:</strong> logs/teste-acesso.log</p>";
echo "</div>";
echo "</body></html>";
?>
