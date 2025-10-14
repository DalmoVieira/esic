<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
    
    echo "Conectado ao banco esic\n\n";
    
    // Mostrar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tabelas encontradas: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    } else {
        echo "Nenhuma tabela encontrada!\n";
        
        // Tentar executar um comando CREATE simples para testar
        try {
            $pdo->exec("CREATE TEMPORARY TABLE test_table (id INT)");
            echo "✓ Permissões de CREATE funcionando\n";
            $pdo->exec("DROP TEMPORARY TABLE test_table");
        } catch (Exception $e) {
            echo "❌ Erro nas permissões de CREATE: " . $e->getMessage() . "\n";
        }
    }
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}