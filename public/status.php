<!DOCTYPE html>
<html>
<head>
    <title>Sistema E-SIC - Status</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🏥 Sistema E-SIC - Diagnóstico Final</h1>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // 1. Teste básico
    echo "<h2>1. Teste Básico PHP</h2>";
    echo "<p class='success'>✅ PHP está funcionando - Versão: " . phpversion() . "</p>";
    
    // 2. Teste de arquivos
    echo "<h2>2. Arquivos Necessários</h2>";
    $files = [
        'index.php' => 'Controlador principal',
        '.htaccess' => 'Configuração Apache',
        '../app/controllers/HomeController.php' => 'Controller Home',
        '../app/views/errors/404.php' => 'Página de erro 404'
    ];
    
    foreach ($files as $file => $desc) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ $desc</p>";
        } else {
            echo "<p class='error'>❌ $desc - FALTANDO: $file</p>";
        }
    }
    
    // 3. Teste das URLs
    echo "<h2>3. Teste de URLs</h2>";
    echo "<p class='info'>URL atual: " . $_SERVER['REQUEST_URI'] . "</p>";
    
    echo "<h3>Links Funcionais:</h3>";
    echo "<ul>";
    echo "<li><a href='/esic/public/index.php'>✅ Index direto</a></li>";
    echo "<li><a href='/esic/public/teste-controller.php'>✅ Teste Controller</a></li>";
    echo "<li><a href='/esic/public/diagnostico.php'>✅ Diagnóstico</a></li>";
    echo "</ul>";
    
    echo "<h3>Links com Rewrite:</h3>";
    echo "<ul>";
    echo "<li><a href='/esic/public/'>🎯 Página Principal (pode dar 404)</a></li>";
    echo "<li><a href='/esic/public/home'>🎯 Home</a></li>";
    echo "<li><a href='/esic/public/novo-pedido'>🎯 Novo Pedido</a></li>";
    echo "</ul>";
    
    // 4. Informações do Apache
    echo "<h2>4. Configuração Apache</h2>";
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "<p class='success'>✅ mod_rewrite habilitado</p>";
        } else {
            echo "<p class='error'>❌ mod_rewrite não encontrado</p>";
        }
    } else {
        echo "<p class='info'>ℹ️ Não foi possível verificar módulos Apache</p>";
    }
    
    // 5. Conteúdo do .htaccess
    echo "<h2>5. Conteúdo .htaccess</h2>";
    if (file_exists('.htaccess')) {
        echo "<pre>";
        echo htmlspecialchars(file_get_contents('.htaccess'));
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ Arquivo .htaccess não encontrado</p>";
    }
    ?>
    
    <hr>
    <h2>📋 Resumo</h2>
    <p>Se você está vendo esta página, o Apache está funcionando.</p>
    <p><strong>Se a página principal dá 404:</strong></p>
    <ol>
        <li>Verifique se mod_rewrite está habilitado no Apache</li>
        <li>Teste acessando <a href="index.php">index.php</a> diretamente</li>
        <li>Verifique logs do Apache: C:\xampp\apache\logs\error.log</li>
    </ol>
    
    <p><strong>URLs alternativas:</strong></p>
    <ul>
        <li>Servidor PHP: <a href="http://localhost:8080/">http://localhost:8080/</a></li>
        <li>Index direto: <a href="/esic/public/index.php">/esic/public/index.php</a></li>
    </ul>
</body>
</html>