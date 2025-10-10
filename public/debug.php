<?php
echo "Sistema E-SIC está funcionando!<br>";
echo "Data: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Testar se os arquivos existem
$files = [
    '../app/config/Database.php',
    '../app/config/Auth.php',
    '../app/controllers/HomeController.php'
];

echo "<h3>Verificação de arquivos:</h3>";
foreach ($files as $file) {
    echo $file . ": " . (file_exists($file) ? "EXISTS" : "NOT FOUND") . "<br>";
}

// Testar autoload
echo "<h3>Teste de Autoload:</h3>";
spl_autoload_register(function ($class) {
    echo "Tentando carregar classe: $class<br>";
    $directories = [
        '../app/controllers/',
        '../app/models/',
        '../app/config/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            echo "Carregado: $file<br>";
            return;
        }
    }
});

// Testar se consegue carregar o HomeController
try {
    $controller = new HomeController();
    echo "HomeController carregado com sucesso!<br>";
} catch (Exception $e) {
    echo "Erro ao carregar HomeController: " . $e->getMessage() . "<br>";
}
?>