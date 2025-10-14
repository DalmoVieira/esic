<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-activity me-2"></i>
            Monitoramento em Tempo Real
        </h1>
        <p class="text-muted mb-0">Acompanhamento ao vivo das atividades do sistema E-SIC</p>
    </div>
    <div class="d-flex gap-2">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
            <label class="form-check-label" for="autoRefresh">
                Auto-atualização
            </label>
        </div>
        <button class="btn btn-outline-primary" onclick="atualizarDados()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Atualizar
        </button>
        <button class="btn btn-outline-info" onclick="exportarRelatorio()">
            <i class="bi bi-download me-1"></i>
            Exportar
        </button>
    </div>
</div>

<!-- Status Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-activity text-success fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Sistema</p>
                            <h5 class="mb-0 fw-bold text-success" id="statusSistema">Online</h5>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-success-subtle text-success">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-people text-info fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Usuários Online</p>
                            <h5 class="mb-0 fw-bold" id="usuariosOnline">42</h5>
                        </div>
                        <div class="text-end">
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 12%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-file-text text-warning fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Pedidos Hoje</p>
                            <h5 class="mb-0 fw-bold" id="pedidosHoje">18</h5>
                        </div>
                        <div class="text-end">
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 8%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-0 small">Vencendo Hoje</p>
                            <h5 class="mb-0 fw-bold" id="vencendoHoje">3</h5>
                        </div>
                        <div class="text-end">
                            <small class="text-danger">
                                <i class="bi bi-exclamation-circle"></i>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-time Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up text-primary me-2"></i>
                    Atividade em Tempo Real
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" onclick="alterarPeriodo('1h')">1h</button>
                    <button class="btn btn-outline-primary" onclick="alterarPeriodo('24h')">24h</button>
                    <button class="btn btn-outline-primary" onclick="alterarPeriodo('7d')">7d</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="atividadeChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-pie-chart text-primary me-2"></i>
                    Status dos Pedidos
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Live Activity Feed and System Metrics -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul text-primary me-2"></i>
                    Feed de Atividades
                </h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="limparFeed()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div id="activityFeed" class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    <!-- Activities will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer2 text-primary me-2"></i>
                    Métricas do Sistema
                </h5>
            </div>
            <div class="card-body">
                <!-- CPU Usage -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">CPU</span>
                        <span class="small fw-bold" id="cpuValue">45%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" id="cpuBar" style="width: 45%"></div>
                    </div>
                </div>
                
                <!-- Memory Usage -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Memória</span>
                        <span class="small fw-bold" id="memoryValue">68%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" id="memoryBar" style="width: 68%"></div>
                    </div>
                </div>
                
                <!-- Disk Usage -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Disco</span>
                        <span class="small fw-bold" id="diskValue">32%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" id="diskBar" style="width: 32%"></div>
                    </div>
                </div>
                
                <!-- Network -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Rede</span>
                        <span class="small fw-bold" id="networkValue">12 MB/s</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" id="networkBar" style="width: 24%"></div>
                    </div>
                </div>
                
                <!-- Database Connections -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Conexões DB</span>
                        <span class="small fw-bold" id="dbValue">8/50</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-secondary" id="dbBar" style="width: 16%"></div>
                    </div>
                </div>
                
                <!-- Response Time -->
                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Tempo Resposta</span>
                        <span class="small fw-bold text-success" id="responseValue">142ms</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Active Sessions and Alerts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-person-check text-primary me-2"></i>
                    Sessões Ativas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>IP</th>
                                <th>Localização</th>
                                <th>Última Atividade</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="activeSessions">
                            <!-- Sessions will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-bell text-primary me-2"></i>
                    Alertas do Sistema
                </h5>
            </div>
            <div class="card-body p-0">
                <div id="systemAlerts" class="list-group list-group-flush">
                    <!-- Alerts will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Charts -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    <i class="bi bi-speedometer text-primary me-2"></i>
                    Performance Detalhada
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <canvas id="performanceChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6 mb-3">
                        <canvas id="errorsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global variables
let refreshInterval;
let charts = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    loadInitialData();
    startAutoRefresh();
});

// Initialize all charts
function initializeCharts() {
    // Activity Chart
    const atividadeCtx = document.getElementById('atividadeChart').getContext('2d');
    charts.atividade = new Chart(atividadeCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Pedidos',
                data: [],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }, {
                label: 'Recursos',
                data: [],
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    charts.status = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Atendidos', 'Em Análise', 'Pendentes', 'Negados'],
            datasets: [{
                data: [65, 20, 10, 5],
                backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    charts.performance = new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Tempo de Resposta (ms)',
                data: [],
                borderColor: '#20c997',
                backgroundColor: 'rgba(32, 201, 151, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Errors Chart
    const errorsCtx = document.getElementById('errorsChart').getContext('2d');
    charts.errors = new Chart(errorsCtx, {
        type: 'bar',
        data: {
            labels: ['4xx', '5xx', 'Timeout', 'DB Error'],
            datasets: [{
                label: 'Erros por Tipo',
                data: [12, 3, 1, 2],
                backgroundColor: ['#ffc107', '#dc3545', '#fd7e14', '#6f42c1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Load initial data
function loadInitialData() {
    atualizarDados();
    carregarAtividades();
    carregarSessoesAtivas();
    carregarAlertas();
}

// Auto refresh functionality
function startAutoRefresh() {
    const autoRefreshCheckbox = document.getElementById('autoRefresh');
    
    function updateRefreshState() {
        if (autoRefreshCheckbox.checked) {
            refreshInterval = setInterval(atualizarDados, 5000); // 5 seconds
        } else {
            clearInterval(refreshInterval);
        }
    }
    
    autoRefreshCheckbox.addEventListener('change', updateRefreshState);
    updateRefreshState();
}

// Update all data
function atualizarDados() {
    // Simulate data updates
    updateMetrics();
    updateCharts();
}

// Update metrics
function updateMetrics() {
    // Simulate real-time metrics
    const metrics = {
        cpu: Math.floor(Math.random() * 40) + 30,
        memory: Math.floor(Math.random() * 30) + 50,
        disk: Math.floor(Math.random() * 20) + 25,
        network: (Math.random() * 20 + 5).toFixed(1),
        db: Math.floor(Math.random() * 15) + 5,
        response: Math.floor(Math.random() * 100) + 100
    };
    
    // Update displays
    document.getElementById('cpuValue').textContent = metrics.cpu + '%';
    document.getElementById('cpuBar').style.width = metrics.cpu + '%';
    
    document.getElementById('memoryValue').textContent = metrics.memory + '%';
    document.getElementById('memoryBar').style.width = metrics.memory + '%';
    
    document.getElementById('diskValue').textContent = metrics.disk + '%';
    document.getElementById('diskBar').style.width = metrics.disk + '%';
    
    document.getElementById('networkValue').textContent = metrics.network + ' MB/s';
    document.getElementById('networkBar').style.width = (metrics.network * 2) + '%';
    
    document.getElementById('dbValue').textContent = metrics.db + '/50';
    document.getElementById('dbBar').style.width = (metrics.db * 2) + '%';
    
    document.getElementById('responseValue').textContent = metrics.response + 'ms';
    
    // Update counters
    document.getElementById('usuariosOnline').textContent = Math.floor(Math.random() * 20) + 30;
    document.getElementById('pedidosHoje').textContent = Math.floor(Math.random() * 10) + 15;
    document.getElementById('vencendoHoje').textContent = Math.floor(Math.random() * 5) + 1;
}

// Update charts
function updateCharts() {
    const now = new Date();
    const timeLabel = now.toLocaleTimeString();
    
    // Update activity chart
    if (charts.atividade.data.labels.length >= 20) {
        charts.atividade.data.labels.shift();
        charts.atividade.data.datasets[0].data.shift();
        charts.atividade.data.datasets[1].data.shift();
    }
    
    charts.atividade.data.labels.push(timeLabel);
    charts.atividade.data.datasets[0].data.push(Math.floor(Math.random() * 10) + 5);
    charts.atividade.data.datasets[1].data.push(Math.floor(Math.random() * 3) + 1);
    charts.atividade.update('none');
    
    // Update performance chart
    if (charts.performance.data.labels.length >= 20) {
        charts.performance.data.labels.shift();
        charts.performance.data.datasets[0].data.shift();
    }
    
    charts.performance.data.labels.push(timeLabel);
    charts.performance.data.datasets[0].data.push(Math.floor(Math.random() * 100) + 100);
    charts.performance.update('none');
}

// Load activities
function carregarAtividades() {
    const activities = [
        { type: 'novo_pedido', user: 'João Silva', time: '2 min', icon: 'file-plus', color: 'success' },
        { type: 'recurso', user: 'Maria Santos', time: '5 min', icon: 'arrow-up-right', color: 'warning' },
        { type: 'resposta', user: 'Admin', time: '8 min', icon: 'reply', color: 'info' },
        { type: 'login', user: 'Pedro Costa', time: '12 min', icon: 'person-check', color: 'primary' },
        { type: 'logout', user: 'Ana Oliveira', time: '15 min', icon: 'person-x', color: 'secondary' }
    ];
    
    const feed = document.getElementById('activityFeed');
    feed.innerHTML = activities.map(activity => `
        <div class="list-group-item list-group-item-action border-0">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-${activity.color} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 32px; height: 32px;">
                        <i class="bi bi-${activity.icon} text-${activity.color}"></i>
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 small">${activity.user}</h6>
                            <p class="mb-0 small text-muted">${getActivityDescription(activity.type)}</p>
                        </div>
                        <small class="text-muted">${activity.time}</small>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Get activity description
function getActivityDescription(type) {
    const descriptions = {
        novo_pedido: 'Criou um novo pedido',
        recurso: 'Interpôs um recurso',
        resposta: 'Respondeu um pedido',
        login: 'Fez login no sistema',
        logout: 'Saiu do sistema'
    };
    return descriptions[type] || 'Atividade desconhecida';
}

// Load active sessions
function carregarSessoesAtivas() {
    const sessions = [
        { user: 'Admin', type: 'Administrador', ip: '192.168.1.100', location: 'São Paulo, SP', lastActivity: '1 min', status: 'active' },
        { user: 'João Silva', type: 'Cidadão', ip: '201.45.123.45', location: 'Rio de Janeiro, RJ', lastActivity: '3 min', status: 'active' },
        { user: 'Maria Santos', type: 'Operador', ip: '192.168.1.105', location: 'Brasília, DF', lastActivity: '8 min', status: 'idle' },
        { user: 'Pedro Costa', type: 'Cidadão', ip: '179.123.45.67', location: 'Belo Horizonte, MG', lastActivity: '12 min', status: 'active' }
    ];
    
    const tbody = document.getElementById('activeSessions');
    tbody.innerHTML = sessions.map(session => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-xs bg-${session.status === 'active' ? 'success' : 'warning'} bg-opacity-10 rounded-circle me-2">
                        <i class="bi bi-person text-${session.status === 'active' ? 'success' : 'warning'}"></i>
                    </div>
                    ${session.user}
                </div>
            </td>
            <td><span class="badge bg-light text-dark">${session.type}</span></td>
            <td><code class="small">${session.ip}</code></td>
            <td class="small">${session.location}</td>
            <td class="small">${session.lastActivity}</td>
            <td>
                <span class="badge bg-${session.status === 'active' ? 'success' : 'warning'}-subtle text-${session.status === 'active' ? 'success' : 'warning'}">
                    ${session.status === 'active' ? 'Ativo' : 'Inativo'}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-danger" onclick="encerrarSessao('${session.user}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Load system alerts
function carregarAlertas() {
    const alerts = [
        { type: 'warning', message: '3 pedidos vencem hoje', time: '5 min' },
        { type: 'info', message: 'Backup concluído com sucesso', time: '1 hora' },
        { type: 'success', message: 'Sistema atualizado', time: '2 horas' }
    ];
    
    const alertsContainer = document.getElementById('systemAlerts');
    alertsContainer.innerHTML = alerts.map(alert => `
        <div class="list-group-item border-0">
            <div class="d-flex align-items-start">
                <i class="bi bi-${alert.type === 'warning' ? 'exclamation-triangle' : alert.type === 'info' ? 'info-circle' : 'check-circle'} 
                   text-${alert.type === 'warning' ? 'warning' : alert.type === 'info' ? 'info' : 'success'} me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <p class="mb-1 small">${alert.message}</p>
                    <small class="text-muted">${alert.time} atrás</small>
                </div>
            </div>
        </div>
    `).join('');
}

// Utility functions
function alterarPeriodo(periodo) {
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    // Implement period change logic here
}

function limparFeed() {
    document.getElementById('activityFeed').innerHTML = '<div class="text-center py-3 text-muted">Feed limpo</div>';
}

function encerrarSessao(user) {
    if (confirm(`Deseja realmente encerrar a sessão de ${user}?`)) {
        // Implement session termination logic
        showNotification(`Sessão de ${user} encerrada`, 'success');
        carregarSessoesAtivas();
    }
}

function exportarRelatorio() {
    showNotification('Relatório de monitoramento será exportado em breve...', 'info');
}

// Notification system
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<style>
.avatar-xs {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.progress {
    background-color: #e9ecef;
}

.card-body canvas {
    max-height: 300px;
}

@media (max-width: 768px) {
    .btn-group-sm .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
}

/* Real-time pulse animation for active status */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.badge .bi-circle-fill {
    animation: pulse 2s infinite;
}
</style>