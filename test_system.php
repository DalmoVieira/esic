<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste E-SIC</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <h1>Teste do Sistema E-SIC</h1>
    
    <?php
    echo '<div class="status success">✓ PHP funcionando - ' . date('Y-m-d H:i:s') . '</div>';
    
    // Testar bootstrap
    try {
        require_once __DIR__ . '/bootstrap.php';
        echo '<div class="status success">✓ Bootstrap carregado</div>';
    } catch (Exception $e) {
        echo '<div class="status error">❌ Erro no bootstrap: ' . $e->getMessage() . '</div>';
    }
    
    // Testar conexão com banco
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
        echo '<div class="status success">✓ Conexão com banco OK</div>';
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categorias");
        $result = $stmt->fetch();
        echo '<div class="status success">✓ Banco com ' . $result['total'] . ' categorias</div>';
    } catch (Exception $e) {
        echo '<div class="status error">❌ Erro no banco: ' . $e->getMessage() . '</div>';
    }
    
    // Testar controller
    try {
        $controller = new \App\Controllers\PublicController();
        echo '<div class="status success">✓ Controller PublicController criado</div>';
    } catch (Exception $e) {
        echo '<div class="status error">❌ Erro no controller: ' . $e->getMessage() . '</div>';
    }
    ?>
    
    <h2>Links de Teste:</h2>
    <ul>
        <li><a href="index.php" target="_blank">index.php direto</a></li>
        <li><a href="./" target="_blank">Diretório atual</a></li>
        <li><a href="/esic/" target="_blank">/esic/ (absoluto)</a></li>
        <li><a href="debug_routes.php" target="_blank">Debug de rotas</a></li>
    </ul>
    
    <h2>Informações do Servidor:</h2>
    <ul>
        <li><strong>DOCUMENT_ROOT:</strong> <?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></li>
        <li><strong>SCRIPT_NAME:</strong> <?= $_SERVER['SCRIPT_NAME'] ?? 'N/A' ?></li>
        <li><strong>REQUEST_URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?></li>
        <li><strong>HTTP_HOST:</strong> <?= $_SERVER['HTTP_HOST'] ?? 'N/A' ?></li>
    </ul>
</body>
</html>