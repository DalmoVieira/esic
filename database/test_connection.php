<?php

try {
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    echo "✓ Conexão com MySQL OK\n";
    
    // Testar se existe banco esic
    $stmt = $pdo->query("SHOW DATABASES LIKE 'esic'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Database 'esic' já existe\n";
    } else {
        echo "ℹ Database 'esic' não existe, será criada\n";
    }
    
} catch(Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}