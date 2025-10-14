<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-file-earmark-text me-2"></i>
            Gerenciar Pedidos
        </h1>
        <p class="text-muted mb-0">Administração de pedidos de acesso à informação</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="exportarPedidos()">
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
                <small class="text-muted">Pendentes</small>
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
                <h4 class="fw-bold text-success"><?= number_format($stats['respondidos'] ?? 0) ?></h4>
                <small class="text-muted">Respondidos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-danger mb-2">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-danger"><?= number_format($stats['vencendo'] ?? 0) ?></h4>
                <small class="text-muted">Prazo Vencendo</small>
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
                        Vencendo (<?= number_format($stats['vencendo'] ?? 0) ?>)
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="Buscar por protocolo, nome ou assunto..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="buscarPedidos()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pedidos Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Pedidos</h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">
                    Mostrando <?= count($pedidos) ?> de <?= number_format($total_pedidos) ?> pedidos
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
                        <li><a class="dropdown-item" href="?per_page=100">100 por página</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="pedidosTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th class="border-0">Protocolo</th>
                        <th class="border-0">Solicitante</th>
                        <th class="border-0">Assunto</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Prazo</th>
                        <th class="border-0">Data</th>
                        <th class="border-0">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pedidos)): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                        <tr data-id="<?= $pedido['id'] ?>">
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" 
                                       value="<?= $pedido['id'] ?>">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="font-monospace fw-bold">
                                        <?= htmlspecialchars($pedido['protocolo']) ?>
                                    </span>
                                    <?php if ($pedido['urgente']): ?>
                                    <span class="badge bg-danger ms-2" title="Urgente">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($pedido['nome_solicitante']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($pedido['email']) ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" 
                                     title="<?= htmlspecialchars($pedido['assunto']) ?>">
                                    <?= htmlspecialchars($pedido['assunto']) ?>
                                </div>
                                <?php if ($pedido['categoria']): ?>
                                <small class="badge bg-light text-dark">
                                    <?= htmlspecialchars($pedido['categoria']) ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($pedido['status']) ?>">
                                    <?= getStatusLabel($pedido['status']) ?>
                                </span>
                                <?php if ($pedido['status'] === 'em_analise' && $pedido['responsavel']): ?>
                                <br><small class="text-muted">Por: <?= htmlspecialchars($pedido['responsavel']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $prazo = strtotime($pedido['prazo_resposta']);
                                $hoje = time();
                                $diff = floor(($prazo - $hoje) / (60 * 60 * 24));
                                $prazoClass = $diff < 0 ? 'text-danger' : ($diff <= 2 ? 'text-warning' : 'text-muted');
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
                                <div><?= date('d/m/Y', strtotime($pedido['data_pedido'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($pedido['data_pedido'])) ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('/admin/pedidos/ver/' . $pedido['id']) ?>" 
                                       class="btn btn-outline-primary" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if (in_array($pedido['status'], ['pendente', 'em_analise'])): ?>
                                    <a href="<?= url('/admin/pedidos/responder/' . $pedido['id']) ?>" 
                                       class="btn btn-outline-success" title="Responder">
                                        <i class="bi bi-reply"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($pedido['status'] === 'pendente'): ?>
                                    <button class="btn btn-outline-info" 
                                            onclick="assumirPedido(<?= $pedido['id'] ?>)" 
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
                                                   href="<?= url('/admin/pedidos/editar/' . $pedido['id']) ?>">
                                                    <i class="bi bi-pencil me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="<?= url('/admin/pedidos/historico/' . $pedido['id']) ?>">
                                                    <i class="bi bi-clock-history me-2"></i>Histórico
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-warning" 
                                                        onclick="marcarUrgente(<?= $pedido['id'] ?>)">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>Marcar Urgente
                                                </button>
                                            </li>
                                            <?php if (hasPermission('admin')): ?>
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="excluirPedido(<?= $pedido['id'] ?>)">
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
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Nenhum pedido encontrado</h5>
                            <p class="mb-0">Não há pedidos com os filtros aplicados.</p>
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
        <nav aria-label="Paginação dos pedidos">
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
                
                <?php if ($start > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1<?= $query_params ?>">1</a>
                </li>
                <?php if ($start > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= $query_params ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $total_pages ?><?= $query_params ?>"><?= $total_pages ?></a>
                </li>
                <?php endif; ?>
                
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
                <strong id="selectedCount">0</strong> pedidos selecionados
            </div>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-info" onclick="bulkAction('assumir')">
                    <i class="bi bi-person-check me-1"></i>Assumir
                </button>
                <button class="btn btn-outline-success" onclick="bulkAction('aprovar')">
                    <i class="bi bi-check-circle me-1"></i>Aprovar
                </button>
                <button class="btn btn-outline-warning" onclick="bulkAction('urgente')">
                    <i class="bi bi-exclamation-triangle me-1"></i>Marcar Urgente
                </button>
                <button class="btn btn-outline-secondary" onclick="bulkAction('exportar')">
                    <i class="bi bi-download me-1"></i>Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
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
                                <option value="respondido">Respondido</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="negado">Negado</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Categoria</label>
                            <select class="form-select" name="categoria">
                                <option value="">Todas as categorias</option>
                                <option value="administrativa">Administrativa</option>
                                <option value="financeira">Financeira</option>
                                <option value="pessoal">Pessoal</option>
                                <option value="contratos">Contratos</option>
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Forma de Resposta</label>
                            <select class="form-select" name="forma_resposta">
                                <option value="">Todas</option>
                                <option value="email">E-mail</option>
                                <option value="sistema">Sistema</option>
                                <option value="fisico">Retirada Física</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="apenas_urgentes" id="apenasUrgentes">
                            <label class="form-check-label" for="apenasUrgentes">
                                Apenas pedidos urgentes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="prazo_vencendo" id="prazoVencendo">
                            <label class="form-check-label" for="prazoVencendo">
                                Prazo vencendo em 2 dias
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
        buscarPedidos();
    }
});

function buscarPedidos() {
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
function assumirPedido(id) {
    if (confirm('Deseja assumir a análise deste pedido?')) {
        fetch(`/admin/pedidos/${id}/assumir`, {
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
                alert('Erro ao assumir pedido: ' + data.message);
            }
        });
    }
}

function marcarUrgente(id) {
    if (confirm('Deseja marcar este pedido como urgente?')) {
        fetch(`/admin/pedidos/${id}/urgente`, {
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

function excluirPedido(id) {
    if (confirm('Tem certeza que deseja excluir este pedido? Esta ação não pode ser desfeita.')) {
        fetch(`/admin/pedidos/${id}`, {
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
                alert('Erro ao excluir pedido: ' + data.message);
            }
        });
    }
}

// Bulk actions
function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                         .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Selecione ao menos um pedido.');
        return;
    }
    
    let confirmMessage;
    switch(action) {
        case 'assumir':
            confirmMessage = `Deseja assumir ${selected.length} pedidos?`;
            break;
        case 'aprovar':
            confirmMessage = `Deseja aprovar ${selected.length} pedidos?`;
            break;
        case 'urgente':
            confirmMessage = `Deseja marcar ${selected.length} pedidos como urgentes?`;
            break;
        case 'exportar':
            exportarSelecionados(selected);
            return;
    }
    
    if (confirm(confirmMessage)) {
        fetch(`/admin/pedidos/bulk/${action}`, {
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
function exportarPedidos() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('export', 'excel');
    window.open('/admin/pedidos?' + urlParams.toString(), '_blank');
}

function exportarSelecionados(ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/pedidos/export-selected';
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
    ['status', 'categoria', 'data_inicial', 'data_final', 'responsavel', 
     'forma_resposta', 'apenas_urgentes', 'prazo_vencendo', 'sem_responsavel'].forEach(param => {
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

// Auto-refresh for real-time updates
setInterval(() => {
    // Check for new pedidos without full page reload
    fetch('/admin/api/pedidos-stats')
        .then(response => response.json())
        .then(data => {
            // Update counters if they changed
            const pendentesEl = document.querySelector('.text-warning + h4');
            if (pendentesEl && pendentesEl.textContent !== data.pendentes.toString()) {
                // Show notification of new pedidos
                showNotification('Novos pedidos recebidos!', 'info');
            }
        })
        .catch(error => console.error('Error checking for updates:', error));
}, 60000); // Check every minute

function showNotification(message, type = 'info') {
    // Simple toast notification
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

// Status helper functions (same as other admin pages)
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

function hasPermission($permission) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $permission;
}
?>
</script>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-check:checked + .btn-outline-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000;
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
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