<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso negado | E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <div class="mb-4">
                <i class="bi bi-shield-exclamation text-danger" style="font-size: 6rem;"></i>
            </div>
            
            <h1 class="display-1 fw-bold text-danger">403</h1>
            <h2 class="mb-4">Acesso Negado</h2>
            <p class="lead text-muted mb-4">
                Você não tem permissão para acessar esta página.
            </p>
            
            <div class="card bg-warning bg-opacity-10 border-warning mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-info-circle me-2"></i>
                        Informações Importantes
                    </h6>
                    <ul class="list-unstyled text-start mb-0 small">
                        <li>• Esta é uma área administrativa</li>
                        <li>• É necessário fazer login com uma conta autorizada</li>
                        <li>• Entre em contato com o administrador para obter acesso</li>
                    </ul>
                </div>
            </div>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                <a href="/" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>
                    Página Inicial
                </a>
                <a href="/auth/login" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Fazer Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>