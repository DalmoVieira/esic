<?php

/**
 * Script de instalação do banco de dados E-SIC
 * 
 * Execute este script para criar a estrutura do banco de dados
 */

require_once __DIR__ . '/../config/constants.php';

echo "=== Instalação do E-SIC ===\n\n";
echo "Configurações do banco:\n";
echo "Host: " . DB_HOST . "\n";
echo "Database: " . DB_DATABASE . "\n";
echo "Username: " . DB_USERNAME . "\n";
echo "Password: " . (empty(DB_PASSWORD) ? '(vazio)' : '(definida)') . "\n\n";

try {
    // Conectar ao MySQL sem especificar database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    echo "✓ Conectado ao MySQL\n";
    
    // Criar database se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_DATABASE . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '" . DB_DATABASE . "' criada/verificada\n";
    
    // Conectar ao database específico
    $pdo->exec("USE `" . DB_DATABASE . "`");
    
    // Ler e executar o schema SQL
    $sql = file_get_contents(__DIR__ . '/esic_schema.sql');
    
    if ($sql === false) {
        throw new Exception("Não foi possível ler o arquivo de schema");
    }
    
    // Dividir em comandos individuais
    $commands = explode(';', $sql);
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (empty($command) || substr($command, 0, 2) === '--') {
            continue;
        }
        
        try {
            $pdo->exec($command);
        } catch (PDOException $e) {
            // Ignorar erros de DROP TABLE se tabela não existir
            if (strpos($e->getMessage(), 'Unknown table') === false) {
                echo "Erro ao executar comando: " . $e->getMessage() . "\n";
                echo "Comando: " . substr($command, 0, 100) . "...\n\n";
            }
        }
    }
    
    echo "✓ Schema do banco criado com sucesso\n";
    
    // Verificar se as tabelas foram criadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expectedTables = ['configuracoes', 'categorias', 'orgaos', 'usuarios', 'pedidos', 'recursos', 'pedido_historico'];
    $missingTables = array_diff($expectedTables, $tables);
    
    if (empty($missingTables)) {
        echo "✓ Todas as tabelas foram criadas com sucesso\n";
        
        // Verificar dados iniciais
        $stmt = $pdo->query("SELECT COUNT(*) FROM configuracoes");
        $configCount = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
        $catCount = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
        $userCount = $stmt->fetchColumn();
        
        echo "✓ Dados iniciais inseridos:\n";
        echo "  - Configurações: $configCount\n";
        echo "  - Categorias: $catCount\n";
        echo "  - Usuários: $userCount\n";
        
    } else {
        echo "⚠ Tabelas não criadas: " . implode(', ', $missingTables) . "\n";
    }
    
    // Criar diretórios necessários
    $dirs = [
        __DIR__ . '/../uploads',
        __DIR__ . '/../uploads/pedidos',
        __DIR__ . '/../uploads/recursos',
        __DIR__ . '/../uploads/temp',
        __DIR__ . '/../logs',
        __DIR__ . '/../cache'
    ];
    
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            echo "✓ Diretório criado: " . basename($dir) . "\n";
        }
    }
    
    // Criar arquivo .htaccess para uploads
    $htaccess = __DIR__ . '/../uploads/.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "Deny from all\n<Files ~ \"\\.(jpg|jpeg|png|gif|pdf|doc|docx|txt|zip|rar)$\">\nAllow from all\n</Files>");
        echo "✓ Arquivo .htaccess criado para uploads\n";
    }
    
    echo "\n=== Instalação Completa! ===\n\n";
    echo "Credenciais do administrador:\n";
    echo "Email: admin@exemplo.gov.br\n";
    echo "Senha: admin123\n\n";
    echo "⚠ IMPORTANTE: Altere a senha do administrador após o primeiro login!\n\n";
    
} catch (Exception $e) {
    echo "❌ Erro durante a instalação: " . $e->getMessage() . "\n";
    exit(1);
}