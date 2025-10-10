<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4 fade-in">
                    Sistema Eletrônico do Serviço de Informação ao Cidadão
                </h1>
                <p class="lead mb-4 fade-in">
                    Solicite informações públicas de forma fácil e transparente. 
                    Implementação da Lei nº 12.527/2011 (Lei de Acesso à Informação).
                </p>
                <div class="d-flex flex-wrap gap-3 fade-in">
                    <a href="<?= url('/pedido/novo') ?>" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-plus-circle-fill me-2"></i>Fazer Pedido
                    </a>
                    <a href="<?= url('/acompanhar') ?>" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-search me-2"></i>Acompanhar Pedido
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="bg-white rounded-3 p-4 shadow-lg">
                    <i class="bi bi-shield-check text-primary" style="font-size: 4rem;"></i>
                    <h4 class="text-primary mt-3 mb-2">Transparência</h4>
                    <p class="text-muted mb-0">Acesso garantido às informações públicas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Estatísticas do Sistema</h2>
                <p class="text-muted lead">Dados atualizados em tempo real</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-file-earmark-text display-4 mb-3"></i>
                        <h3 class="card-title"><?= number_format($stats['total_pedidos'] ?? 0) ?></h3>
                        <p class="card-text mb-0">Pedidos Recebidos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-check-circle display-4 mb-3"></i>
                        <h3 class="card-title"><?= number_format($stats['pedidos_deferidos'] ?? 0) ?></h3>
                        <p class="card-text mb-0">Pedidos Deferidos</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-clock display-4 mb-3"></i>
                        <h3 class="card-title"><?= $stats['tempo_medio_resposta'] ?? 0 ?></h3>
                        <p class="card-text mb-0">Dias (Tempo Médio)</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="card stats-card h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-people display-4 mb-3"></i>
                        <h3 class="card-title"><?= number_format($stats['cidadaos_atendidos'] ?? 0) ?></h3>
                        <p class="card-text mb-0">Cidadãos Atendidos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold mb-3">Como Funciona</h2>
                <p class="text-muted lead">Processo simples em 4 etapas</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">1</span>
                        </div>
                        <h5 class="card-title">Faça seu Pedido</h5>
                        <p class="card-text text-muted">
                            Preencha o formulário com sua solicitação de informação
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">2</span>
                        </div>
                        <h5 class="card-title">Receba o Protocolo</h5>
                        <p class="card-text text-muted">
                            Guarde seu número de protocolo para acompanhamento
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">3</span>
                        </div>
                        <h5 class="card-title">Aguarde Análise</h5>
                        <p class="card-text text-muted">
                            Seu pedido será analisado em até 20 dias úteis
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card card-hover h-100 text-center border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4">4</span>
                        </div>
                        <h5 class="card-title">Receba a Resposta</h5>
                        <p class="card-text text-muted">
                            A resposta será enviada por email e ficará disponível no sistema
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Requests Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-hover border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Pedidos Recentes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_requests)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Protocolo</th>
                                            <th>Assunto</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_requests as $request): ?>
                                            <tr>
                                                <td>
                                                    <code class="protocol-display"><?= htmlspecialchars($request['protocolo']) ?></code>
                                                </td>
                                                <td><?= htmlspecialchars(substr($request['assunto'], 0, 50)) ?>...</td>
                                                <td>
                                                    <?php
                                                    $statusClass = match($request['status']) {
                                                        'deferido' => 'success',
                                                        'indeferido' => 'danger',
                                                        'em_andamento' => 'info',
                                                        'pendente' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                    $statusText = match($request['status']) {
                                                        'deferido' => 'Deferido',
                                                        'indeferido' => 'Indeferido',
                                                        'em_andamento' => 'Em Andamento',
                                                        'pendente' => 'Pendente',
                                                        default => 'Desconhecido'
                                                    };
                                                    ?>
                                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($request['data_criacao'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">Nenhum pedido registrado ainda.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning-fill me-2"></i>
                            Ações Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= url('/pedido/novo') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>Novo Pedido
                            </a>
                            <a href="<?= url('/acompanhar') ?>" class="btn btn-outline-info">
                                <i class="bi bi-search me-2"></i>Acompanhar Pedido
                            </a>
                            <a href="<?= url('/recurso/novo') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-repeat me-2"></i>Interpor Recurso
                            </a>
                            <a href="<?= url('/transparencia') ?>" class="btn btn-outline-success">
                                <i class="bi bi-graph-up me-2"></i>Transparência
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Information Box -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Informações Importantes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <p><strong>Prazo de Resposta:</strong> Até 20 dias úteis</p>
                            <p><strong>Recurso:</strong> Até 10 dias após a resposta</p>
                            <p><strong>Lei de Acesso:</strong> Lei nº 12.527/2011</p>
                            <p class="mb-0"><strong>Atendimento:</strong> Segunda a Sexta, 8h às 17h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About LAI Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Sobre a Lei de Acesso à Informação</h2>
                <p class="lead text-muted mb-4">
                    A Lei nº 12.527/2011 regulamenta o direito constitucional de acesso às informações públicas.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                            <span>Transparência ativa</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                            <span>Transparência passiva</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                            <span>Direito de recurso</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                            <span>Prazos definidos</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?= url('/sobre-lai') ?>" class="btn btn-primary">
                        Saiba Mais
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="https://via.placeholder.com/500x300/0d47a1/ffffff?text=Lei+de+Acesso" 
                         alt="Lei de Acesso à Informação" 
                         class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>