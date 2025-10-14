<?php
/**
 * Teste de Dependências e Includes
 */

echo "=== TESTE DE DEPENDÊNCIAS E INCLUDES ===" . PHP_EOL . PHP_EOL;

// 1. Verificar extensões PHP necessárias
echo "1. EXTENSÕES PHP:" . PHP_EOL;
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'curl', 'json', 'fileinfo'];

foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✓" : "✗";
    echo "   {$status} {$ext}" . PHP_EOL;
}

echo PHP_EOL;

// 2. Verificar autoload do Composer
echo "2. AUTOLOAD DO COMPOSER:" . PHP_EOL;
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "   ✓ Autoload do Composer carregado" . PHP_EOL;
} else {
    echo "   ✗ Arquivo vendor/autoload.php não encontrado" . PHP_EOL;
    echo "     Execute: composer install" . PHP_EOL;
}

echo PHP_EOL;

// 3. Verificar carregamento das classes principais
echo "3. CLASSES DO SISTEMA:" . PHP_EOL;

$classes_to_test = [
    'Database' => 'app/config/Database.php',
    'Auth' => 'app/config/Auth.php',
    'OAuthHandler' => 'app/config/OAuthHandler.php',
    'AuthMiddleware' => 'app/middleware/AuthMiddleware.php'
];

foreach ($classes_to_test as $class => $file) {
    if (file_exists($file)) {
        try {
            require_once $file;
            if (class_exists($class)) {
                echo "   ✓ {$class} carregado com sucesso" . PHP_EOL;
            } else {
                echo "   ✗ {$class} não foi definido no arquivo {$file}" . PHP_EOL;
            }
        } catch (Exception $e) {
            echo "   ✗ Erro ao carregar {$class}: " . $e->getMessage() . PHP_EOL;
        }
    } else {
        echo "   ✗ Arquivo {$file} não encontrado" . PHP_EOL;
    }
}

echo PHP_EOL;

// 4. Testar instanciação das classes principais
echo "4. TESTE DE INSTANCIAÇÃO:" . PHP_EOL;

try {
    $db = Database::getInstance();
    echo "   ✓ Database::getInstance() funciona" . PHP_EOL;
} catch (Exception $e) {
    echo "   ✗ Database::getInstance() falhou: " . $e->getMessage() . PHP_EOL;
}

try {
    // Auth requires web environment, so we'll just check class existence
    if (class_exists('Auth')) {
        echo "   ✓ Classe Auth definida corretamente" . PHP_EOL;
    } else {
        echo "   ✗ Classe Auth não definida" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "   ✗ Auth falhou: " . $e->getMessage() . PHP_EOL;
}

try {
    // OAuthHandler requires web environment, check class existence
    if (class_exists('OAuthHandler')) {
        echo "   ✓ Classe OAuthHandler definida corretamente" . PHP_EOL;
    } else {
        echo "   ✗ Classe OAuthHandler não definida" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "   ✗ OAuthHandler falhou: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL;

// 5. Verificar se os modelos podem ser carregados
echo "5. MODELOS:" . PHP_EOL;
$model_files = glob('app/models/*.php');

foreach ($model_files as $file) {
    $class_name = pathinfo($file, PATHINFO_FILENAME);
    try {
        require_once $file;
        if (class_exists($class_name)) {
            echo "   ✓ Modelo {$class_name} carregado" . PHP_EOL;
        } else {
            echo "   ✗ Modelo {$class_name} não definido em {$file}" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   ✗ Erro no modelo {$class_name}: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL;

// 6. Verificar controllers
echo "6. CONTROLLERS:" . PHP_EOL;
$controller_files = glob('app/controllers/*.php');

foreach ($controller_files as $file) {
    $class_name = pathinfo($file, PATHINFO_FILENAME);
    try {
        require_once $file;
        if (class_exists($class_name)) {
            echo "   ✓ Controller {$class_name} carregado" . PHP_EOL;
        } else {
            echo "   ✗ Controller {$class_name} não definido em {$file}" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "   ✗ Erro no controller {$class_name}: " . $e->getMessage() . PHP_EOL;
    }
}

echo PHP_EOL;

// 7. Verificar diretórios e permissões importantes
echo "7. DIRETÓRIOS E PERMISSÕES:" . PHP_EOL;

$dirs_to_check = [
    'uploads' => 'Diretório para arquivos enviados',
    'logs' => 'Diretório para logs do sistema',
    'app/views/cache' => 'Cache de views (se usado)',
    'tmp' => 'Diretório temporário'
];

foreach ($dirs_to_check as $dir => $desc) {
    if (file_exists($dir)) {
        $writable = is_writable($dir) ? "✓ Gravável" : "✗ Não gravável";
        echo "   ✓ {$dir} existe - {$writable}" . PHP_EOL;
    } else {
        echo "   ✗ {$dir} não existe - {$desc}" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "=== FIM DO TESTE ===" . PHP_EOL;