<?php
session_start();

// Verificar se é administrador
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if ($tipo_usuario !== 'administrador') {
    header('Location: login.php');
    exit;
}

// Carregar usuários cadastrados
$usuarios_file = 'api/data/usuarios.json';
$usuarios = [];
if (file_exists($usuarios_file)) {
    $usuarios = json_decode(file_get_contents($usuarios_file), true) ?: [];
}

// Carregar logs de acesso
$logs_file = 'api/data/access_log.json';
$logs = [];
if (file_exists($logs_file)) {
    $logs = json_decode(file_get_contents($logs_file), true) ?: [];
    $logs = array_slice(array_reverse($logs), 0, 50); // Últimos 50 logs
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração - E-SIC Rio Claro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=administrador">
                <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php?tipo=administrador">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="text-primary mb-4">
                    <i class="bi bi-gear"></i> Administração do Sistema E-SIC
                </h2>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-2 mb-2"></i>
                        <h4><?= count($usuarios) ?></h4>
                        <small>Usuários Cadastrados</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check fs-2 mb-2"></i>
                        <h4><?= count(array_filter($usuarios, function($u) { return $u['status'] === 'ativo'; })) ?></h4>
                        <small>Usuários Ativos</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-person-vcard fs-2 mb-2"></i>
                        <h4><?= count(array_filter($usuarios, function($u) { return $u['tipo_documento'] === 'cpf'; })) ?></h4>
                        <small>Pessoas Físicas</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-building fs-2 mb-2"></i>
                        <h4><?= count(array_filter($usuarios, function($u) { return $u['tipo_documento'] === 'cnpj'; })) ?></h4>
                        <small>Pessoas Jurídicas</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abas -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">
                    <i class="bi bi-people"></i> Usuários Cadastrados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                    <i class="bi bi-list-ul"></i> Logs de Acesso
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="config-tab" data-bs-toggle="tab" data-bs-target="#config" type="button" role="tab">
                    <i class="bi bi-gear"></i> Configurações
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <!-- Aba Usuários -->
            <div class="tab-pane fade show active" id="usuarios" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-people"></i> Lista de Usuários
                        </h5>
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchUsuarios" placeholder="Buscar usuários...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($usuarios)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-person-x fs-1 text-muted"></i>
                                <p class="text-muted">Nenhum usuário cadastrado ainda.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tabelaUsuarios">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Documento</th>
                                            <th>Nome/Razão Social</th>
                                            <th>E-mail</th>
                                            <th>Telefone</th>
                                            <th>Data Cadastro</th>
                                            <th>Último Acesso</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?= $usuario['tipo_documento'] === 'cpf' ? 'primary' : 'info' ?>">
                                                    <?= strtoupper($usuario['tipo_documento']) ?>
                                                </span><br>
                                                <small class="text-muted"><?= $usuario['documento_formatado'] ?></small>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($usuario['nome']) ?></strong>
                                                <?php if (!empty($usuario['endereco'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($usuario['endereco'], 0, 50)) ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="mailto:<?= $usuario['email'] ?>" class="text-decoration-none">
                                                    <?= $usuario['email'] ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= !empty($usuario['telefone']) ? $usuario['telefone'] : '<span class="text-muted">-</span>' ?>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($usuario['ultimo_acesso']): ?>
                                                    <small><?= date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Nunca acessou</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($usuario['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="verDetalhes('<?= $usuario['id'] ?>')">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" onclick="editarUsuario('<?= $usuario['id'] ?>')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="toggleStatus('<?= $usuario['id'] ?>')">
                                                        <i class="bi bi-<?= $usuario['status'] === 'ativo' ? 'x' : 'check' ?>-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Aba Logs -->
            <div class="tab-pane fade" id="logs" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Últimas Atividades (50 registros)
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($logs)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                                <p class="text-muted">Nenhum log disponível.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>IP</th>
                                            <th>Ação</th>
                                            <th>Detalhes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td>
                                                <small><?= date('d/m/Y H:i:s', strtotime($log['timestamp'])) ?></small>
                                            </td>
                                            <td>
                                                <code><?= $log['ip'] ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $log['acao'] === 'login' ? 'success' : 
                                                        ($log['acao'] === 'cadastro' ? 'primary' : 
                                                        ($log['acao'] === 'recuperacao' ? 'warning' : 'secondary'));
                                                ?>">
                                                    <?= ucfirst($log['acao']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php if (!empty($log['dados'])): ?>
                                                        <?= json_encode($log['dados'], JSON_UNESCAPED_UNICODE) ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Aba Configurações -->
            <div class="tab-pane fade" id="config" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-check"></i> Configurações de Segurança
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Validação de CPF/CNPJ</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="validarDocumentos" checked>
                                        <label class="form-check-label" for="validarDocumentos">
                                            Ativar validação rigorosa de documentos
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Log de Atividades</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="logAtividades" checked>
                                        <label class="form-check-label" for="logAtividades">
                                            Registrar todas as atividades do sistema
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-envelope"></i> Configurações de E-mail
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="emailAdmin" class="form-label">E-mail do Administrador</label>
                                    <input type="email" class="form-control" id="emailAdmin" value="admin@rioclaro.rj.gov.br">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notificações por E-mail</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notifCadastro" checked>
                                        <label class="form-check-label" for="notifCadastro">
                                            Novos cadastros
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notifRecuperacao" checked>
                                        <label class="form-check-label" for="notifRecuperacao">
                                            Recuperações de senha
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Busca em tempo real
            ESICApp.initTableSearch('tabelaUsuarios', 'searchUsuarios');
            
            // Mostrar toast de carregamento
            setTimeout(() => {
                ESICApp.showToast('Painel administrativo carregado!', 'success');
            }, 500);
        });
        
        function verDetalhes(userId) {
            ESICApp.showToast('Funcionalidade em desenvolvimento', 'info');
        }
        
        function editarUsuario(userId) {
            ESICApp.showToast('Funcionalidade em desenvolvimento', 'info');
        }
        
        function toggleStatus(userId) {
            if (confirm('Deseja alterar o status deste usuário?')) {
                ESICApp.showToast('Funcionalidade em desenvolvimento', 'warning');
            }
        }
    </script>
</body>
</html>