<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            Relatórios e Estatísticas
        </h1>
        <p class="text-muted mb-0">Análises e relatórios do sistema E-SIC</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" onclick="exportarRelatorio('excel')">
            <i class="bi bi-file-earmark-excel me-1"></i>
            Excel
        </button>
        <button class="btn btn-outline-danger" onclick="exportarRelatorio('pdf')">
            <i class="bi bi-file-earmark-pdf me-1"></i>
            PDF
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#relatorioPersonalizadoModal">
            <i class="bi bi-gear me-1"></i>
            Personalizar
        </button>
    </div>
</div>

<!-- Period Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="periodo" id="mensal" value="mensal" checked>
                    <label class="btn btn-outline-primary" for="mensal">Este Mês</label>
                    
                    <input type="radio" class="btn-check" name="periodo" id="anual" value="anual">
                    <label class="btn btn-outline-primary" for="anual">Este Ano</label>
                    
                    <input type="radio" class="btn-check" name="periodo" id="custom" value="custom">
                    <label class="btn btn-outline-primary" for="custom">Personalizado</label>
                </div>
            </div>
            <div class="col-md-4" id="customPeriod" style="display: none;">
                <div class="row">
                    <div class="col-6">
                        <input type="date" class="form-control form-control-sm" id="dataInicial" 
                               value="<?= date('Y-m-01') ?>">
                    </div>
                    <div class="col-6">
                        <input type="date" class="form-control form-control-sm" id="dataFinal" 
                               value="<?= date('Y-m-t') ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success w-100" onclick="atualizarRelatorios()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Atualizar Dados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics -->
<div class="row g-4 mb-4">
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
                        <div class="h2 fw-bold text-primary mb-0" id="totalPedidos">
                            <?= number_format($relatorio['total_pedidos'] ?? 0) ?>
                        </div>
                        <div class="small text-success">
                            <i class="bi bi-arrow-up"></i>
                            <?= $relatorio['variacao_pedidos'] ?? '+0' ?>% vs período anterior
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
                        <div class="small text-muted text-uppercase fw-semibold">Taxa de Resposta</div>
                        <div class="h2 fw-bold text-success mb-0" id="taxaResposta">
                            <?= number_format($relatorio['taxa_resposta'] ?? 0, 1) ?>%
                        </div>
                        <div class="small text-muted">
                            Meta: 95%
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
                        <div class="h2 fw-bold text-info mb-0" id="tempoMedio">
                            <?= number_format($relatorio['tempo_medio_resposta'] ?? 0, 1) ?>
                        </div>
                        <div class="small text-muted">
                            dias para resposta
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
                            <i class="bi bi-arrow-repeat text-white" style="font-size: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="small text-muted text-uppercase fw-semibold">Recursos</div>
                        <div class="h2 fw-bold text-warning mb-0" id="totalRecursos">
                            <?= number_format($relatorio['total_recursos'] ?? 0) ?>
                        </div>
                        <div class="small text-muted">
                            <?= number_format($relatorio['percentual_recursos'] ?? 0, 1) ?>% dos pedidos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Volume de Pedidos -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Volume de Pedidos
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" onclick="changeChartView('daily')">
                            Diário
                        </button>
                        <button class="btn btn-outline-secondary" onclick="changeChartView('weekly')">
                            Semanal
                        </button>
                        <button class="btn btn-outline-secondary" onclick="changeChartView('monthly')">
                            Mensal
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="volumeChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    Distribuição por Status
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="300"></canvas>
                <div class="mt-3">
                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <div class="small text-muted">Pendentes</div>
                            <div class="fw-bold text-warning"><?= $relatorio['pedidos_pendentes'] ?? 0 ?></div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="small text-muted">Respondidos</div>
                            <div class="fw-bold text-success"><?= $relatorio['pedidos_respondidos'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Reports -->
<div class="row g-4 mb-4">
    <!-- Performance Metrics -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer me-2"></i>
                    Métricas de Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Cumprimento de Prazo</span>
                        <span class="fw-bold"><?= number_format($relatorio['cumprimento_prazo'] ?? 0, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $relatorio['cumprimento_prazo'] ?? 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Taxa de Satisfação</span>
                        <span class="fw-bold"><?= number_format($relatorio['taxa_satisfacao'] ?? 0, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: <?= $relatorio['taxa_satisfacao'] ?? 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Pedidos Complexos</span>
                        <span class="fw-bold"><?= number_format($relatorio['pedidos_complexos'] ?? 0, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: <?= $relatorio['pedidos_complexos'] ?? 0 ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Taxa de Recursos</span>
                        <span class="fw-bold"><?= number_format($relatorio['taxa_recursos'] ?? 0, 1) ?>%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: <?= $relatorio['taxa_recursos'] ?? 0 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Categories -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-ol me-2"></i>
                    Categorias Mais Solicitadas
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <?php if (!empty($relatorio['top_categorias'])): ?>
                                <?php foreach ($relatorio['top_categorias'] as $index => $categoria): ?>
                                <tr>
                                    <td class="text-center" style="width: 40px;">
                                        <span class="badge bg-primary rounded-pill"><?= $index + 1 ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($categoria['nome']) ?></div>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $categoria['percentual'] ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end" style="width: 80px;">
                                        <div class="fw-bold"><?= number_format($categoria['total']) ?></div>
                                        <small class="text-muted"><?= number_format($categoria['percentual'], 1) ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Nenhum dado disponível para o período selecionado
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Time Analysis -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Análise de Tempo de Resposta
                </h5>
            </div>
            <div class="card-body">
                <canvas id="responseTimeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Tables -->
<div class="row g-4">
    <!-- By Department -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>
                        Por Departamento
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportarDepartamentos()">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Departamento</th>
                                <th class="text-center">Pedidos</th>
                                <th class="text-center">Tempo Médio</th>
                                <th class="text-center">Taxa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($relatorio['por_departamento'])): ?>
                                <?php foreach ($relatorio['por_departamento'] as $dept): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($dept['nome']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary"><?= number_format($dept['total']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($dept['tempo_medio'], 1) ?>d
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $dept['taxa_resposta'] >= 90 ? 'success' : ($dept['taxa_resposta'] >= 70 ? 'warning' : 'danger') ?>">
                                            <?= number_format($dept['taxa_resposta'], 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Nenhum dado disponível
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- By User -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Por Usuário
                    </h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportarUsuarios()">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuário</th>
                                <th class="text-center">Atendidos</th>
                                <th class="text-center">Tempo Médio</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($relatorio['por_usuario'])): ?>
                                <?php foreach ($relatorio['por_usuario'] as $user): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($user['nome']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($user['setor']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info"><?= number_format($user['atendidos']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?= number_format($user['tempo_medio'], 1) ?>d
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $performance = $user['performance'] ?? 0;
                                        $performanceClass = $performance >= 90 ? 'success' : ($performance >= 70 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $performanceClass ?>">
                                            <?= number_format($performance, 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Nenhum dado disponível
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="relatorioPersonalizadoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-gear me-2"></i>
                    Relatório Personalizado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Período</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control" name="data_inicio" required>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" name="data_fim" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Formato de Exportação</label>
                            <select class="form-select" name="formato" required>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Incluir Seções</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="resumo" id="incluirResumo" checked>
                                    <label class="form-check-label" for="incluirResumo">
                                        Resumo Executivo
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="pedidos" id="incluirPedidos" checked>
                                    <label class="form-check-label" for="incluirPedidos">
                                        Análise de Pedidos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="performance" id="incluirPerformance" checked>
                                    <label class="form-check-label" for="incluirPerformance">
                                        Métricas de Performance
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="recursos" id="incluirRecursos">
                                    <label class="form-check-label" for="incluirRecursos">
                                        Análise de Recursos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="departamentos" id="incluirDepartamentos">
                                    <label class="form-check-label" for="incluirDepartamentos">
                                        Por Departamento
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="secoes[]" 
                                           value="graficos" id="incluirGraficos">
                                    <label class="form-check-label" for="incluirGraficos">
                                        Gráficos e Charts
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Filtros Adicionais</label>
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select" name="departamento">
                                    <option value="">Todos os departamentos</option>
                                    <option value="admin">Administração</option>
                                    <option value="rh">Recursos Humanos</option>
                                    <option value="financeiro">Financeiro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" name="status">
                                    <option value="">Todos os status</option>
                                    <option value="respondido">Apenas Respondidos</option>
                                    <option value="pendente">Apenas Pendentes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="gerarRelatorioPersonalizado()">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i>
                    Gerar Relatório
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

// Period filter handling
document.querySelectorAll('input[name="periodo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const customPeriod = document.getElementById('customPeriod');
        if (this.value === 'custom') {
            customPeriod.style.display = 'block';
        } else {
            customPeriod.style.display = 'none';
            atualizarRelatorios();
        }
    });
});

function initializeCharts() {
    // Volume Chart
    const volumeCtx = document.getElementById('volumeChart').getContext('2d');
    const volumeChart = new Chart(volumeCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($relatorio['volume_labels'] ?? []) ?>,
            datasets: [{
                label: 'Pedidos Recebidos',
                data: <?= json_encode($relatorio['volume_data'] ?? []) ?>,
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Pedidos Respondidos',
                data: <?= json_encode($relatorio['volume_respondidos'] ?? []) ?>,
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
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pendente', 'Em Análise', 'Respondido', 'Finalizado'],
            datasets: [{
                data: <?= json_encode($relatorio['status_data'] ?? [0,0,0,0]) ?>,
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
    
    // Response Time Chart
    const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
    const responseTimeChart = new Chart(responseTimeCtx, {
        type: 'bar',
        data: {
            labels: ['0-5 dias', '6-10 dias', '11-15 dias', '16-20 dias', '21+ dias'],
            datasets: [{
                label: 'Número de Pedidos',
                data: <?= json_encode($relatorio['response_time_data'] ?? [0,0,0,0,0]) ?>,
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(13, 202, 240, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(253, 126, 20, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgb(25, 135, 84)',
                    'rgb(13, 202, 240)',
                    'rgb(255, 193, 7)',
                    'rgb(253, 126, 20)',
                    'rgb(220, 53, 69)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function changeChartView(view) {
    // Update chart view (daily/weekly/monthly)
    document.querySelectorAll('.btn-group-sm .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Here you would typically fetch new data and update the chart
    console.log('Changing chart view to:', view);
}

function atualizarRelatorios() {
    const periodo = document.querySelector('input[name="periodo"]:checked').value;
    let dataInicial, dataFinal;
    
    if (periodo === 'custom') {
        dataInicial = document.getElementById('dataInicial').value;
        dataFinal = document.getElementById('dataFinal').value;
        
        if (!dataInicial || !dataFinal) {
            alert('Por favor, selecione o período personalizado.');
            return;
        }
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-arrow-clockwise spinning me-1"></i>Atualizando...';
    button.disabled = true;
    
    // Prepare data
    const params = new URLSearchParams({
        periodo: periodo,
        ...(periodo === 'custom' && { data_inicial: dataInicial, data_final: dataFinal })
    });
    
    // Fetch updated data
    fetch(`/admin/api/relatorios?${params}`)
        .then(response => response.json())
        .then(data => {
            // Update metrics
            document.getElementById('totalPedidos').textContent = new Intl.NumberFormat().format(data.total_pedidos);
            document.getElementById('taxaResposta').textContent = data.taxa_resposta.toFixed(1) + '%';
            document.getElementById('tempoMedio').textContent = data.tempo_medio_resposta.toFixed(1);
            document.getElementById('totalRecursos').textContent = new Intl.NumberFormat().format(data.total_recursos);
            
            // Update charts would go here
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        })
        .catch(error => {
            console.error('Error updating reports:', error);
            alert('Erro ao atualizar relatórios. Tente novamente.');
            
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        });
}

function exportarRelatorio(formato) {
    const periodo = document.querySelector('input[name="periodo"]:checked').value;
    let params = new URLSearchParams({
        export: formato,
        periodo: periodo
    });
    
    if (periodo === 'custom') {
        const dataInicial = document.getElementById('dataInicial').value;
        const dataFinal = document.getElementById('dataFinal').value;
        
        if (!dataInicial || !dataFinal) {
            alert('Por favor, selecione o período personalizado.');
            return;
        }
        
        params.append('data_inicial', dataInicial);
        params.append('data_final', dataFinal);
    }
    
    window.open(`/admin/relatorios/export?${params}`, '_blank');
}

function exportarDepartamentos() {
    window.open('/admin/relatorios/departamentos/export', '_blank');
}

function exportarUsuarios() {
    window.open('/admin/relatorios/usuarios/export', '_blank');
}

function gerarRelatorioPersonalizado() {
    const form = document.getElementById('customReportForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const dataInicio = formData.get('data_inicio');
    const dataFim = formData.get('data_fim');
    const formato = formData.get('formato');
    
    if (!dataInicio || !dataFim || !formato) {
        alert('Por favor, preencha todos os campos obrigatórios.');
        return;
    }
    
    // Create form for submission
    const submitForm = document.createElement('form');
    submitForm.method = 'POST';
    submitForm.action = '/admin/relatorios/personalizado';
    submitForm.target = '_blank';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
    submitForm.appendChild(csrfInput);
    
    // Add form data
    for (const [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        submitForm.appendChild(input);
    }
    
    document.body.appendChild(submitForm);
    submitForm.submit();
    document.body.removeChild(submitForm);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('relatorioPersonalizadoModal'));
    modal.hide();
}

// Auto-refresh data every 5 minutes
setInterval(() => {
    if (document.querySelector('input[name="periodo"]:checked').value !== 'custom') {
        atualizarRelatorios();
    }
}, 300000);
</script>

<style>
.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.progress {
    background-color: #e9ecef;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 0.25rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>