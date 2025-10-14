<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Acompanhar Pedido - Sistema E-SIC'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .search-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
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
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }
        .timeline-item.active::before {
            background: #198754;
            box-shadow: 0 0 0 2px #198754;
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
                        <a class="nav-link active" href="/acompanhar">Acompanhar</a>
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

    <div class="container my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item active">Acompanhar Pedido</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 text-primary">
                    <i class="fas fa-search me-2"></i>
                    Acompanhar Pedido de Informação
                </h1>
                <p class="lead">Digite o protocolo do seu pedido para acompanhar o andamento</p>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo $message['type'] === 'error' ? 'danger' : $message['type']; ?> alert-dismissible fade show">
                    <?php echo $message['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Search Form -->
        <div class="search-section mb-5">
            <form method="POST" action="/acompanhar">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label for="protocolo" class="form-label">Número do Protocolo</label>
                        <input type="text" class="form-control form-control-lg" id="protocolo" name="protocolo" 
                               placeholder="Ex: 2025001234567" 
                               value="<?php echo htmlspecialchars($_POST['protocolo'] ?? ''); ?>"
                               required>
                        <div class="form-text">
                            O protocolo foi enviado por e-mail quando você fez o pedido
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i>
                            Consultar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results -->
        <?php if (isset($pedido) && $pedido): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Pedido #<?php echo $pedido['protocolo']; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6>Assunto</h6>
                        <p class="mb-3"><?php echo htmlspecialchars($pedido['assunto']); ?></p>
                        
                        <h6>Descrição</h6>
                        <p class="mb-3"><?php echo nl2br(htmlspecialchars($pedido['descricao'])); ?></p>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <h6>Data do Pedido</h6>
                                <p><?php echo date('d/m/Y H:i', strtotime($pedido['created_at'])); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <h6>Prazo de Resposta</h6>
                                <p><?php echo date('d/m/Y', strtotime($pedido['prazo_resposta'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6>Status Atual</h6>
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
                            <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
                            <?php 
                            $statusTexts = [
                                'pendente' => 'Pendente',
                                'em_analise' => 'Em Análise',
                                'respondido' => 'Respondido',
                                'negado' => 'Negado'
                            ];
                            echo $statusTexts[$pedido['status']] ?? 'Desconhecido';
                            ?>
                        </span>
                        
                        <?php if ($pedido['status'] === 'respondido' && !empty($pedido['resposta'])): ?>
                            <div class="mt-3">
                                <h6>Resposta</h6>
                                <div class="alert alert-success">
                                    <?php echo nl2br(htmlspecialchars($pedido['resposta'])); ?>
                                </div>
                                <?php if (!empty($pedido['resposta_arquivo'])): ?>
                                    <a href="/download/<?php echo $pedido['resposta_arquivo']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>
                                        Baixar Arquivo
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($pedido['status'] === 'negado' && !empty($pedido['justificativa_negacao'])): ?>
                            <div class="mt-3">
                                <h6>Justificativa</h6>
                                <div class="alert alert-danger">
                                    <?php echo nl2br(htmlspecialchars($pedido['justificativa_negacao'])); ?>
                                </div>
                                <small class="text-muted">
                                    Você pode entrar com recurso caso discorde da decisão.
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <?php if (isset($historico) && !empty($historico)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Histórico do Pedido
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach ($historico as $item): ?>
                    <div class="timeline-item <?php echo $item['ativo'] ? 'active' : ''; ?>">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo htmlspecialchars($item['evento']); ?></strong>
                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></small>
                        </div>
                        <?php if (!empty($item['observacoes'])): ?>
                        <p class="mb-0 mt-1"><?php echo htmlspecialchars($item['observacoes']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Actions -->
        <?php if (isset($pedido) && $pedido): ?>
        <div class="card mt-4">
            <div class="card-body text-center">
                <h6>Ações Disponíveis</h6>
                <div class="btn-group" role="group">
                    <a href="/pedido/<?php echo $pedido['protocolo']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>
                        Ver Detalhes
                    </a>
                    <?php if (in_array($pedido['status'], ['negado', 'parcial'])): ?>
                    <a href="/recurso/<?php echo $pedido['protocolo']; ?>" class="btn btn-outline-warning">
                        <i class="fas fa-gavel me-1"></i>
                        Entrar com Recurso
                    </a>
                    <?php endif; ?>
                    <a href="mailto:esic@orgao.gov.br?subject=Pedido <?php echo $pedido['protocolo']; ?>" 
                       class="btn btn-outline-info">
                        <i class="fas fa-envelope me-1"></i>
                        Entrar em Contato
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para protocolo
            const protocoloInput = document.getElementById('protocolo');
            protocoloInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                this.value = value;
            });
        });
    </script>
</body>
</html>