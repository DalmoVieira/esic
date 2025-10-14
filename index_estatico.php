<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SIC - Sistema Eletrônico de Informações ao Cidadão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #1565c0;
        }
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 0;
        }
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-info-circle"></i> E-SIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#novo-pedido">Nova Solicitação</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#acompanhar">Acompanhar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#transparencia">Transparência</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Sistema E-SIC</h1>
            <p class="lead mb-4">Sistema Eletrônico de Informações ao Cidadão</p>
            <p class="mb-4">Acesse informações públicas conforme a Lei de Acesso à Informação (LAI)</p>
            <div class="alert alert-light d-inline-block">
                <i class="bi bi-check-circle text-success"></i> Sistema funcionando - <?= date('d/m/Y H:i:s') ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Nova Solicitação -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-plus-circle text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Nova Solicitação</h5>
                            <p class="card-text">Faça uma nova solicitação de informações públicas de acordo com a Lei de Acesso à Informação.</p>
                            <a href="novo-pedido.php" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Fazer Solicitação
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Acompanhar Pedido -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-search text-info" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Acompanhar Pedido</h5>
                            <p class="card-text">Consulte o andamento da sua solicitação usando o número do protocolo e seu CPF.</p>
                            <a href="acompanhar.php" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Acompanhar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Transparência -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 card-hover">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="bi bi-bar-chart text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="card-title">Transparência Pública</h5>
                            <p class="card-text">Acesse dados de transparência pública, relatórios e estatísticas do sistema.</p>
                            <a href="transparencia.php" class="btn btn-outline-success">
                                <i class="bi bi-graph-up"></i> Ver Dados
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-4">Estatísticas do Sistema</h3>
                </div>
                <div class="col-md-3 col-6 text-center mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-primary">150</h4>
                            <p class="mb-0">Pedidos Ativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-success">95%</h4>
                            <p class="mb-0">Taxa de Atendimento</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-info">12</h4>
                            <p class="mb-0">Dias Médios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 text-center mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-warning">5</h4>
                            <p class="mb-0">Recursos Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>Sistema E-SIC</h6>
                    <p class="mb-0">Sistema Eletrônico de Informações ao Cidadão</p>
                    <small class="text-muted">Lei nº 12.527/2011 - Lei de Acesso à Informação</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="bi bi-telephone"></i> (11) 1234-5678<br>
                        <i class="bi bi-envelope"></i> esic@exemplo.gov.br
                    </p>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2025 Sistema E-SIC. Desenvolvido para transparência pública.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>