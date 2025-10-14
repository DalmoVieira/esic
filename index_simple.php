<?php
// E-SIC - Sistema simplificado para teste
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>E-SIC - Sistema Eletrônico de Informações ao Cidadão</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-4'>
        <h1 class='text-primary'>Sistema E-SIC</h1>
        <p class='lead'>Sistema Eletrônico de Informações ao Cidadão</p>
        
        <div class='alert alert-success'>
            <strong>Sistema funcionando!</strong> Data/hora: " . date('d/m/Y H:i:s') . "
        </div>";

try {
    // Tentar carregar o sistema
    require_once __DIR__ . '/bootstrap.php';
    
    echo "<div class='alert alert-info'>✓ Bootstrap carregado com sucesso</div>";
    
    // Testar banco
    $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
    echo "<div class='alert alert-info'>✓ Conexão com banco estabelecida</div>";
    
    // Carregar controller
    $controller = new \App\Controllers\PublicController();
    echo "<div class='alert alert-info'>✓ Controller carregado</div>";
    
    echo "<div class='row mt-4'>
            <div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>Fazer Nova Solicitação</h5>
                        <p class='card-text'>Solicite informações públicas conforme a Lei de Acesso à Informação.</p>
                        <a href='#' class='btn btn-primary'>Nova Solicitação</a>
                    </div>
                </div>
            </div>
            <div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>Acompanhar Pedido</h5>
                        <p class='card-text'>Acompanhe o status da sua solicitação usando o protocolo.</p>
                        <a href='#' class='btn btn-outline-primary'>Acompanhar</a>
                    </div>
                </div>
            </div>
            <div class='col-md-4'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>Transparência</h5>
                        <p class='card-text'>Acesse dados e informações de transparência pública.</p>
                        <a href='#' class='btn btn-outline-primary'>Ver Dados</a>
                    </div>
                </div>
            </div>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>
            <strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "
          </div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>