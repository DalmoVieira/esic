<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Detalhes do Pedido - Sistema E-SIC'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .protocol-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
            color: white;
            padding: 2rem 0;
        }
        .status-badge {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
        }
        .info-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #6c757d;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }
        .timeline-item.active::before {
            background: #198754;
            box-shadow: 0 0 0 2px #198754;
        }
        .timeline-item.current::before {
            background: #0d6efd;
            box-shadow: 0 0 0 2px #0d6efd;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-university me-2"></i>
                Sistema E-SIC
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
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Entrar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Protocol Header -->
    <div class="protocol-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">
                        <i class="fas fa-file-alt me-2"></i>
                        Pedido #<?php echo htmlspecialchars($pedido['protocolo']); ?>
                    </h1>
                    <p class="mb-0 opacity-75"><?php echo htmlspecialchars($pedido['assunto']); ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <?php
                    $statusClass = 'secondary';
                    $statusIcon = 'clock';
                    switch($pedido['status']) {
                        case 'pendente':
                            $statusClass = 'warning';
                            $statusIcon = 'clock';
                            break;
                        case 'em_analise':
                            $statusClass = 'info';
                            $statusIcon = 'search';
                            break;
                        case 'respondido':
                            $statusClass = 'success';
                            $statusIcon = 'check-circle';
                            break;
                        case 'negado':
                            $statusClass = 'danger';
                            $statusIcon = 'times-circle';
                            break;
                    }
                    ?>
                    <span class="badge bg-<?php echo $statusClass; ?> status-badge">
                        <i class="fas fa-<?php echo $statusIcon; ?> me-2"></i>
                        <?php 
                        $statusTexts = [
                            'pendente' => 'Pendente de Análise',
                            'em_analise' => 'Em Análise',
                            'respondido' => 'Respondido',
                            'negado' => 'Negado'
                        ];
                        echo $statusTexts[$pedido['status']] ?? 'Status Desconhecido';
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><a href="/acompanhar">Acompanhar</a></li>
                <li class="breadcrumb-item active">Protocolo <?php echo htmlspecialchars($pedido['protocolo']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Pedido Details -->
                <div class="card info-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações do Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="text-primary">Assunto</h6>
                            <p class="mb-0"><?php echo htmlspecialchars($pedido['assunto']); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-primary">Descrição Completa</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($pedido['descricao'])); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-primary">Data do Pedido</h6>
                                <p class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <?php echo date('d/m/Y', strtotime($pedido['created_at'])); ?>
                                </p>
                                <small class="text-muted"><?php echo date('H:i', strtotime($pedido['created_at'])); ?></small>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-primary">Prazo de Resposta</h6>
                                <p class="mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php echo date('d/m/Y', strtotime($pedido['prazo_resposta'])); ?>
                                </p>
                                <?php 
                                $prazo = strtotime($pedido['prazo_resposta']);
                                $hoje = time();
                                $diff = floor(($prazo - $hoje) / (60 * 60 * 24));
                                ?>
                                <small class="<?php echo $diff < 0 ? 'text-danger' : ($diff < 5 ? 'text-warning' : 'text-muted'); ?>">
                                    <?php 
                                    if ($diff < 0) {
                                        echo abs($diff) . ' dias em atraso';
                                    } elseif ($diff == 0) {
                                        echo 'Vence hoje';
                                    } else {
                                        echo $diff . ' dias restantes';
                                    }
                                    ?>
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-primary">Forma de Resposta</h6>
                                <p class="mb-0">
                                    <?php 
                                    $formas = [
                                        'email' => '<i class="fas fa-envelope me-2"></i>Por e-mail',
                                        'sistema' => '<i class="fas fa-desktop me-2"></i>Consulta pelo sistema',
                                        'fisico' => '<i class="fas fa-building me-2"></i>Retirada física'
                                    ];
                                    echo $formas[$pedido['forma_resposta']] ?? 'Não especificado';
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <h6 class="text-primary">Requerente</h6>
                                <p class="mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo htmlspecialchars($pedido['nome_requerente']); ?>
                                </p>
                                <small class="text-muted"><?php echo htmlspecialchars($pedido['email_requerente']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response -->
                <?php if ($pedido['status'] === 'respondido' && !empty($pedido['resposta'])): ?>
                <div class="card info-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-reply me-2"></i>
                            Resposta do Órgão
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">
                                Respondido em <?php echo date('d/m/Y H:i', strtotime($pedido['data_resposta'])); ?>
                            </small>
                        </div>
                        
                        <div class="bg-light p-4 rounded">
                            <?php echo nl2br(htmlspecialchars($pedido['resposta'])); ?>
                        </div>
                        
                        <?php if (!empty($pedido['resposta_arquivo'])): ?>
                        <div class="mt-3">
                            <h6>Arquivo de Resposta</h6>
                            <a href="/download/<?php echo $pedido['resposta_arquivo']; ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>
                                Baixar Arquivo
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Justificativa de Negação -->
                <?php if ($pedido['status'] === 'negado' && !empty($pedido['justificativa_negacao'])): ?>
                <div class="card info-card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Justificativa da Negação
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <?php echo nl2br(htmlspecialchars($pedido['justificativa_negacao'])); ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Seus Direitos</h6>
                            <p class="mb-0">
                                Você pode contestar esta decisão entrando com um recurso no prazo de 10 dias.
                                O recurso será analisado por uma instância superior.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions -->
                <div class="card info-card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-tools me-2"></i>
                            Ações Disponíveis
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/acompanhar" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar à Consulta
                            </a>
                            
                            <?php if (in_array($pedido['status'], ['negado', 'parcial'])): ?>
                            <a href="/recurso/<?php echo $pedido['protocolo']; ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-gavel me-2"></i>
                                Entrar com Recurso
                            </a>
                            <?php endif; ?>
                            
                            <a href="mailto:esic@orgao.gov.br?subject=Pedido <?php echo $pedido['protocolo']; ?>" 
                               class="btn btn-outline-info">
                                <i class="fas fa-envelope me-2"></i>
                                Entrar em Contato
                            </a>
                            
                            <button onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="fas fa-print me-2"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <?php if (isset($historico) && !empty($historico)): ?>
                <div class="card info-card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Histórico
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($historico as $index => $item): ?>
                            <div class="timeline-item <?php echo $index === 0 ? 'current' : ($item['concluido'] ? 'active' : ''); ?>">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong class="text-primary"><?php echo htmlspecialchars($item['evento']); ?></strong>
                                    <small class="text-muted"><?php echo date('d/m H:i', strtotime($item['created_at'])); ?></small>
                                </div>
                                <?php if (!empty($item['observacoes'])): ?>
                                <p class="mb-0 small"><?php echo htmlspecialchars($item['observacoes']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($item['responsavel'])): ?>
                                <small class="text-muted">Por: <?php echo htmlspecialchars($item['responsavel']); ?></small>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Help -->
                <div class="card info-card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Precisa de Ajuda?
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-3">
                            Tem dúvidas sobre seu pedido? Consulte nossos recursos de ajuda:
                        </p>
                        <div class="d-grid gap-2">
                            <a href="/lei-acesso-informacao" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-book me-2"></i>
                                Lei de Acesso
                            </a>
                            <a href="/faq" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-question me-2"></i>
                                Perguntas Frequentes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema E-SIC</h5>
                    <p>Lei de Acesso à Informação - Lei nº 12.527/2011</p>
                </div>
                <div class="col-md-6">
                    <h6>Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="/lei-acesso-informacao" class="text-light">Lei de Acesso à Informação</a></li>
                        <li><a href="/transparencia" class="text-light">Transparência</a></li>
                        <li><a href="/novo-pedido" class="text-light">Novo Pedido</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>