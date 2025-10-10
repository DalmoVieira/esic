<!-- Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">Dashboard</h1>
        <p class="text-muted mb-0">Visão geral do sistema E-SIC</p>
    </div>
    <div class="text-end">
        <div class="text-muted small">Última atualização:</div>
        <div class="fw-semibold" id="last-update"><?= date('d/m/Y H:i:s') ?></div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-gradient rounded-3 p-3">
                            <i class="bi bi-file-earmark-text text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small text-muted text-uppercase fw-semibold">Total de Pedidos</div>
                        <div class="h2 fw-bold text-primary mb-0" id="total-pedidos">
                            <?= number_format($stats['total_pedidos'] ?? 0) ?>
                        </div>
                        <div class="small text-success">
                            <i class="bi bi-arrow-up"></i>
                            +<?= $stats['pedidos_mes'] ?? 0 ?> este mês
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-gradient rounded-3 p-3">
                            <i class="bi bi-clock-history text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small text-muted text-uppercase fw-semibold">Pendentes</div>
                        <div class="h2 fw-bold text-warning mb-0" id="pedidos-pendentes">
                            <?= number_format($stats['pedidos_pendentes'] ?? 0) ?>
                        </div>
                        <div class="small text-muted">
                            Aguardando análise
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-gradient rounded-3 p-3">
                            <i class="bi bi-check-circle text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small text-muted text-uppercase fw-semibold">Respondidos</div>
                        <div class="h2 fw-bold text-success mb-0" id="pedidos-respondidos">
                            <?= number_format($stats['pedidos_respondidos'] ?? 0) ?>
                        </div>
                        <div class="small text-muted">
                            Taxa: <?= number_format($stats['taxa_resposta'] ?? 0, 1) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-gradient rounded-3 p-3">
                            <i class="bi bi-speedometer2 text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small text-muted text-uppercase fw-semibold">Tempo Médio</div>
                        <div class="h2 fw-bold text-info mb-0" id="tempo-medio">
                            <?= number_format($stats['tempo_medio_resposta'] ?? 0) ?>
                        </div>
                        <div class="small text-muted">
                            dias para resposta
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <!-- Requests Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Pedidos por Mês
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                data-bs-toggle="dropdown">
                            <i class="bi bi-calendar3"></i> 2024
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateChart(2024)">2024</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateChart(2023)">2023</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="pedidosChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Status Pie Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart text-primary me-2"></i>
                    Status dos Pedidos
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity and Alerts -->
<div class="row g-4">
    <!-- Recent Requests -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Pedidos Recentes
                    </h5>
                    <a href="<?= url('/admin/pedidos') ?>" class="btn btn-outline-primary btn-sm">
                        Ver Todos
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Protocolo</th>
                                <th class="border-0">Solicitante</th>
                                <th class="border-0">Assunto</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Data</th>
                                <th class="border-0">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pedidos_recentes)): ?>
                                <?php foreach ($pedidos_recentes as $pedido): ?>
                                <tr>
                                    <td>
                                        <span class="font-monospace small"><?= htmlspecialchars($pedido['protocolo']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($pedido['nome_solicitante']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($pedido['email']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" 
                                             title="<?= htmlspecialchars($pedido['assunto']) ?>">
                                            <?= htmlspecialchars($pedido['assunto']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= getStatusBadgeClass($pedido['status']) ?>">
                                            <?= getStatusLabel($pedido['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small"><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
                                        <div class="small text-muted"><?= date('H:i', strtotime($pedido['data_pedido'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= url('/admin/pedidos/ver/' . $pedido['id']) ?>" 
                                               class="btn btn-outline-primary" title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($pedido['status'] === 'pendente'): ?>
                                            <a href="<?= url('/admin/pedidos/responder/' . $pedido['id']) ?>" 
                                               class="btn btn-outline-success" title="Responder">
                                                <i class="bi bi-reply"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox"></i>
                                    Nenhum pedido recente
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alerts and Actions -->
    <div class="col-lg-4">
        <!-- Urgent Alerts -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark py-3">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Alertas Urgentes
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($alertas_urgentes)): ?>
                        <?php foreach ($alertas_urgentes as $alerta): ?>
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-warning mb-1">
                                        <?= htmlspecialchars($alerta['titulo']) ?>
                                    </div>
                                    <div class="small text-muted">
                                        <?= htmlspecialchars($alerta['descricao']) ?>
                                    </div>
                                </div>
                                <span class="badge bg-danger"><?= $alerta['quantidade'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item border-0 py-3 text-center text-muted">
                            <i class="bi bi-check-circle"></i>
                            Nenhum alerta urgente
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0">
                    <i class="bi bi-lightning text-primary me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('/admin/pedidos?status=pendente') ?>" 
                       class="btn btn-outline-warning">
                        <i class="bi bi-clock me-2"></i>
                        Ver Pendentes (<?= $stats['pedidos_pendentes'] ?? 0 ?>)
                    </a>
                    
                    <a href="<?= url('/admin/pedidos?vencendo=true') ?>" 
                       class="btn btn-outline-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Prazo Vencendo (<?= $stats['pedidos_vencendo'] ?? 0 ?>)
                    </a>
                    
                    <a href="<?= url('/admin/recursos') ?>" 
                       class="btn btn-outline-info">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Recursos (<?= $stats['recursos_pendentes'] ?? 0 ?>)
                    </a>
                    
                    <a href="<?= url('/admin/relatorios') ?>" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>
                        Gerar Relatório
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up text-primary me-2"></i>
                    Métricas de Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="border-end">
                            <div class="h4 fw-bold text-primary"><?= number_format($stats['taxa_resposta'] ?? 0, 1) ?>%</div>
                            <div class="small text-muted">Taxa de Resposta</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <div class="h4 fw-bold text-success"><?= number_format($stats['satisfacao'] ?? 0, 1) ?>%</div>
                            <div class="small text-muted">Satisfação</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <div class="h4 fw-bold text-info"><?= number_format($stats['tempo_medio_resposta'] ?? 0) ?></div>
                            <div class="small text-muted">Dias Médios</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <div class="h4 fw-bold text-warning"><?= number_format($stats['recursos_total'] ?? 0) ?></div>
                            <div class="small text-muted">Recursos</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="border-end">
                            <div class="h4 fw-bold text-danger"><?= number_format($stats['pedidos_vencidos'] ?? 0) ?></div>
                            <div class="small text-muted">Vencidos</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="h4 fw-bold text-secondary"><?= number_format($stats['usuarios_ativos'] ?? 0) ?></div>
                        <div class="small text-muted">Usuários Ativos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js Configuration
document.addEventListener('DOMContentLoaded', function() {
    // Requests per month chart
    const pedidosCtx = document.getElementById('pedidosChart').getContext('2d');
    const pedidosChart = new Chart(pedidosCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Pedidos',
                data: <?= json_encode($chart_data['pedidos_mes'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]) ?>,
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Respondidos',
                data: <?= json_encode($chart_data['respondidos_mes'] ?? [0,0,0,0,0,0,0,0,0,0,0,0]) ?>,
                borderColor: 'rgb(25, 135, 84)',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Status pie chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pendente', 'Em Análise', 'Respondido', 'Finalizado'],
            datasets: [{
                data: <?= json_encode($chart_data['status_distribution'] ?? [0,0,0,0]) ?>,
                backgroundColor: [
                    'rgb(255, 193, 7)',
                    'rgb(13, 202, 240)',
                    'rgb(25, 135, 84)',
                    'rgb(13, 110, 253)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    
    // Auto-refresh dashboard data
    setInterval(refreshDashboard, 300000); // 5 minutes
});

function refreshDashboard() {
    fetch('<?= url('/admin/api/dashboard-stats') ?>')
        .then(response => response.json())
        .then(data => {
            // Update statistics
            document.getElementById('total-pedidos').textContent = new Intl.NumberFormat().format(data.total_pedidos);
            document.getElementById('pedidos-pendentes').textContent = new Intl.NumberFormat().format(data.pedidos_pendentes);
            document.getElementById('pedidos-respondidos').textContent = new Intl.NumberFormat().format(data.pedidos_respondidos);
            document.getElementById('tempo-medio').textContent = new Intl.NumberFormat().format(data.tempo_medio_resposta);
            
            // Update timestamp
            document.getElementById('last-update').textContent = new Date().toLocaleString('pt-BR');
        })
        .catch(error => console.error('Error refreshing dashboard:', error));
}

function updateChart(year) {
    // Implementation for updating chart data by year
    console.log('Updating chart for year:', year);
}

// Status badge helper function (same as in other templates)
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
</script>

<style>
.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.border-end:last-child {
    border-right: none !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
    
    .border-end:last-child {
        border-bottom: none !important;
        margin-bottom: 0;
        padding-bottom: 0;
    }
}
</style>