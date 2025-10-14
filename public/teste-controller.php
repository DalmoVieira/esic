<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Teste Direto HomeController</h1>";

// Autoload
spl_autoload_register(function ($class) {
    $directories = [
        '../app/controllers/',
        '../app/models/',
        '../app/libraries/',
        '../app/middleware/',
        '../app/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            echo "<p>📁 Carregado: $file</p>";
            return;
        }
    }
    echo "<p style='color: red;'>❌ Não foi possível carregar: $class</p>";
});

try {
    echo "<h2>1. Carregando configurações...</h2>";
    require_once '../app/config/Database.php';
    echo "<p>✅ Database.php carregado</p>";
    
    require_once '../app/config/Auth.php';
    echo "<p>✅ Auth.php carregado</p>";
    
    echo "<h2>2. Testando HomeController...</h2>";
    
    if (!class_exists('HomeController')) {
        echo "<p style='color: red;'>❌ HomeController não encontrado</p>";
        exit;
    }
    
    echo "<p>✅ Classe HomeController existe</p>";
    
    $controller = new HomeController();
    echo "<p>✅ HomeController instanciado com sucesso</p>";
    
    if (method_exists($controller, 'index')) {
        echo "<p>✅ Método index() existe</p>";
        
        echo "<h2>3. Executando método index()...</h2>";
        
        // Simular sessão
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        ob_start();
        $result = $controller->index();
        $output = ob_get_clean();
        
        echo "<p>✅ Método executado com sucesso</p>";
        echo "<h3>Output do método:</h3>";
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
        echo $output;
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Método index() não existe</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erro:</h2>";
    echo "<pre style='background: #ffe6e6; padding: 15px; border-radius: 5px;'>";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n"; 
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo "</pre>";
} catch (Error $e) {
    echo "<h2 style='color: red;'>❌ Fatal Error:</h2>";
    echo "<pre style='background: #ffe6e6; padding: 15px; border-radius: 5px;'>";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>