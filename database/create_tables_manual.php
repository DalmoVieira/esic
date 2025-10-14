<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
    
    echo "Criando tabela de configurações...\n";
    
    $sql = "
    CREATE TABLE `configuracoes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `chave` varchar(100) NOT NULL,
        `valor` text,
        `descricao` text,
        `tipo` enum('text','textarea','number','boolean','select') DEFAULT 'text',
        `opcoes` text,
        `categoria` varchar(50) DEFAULT 'geral',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `chave` (`chave`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✓ Tabela configuracoes criada\n";
    
    echo "Criando tabela de categorias...\n";
    
    $sql = "
    CREATE TABLE `categorias` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nome` varchar(100) NOT NULL,
        `descricao` text,
        `icone` varchar(50) DEFAULT NULL,
        `cor` varchar(7) DEFAULT '#007bff',
        `prazo_dias` int(11) DEFAULT 20,
        `ativo` tinyint(1) DEFAULT 1,
        `ordem` int(11) DEFAULT 0,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✓ Tabela categorias criada\n";
    
    // Verificar tabelas criadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nTabelas no banco: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}