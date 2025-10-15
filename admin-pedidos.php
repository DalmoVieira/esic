<?php
session_start();

// Verificar se é administrador
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if ($tipo_usuario !== 'administrador' && $tipo_usuario !== 'funcionario') {
    header('Location: login.php');
    exit;
}

// Incluir dependências
require_once 'app/config/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar estatísticas
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'aguardando' THEN 1 ELSE 0 END) as aguardando,
            SUM(CASE WHEN status = 'em_analise' THEN 1 ELSE 0 END) as em_analise,
            SUM(CASE WHEN status = 'respondido' THEN 1 ELSE 0 END) as respondido,
            SUM(CASE WHEN status = 'negado' THEN 1 ELSE 0 END) as negado,
            SUM(CASE WHEN DATE(data_limite) < CURDATE() AND status NOT IN ('respondido', 'negado', 'cancelado') THEN 1 ELSE 0 END) as vencidos
        FROM pedidos
    ");
    $stats = $stmt->fetch();
    
    // Buscar pedidos recentes
    $stmt = $pdo->query("
        SELECT 
            p.*,
            u.nome as requerente_nome,
            o.nome as orgao_nome,
            o.sigla as orgao_sigla,
            DATEDIFF(p.data_limite, CURDATE()) as dias_restantes
        FROM pedidos p
        LEFT JOIN usuarios u ON p.requerente_id = u.id
        LEFT JOIN orgaos_setores o ON p.orgao_id = o.id
        ORDER BY p.data_cadastro DESC
        LIMIT 20
    ");
    $pedidos = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = "Erro ao carregar dados: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pedidos - E-SIC Rio Claro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stats-card {
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .table-actions {
            white-space: nowrap;
        }
        .badge-vencido {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="admin-pedidos.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-file-earmark-text"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin-recursos.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-arrow-counterclockwise"></i> Recursos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-people"></i> Usuários
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-primary fw-bold mb-3">
                    <i class="bi bi-speedometer2"></i> Gerenciamento de Pedidos
                </h2>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-folder text-secondary" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0"><?= $stats['total'] ?></h3>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0"><?= $stats['aguardando'] ?></h3>
                        <small class="text-muted">Aguardando</small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history text-info" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0"><?= $stats['em_analise'] ?></h3>
                        <small class="text-muted">Em Análise</small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0"><?= $stats['respondido'] ?></h3>
                        <small class="text-muted">Respondidos</small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0"><?= $stats['negado'] ?></h3>
                        <small class="text-muted">Negados</small>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stats-card border-0 shadow-sm h-100 border-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0 text-danger"><?= $stats['vencidos'] ?></h3>
                        <small class="text-muted">Vencidos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="searchProtocolo" placeholder="Buscar por protocolo...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Todos os Status</option>
                                    <option value="aguardando">Aguardando</option>
                                    <option value="em_analise">Em Análise</option>
                                    <option value="respondido">Respondido</option>
                                    <option value="negado">Negado</option>
                                    <option value="parcialmente_atendido">Parcialmente Atendido</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterPrazo">
                                    <option value="">Todos os Prazos</option>
                                    <option value="vencido">Vencidos</option>
                                    <option value="proximo">Próximo ao Vencimento (5 dias)</option>
                                    <option value="normal">Dentro do Prazo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" onclick="aplicarFiltros()">
                                    <i class="bi bi-filter"></i> Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pedidos Recentes</h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="atualizarLista()">
                                <i class="bi bi-arrow-clockwise"></i> Atualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Protocolo</th>
                                        <th>Requerente</th>
                                        <th>Assunto</th>
                                        <th>Órgão</th>
                                        <th>Status</th>
                                        <th>Prazo</th>
                                        <th>Data</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelaPedidos">
                                    <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($pedido['protocolo']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($pedido['requerente_nome']) ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($pedido['assunto']) ?>">
                                                <?= htmlspecialchars($pedido['assunto']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($pedido['orgao_sigla'] ?? $pedido['orgao_nome']) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'aguardando' => 'warning',
                                                'em_analise' => 'info',
                                                'respondido' => 'success',
                                                'negado' => 'danger',
                                                'parcialmente_atendido' => 'primary',
                                                'cancelado' => 'secondary'
                                            ];
                                            $color = $statusColors[$pedido['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>">
                                                <?= ucfirst(str_replace('_', ' ', $pedido['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pedido['dias_restantes'] < 0): ?>
                                                <span class="badge bg-danger badge-vencido">
                                                    <i class="bi bi-exclamation-triangle"></i> Vencido
                                                </span>
                                            <?php elseif ($pedido['dias_restantes'] <= 5): ?>
                                                <span class="badge bg-warning">
                                                    <?= $pedido['dias_restantes'] ?> dias
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <?= $pedido['dias_restantes'] ?> dias
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($pedido['data_cadastro'])) ?>
                                            </small>
                                        </td>
                                        <td class="table-actions text-center">
                                            <button class="btn btn-sm btn-primary" onclick="visualizarPedido('<?= $pedido['protocolo'] ?>')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="responderPedido(<?= $pedido['id'] ?>)">
                                                <i class="bi bi-reply"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="alterarStatus(<?= $pedido['id'] ?>, 'em_analise')">
                                                    <i class="bi bi-clock"></i> Em Análise
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="alterarStatus(<?= $pedido['id'] ?>, 'respondido')">
                                                    <i class="bi bi-check"></i> Marcar como Respondido
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="alterarStatus(<?= $pedido['id'] ?>, 'negado')">
                                                    <i class="bi bi-x"></i> Negar
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="cancelarPedido(<?= $pedido['id'] ?>)">
                                                    <i class="bi bi-trash"></i> Cancelar
                                                </a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div class="modal fade" id="modalVisualizarPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="conteudoPedido">
                    <div class="text-center">
                        <div class="spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Resposta -->
    <div class="modal fade" id="modalResponderPedido" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Responder Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formResposta">
                        <input type="hidden" id="pedidoIdResposta" name="pedido_id">
                        
                        <div class="mb-3">
                            <label for="tipoResposta" class="form-label">Tipo de Resposta</label>
                            <select class="form-select" id="tipoResposta" name="tipo_resposta" required>
                                <option value="deferido">Deferido - Informação Fornecida</option>
                                <option value="parcial">Parcialmente Atendido</option>
                                <option value="indeferido">Indeferido - Negativa</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="respostaTexto" class="form-label">Resposta</label>
                            <textarea class="form-control" id="respostaTexto" name="resposta" rows="6" required></textarea>
                            <div class="form-text">
                                Forneça uma resposta clara e completa ao requerente.
                            </div>
                        </div>
                        
                        <div class="mb-3" id="divMotivoNegativa" style="display: none;">
                            <label for="motivoNegativa" class="form-label">Motivo da Negativa</label>
                            <textarea class="form-control" id="motivoNegativa" name="motivo_negativa" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="enviarResposta()">
                        <i class="bi bi-send"></i> Enviar Resposta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Visualizar pedido
        function visualizarPedido(protocolo) {
            const modal = new bootstrap.Modal(document.getElementById('modalVisualizarPedido'));
            modal.show();
            
            fetch(`api/pedidos.php?action=buscar&protocolo=${protocolo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const pedido = data.data;
                        document.getElementById('conteudoPedido').innerHTML = `
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Protocolo:</strong><br>${pedido.protocolo}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Data:</strong><br>${pedido.data_cadastro_formatada}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Requerente:</strong><br>${pedido.requerente_nome}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Email:</strong><br>${pedido.requerente_email}
                                </div>
                                <div class="col-12 mb-3">
                                    <strong>Assunto:</strong><br>${pedido.assunto}
                                </div>
                                <div class="col-12 mb-3">
                                    <strong>Descrição:</strong><br>
                                    <div class="border p-3 bg-light">${pedido.descricao.replace(/\n/g, '<br>')}</div>
                                </div>
                                ${pedido.resposta ? `
                                <div class="col-12">
                                    <strong>Resposta:</strong><br>
                                    <div class="alert alert-info">${pedido.resposta.replace(/\n/g, '<br>')}</div>
                                </div>
                                ` : ''}
                            </div>
                        `;
                    } else {
                        ESICApp.showToast('Erro ao carregar pedido', 'danger');
                    }
                })
                .catch(error => {
                    ESICApp.showToast('Erro na requisição: ' + error.message, 'danger');
                });
        }

        // Responder pedido
        function responderPedido(pedidoId) {
            document.getElementById('pedidoIdResposta').value = pedidoId;
            const modal = new bootstrap.Modal(document.getElementById('modalResponderPedido'));
            modal.show();
        }

        // Mostrar/ocultar motivo negativa
        document.getElementById('tipoResposta')?.addEventListener('change', function() {
            const div = document.getElementById('divMotivoNegativa');
            div.style.display = this.value === 'indeferido' ? 'block' : 'none';
        });

        // Enviar resposta
        function enviarResposta() {
            const form = document.getElementById('formResposta');
            const formData = new FormData(form);
            formData.append('action', 'responder');
            
            fetch('api/pedidos-admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ESICApp.showToast('Resposta enviada com sucesso!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('modalResponderPedido')).hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    ESICApp.showToast(data.message || 'Erro ao enviar resposta', 'danger');
                }
            })
            .catch(error => {
                ESICApp.showToast('Erro na requisição: ' + error.message, 'danger');
            });
        }

        // Alterar status
        function alterarStatus(pedidoId, novoStatus) {
            if (!confirm('Confirma a alteração de status?')) return;
            
            const formData = new FormData();
            formData.append('action', 'alterar_status');
            formData.append('pedido_id', pedidoId);
            formData.append('status', novoStatus);
            
            fetch('api/pedidos-admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    ESICApp.showToast('Status alterado com sucesso!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    ESICApp.showToast(data.message || 'Erro ao alterar status', 'danger');
                }
            });
        }

        // Atualizar lista
        function atualizarLista() {
            location.reload();
        }

        // Aplicar filtros
        function aplicarFiltros() {
            // Implementar filtros via AJAX
            ESICApp.showToast('Funcionalidade em desenvolvimento', 'info');
        }
    </script>
</body>
</html>