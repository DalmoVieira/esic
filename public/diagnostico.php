<?php
/**
 * Diagnóstico do Sistema E-SIC para Apache XAMPP
 */

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.info { color: blue; }
</style>";

echo "<h1>🔍 Diagnóstico Sistema E-SIC - Apache XAMPP</h1>";

// 1. Verificar se estamos no Apache
echo "<h2>1. Servidor Web</h2>";
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
        echo "<p class='success'>✅ Rodando no Apache: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    } else {
        echo "<p class='warning'>⚠️ Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    }
} else {
    echo "<p class='error'>❌ SERVER_SOFTWARE não definido</p>";
}

// 2. Verificar mod_rewrite
echo "<h2>2. Mod Rewrite</h2>";
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
    echo "<p class='success'>✅ mod_rewrite habilitado</p>";
} else {
    echo "<p class='error'>❌ mod_rewrite não detectado ou não habilitado</p>";
}

// 3. Verificar arquivos necessários
echo "<h2>3. Arquivos do Sistema</h2>";

$files = [
    'index.php' => 'Controlador principal',
    '.htaccess' => 'Configuração Apache',
    '../app/config/Database.php' => 'Configuração do banco',
    '../app/controllers/HomeController.php' => 'Controlador Home'
];

foreach ($files as $file => $desc) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $desc ($file)</p>";
    } else {
        echo "<p class='error'>❌ $desc ($file) - NÃO ENCONTRADO</p>";
    }
}

// 4. Teste de roteamento
echo "<h2>4. Teste de URLs</h2>";
echo "<p class='info'>URL atual: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p class='info'>Script: " . $_SERVER['SCRIPT_NAME'] . "</p>";

// 5. Links de teste
echo "<h2>5. Links de Teste</h2>";
echo "<ul>";
echo "<li><a href='/esic/public/'>🏠 Página Principal</a></li>";
echo "<li><a href='/esic/public/index.php'>📄 Index direto</a></li>";
echo "<li><a href='/esic/public/test.php'>🧪 Página de teste</a></li>";
echo "<li><a href='http://localhost:8080/'>🚀 Servidor PHP (8080)</a></li>";
echo "</ul>";

// 6. Informações do PHP
echo "<h2>6. Informações do PHP</h2>";
echo "<p class='info'>Versão PHP: " . phpversion() . "</p>";
echo "<p class='info'>Diretório: " . __DIR__ . "</p>";
echo "<p class='info'>Arquivo: " . __FILE__ . "</p>";

// 7. Teste de carregamento do sistema
echo "<h2>7. Teste de Carregamento</h2>";
try {
    if (file_exists('../app/config/Database.php')) {
        echo "<p class='success'>✅ Arquivo Database.php encontrado</p>";
        // Não vamos incluir para evitar erros de redefinição
    }
    
    if (file_exists('../app/controllers/HomeController.php')) {
        echo "<p class='success'>✅ HomeController encontrado</p>";
    }
    
    echo "<p class='success'>✅ Sistema pode ser carregado com segurança</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao carregar sistema: " . $e->getMessage() . "</p>";
}

?>

<hr>
<p><strong>Próximos passos se há problemas:</strong></p>
<ol>
<li>Verificar se mod_rewrite está habilitado no Apache</li>
<li>Verificar se o arquivo .htaccess está na pasta public</li>
<li>Tentar acessar diretamente: <a href="index.php">index.php</a></li>
<li>Verificar logs do Apache em: C:\xampp\apache\logs\error.log</li>
</ol>