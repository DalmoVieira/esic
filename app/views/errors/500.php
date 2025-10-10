<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erro interno | E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-exclamation-octagon text-danger" style="font-size: 6rem;"></i>
            </div>
            
            <h1 class="display-1 fw-bold text-danger">500</h1>
            <h2 class="mb-4">Erro Interno do Servidor</h2>
            <p class="lead text-muted mb-4">
                Ocorreu um erro interno. Nossa equipe técnica foi notificada.
            </p>
            
            <div class="card bg-danger bg-opacity-10 border-danger mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h6 class="card-title text-danger">
                        <i class="bi bi-gear me-2"></i>
                        O que fazer?
                    </h6>
                    <ul class="list-unstyled text-start mb-0 small">
                        <li>• Tente novamente em alguns minutos</li>
                        <li>• Verifique se sua solicitação é válida</li>
                        <li>• Se o problema persistir, entre em contato</li>
                    </ul>
                </div>
            </div>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                <a href="/" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>
                    Página Inicial
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>
                    Voltar
                </button>
                <button onclick="window.location.reload()" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Tentar Novamente
                </button>
            </div>
            
            <div class="mt-5">
                <p class="text-muted small">
                    ID do Erro: <?= uniqid() ?> | <?= date('Y-m-d H:i:s') ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>