<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico E-SIC</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #f8f9fa; }
        .btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; color: white; }
        .btn-primary { background: #007bff; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #212529; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico Completo - Sistema E-SIC</h1>
        <p><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></p>
        
        <?php
        // Teste 1: Informações do Servidor
        echo '<h2>📊 Informações do Servidor</h2>';
        echo '<table>';
        echo '<tr><th>Parâmetro</th><th>Valor</th></tr>';
        echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
        echo '<tr><td>Server Software</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>Document Root</td><td>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>Script Name</td><td>' . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>Request URI</td><td>' . ($_SERVER['REQUEST_URI'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>HTTP Host</td><td>' . ($_SERVER['HTTP_HOST'] ?? 'N/A') . '</td></tr>';
        echo '<tr><td>Request Method</td><td>' . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . '</td></tr>';
        echo '</table>';
        
        // Teste 2: Arquivos do Sistema
        echo '<h2>📁 Arquivos do Sistema E-SIC</h2>';
        $files = ['index.php', 'bootstrap.php', '.htaccess'];
        foreach ($files as $file) {
            if (file_exists($file)) {
                echo "<div class='status success'>✅ $file - Existe (" . number_format(filesize($file)) . " bytes)</div>";
            } else {
                echo "<div class='status error'>❌ $file - Não encontrado</div>";
            }
        }
        
        // Teste 3: Diretórios
        echo '<h2>📂 Diretórios do Sistema</h2>';
        $dirs = ['app', 'config', 'public', 'uploads', 'database'];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                echo "<div class='status success'>✅ $dir/ - Existe</div>";
            } else {
                echo "<div class='status warning'>⚠️ $dir/ - Não encontrado</div>";
            }
        }
        
        // Teste 4: Bootstrap
        echo '<h2>🚀 Teste do Bootstrap</h2>';
        try {
            if (file_exists('bootstrap.php')) {
                require_once 'bootstrap.php';
                echo "<div class='status success'>✅ Bootstrap carregado com sucesso</div>";
            } else {
                echo "<div class='status warning'>⚠️ Arquivo bootstrap.php não encontrado</div>";
            }
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Erro no bootstrap: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // Teste 5: Conexão com Banco
        echo '<h2>🗄️ Teste de Conexão com Banco</h2>';
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
            echo "<div class='status success'>✅ Conexão com MySQL estabelecida</div>";
            
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<div class='status info'>ℹ️ Tabelas encontradas: " . count($tables) . " (" . implode(', ', array_slice($tables, 0, 5)) . (count($tables) > 5 ? '...' : '') . ")</div>";
            
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Erro na conexão com banco: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // Teste 6: Controller
        echo '<h2>🎮 Teste do Controller</h2>';
        try {
            if (class_exists('\\App\\Controllers\\PublicController')) {
                $controller = new \App\Controllers\PublicController();
                echo "<div class='status success'>✅ PublicController carregado com sucesso</div>";
            } else {
                echo "<div class='status error'>❌ Classe PublicController não encontrada</div>";
            }
        } catch (Exception $e) {
            echo "<div class='status error'>❌ Erro no controller: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // Teste 7: URLs de Teste
        echo '<h2>🔗 Links de Teste</h2>';
        echo '<div>';
        echo '<a href="/" class="btn btn-primary">Página Inicial (/)</a>';
        echo '<a href="/esic/" class="btn btn-primary">E-SIC (/esic/)</a>';
        echo '<a href="/esic/index.php" class="btn btn-success">Index Direto</a>';
        echo '<a href="/esic/info.php" class="btn btn-warning">PHP Info</a>';
        echo '</div>';
        
        // Teste 8: JavaScript Test
        echo '<h2>🧪 Teste JavaScript</h2>';
        echo '<div id="js-test" class="status warning">⏳ Testando JavaScript...</div>';
        ?>
        
        <script>
        // Teste JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            var jsTest = document.getElementById('js-test');
            jsTest.className = 'status success';
            jsTest.innerHTML = '✅ JavaScript funcionando corretamente';
            
            // Teste de fetch para o próprio sistema
            fetch('/esic/index.php')
                .then(response => {
                    if (response.ok) {
                        jsTest.innerHTML += '<br>✅ Fetch API funcionando - Status: ' + response.status;
                    } else {
                        jsTest.innerHTML += '<br>⚠️ Fetch API - Status: ' + response.status;
                    }
                })
                .catch(error => {
                    jsTest.innerHTML += '<br>❌ Erro no Fetch: ' + error.message;
                });
        });
        </script>
        
        <div style="margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px;">
            <h3>🎯 Resumo dos Problemas Comuns</h3>
            <ul>
                <li><strong>Cache do Navegador:</strong> Pressione Ctrl+F5 para recarregar sem cache</li>
                <li><strong>Bloqueador de Ads:</strong> Desabilite extensões como uBlock, AdBlock</li>
                <li><strong>JavaScript Desabilitado:</strong> Verifique se JS está habilitado</li>
                <li><strong>Firewall:</strong> Verifique se localhost não está bloqueado</li>
                <li><strong>Porta:</strong> Confirme que Apache está na porta 80</li>
            </ul>
        </div>
    </div>
</body>
</html>