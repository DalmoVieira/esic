<?php
// Teste simples de funcionamento

echo "🔍 Diagnóstico do Sistema E-SIC\n\n";

// 1. Testar PHP básico
echo "✅ PHP funcionando: " . phpversion() . "\n";

// 2. Testar se consegue incluir arquivos
try {
    if (file_exists('../app/config/Database.php')) {
        echo "✅ Arquivo Database.php encontrado\n";
    } else {
        echo "❌ Database.php não encontrado\n";
    }
    
    if (file_exists('../app/controllers/HomeController.php')) {
        echo "✅ HomeController.php encontrado\n";
    } else {
        echo "❌ HomeController.php não encontrado\n";
    }
    
    // 3. Testar autoload básico
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
                echo "✅ Carregado: $class\n";
                return;
            }
        }
    });
    
    // 4. Testar carregamento da configuração
    require_once '../app/config/Database.php';
    echo "✅ Database.php carregado\n";
    
    // 5. Testar HomeController
    if (class_exists('HomeController')) {
        echo "✅ HomeController existe\n";
    } else {
        echo "❌ HomeController não pode ser carregado\n";
    }
    
    echo "\n🎯 Sistema parece estar OK! \n";
    echo "Teste via navegador: http://localhost:8090/teste-rapido.php\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}
?>