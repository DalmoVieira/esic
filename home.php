<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SIC - Prefeitura de Rio Claro - RJ</title>
    <meta name="description" content="Sistema Eletrônico de Informações ao Cidadão - Prefeitura Municipal de Rio Claro - RJ">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="novo-pedido.php">Nova Solicitação</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="acompanhar.php">Acompanhar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transparencia.php">Transparência</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <img src="assets/images/logo-rioclaro.svg" alt="Logo Prefeitura de Rio Claro" class="mb-3" style="max-height: 80px;" onerror="this.style.display='none'">
                    <h1 class="display-4 text-primary mb-3">E-SIC Rio Claro</h1>
                    <p class="lead">Prefeitura Municipal de Rio Claro - RJ</p>
                    <p class="text-muted">Sistema Eletrônico de Informações ao Cidadão</p>
                    <div class="alert alert-info border-primary">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-info-circle text-primary me-2" style="font-size: 1.5rem;"></i>
                            <strong>Sistema Oficial E-SIC</strong>
                        </div>
                        <p class="mb-2">A Prefeitura de Rio Claro possui sistema oficial E-SIC em funcionamento:</p>
                        <a href="https://gpi-services.cloud.el.com.br/rj-rioclaro-pm/e-sic/" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-up-right-square"></i> Acessar Sistema Oficial
                        </a>
                        <hr class="my-2">
                        <small class="text-muted">
                            <i class="bi bi-telephone"></i> (24) 99828-1427 • 
                            <i class="bi bi-envelope"></i> pmrc@rioclaro.rj.gov.br
                        </small>
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Sistema de desenvolvimento - <?= date('d/m/Y H:i:s') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center card-hover">
                            <div class="card-body">
                                <i class="bi bi-plus-circle text-primary mb-3" style="font-size: 3rem;"></i>
                                <h5 class="card-title">Nova Solicitação</h5>
                                <p class="card-text">Faça uma nova solicitação de informações públicas conforme a Lei de Acesso à Informação (LAI).</p>
                                <a href="novo-pedido.php" class="btn btn-primary">
                                    <i class="bi bi-plus"></i> Fazer Solicitação
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center card-hover">
                            <div class="card-body">
                                <i class="bi bi-search text-info mb-3" style="font-size: 3rem;"></i>
                                <h5 class="card-title">Acompanhar Pedido</h5>
                                <p class="card-text">Consulte o andamento da sua solicitação usando o número do protocolo e seu CPF ou CNPJ.</p>
                                <a href="acompanhar.php" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Acompanhar
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center card-hover">
                            <div class="card-body">
                                <i class="bi bi-graph-up text-success mb-3" style="font-size: 3rem;"></i>
                                <h5 class="card-title">Transparência Pública</h5>
                                <p class="card-text">Acesse dados de transparência pública, relatórios e estatísticas do portal.</p>
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
                        <div class="card card-hover">
                            <div class="card-body">
                                <h4 class="text-primary">150</h4>
                                <p class="mb-0 small">Pedidos Ativos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <div class="card card-hover">
                            <div class="card-body">
                                <h4 class="text-success">95%</h4>
                                <p class="mb-0 small">Taxa de Atendimento</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <div class="card card-hover">
                            <div class="card-body">
                                <h4 class="text-info">12</h4>
                                <p class="mb-0 small">Dias Médios</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 text-center mb-3">
                        <div class="card card-hover">
                            <div class="card-body">
                                <h4 class="text-warning">5</h4>
                                <p class="mb-0 small">Recursos Ativos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações Legais -->
                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="bi bi-info-circle text-primary"></i> Lei de Acesso à Informação</h5>
                                <p class="small mb-0">
                                    A Lei nº 12.527/2011 regulamenta o direito constitucional de acesso às informações públicas. 
                                    Este sistema permite que qualquer pessoa, física ou jurídica, solicite informações aos órgãos públicos.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5><i class="bi bi-clock text-info"></i> Prazos de Resposta</h5>
                                <ul class="small mb-0">
                                    <li>Resposta: até 20 dias (prorrogáveis por mais 10)</li>
                                    <li>Recurso 1ª instância: até 10 dias</li>
                                    <li>Recurso 2ª instância: até 15 dias</li>
                                    <li>Recurso ao CGU: até 20 dias</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status do Sistema -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6 class="mb-3">Status do Sistema</h6>
                                <span class="badge bg-success me-2">
                                    <i class="bi bi-check-circle"></i> Operacional
                                </span>
                                <small class="text-muted">
                                    Última atualização: <?= date('d/m/Y H:i:s') ?> | 
                                    PHP <?= phpversion() ?> | 
                                    <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/PHP' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h6><i class="bi bi-building"></i> Prefeitura de Rio Claro</h6>
                    <p class="small mb-0">
                        Av. João Baptista Portugal, 230<br>
                        Rio Claro - RJ - CEP: 27.460-000
                    </p>
                    <small class="text-muted">Lei nº 12.527/2011</small>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-telephone"></i> Contato</h6>
                    <p class="small mb-0">
                        Telefone: (24) 99828-1427<br>
                        Email: pmrc@rioclaro.rj.gov.br<br>
                        Horário: 8h às 17h (dias úteis)
                    </p>
                </div>
                <div class="col-md-4">
                    <h6><i class="bi bi-link-45deg"></i> Links Úteis</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white-50">Manual do E-SIC</a></li>
                        <li><a href="#" class="text-white-50">Perguntas Frequentes</a></li>
                        <li><a href="#" class="text-white-50">Legislação</a></li>
                        <li><a href="#" class="text-white-50">Fale Conosco</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2025 Prefeitura Municipal de Rio Claro - RJ. Sistema E-SIC para transparência pública e acesso à informação.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>