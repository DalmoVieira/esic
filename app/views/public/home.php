<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema E-SIC - Acesso à Informação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .feature-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .stats-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-balance-scale me-2"></i>
                Sistema E-SIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/novo-pedido">Fazer Pedido</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acompanhar">Acompanhar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sobre">Sobre a LAI</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Área Restrita
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-unlock-alt me-3"></i>
                Sistema Eletrônico do Serviço de Informação ao Cidadão
            </h1>
            <p class="lead mb-5">
                Acesso rápido e transparente às informações públicas através da Lei de Acesso à Informação (LAI)
            </p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                        <a href="/novo-pedido" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-plus-circle me-2"></i>
                            Fazer Novo Pedido
                        </a>
                        <a href="/acompanhar" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-search me-2"></i>
                            Acompanhar Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col">
                    <h2 class="fw-bold">Como Funciona</h2>
                    <p class="text-muted">Simples, rápido e transparente</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="text-primary mb-3">
                                <i class="fas fa-edit fa-3x"></i>
                            </div>
                            <h5 class="card-title">1. Faça seu Pedido</h5>
                            <p class="card-text">
                                Preencha o formulário com sua solicitação de informação de forma simples e objetiva.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="text-success mb-3">
                                <i class="fas fa-clock fa-3x"></i>
                            </div>
                            <h5 class="card-title">2. Aguarde o Prazo</h5>
                            <p class="card-text">
                                O órgão tem até 20 dias para responder seu pedido, conforme previsto na LAI.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100 text-center p-4">
                        <div class="card-body">
                            <div class="text-info mb-3">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <h5 class="card-title">3. Receba a Resposta</h5>
                            <p class="card-text">
                                Acompanhe seu pedido pelo protocolo e receba a resposta por email.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-primary fw-bold"><?php echo $stats['total_pedidos'] ?? '0'; ?></div>
                    <h5>Pedidos Realizados</h5>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-success fw-bold"><?php echo $stats['pedidos_respondidos'] ?? '0'; ?></div>
                    <h5>Pedidos Atendidos</h5>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-info fw-bold"><?php echo round($stats['tempo_medio'] ?? 0); ?></div>
                    <h5>Tempo Médio (dias)</h5>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="display-4 text-warning fw-bold"><?php echo $stats['pedidos_mes'] ?? '0'; ?></div>
                    <h5>Pedidos Este Mês</h5>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <h3 class="fw-bold mb-3">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Lei de Acesso à Informação
                    </h3>
                    <p>
                        A Lei nº 12.527/2011 (Lei de Acesso à Informação - LAI) regulamenta o direito 
                        constitucional de acesso às informações públicas. Esta norma entrou em vigor 
                        em 16 de maio de 2012 e criou mecanismos que possibilitam a qualquer pessoa, 
                        física ou jurídica, sem necessidade de apresentar motivo, o recebimento de 
                        informações públicas dos órgãos e entidades.
                    </p>
                    <a href="/lei-acesso-informacao" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i>
                        Saiba Mais
                    </a>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <h3 class="fw-bold mb-3">
                        <i class="fas fa-question-circle text-success me-2"></i>
                        Perguntas Frequentes
                    </h3>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Quem pode fazer pedidos?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Qualquer pessoa, física ou jurídica, pode solicitar informações aos órgãos públicos.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Qual o prazo para resposta?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O prazo é de 20 dias, prorrogável por mais 10 dias mediante justificativa.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-balance-scale me-2"></i>Sistema E-SIC</h5>
                    <p class="mb-0">
                        Promovendo a transparência e o acesso à informação pública através da tecnologia.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Contato</h5>
                    <p class="mb-1">
                        <i class="fas fa-phone me-2"></i>
                        <?php echo $_ENV['ORGAO_TELEFONE'] ?? '(11) 0000-0000'; ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo $_ENV['ORGAO_EMAIL'] ?? 'contato@orgao.gov.br'; ?>
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>&copy; <?php echo date('Y'); ?> Sistema E-SIC. Todos os direitos reservados.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>
                        Desenvolvido em conformidade com a 
                        <strong>Lei nº 12.527/2011</strong>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>