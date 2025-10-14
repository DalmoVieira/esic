<?php
// E-SIC - Sistema Eletrônico de Informações ao Cidadão
// Versão simplificada e funcional

// Configurações de erro
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Função para capturar erros fatais
function handleFatalError() {
    $error = error_get_last();
    if ($error && $error['type'] === E_ERROR) {
        echo "<!DOCTYPE html><html><body>";
        echo "<h1>Erro no Sistema</h1>";
        echo "<p>O sistema encontrou um erro. Tente novamente.</p>";
        echo "</body></html>";
    }
}
register_shutdown_function('handleFatalError');

try {
    // Carregar o bootstrap com tratamento de erro
    if (file_exists(__DIR__ . '/bootstrap.php')) {
        require_once __DIR__ . '/bootstrap.php';
    }
    
    // Tentar carregar o controller
    if (class_exists('\App\Controllers\PublicController')) {
        $controller = new \App\Controllers\PublicController();
        
        // Roteamento simples
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Remove base path if exists
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        $path = '/' . trim($path, '/');
        
        // Roteamento
        switch ($path) {
            case '/':
            case '/home':
                $controller->index();
                break;
            default:
                $controller->index();
                break;
        }
    } else {
        throw new Exception('Controller não encontrado');
    }
    
} catch (Exception $e) {
    // Página de erro amigável
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-SIC - Sistema Eletrônico de Informações ao Cidadão</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="text-primary mb-4">Sistema E-SIC</h1>
                            <p class="lead">Sistema Eletrônico de Informações ao Cidadão</p>
                            
                            <div class="alert alert-warning">
                                <h5>Sistema em Manutenção</h5>
                                <p>O sistema está sendo configurado. Tente novamente em alguns minutos.</p>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6>Nova Solicitação</h6>
                                            <p class="small">Solicite informações públicas</p>
                                            <button class="btn btn-primary btn-sm" disabled>Em breve</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6>Acompanhar Pedido</h6>
                                            <p class="small">Consulte o status do seu pedido</p>
                                            <button class="btn btn-outline-primary btn-sm" disabled>Em breve</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6>Transparência</h6>
                                            <p class="small">Acesse dados públicos</p>
                                            <button class="btn btn-outline-primary btn-sm" disabled>Em breve</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <small class="text-muted">Sistema funcionando - <?= date('d/m/Y H:i:s') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>