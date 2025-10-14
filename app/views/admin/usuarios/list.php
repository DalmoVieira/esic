<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-people me-2"></i>
            Gerenciar Usuários
        </h1>
        <p class="text-muted mb-0">Administração de usuários do sistema</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/admin/usuarios/create') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Novo Usuário
        </a>
        <button class="btn btn-outline-secondary" onclick="exportarUsuarios()">
            <i class="bi bi-download me-1"></i>
            Exportar
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-primary"><?= number_format($stats['total'] ?? 0) ?></h4>
                <small class="text-muted">Total de Usuários</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-person-check" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-success"><?= number_format($stats['ativos'] ?? 0) ?></h4>
                <small class="text-muted">Usuários Ativos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-warning"><?= number_format($stats['administradores'] ?? 0) ?></h4>
                <small class="text-muted">Administradores</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="text-info mb-2">
                    <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                </div>
                <h4 class="fw-bold text-info"><?= number_format($stats['online'] ?? 0) ?></h4>
                <small class="text-muted">Online Agora</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="statusFilter" id="todos" value="" checked>
                    <label class="btn btn-outline-primary" for="todos">
                        Todos (<?= number_format($stats['total'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="ativos" value="ativo">
                    <label class="btn btn-outline-success" for="ativos">
                        Ativos (<?= number_format($stats['ativos'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="inativos" value="inativo">
                    <label class="btn btn-outline-danger" for="inativos">
                        Inativos (<?= number_format($stats['inativos'] ?? 0) ?>)
                    </label>
                    
                    <input type="radio" class="btn-check" name="statusFilter" id="admins" value="admin">
                    <label class="btn btn-outline-warning" for="admins">
                        Admins (<?= number_format($stats['administradores'] ?? 0) ?>)
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" 
                           placeholder="Buscar por nome, email ou CPF..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-outline-secondary" type="button" onclick="buscarUsuarios()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Usuários</h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">
                    Mostrando <?= count($usuarios) ?> de <?= number_format($total_usuarios) ?> usuários
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
                        <th class="border-0">Usuário</th>
                        <th class="border-0">Perfil</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Último Acesso</th>
                        <th class="border-0">Criado em</th>
                        <th class="border-0">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr data-id="<?= $usuario['id'] ?>">
                            <td>
                                <input type="checkbox" class="form-check-input row-checkbox" 
                                       value="<?= $usuario['id'] ?>">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        <?php if (!empty($usuario['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($usuario['avatar']) ?>" 
                                             alt="Avatar" class="rounded-circle" width="40" height="40">
                                        <?php else: ?>
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <span class="text-white fw-bold">
                                                <?= strtoupper(substr($usuario['nome'], 0, 1)) ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($usuario['nome']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($usuario['email']) ?></small>
                                        <?php if (!empty($usuario['cpf'])): ?>
                                        <br><small class="text-muted font-monospace">CPF: <?= formatCpf($usuario['cpf']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-<?= getRoleBadgeClass($usuario['role']) ?>">
                                        <?= getRoleLabel($usuario['role']) ?>
                                    </span>
                                    <?php if (!empty($usuario['setor'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($usuario['setor']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($usuario['status'] === 'ativo'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Ativo
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inativo
                                    </span>
                                    <?php endif; ?>
                                    
                                    <?php if (isUserOnline($usuario['ultimo_acesso'])): ?>
                                    <span class="badge bg-info ms-2">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5em;"></i>Online
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($usuario['ultimo_acesso']): ?>
                                <div><?= date('d/m/Y', strtotime($usuario['ultimo_acesso'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($usuario['ultimo_acesso'])) ?></small>
                                <?php else: ?>
                                <span class="text-muted">Nunca acessou</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($usuario['created_at'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($usuario['created_at'])) ?></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= url('/admin/usuarios/edit/' . $usuario['id']) ?>" 
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <a href="<?= url('/admin/usuarios/view/' . $usuario['id']) ?>" 
                                       class="btn btn-outline-info" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="Mais ações">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php if ($usuario['status'] === 'ativo'): ?>
                                            <li>
                                                <button class="dropdown-item" 
                                                        onclick="alterarStatus(<?= $usuario['id'] ?>, 'inativo')">
                                                    <i class="bi bi-person-x me-2"></i>Desativar
                                                </button>
                                            </li>
                                            <?php else: ?>
                                            <li>
                                                <button class="dropdown-item" 
                                                        onclick="alterarStatus(<?= $usuario['id'] ?>, 'ativo')">
                                                    <i class="bi bi-person-check me-2"></i>Ativar
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <li>
                                                <button class="dropdown-item" 
                                                        onclick="resetarSenha(<?= $usuario['id'] ?>)">
                                                    <i class="bi bi-key me-2"></i>Resetar Senha
                                                </button>
                                            </li>
                                            
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="<?= url('/admin/usuarios/historico/' . $usuario['id']) ?>">
                                                    <i class="bi bi-clock-history me-2"></i>Histórico
                                                </a>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <?php if ($usuario['role'] !== 'admin'): ?>
                                            <li>
                                                <button class="dropdown-item text-warning" 
                                                        onclick="promoverAdmin(<?= $usuario['id'] ?>)">
                                                    <i class="bi bi-shield-plus me-2"></i>Promover a Admin
                                                </button>
                                            </li>
                                            <?php else: ?>
                                            <li>
                                                <button class="dropdown-item text-warning" 
                                                        onclick="rebaixarUsuario(<?= $usuario['id'] ?>)">
                                                    <i class="bi bi-shield-minus me-2"></i>Remover Admin
                                                </button>
                                            </li>
                                            <?php endif; ?>
                                            
                                            <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                            <li>
                                                <button class="dropdown-item text-danger" 
                                                        onclick="excluirUsuario(<?= $usuario['id'] ?>)">
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="bi bi-people" style="font-size: 3rem;"></i>
                            </div>
                            <h5>Nenhum usuário encontrado</h5>
                            <p class="mb-0">Não há usuários com os filtros aplicados.</p>
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
        <nav aria-label="Paginação dos usuários">
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
                <strong id="selectedCount">0</strong> usuários selecionados
            </div>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-success" onclick="bulkAction('ativar')">
                    <i class="bi bi-person-check me-1"></i>Ativar
                </button>
                <button class="btn btn-outline-danger" onclick="bulkAction('desativar')">
                    <i class="bi bi-person-x me-1"></i>Desativar
                </button>
                <button class="btn btn-outline-warning" onclick="bulkAction('resetar')">
                    <i class="bi bi-key me-1"></i>Resetar Senhas
                </button>
                <button class="btn btn-outline-secondary" onclick="bulkAction('exportar')">
                    <i class="bi bi-download me-1"></i>Exportar
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
        buscarUsuarios();
    }
});

function buscarUsuarios() {
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
function alterarStatus(id, novoStatus) {
    const acao = novoStatus === 'ativo' ? 'ativar' : 'desativar';
    
    if (confirm(`Deseja ${acao} este usuário?`)) {
        fetch(`/admin/usuarios/${id}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({status: novoStatus})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao alterar status: ' + data.message);
            }
        });
    }
}

function resetarSenha(id) {
    if (confirm('Deseja resetar a senha deste usuário? Uma nova senha será enviada por e-mail.')) {
        fetch(`/admin/usuarios/${id}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Senha resetada com sucesso! Nova senha enviada por e-mail.');
            } else {
                alert('Erro ao resetar senha: ' + data.message);
            }
        });
    }
}

function promoverAdmin(id) {
    if (confirm('Deseja promover este usuário a administrador?')) {
        fetch(`/admin/usuarios/${id}/promote`, {
            method: 'PUT',
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
                alert('Erro ao promover usuário: ' + data.message);
            }
        });
    }
}

function rebaixarUsuario(id) {
    if (confirm('Deseja remover privilégios de administrador deste usuário?')) {
        fetch(`/admin/usuarios/${id}/demote`, {
            method: 'PUT',
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
                alert('Erro ao rebaixar usuário: ' + data.message);
            }
        });
    }
}

function excluirUsuario(id) {
    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
        fetch(`/admin/usuarios/${id}`, {
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
                alert('Erro ao excluir usuário: ' + data.message);
            }
        });
    }
}

// Bulk actions
function bulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                         .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Selecione ao menos um usuário.');
        return;
    }
    
    let confirmMessage;
    switch(action) {
        case 'ativar':
            confirmMessage = `Deseja ativar ${selected.length} usuários?`;
            break;
        case 'desativar':
            confirmMessage = `Deseja desativar ${selected.length} usuários?`;
            break;
        case 'resetar':
            confirmMessage = `Deseja resetar a senha de ${selected.length} usuários?`;
            break;
        case 'exportar':
            exportarSelecionados(selected);
            return;
    }
    
    if (confirm(confirmMessage)) {
        fetch(`/admin/usuarios/bulk/${action}`, {
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
                if (action === 'resetar') {
                    alert('Senhas resetadas com sucesso! E-mails enviados.');
                } else {
                    location.reload();
                }
            } else {
                alert('Erro na operação: ' + data.message);
            }
        });
    }
}

// Export functions
function exportarUsuarios() {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('export', 'excel');
    window.open('/admin/usuarios?' + urlParams.toString(), '_blank');
}

function exportarSelecionados(ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/usuarios/export-selected';
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

// Helper functions
<?php
function getRoleLabel($role) {
    $labels = [
        'admin' => 'Administrador',
        'analista' => 'Analista',
        'operador' => 'Operador',
        'usuario' => 'Usuário'
    ];
    return $labels[$role] ?? 'Usuário';
}

function getRoleBadgeClass($role) {
    $classes = [
        'admin' => 'danger',
        'analista' => 'warning',
        'operador' => 'info',
        'usuario' => 'secondary'
    ];
    return $classes[$role] ?? 'secondary';
}

function formatCpf($cpf) {
    return substr($cpf, 0, 3) . '.' . 
           substr($cpf, 3, 3) . '.' . 
           substr($cpf, 6, 3) . '-' . 
           substr($cpf, 9, 2);
}

function isUserOnline($ultimoAcesso) {
    if (!$ultimoAcesso) return false;
    $lastAccess = strtotime($ultimoAcesso);
    $now = time();
    return ($now - $lastAccess) < 900; // 15 minutes
}
?>
</script>

<style>
.avatar-circle img,
.avatar-circle div {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.btn-check:checked + .btn-outline-primary,
.btn-check:checked + .btn-outline-success,
.btn-check:checked + .btn-outline-danger,
.btn-check:checked + .btn-outline-warning {
    color: white;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    border-color: #198754;
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000 !important;
}

@media (max-width: 768px) {
    .btn-group {
        flex-wrap: wrap;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>