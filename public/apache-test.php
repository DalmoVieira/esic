<?php
/**
 * Teste de Configuração do Apache/XAMPP para Sistema E-SIC
 */

echo "<h1>Sistema E-SIC - Teste Apache</h1>";

// Verificar se mod_rewrite está habilitado
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✅ mod_rewrite está HABILITADO</p>";
    } else {
        echo "<p style='color: red;'>❌ mod_rewrite NÃO está habilitado</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Não foi possível verificar mod_rewrite (função apache_get_modules não disponível)</p>";
}

// Verificar diretório atual
echo "<p><strong>Diretório atual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Script atual:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Verificar se arquivo index.php existe
$indexFile = __DIR__ . '/index.php';
if (file_exists($indexFile)) {
    echo "<p style='color: green;'>✅ index.php encontrado</p>";
} else {
    echo "<p style='color: red;'>❌ index.php NÃO encontrado</p>";
}

// Verificar se .htaccess existe
$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    echo "<p style='color: green;'>✅ .htaccess encontrado</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars(file_get_contents($htaccessFile));
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ .htaccess NÃO encontrado</p>";
}

// Links de teste
echo "<h2>Links de Teste:</h2>";
echo "<ul>";
echo "<li><a href='/esic/public/'>Página Principal</a></li>";
echo "<li><a href='/esic/public/test.php'>Esta página de teste</a></li>";
echo "<li><a href='/esic/public/auth/login'>Login (com rewrite)</a></li>";
echo "<li><a href='http://localhost:8080/'>Servidor PHP (porta 8080)</a></li>";
echo "</ul>";

phpinfo();
?>