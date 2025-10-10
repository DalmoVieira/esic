<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="fw-bold text-primary mb-3">
                    <i class="bi bi-search me-2"></i>
                    Consultar Pedido
                </h1>
                <p class="lead text-muted">
                    Acompanhe o status da sua solicitação de informação
                </p>
            </div>

            <!-- Search Card -->
            <div class="card border-0 shadow-lg mb-5">
                <div class="card-body p-4">
                    <form id="consultaForm" method="GET" action="<?= url('/pedido/consultar') ?>">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label for="protocolo" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-code me-2"></i>
                                    Número do Protocolo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-hash"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-lg" 
                                           id="protocolo" name="protocolo" 
                                           placeholder="Ex: 2024001234567"
                                           value="<?= htmlspecialchars($protocolo ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-2"></i>
                                    Email do Solicitante
                                </label>
                                <input type="email" class="form-control form-control-lg" 
                                       id="email" name="email" 
                                       placeholder="seu@email.com"
                                       value="<?= htmlspecialchars($email ?? '') ?>">
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-search me-2"></i>
                                    Consultar
                                </button>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Informe o número do protocolo ou seu email para consultar todos os seus pedidos
                            </small>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($pedido) && $pedido): ?>
            <!-- Single Request Result -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            Protocolo: <?= htmlspecialchars($pedido['protocolo']) ?>
                        </h5>
                        <span class="badge bg-<?= getStatusBadgeClass($pedido['status']) ?> fs-6">
                            <?= getStatusLabel($pedido['status']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Progress Timeline -->
                    <div class="timeline-container mb-4">
                        <div class="timeline">
                            <?php
                            $statusOrder = ['pendente', 'em_analise', 'respondido', 'finalizado'];
                            $currentStatusIndex = array_search($pedido['status'], $statusOrder);
                            ?>
                            
                            <?php foreach ($statusOrder as $index => $status): ?>
                            <div class="timeline-item <?= $index <= $currentStatusIndex ? 'active' : '' ?>">
                                <div class="timeline-marker">
                                    <?php if ($index < $currentStatusIndex): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    <?php elseif ($index == $currentStatusIndex): ?>
                                        <i class="bi bi-clock-fill text-primary"></i>
                                    <?php else: ?>
                                        <i class="bi bi-circle text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?= getStatusLabel($status) ?></h6>
                                    <?php if ($index == $currentStatusIndex): ?>
                                        <small class="text-muted">Status atual</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Request Details -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Detalhes do Pedido
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Assunto:</label>
                                            <div class="detail-value"><?= htmlspecialchars($pedido['assunto']) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Data da Solicitação:</label>
                                            <div class="detail-value"><?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="detail-item">
                                            <label class="detail-label">Descrição:</label>
                                            <div class="detail-value"><?= nl2br(htmlspecialchars($pedido['descricao'])) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Solicitante:</label>
                                            <div class="detail-value"><?= htmlspecialchars($pedido['nome_solicitante']) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-sm-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Email:</label>
                                            <div class="detail-value"><?= htmlspecialchars($pedido['email']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Status Info -->
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-clock-history me-2"></i>
                                        Prazos
                                    </h6>
                                    
                                    <?php 
                                    $dataLimite = date('Y-m-d', strtotime($pedido['data_pedido'] . ' +20 days'));
                                    $diasRestantes = max(0, (strtotime($dataLimite) - time()) / (60 * 60 * 24));
                                    ?>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Prazo para resposta:</small>
                                        <div class="fw-semibold"><?= date('d/m/Y', strtotime($dataLimite)) ?></div>
                                    </div>
                                    
                                    <div class="progress mb-2" style="height: 8px;">
                                        <?php 
                                        $progresso = max(0, min(100, ((20 - $diasRestantes) / 20) * 100));
                                        $progressClass = $diasRestantes <= 5 ? 'bg-warning' : ($diasRestantes <= 2 ? 'bg-danger' : 'bg-success');
                                        ?>
                                        <div class="progress-bar <?= $progressClass ?>" 
                                             style="width: <?= $progresso ?>%"></div>
                                    </div>
                                    
                                    <small class="<?= $diasRestantes <= 5 ? 'text-warning' : 'text-muted' ?>">
                                        <?php if ($diasRestantes > 0): ?>
                                            <?= ceil($diasRestantes) ?> dia(s) restante(s)
                                        <?php else: ?>
                                            Prazo vencido
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <?php if ($pedido['status'] === 'respondido'): ?>
                                    <button class="btn btn-success" onclick="window.print()">
                                        <i class="bi bi-printer me-2"></i>
                                        Imprimir
                                    </button>
                                <?php endif; ?>
                                
                                <?php if (in_array($pedido['status'], ['negado', 'parcialmente_atendido'])): ?>
                                    <a href="<?= url('/recurso/novo/' . $pedido['id']) ?>" 
                                       class="btn btn-warning">
                                        <i class="bi bi-arrow-repeat me-2"></i>
                                        Interpor Recurso
                                    </a>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline-primary" onclick="compartilhar()">
                                    <i class="bi bi-share me-2"></i>
                                    Compartilhar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Response Section -->
                    <?php if (!empty($pedido['resposta'])): ?>
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-chat-square-text me-2"></i>
                            Resposta do Órgão
                        </h6>
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted">Respondido em: <?= date('d/m/Y H:i', strtotime($pedido['data_resposta'])) ?></small>
                                </div>
                                <div><?= nl2br(htmlspecialchars($pedido['resposta'])) ?></div>
                                
                                <?php if (!empty($pedido['anexos_resposta'])): ?>
                                <div class="mt-3">
                                    <h6 class="mb-2">Anexos da Resposta:</h6>
                                    <?php foreach (explode(',', $pedido['anexos_resposta']) as $anexo): ?>
                                        <a href="<?= url('/anexo/' . trim($anexo)) ?>" 
                                           class="btn btn-sm btn-outline-primary me-2 mb-2" 
                                           target="_blank">
                                            <i class="bi bi-paperclip me-1"></i>
                                            <?= htmlspecialchars(basename(trim($anexo))) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php elseif (isset($pedidos) && !empty($pedidos)): ?>
            <!-- Multiple Requests Results -->
            <div class="mb-4">
                <h4 class="text-primary">
                    <i class="bi bi-list-ul me-2"></i>
                    Seus Pedidos (<?= count($pedidos) ?>)
                </h4>
                <p class="text-muted">Clique em um pedido para ver os detalhes completos</p>
            </div>
            
            <div class="row">
                <?php foreach ($pedidos as $pedido): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 pedido-card" 
                         onclick="window.location.href='<?= url('/pedido/consultar?protocolo=' . $pedido['protocolo']) ?>'">
                        <div class="card-header bg-light border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="bi bi-hash me-1"></i>
                                    <?= htmlspecialchars($pedido['protocolo']) ?>
                                </h6>
                                <span class="badge bg-<?= getStatusBadgeClass($pedido['status']) ?>">
                                    <?= getStatusLabel($pedido['status']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title mb-2"><?= htmlspecialchars($pedido['assunto']) ?></h6>
                            <p class="card-text text-muted small mb-3">
                                <?= mb_substr(htmlspecialchars($pedido['descricao']), 0, 150) ?>
                                <?= mb_strlen($pedido['descricao']) > 150 ? '...' : '' ?>
                            </p>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <small class="text-muted d-block">Solicitado em</small>
                                    <strong><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Prazo até</small>
                                    <strong><?= date('d/m/Y', strtotime($pedido['data_pedido'] . ' +20 days')) ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <small class="text-primary">
                                <i class="bi bi-eye me-1"></i>
                                Clique para ver detalhes
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php elseif (isset($protocolo) || isset($email)): ?>
            <!-- No Results Found -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted">Nenhum pedido encontrado</h4>
                <p class="text-muted mb-4">
                    Verifique se o protocolo ou email estão corretos e tente novamente.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="<?= url('/pedido/novo') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Fazer Novo Pedido
                    </a>
                    <button onclick="document.getElementById('consultaForm').reset()" 
                            class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Nova Consulta
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        Precisa de Ajuda?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Como consultar meu pedido?</h6>
                            <ul class="list-unstyled small">
                                <li>• Use o número do protocolo recebido por email</li>
                                <li>• Ou informe seu email para ver todos os pedidos</li>
                                <li>• Guarde sempre o número do protocolo</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Não recebeu o protocolo?</h6>
                            <ul class="list-unstyled small">
                                <li>• Verifique sua caixa de spam</li>
                                <li>• Consulte usando seu email</li>
                                <li>• Entre em contato pelo telefone</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pedido-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.pedido-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.timeline {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    padding: 0 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}

.timeline-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.timeline-item.active::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    width: calc(100% + 20px);
    height: 2px;
    background: var(--bs-primary);
    z-index: 1;
}

.timeline-item:first-child.active::before {
    left: 50%;
    width: 50%;
}

.timeline-item:last-child.active::before {
    left: 0;
    width: 50%;
}

.timeline-item:not(:first-child):not(:last-child).active::before {
    left: -50%;
    width: 100%;
}

.timeline-marker {
    background: white;
    border-radius: 50%;
    padding: 5px;
    margin-bottom: 10px;
    font-size: 1.25rem;
}

.timeline-content h6 {
    font-size: 0.875rem;
    margin-bottom: 5px;
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 0.95rem;
    color: #495057;
}

@media (max-width: 768px) {
    .timeline {
        flex-direction: column;
        align-items: stretch;
        padding: 0;
    }
    
    .timeline::before {
        display: none;
    }
    
    .timeline-item {
        flex-direction: row;
        text-align: left;
        margin-bottom: 1rem;
    }
    
    .timeline-item::before {
        display: none !important;
    }
    
    .timeline-marker {
        margin-right: 1rem;
        margin-bottom: 0;
    }
}
</style>

<script>
function compartilhar() {
    if (navigator.share) {
        navigator.share({
            title: 'Pedido de Informação - E-SIC',
            text: 'Acompanhe meu pedido de informação',
            url: window.location.href
        });
    } else {
        // Fallback para navegadores que não suportam Web Share API
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado para a área de transferência!');
        });
    }
}

// Auto-format protocol input
document.getElementById('protocolo').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 13) {
        value = value.substring(0, 13);
    }
    e.target.value = value;
});

// Form validation
document.getElementById('consultaForm').addEventListener('submit', function(e) {
    const protocolo = document.getElementById('protocolo').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (!protocolo && !email) {
        e.preventDefault();
        alert('Por favor, informe o número do protocolo ou seu email.');
        return false;
    }
});
</script>

<?php
function getStatusLabel($status) {
    $labels = [
        'pendente' => 'Pendente',
        'em_analise' => 'Em Análise',
        'respondido' => 'Respondido',
        'finalizado' => 'Finalizado',
        'negado' => 'Negado',
        'parcialmente_atendido' => 'Parcialmente Atendido'
    ];
    return $labels[$status] ?? 'Desconhecido';
}

function getStatusBadgeClass($status) {
    $classes = [
        'pendente' => 'warning',
        'em_analise' => 'info',
        'respondido' => 'success',
        'finalizado' => 'primary',
        'negado' => 'danger',
        'parcialmente_atendido' => 'secondary'
    ];
    return $classes[$status] ?? 'secondary';
}
?>