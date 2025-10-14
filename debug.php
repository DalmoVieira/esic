<?php
echo "<h1>Teste E-SIC</h1>";
echo "<p>Data: " . date('Y-m-d H:i:s') . "</p>";

// Testar se o bootstrap carrega sem problemas
try {
    require_once __DIR__ . '/bootstrap.php';
    echo "<p>✓ Bootstrap carregado</p>";
    
    // Testar conexão com o banco
    $db = \App\Core\Database::getInstance();
    echo "<p>✓ Banco conectado</p>";
    
    // Testar se as tabelas existem
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>✓ Tabelas no banco: " . count($tables) . "</p>";
    
    // Testar controller
    $controller = new \App\Controllers\PublicController();
    echo "<p>✓ Controller criado</p>";
    
    echo "<h2>Sistema funcionando perfeitamente!</h2>";
    echo "<p><a href='/esic/'>Ir para o E-SIC</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>