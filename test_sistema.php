<?php
/**
 * Teste Abrangente do Sistema E-SIC
 * Validação final de todas as funcionalidades
 */

echo "=== TESTE FINAL DO SISTEMA E-SIC ===" . PHP_EOL . PHP_EOL;

// =======================================================
// 1. ESTRUTURA DO PROJETO
// =======================================================
echo "1. VERIFICAÇÃO DA ESTRUTURA:" . PHP_EOL;

$requiredDirs = [
    'app/controllers' => 'Controllers da aplicação',
    'app/models' => 'Modelos de dados',
    'app/views' => 'Templates de visualização',
    'app/config' => 'Configurações do sistema',
    'app/middleware' => 'Middlewares de autenticação',
    'app/libraries' => 'Bibliotecas personalizadas',
    'public' => 'Arquivos públicos',
    'database' => 'Scripts de banco de dados',
    'uploads' => 'Diretório de uploads',
    'logs' => 'Logs do sistema',
    'tmp' => 'Arquivos temporários'
];

$allDirsExist = true;
foreach ($requiredDirs as $dir => $desc) {
    if (is_dir($dir)) {
        echo "   ✓ {$dir}/ - {$desc}" . PHP_EOL;
    } else {
        echo "   ✗ {$dir}/ - FALTANDO: {$desc}" . PHP_EOL;
        $allDirsExist = false;
    }
}

echo "   Estrutura: " . ($allDirsExist ? "✓ COMPLETA" : "✗ INCOMPLETA") . PHP_EOL . PHP_EOL;

// =======================================================
// 2. ARQUIVOS ESSENCIAIS
// =======================================================
echo "2. ARQUIVOS ESSENCIAIS:" . PHP_EOL;

$requiredFiles = [
    'public/index.php' => 'Front controller e sistema de rotas',
    'app/config/Database.php' => 'Configuração do banco de dados',
    'app/config/Auth.php' => 'Sistema de autenticação',
    'app/config/OAuthHandler.php' => 'Handler OAuth para Google/Gov.br',
    'app/middleware/AuthMiddleware.php' => 'Middleware de autenticação',
    'app/libraries/EmailService.php' => 'Serviço de envio de emails',
    'database/schema.sql' => 'Schema do banco de dados',
    '.env.example' => 'Exemplo de configurações',
    '.env' => 'Configurações de ambiente',
    'composer.json' => 'Dependências do projeto',
    'README.md' => 'Documentação do projeto'
];

$allFilesExist = true;
foreach ($requiredFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "   ✓ {$file} - {$desc}" . PHP_EOL;
    } else {
        echo "   ✗ {$file} - FALTANDO: {$desc}" . PHP_EOL;
        $allFilesExist = false;
    }
}

echo "   Arquivos: " . ($allFilesExist ? "✓ COMPLETOS" : "✗ INCOMPLETOS") . PHP_EOL . PHP_EOL;

// =======================================================
// 3. CARREGAMENTO DE CLASSES
// =======================================================
echo "3. CLASSES DO SISTEMA:" . PHP_EOL;

require_once 'app/config/Database.php';
require_once 'app/config/Auth.php';
require_once 'app/config/OAuthHandler.php';
require_once 'app/middleware/AuthMiddleware.php';
require_once 'app/libraries/EmailService.php';

$classes = [
    'Database' => 'Conexão com banco de dados',
    'Auth' => 'Sistema de autenticação',
    'OAuthHandler' => 'Handler OAuth',
    'AuthMiddleware' => 'Middleware de autenticação',
    'EmailService' => 'Serviço de email'
];

$allClassesLoaded = true;
foreach ($classes as $class => $desc) {
    if (class_exists($class)) {
        echo "   ✓ {$class} - {$desc}" . PHP_EOL;
    } else {
        echo "   ✗ {$class} - FALHA: {$desc}" . PHP_EOL;
        $allClassesLoaded = false;
    }
}

echo "   Classes: " . ($allClassesLoaded ? "✓ CARREGADAS" : "✗ FALHAS NO CARREGAMENTO") . PHP_EOL . PHP_EOL;

// =======================================================
// 4. CONEXÃO COM BANCO DE DADOS
// =======================================================
echo "4. BANCO DE DADOS:" . PHP_EOL;

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "   ✓ Conexão estabelecida com sucesso" . PHP_EOL;
    
    // Testar query básica
    $result = $db->selectOne('SELECT VERSION() as version, DATABASE() as database_name');
    echo "   ✓ MySQL/MariaDB: " . $result['version'] . PHP_EOL;
    echo "   ✓ Database: " . $result['database_name'] . PHP_EOL;
    
    // Verificar tabelas principais
    $tables = ['usuarios', 'pedidos', 'recursos', 'configuracoes'];
    $tablesExist = true;
    
    foreach ($tables as $table) {
        $exists = $db->select("SHOW TABLES LIKE '$table'");
        if (!empty($exists)) {
            echo "   ✓ Tabela '$table' existe" . PHP_EOL;
        } else {
            echo "   ✗ Tabela '$table' não existe" . PHP_EOL;
            $tablesExist = false;
        }
    }
    
    echo "   Banco de dados: " . ($tablesExist ? "✓ CONFIGURADO" : "✗ TABELAS FALTANDO") . PHP_EOL;
    
} catch (Exception $e) {
    echo "   ✗ Erro na conexão: " . $e->getMessage() . PHP_EOL;
    echo "   Banco de dados: ✗ FALHA" . PHP_EOL;
}

echo PHP_EOL;

// =======================================================
// 5. EXTENSÕES PHP
// =======================================================
echo "5. EXTENSÕES PHP REQUERIDAS:" . PHP_EOL;

$required_extensions = [
    'pdo' => 'Acesso ao banco de dados',
    'pdo_mysql' => 'Driver MySQL para PDO', 
    'mbstring' => 'Manipulação de strings multibyte',
    'openssl' => 'Criptografia e HTTPS',
    'curl' => 'Requisições HTTP (OAuth)',
    'json' => 'Manipulação JSON',
    'fileinfo' => 'Informações de arquivos',
    'session' => 'Gerenciamento de sessões',
    'filter' => 'Filtros e validação',
    'hash' => 'Funções de hash'
];

$allExtensionsLoaded = true;
foreach ($required_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "   ✓ {$ext} - {$desc}" . PHP_EOL;
    } else {
        echo "   ✗ {$ext} - FALTANDO: {$desc}" . PHP_EOL;
        $allExtensionsLoaded = false;
    }
}

echo "   Extensões: " . ($allExtensionsLoaded ? "✓ COMPLETAS" : "✗ EXTENSÕES FALTANDO") . PHP_EOL . PHP_EOL;

// =======================================================
// 6. CONFIGURAÇÕES DE SEGURANÇA
// =======================================================
echo "6. CONFIGURAÇÕES DE SEGURANÇA:" . PHP_EOL;

// Verificar configurações básicas de segurança
$securityChecks = [];

// Session security
$sessionConfig = [
    'session.cookie_httponly' => ini_get('session.cookie_httponly'),
    'session.cookie_secure' => ini_get('session.cookie_secure'),
    'session.use_strict_mode' => ini_get('session.use_strict_mode')
];

foreach ($sessionConfig as $setting => $value) {
    $status = $value ? "✓" : "✗";
    echo "   {$status} {$setting}: " . ($value ? 'ON' : 'OFF') . PHP_EOL;
    $securityChecks[] = (bool)$value;
}

// File upload settings
$uploadMaxSize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');
echo "   ✓ upload_max_filesize: {$uploadMaxSize}" . PHP_EOL;
echo "   ✓ post_max_size: {$postMaxSize}" . PHP_EOL;

// Directory permissions
$dirs = ['uploads', 'logs', 'tmp'];
foreach ($dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "   ✓ Diretório {$dir}/ é gravável" . PHP_EOL;
    } else {
        echo "   ✗ Diretório {$dir}/ não é gravável" . PHP_EOL;
        $securityChecks[] = false;
    }
}

echo PHP_EOL;

// =======================================================
// 7. CONTROLLERS E MODELOS
// =======================================================
echo "7. CONTROLLERS E MODELOS:" . PHP_EOL;

// Verificar controllers principais
$controllers = [
    'HomeController',
    'PedidoController',
    'RecursoController', 
    'AdminController',
    'AuthController',
    'ApiController'
];

foreach ($controllers as $controller) {
    $file = "app/controllers/{$controller}.php";
    if (file_exists($file)) {
        require_once $file;
        if (class_exists($controller)) {
            echo "   ✓ {$controller}" . PHP_EOL;
        } else {
            echo "   ✗ {$controller} - classe não definida" . PHP_EOL;
        }
    } else {
        echo "   ✗ {$controller} - arquivo não existe" . PHP_EOL;
    }
}

// Verificar modelos principais
$models = ['Usuario', 'Pedido', 'Recurso', 'AuthLog'];
foreach ($models as $model) {
    $file = "app/models/{$model}.php";
    if (file_exists($file)) {
        require_once $file;
        if (class_exists($model)) {
            echo "   ✓ Model {$model}" . PHP_EOL;
        } else {
            echo "   ✗ Model {$model} - classe não definida" . PHP_EOL;
        }
    } else {
        echo "   ✗ Model {$model} - arquivo não existe" . PHP_EOL;
    }
}

echo PHP_EOL;

// =======================================================
// 8. RESUMO FINAL
// =======================================================
echo "=== RESUMO FINAL ===" . PHP_EOL;

$overallStatus = $allDirsExist && $allFilesExist && $allClassesLoaded && $allExtensionsLoaded;

echo "Estrutura do Projeto: " . ($allDirsExist ? "✓ OK" : "✗ PROBLEMAS") . PHP_EOL;
echo "Arquivos Essenciais: " . ($allFilesExist ? "✓ OK" : "✗ PROBLEMAS") . PHP_EOL;
echo "Classes do Sistema: " . ($allClassesLoaded ? "✓ OK" : "✗ PROBLEMAS") . PHP_EOL;
echo "Extensões PHP: " . ($allExtensionsLoaded ? "✓ OK" : "✗ PROBLEMAS") . PHP_EOL;

echo PHP_EOL;

if ($overallStatus) {
    echo "🎉 SISTEMA E-SIC: ✅ PRONTO PARA USO!" . PHP_EOL;
    echo PHP_EOL;
    echo "Próximos passos:" . PHP_EOL;
    echo "1. Acesse: http://localhost:8080" . PHP_EOL;
    echo "2. Login admin: admin@esic.gov.br (senha padrão no banco)" . PHP_EOL;
    echo "3. Configure as variáveis do arquivo .env conforme necessário" . PHP_EOL;
    echo "4. Configure SMTP para envio de emails" . PHP_EOL;
    echo "5. Configure OAuth (Google/Gov.br) se necessário" . PHP_EOL;
} else {
    echo "⚠️  SISTEMA E-SIC: ❌ PROBLEMAS DETECTADOS" . PHP_EOL;
    echo "Verifique os itens marcados com ✗ acima e corrija antes de usar em produção." . PHP_EOL;
}

echo PHP_EOL . "=== FIM DO TESTE ===" . PHP_EOL;