<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E-SIC - Teste de Conectividade</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .links a { display: inline-block; margin: 10px; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Sistema E-SIC</h1>
        <h2>Teste de Conectividade</h2>
        
        <div class="status success">
            ✅ <strong>Servidor PHP funcionando!</strong><br>
            Data/Hora: <?php echo date('d/m/Y H:i:s'); ?><br>
            Versão PHP: <?php echo PHP_VERSION; ?>
        </div>
        
        <div class="status info">
            📁 <strong>Estrutura do projeto:</strong><br>
            <?php
            $dirs = ['../app', '../database', '../uploads', '../logs'];
            foreach ($dirs as $dir) {
                $exists = is_dir($dir);
                echo ($exists ? '✅' : '❌') . ' ' . basename($dir) . '/<br>';
            }
            ?>
        </div>
        
        <div class="links">
            <h3>🔗 Links do Sistema:</h3>
            <a href="/">🏠 Página Inicial</a>
            <a href="/novo-pedido">📝 Novo Pedido</a>
            <a href="/acompanhar">🔍 Acompanhar</a>
            <a href="/auth/login">🔐 Login Admin</a>
        </div>
        
        <div class="status success">
            <strong>✅ Sistema pronto!</strong><br>
            URL correta: <code>http://localhost:8080/</code><br>
            <em>❌ Não use "/isec" - essa rota não existe.</em>
        </div>
    </div>
</body>
</html>