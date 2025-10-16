<?php
echo "<!DOCTYPE html>";
echo "<html><head><title>Teste de Redirecionamento</title></head>";
echo "<body style='font-family: Arial; padding: 20px;'>";
echo "<h1>🔍 Diagnóstico de Acesso</h1>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>URL Atual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Script:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>Método:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<hr>";
echo "<h2>Teste de Links</h2>";
echo "<ul>";
echo "<li><a href='index.php'>index.php (raiz)</a></li>";
echo "<li><a href='public/'>public/ (pasta)</a></li>";
echo "<li><a href='public/index.php'>public/index.php (direto)</a></li>";
echo "<li><a href='login.php'>login.php (teste antigo)</a></li>";
echo "</ul>";
echo "<hr>";
echo "<h2>Arquivos na Raiz</h2>";
echo "<ul>";
$files = scandir('.');
foreach($files as $file) {
    if($file != '.' && $file != '..') {
        $type = is_dir($file) ? '[DIR]' : '[FILE]';
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";
echo "</body></html>";
?>