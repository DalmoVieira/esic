<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-arrow-repeat me-2"></i>
            Gerenciar Recursos
        </h1>
        <p class="text-muted mb-0">Administração de recursos contra decisões</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="exportarRecursos()">
            <i class="bi bi-download me-1"></i>
            Exportar
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filtrosModal">
            <i class="bi bi-funnel me-1"></i>
            Filtros Avançados
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-warning"><?= number_format($stats['pendentes'] ?? 0) ?></h4>
                <small class="text-muted">Pendentes de Análise</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-info mb-2">
                    <i class="bi bi-search" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-info"><?= number_format($stats['em_analise'] ?? 0) ?></h4>
                <small class="text-muted">Em Análise</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-success"><?= number_format($stats['deferidos'] ?? 0) ?></h4>
                <small class="text-muted">Deferidos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-danger mb-2">
                    <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-danger"><?= number_format($stats['indeferidos'] ?? 0) ?></h4>
                <small class="text-muted">Indeferidos</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="statusFilter" id="todos" value="" checked>
                    <label class="btn btn-outline-primary" for="todos">
                        Todos (<?= number_format($stats['total'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="pendente" value="pendente">
                    <label class="btn btn-outline-warning" for="pendente">
                        Pendentes (<?= number_format($stats['pendentes'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="em_analise" value="em_analise">
                    <label class="btn btn-outline-info" for="em_analise">
                        Em Análise (<?= number_format($stats['em_analise'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="vencendo" value="vencendo">
                    <label class="btn btn-outline-danger" for="vencendo">
                        Prazo Vencendo (<?= number_format($stats['vencendo'] ?? 0) ?>)
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="Buscar por protocolo do pedido original ou requerente..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="buscarRecursos()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recursos Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Recursos</h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">
                    Mostrando <?= count($recursos) ?> de <?= number_format($total_recursos) ?> recursos
                </small>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                            data-bs-toggle="dropdown">
                        <?= $_GET['per_page'] ?? 25 ?> por página
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?per_page=10">10 por página</a></li>
                        <li><a class="dropdown-item" href="?per_page=25">25 por página</a></li>
                        <li><a class="dropdown-item" href="?per_page=50">50 por página</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th class="border-0">Protocolo Original</th>
                        <th class="border-0">Requerente</th>
                        <th class="border-0">Tipo de Recurso</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Prazo</th>
                        <th class="border-0">Data Recurso</th>
                        <th class="border-0">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recursos)): ?>
                        <?php foreach ($recursos as $recurso): ?>
                        <tr data-id="<?= $recurso['id'] ?>">
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" 
                                       value="<?= $recurso['id'] ?>">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="font-monospace fw-bold">
                                        <?= htmlspecialchars($recurso['protocolo_pedido']) ?>
                                    </span>
                                    <?php if ($recurso['urgente']): ?>
                                    <span class="badge bg-danger ms-2" title="Urgente">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted d-block">
                                    <?= htmlspecialchars(substr($recurso['assunto_pedido'], 0, 50)) ?><?= strlen($recurso['assunto_pedido']) > 50 ? '...' : '' ?>
                                </small>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($recurso['nome_requerente']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($recurso['email_requerente']) ?></small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-<?= getTipoRecursoBadgeClass($recurso['tipo_recurso']) ?>">
                                        <?= getTipoRecursoLabel($recurso['tipo_recurso']) ?>
                                    </span>
                                    <?php if ($recurso['instancia']): ?>
                                    <br><small class="text-muted"><?= $recurso['instancia'] ?>ª Instância</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= getRecursoStatusBadgeClass($recurso['status']) ?>">
                                    <?= getRecursoStatusLabel($recurso['status']) ?>
                                </span>
                                <?php if ($recurso['status'] === 'em_analise' && $recurso['responsavel_analise']): ?>
                                <br><small class="text-muted">Por: <?= htmlspecialchars($recurso['responsavel_analise']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $prazo = strtotime($recurso['prazo_analise']);
                                $hoje = time();
                                $diff = floor(($prazo - $hoje) / (60 * 60 * 24));
                                $prazoClass = $diff < 0 ? 'text-danger' : ($diff <= 1 ? 'text-warning' : 'text-muted');
                                ?>
                                <div class="<?= $prazoClass ?>">
                                    <?= date('d/m/Y', $prazo) ?>
                                </div>
                                <small class="<?= $prazoClass ?>">
                                    <?php if ($diff < 0): ?>
                                        <?= abs($diff) ?> dias em atraso
                                    <?php elseif ($diff == 0): ?>
                                        Vence hoje
                                    <?php else: ?>
                                        <?= $diff ?> dias restantes
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($recurso['data_recurso'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($recurso['data_recurso'])) ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('/admin/recursos/ver/' . $recurso['id']) ?>" 
                                       class="btn btn-outline-primary" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if (in_array($recurso['status'], ['pendente', 'em_analise'])): ?>
                                    <a href="<?= url('/admin/recursos/analisar/' . $recurso['id']) ?>" 
                                       class="btn btn-outline-success" title="Analisar">
                                        <i class="bi bi-gavel"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($recurso['status'] === 'pendente'): ?>
                                    <button class="btn btn-outline-info" 
                                            onclick="assumirRecurso(<?= $recurso['id'] ?>)" 
                                            title="Assumir análise">
                                        <i class="bi bi-person-check"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="Mais ações">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="<?= url('/admin/pedidos/ver/' . $recurso['pedido_id']) ?>">
                                                    <i class="bi bi-file-earmark-text me-2"></i>Ver Pedido Original
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="<?= url('/admin/recursos/historico/' . $recurso['id']) ?>">
                                                    <i class="bi bi-clock-history me-2"></i>Histórico
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-warning" 
                                                        onclick="marcarUrgente(<?= $recurso['id'] ?>)">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Marcar Urgente
                                                </button>
                                            </li>
                                            <?php if ($recurso['status'] === 'em_analise'): ?>
                                            <li>
                                                <button class="dropdown-item text-info" 
                                                        onclick="transferirRecurso(<?= $recurso['id'] ?>)">
                                                    <i class="bi bi-arrow-right me-2"></i>Transferir
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($recurso['instancia'] < 3): ?>
                                            <li>
                                                <button class="dropdown-item text-primary" 
                                                        onclick="encaminharInstancia(<?= $recurso['id'] ?>)">
                                                    <i class="bi bi-arrow-up me-2"></i>Próxima Instância
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('admin')): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="excluirRecurso(<?= $recurso['id'] ?>)">
                                                    <i class="bi bi-trash me-2"></i>Excluir
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="bi bi-arrow-repeat" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Nenhum recurso encontrado</h5>
                            <p class="mb-0">Não há recursos com os filtros aplicados.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white border-0">
        <nav aria-label="Paginação dos recursos">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?><?= $query_params ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php
                $start = max(1, $current_page - 2);
                $end = min($total_pages, $current_page + 2);
                ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $query_params ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?><?= $query_params ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Bulk Actions Bar -->
<div class="card border-0 shadow-sm mt-3" id="bulkActionsBar" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong id="selectedCount">0</strong> recursos selecionados
            </div>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-info" onclick="bulkAction('assumir')">
                    <i class="bi bi-person-check me-1"></i>Assumir
                </button>
                <button class="btn btn-outline-success" onclick="bulkAction('deferir')">
                    <i class="bi bi-check-circle me-1"></i>Deferir
                </button>
                <button class="btn btn-outline-danger" onclick="bulkAction('indeferir')">
                    <i class="bi bi-x-circle me-1"></i>Indeferir
                </button>
                <button class="btn btn-outline-secondary" onclick="bulkAction('exportar')">
                    <i class="bi bi-download me-1"></i>Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtros Avançados Modal -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-funnel me-2"></i>
                    Filtros Avançados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filtrosForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" multiple>
                                <option value="pendente">Pendente</option>
                                <option value="em_analise">Em Análise</option>
                                <option value="deferido">Deferido</option>
                                <option value="indeferido">Indeferido</option>
                                <option value="parcialmente_deferido">Parcialmente Deferido</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Recurso</label>
                            <select class="form-select" name="tipo_recurso">
                                <option value="">Todos os tipos</option>
                                <option value="negacao_total">Negação Total</option>
                                <option value="atendimento_parcial">Atendimento Parcial</option>
                                <option value="descumprimento_prazo">Descumprimento Prazo</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" name="data_inicial">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Final</label>
                            <input type="date" class="form-control" name="data_final">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instância</label>
                            <select class="form-select" name="instancia">
                                <option value="">Todas as instâncias</option>
                                <option value="1">1ª Instância</option>
                                <option value="2">2ª Instância</option>
                                <option value="3">3ª Instância</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Responsável</label>
                            <select class="form-select" name="responsavel">
                                <option value="">Todos os responsáveis</option>
                                <?php if (!empty($responsaveis)): ?>
                                    <?php foreach ($responsaveis as $resp): ?>
                                    <option value="<?= $resp['id'] ?>"><?= htmlspecialchars($resp['nome']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="apenas_urgentes" id="apenasUrgentes">
                            <label class="form-check-label" for="apenasUrgentes">
                                Apenas recursos urgentes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="prazo_vencendo" id="prazoVencendo">
                            <label class="form-check-label" for="prazoVencendo">
                                Prazo vencendo em 1 dia
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sem_responsavel" id="semResponsavel">
                            <label class="form-check-label" for="semResponsavel">
                                Sem responsável definido
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="limparFiltros()">
                    Limpar Filtros
                </button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">
                    Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Checkbox management
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActionsBar();
});

document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionsBar);
});

function updateBulkActionsBar() {
    const selected = document.querySelectorAll('.row-checkbox:checked');
    const bulkBar = document.getElementById('bulkActionsBar');
    const countEl = document.getElementById('selectedCount');
    
    if (selected.length > 0) {
        bulkBar.style.display = 'block';
        countEl.textContent = selected.length;
    } else {
        bulkBar.style.display = 'none';
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        buscarRecursos();
    }
});

function buscarRecursos() {
    const search = document.getElementById('searchInput').value;
    const urlParams = new URLSearchParams(window.location.search);
    
    if (search) {
        urlParams.set('search', search);
    } else {
        urlParams.delete('search');
    }
    
    window.location.search = urlParams.toString();
}

// Status filter
document.querySelectorAll('input[name="statusFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.checked) {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (this.value) {
                urlParams.set('status', this.value);
            } else {
                urlParams.delete('status');
            }
            
            window.location.search = urlParams.toString();
        }
    });
});

// Individual actions
function assumirRecurso(id) {
    if (confirm('Deseja assumir a análise deste recurso?')) {
        fetch(`/admin/recursos/${id}/assumir`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao assumir recurso: ' + data.message);
            }
        });
    }
}

function marcarUrgente(id) {
    if (confirm('Deseja marcar este recurso como urgente?')) {
        fetch(`/admin/recursos/${id}/urgente`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao marcar como urgente: ' + data.message);
            }
        });
    }
}

function transferirRecurso(id) {
    const novoResponsavel = prompt('Digite o ID do novo responsável:');
    if (novoResponsavel) {
        fetch(`/admin/recursos/${id}/transferir`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({novo_responsavel: novoResponsavel})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao transferir recurso: ' + data.message);
            }
        });
    }
}

function encaminharInstancia(id) {
    if (confirm('Deseja encaminhar este recurso para a próxima instância?')) {
        fetch(`/admin/recursos/${id}/proxima-instancia`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao encaminhar recurso: ' + data.message);
            }
        });
    }
}

function excluirRecurso(id) {
    if (confirm('Tem certeza que deseja excluir este recurso? Esta ação não pode ser desfeita.')) {
        fetch(`/admin/recursos/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir recurso: ' + data.message);
            }
        });
    }
}

// Bulk actions
function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                         .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Selecione ao menos um recurso.');
        return;
    }
    
    let confirmMessage;
    switch(action) {
        case 'assumir':
            confirmMessage = `Deseja assumir ${selected.length} recursos?`;
            break;
        case 'deferir':
            confirmMessage = `Deseja deferir ${selected.length} recursos?`;
            break;
        case 'indeferir':
            confirmMessage = `Deseja indeferir ${selected.length} recursos?`;
            break;
        case 'exportar':
            exportarSelecionados(selected);
            return;
    }
    
    if (confirm(confirmMessage)) {
        fetch(`/admin/recursos/bulk/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ids: selected})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro na operação: ' + data.message);
            }
        });
    }
}

// Export functions
function exportarRecursos() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('export', 'excel');
    window.open('/admin/recursos?' + urlParams.toString(), '_blank');
}

function exportarSelecionados(ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/recursos/export-selected';
    form.target = '_blank';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrfInput);
    
    const idsInput = document.createElement('input');
    idsInput.type = 'hidden';
    idsInput.name = 'ids';
    idsInput.value = JSON.stringify(ids);
    form.appendChild(idsInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Advanced filters
function aplicarFiltros() {
    const form = document.getElementById('filtrosForm');
    const formData = new FormData(form);
    const urlParams = new URLSearchParams(window.location.search);
    
    // Clear existing filters
    ['status', 'tipo_recurso', 'data_inicial', 'data_final', 'instancia', 
     'responsavel', 'apenas_urgentes', 'prazo_vencendo', 'sem_responsavel'].forEach(param => {
        urlParams.delete(param);
    });
    
    // Add new filters
    for (const [key, value] of formData.entries()) {
        if (value) {
            urlParams.set(key, value);
        }
    }
    
    window.location.search = urlParams.toString();
}

function limparFiltros() {
    const form = document.getElementById('filtrosForm');
    form.reset();
    
    // Keep only search and pagination params
    const urlParams = new URLSearchParams(window.location.search);
    const keepParams = ['search', 'page', 'per_page'];
    const newParams = new URLSearchParams();
    
    keepParams.forEach(param => {
        if (urlParams.has(param)) {
            newParams.set(param, urlParams.get(param));
        }
    });
    
    window.location.search = newParams.toString();
}

// Helper functions
<?php
function getTipoRecursoLabel($tipo) {
    $labels = [
        'negacao_total' => 'Negação Total',
        'atendimento_parcial' => 'Atendimento Parcial',
        'descumprimento_prazo' => 'Descumprimento Prazo',
        'outros' => 'Outros'
    ];
    return $labels[$tipo] ?? 'Não Informado';
}

function getTipoRecursoBadgeClass($tipo) {
    $classes = [
        'negacao_total' => 'danger',
        'atendimento_parcial' => 'warning',
        'descumprimento_prazo' => 'info',
        'outros' => 'secondary'
    ];
    return $classes[$tipo] ?? 'secondary';
}

function getRecursoStatusLabel($status) {
    $labels = [
        'pendente' => 'Pendente',
        'em_analise' => 'Em Análise',
        'deferido' => 'Deferido',
        'indeferido' => 'Indeferido',
        'parcialmente_deferido' => 'Parcialmente Deferido'
    ];
    return $labels[$status] ?? 'Desconhecido';
}

function getRecursoStatusBadgeClass($status) {
    $classes = [
        'pendente' => 'warning',
        'em_analise' => 'info',
        'deferido' => 'success',
        'indeferido' => 'danger',
        'parcialmente_deferido' => 'secondary'
    ];
    return $classes[$status] ?? 'secondary';
}

function hasPermission($permission) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $permission;
}
?>
</script>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-check:checked + .btn-outline-primary,
.btn-check:checked + .btn-outline-warning,
.btn-check:checked + .btn-outline-info,
.btn-check:checked + .btn-outline-danger {
    color: white;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000 !important;
}

.btn-check:checked + .btn-outline-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000 !important;
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 768px) {
    .btn-group {
        flex-wrap: wrap;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>