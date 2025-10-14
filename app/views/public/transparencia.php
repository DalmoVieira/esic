<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Transparência'; ?> - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-university me-2"></i>
                E-SIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/novo-pedido">Novo Pedido</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acompanhar">Acompanhar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/transparencia">Transparência</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Transparência</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Portal da Transparência
                        </h1>
                    </div>
                    <div class="card-body">
                        <p class="lead">
                            Acesse informações sobre a gestão pública, execução orçamentária, contratos, 
                            convênios, servidores e outras informações de interesse público.
                        </p>

                        <!-- Estatísticas -->
                        <?php if (isset($transparencia['stats'])): ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                        <h5><?php echo $transparencia['stats']['total_pedidos'] ?? '0'; ?></h5>
                                        <small>Total de Pedidos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-check fa-2x text-success mb-2"></i>
                                        <h5><?php echo $transparencia['stats']['pedidos_atendidos'] ?? '0'; ?></h5>
                                        <small>Pedidos Atendidos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                        <h5><?php echo $transparencia['stats']['tempo_medio'] ?? '0'; ?> dias</h5>
                                        <small>Tempo Médio de Resposta</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light">
                                    <div class="card-body">
                                        <i class="fas fa-percentage fa-2x text-info mb-2"></i>
                                        <h5><?php echo $transparencia['stats']['taxa_atendimento'] ?? '0'; ?>%</h5>
                                        <small>Taxa de Atendimento</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Links de Transparência -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Receitas e Despesas</h5>
                                                <small class="text-muted">Execução orçamentária e financeira</small>
                                            </div>
                                        </div>
                                        <p>Consulte as receitas arrecadadas e despesas executadas pelo órgão.</p>
                                        <a href="#" class="btn btn-outline-success">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-users fa-2x text-primary me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Servidores</h5>
                                                <small class="text-muted">Informações sobre pessoal</small>
                                            </div>
                                        </div>
                                        <p>Dados sobre servidores, cargos, salários e benefícios.</p>
                                        <a href="#" class="btn btn-outline-primary">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-handshake fa-2x text-warning me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Contratos</h5>
                                                <small class="text-muted">Licitações e contratações</small>
                                            </div>
                                        </div>
                                        <p>Informações sobre licitações, contratos e fornecedores.</p>
                                        <a href="#" class="btn btn-outline-warning">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-balance-scale fa-2x text-info me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Convênios</h5>
                                                <small class="text-muted">Parcerias e transferências</small>
                                            </div>
                                        </div>
                                        <p>Dados sobre convênios, parcerias e transferências voluntárias.</p>
                                        <a href="#" class="btn btn-outline-info">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-chart-bar fa-2x text-danger me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Relatórios</h5>
                                                <small class="text-muted">Prestação de contas</small>
                                            </div>
                                        </div>
                                        <p>Relatórios de gestão, prestação de contas e auditorias.</p>
                                        <a href="#" class="btn btn-outline-danger">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-building fa-2x text-secondary me-3"></i>
                                            <div>
                                                <h5 class="card-title mb-0">Estrutura Organizacional</h5>
                                                <small class="text-muted">Organograma e competências</small>
                                            </div>
                                        </div>
                                        <p>Estrutura organizacional, competências e organograma.</p>
                                        <a href="#" class="btn btn-outline-secondary">Acessar <i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informação:</strong> Os dados são atualizados mensalmente conforme determina a legislação. 
                            Em caso de dúvidas ou necessidade de informações mais específicas, utilize o 
                            <a href="/novo-pedido" class="alert-link">Sistema de Pedidos de Informação</a>.
                        </div>

                        <div class="text-center mt-4">
                            <a href="/novo-pedido" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>
                                Solicitar Informação
                            </a>
                            <a href="/lei-acesso-informacao" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-balance-scale me-2"></i>
                                Lei de Acesso à Informação
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>