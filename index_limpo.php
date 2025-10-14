<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SIC - Sistema Eletrônico de Informações ao Cidadão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-info-circle"></i> E-SIC
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <h1 class="display-4 text-primary mb-3">Sistema E-SIC</h1>
                    <p class="lead">Sistema Eletrônico de Informações ao Cidadão</p>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Sistema funcionando - <?= date('d/m/Y H:i:s') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="bi bi-plus-circle text-primary mb-3" style="font-size: 2.5rem;"></i>
                                <h5>Nova Solicitação</h5>
                                <p class="small">Solicite informações públicas</p>
                                <button class="btn btn-primary btn-sm">Fazer Pedido</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="bi bi-search text-info mb-3" style="font-size: 2.5rem;"></i>
                                <h5>Acompanhar</h5>
                                <p class="small">Consulte seu pedido</p>
                                <button class="btn btn-outline-primary btn-sm">Consultar</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="bi bi-graph-up text-success mb-3" style="font-size: 2.5rem;"></i>
                                <h5>Transparência</h5>
                                <p class="small">Dados públicos</p>
                                <button class="btn btn-outline-success btn-sm">Ver Dados</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Informações do Sistema</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Data/Hora:</strong> <?= date('d/m/Y H:i:s') ?></li>
                                    <li><strong>PHP:</strong> <?= phpversion() ?></li>
                                    <li><strong>Servidor:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></li>
                                    <li><strong>Status:</strong> <span class="badge bg-success">Operacional</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>