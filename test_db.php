<?php
/**
 * Teste de Conexão com Banco de Dados
 */

require_once 'app/config/Database.php';

try {
    echo "Tentando conectar ao banco de dados..." . PHP_EOL;
    
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "Conexão estabelecida com sucesso!" . PHP_EOL;
    
    // Testar uma query simples
    $result = $db->selectOne('SELECT 1 as test');
    if ($result && $result['test'] == 1) {
        echo "Query de teste executada com sucesso!" . PHP_EOL;
    }
    
    // Mostrar informações do servidor
    $stats = $db->getStats();
    echo "Versão do MySQL: " . $stats['server']['version'] . PHP_EOL;
    echo "Database: " . $stats['server']['database_name'] . PHP_EOL;
    echo "Connection ID: " . $stats['server']['connection_id'] . PHP_EOL;
    
    // Testar se a database esic_db existe
    $databases = $db->select("SHOW DATABASES LIKE 'esic_db'");
    if (empty($databases)) {
        echo "AVISO: Database 'esic_db' não encontrada!" . PHP_EOL;
        echo "Execute os scripts SQL em database/schema.sql para criar a estrutura." . PHP_EOL;
    } else {
        echo "Database 'esic_db' encontrada!" . PHP_EOL;
        
        // Verificar tabelas principais
        $tables = ['usuarios', 'pedidos', 'recursos', 'configuracoes'];
        foreach ($tables as $table) {
            try {
                $result = $db->select("SHOW TABLES LIKE '$table'");
                if (!empty($result)) {
                    echo "✓ Tabela '$table' existe" . PHP_EOL;
                } else {
                    echo "✗ Tabela '$table' NÃO existe" . PHP_EOL;
                }
            } catch (Exception $e) {
                echo "✗ Erro ao verificar tabela '$table': " . $e->getMessage() . PHP_EOL;
            }
        }
        
        // Contar registros em tabelas importantes
        try {
            $userCount = $db->selectOne("SELECT COUNT(*) as count FROM usuarios");
            $configCount = $db->selectOne("SELECT COUNT(*) as count FROM configuracoes");
            echo "Usuários cadastrados: " . $userCount['count'] . PHP_EOL;
            echo "Configurações: " . $configCount['count'] . PHP_EOL;
        } catch (Exception $e) {
            echo "Erro ao contar registros: " . $e->getMessage() . PHP_EOL;
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . PHP_EOL;
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo PHP_EOL;
        echo "Soluções possíveis:" . PHP_EOL;
        echo "1. Verifique se o XAMPP está rodando" . PHP_EOL;
        echo "2. Inicie o serviço MySQL no painel do XAMPP" . PHP_EOL;
        echo "3. Verifique as configurações em .env" . PHP_EOL;
    }
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo PHP_EOL;
        echo "Problema de autenticação:" . PHP_EOL;
        echo "1. Verifique usuário e senha no arquivo .env" . PHP_EOL;
        echo "2. Configurações padrão XAMPP: usuário 'root', senha vazia" . PHP_EOL;
    }
}