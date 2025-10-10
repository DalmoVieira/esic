<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Token expirado | E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-shield-x text-warning" style="font-size: 6rem;"></i>
            </div>
            
            <h1 class="display-1 fw-bold text-warning">419</h1>
            <h2 class="mb-4">Token de Segurança Expirado</h2>
            <p class="lead text-muted mb-4">
                Sua sessão expirou por motivos de segurança. Tente novamente.
            </p>
            
            <div class="card bg-warning bg-opacity-10 border-warning mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-shield-check me-2"></i>
                        Proteção CSRF
                    </h6>
                    <p class="text-start mb-0 small">
                        Este erro ocorre para proteger você contra ataques de falsificação.
                        Simplesmente recarregue a página e tente novamente.
                    </p>
                </div>
            </div>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                <button onclick="window.location.reload()" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Recarregar Página
                </button>
                <a href="/" class="btn btn-outline-primary">
                    <i class="bi bi-house me-2"></i>
                    Página Inicial
                </a>
            </div>
        </div>
    </div>
</body>
</html>