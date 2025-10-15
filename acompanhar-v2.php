<?php
session_start();

// Verificar se está logado
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (empty($tipo_usuario) || $tipo_usuario === 'anonimo') {
    header('Location: login.php');
    exit;
}

// Processar busca se houver protocolo
$pedido = null;
$error_message = null;
$protocolo_busca = $_GET['protocolo'] ?? $_POST['protocolo'] ?? '';

if (!empty($protocolo_busca)) {
    try {
        $url = 'http://localhost/esic/api/pedidos.php?action=buscar&protocolo=' . urlencode($protocolo_busca);
        $response = file_get_contents($url);
        $result = json_decode($response, true);
        
        if ($result && $result['success']) {
            $pedido = $result['data'];
        } else {
            $error_message = $result['message'] ?? 'Pedido não encontrado';
        }
    } catch (Exception $e) {
        $error_message = 'Erro ao buscar pedido: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar Solicitação - E-SIC Rio Claro</title>
    <meta name="description" content="Acompanhar status de solicitação - E-SIC Rio Claro">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 40px;
            width: 4px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        .timeline-marker {
            position: absolute;
            left: 32px;
            width: 20px;
            height: 20px;
            background: #fff;
            border: 4px solid #007bff;
            border-radius: 50%;
            z-index: 1;
        }
        .timeline-marker.success {
            border-color: #28a745;
            background: #28a745;
        }
        .timeline-marker.warning {
            border-color: #ffc107;
            background: #ffc107;
        }
        .timeline-content {
            margin-left: 80px;
            background: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .search-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="novo-pedido-v2.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-plus-circle"></i> Novo Pedido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="acompanhar.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-search"></i> Acompanhar
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Cabeçalho -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-primary fw-bold mb-2">
                            <i class="bi bi-search"></i> Acompanhar Solicitação
                        </h2>
                        <p class="text-muted mb-0">Consulte o status da sua solicitação pelo protocolo</p>
                    </div>
                    <div>
                        <a href="dashboard.php?tipo=<?= $tipo_usuario ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Busca -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card search-card">
                    <div class="card-body">
                        <h5 class="card-title text-white mb-3">
                            <i class="bi bi-file-text"></i> Consultar Protocolo
                        </h5>
                        
                        <form method="GET" action="" class="row g-3">
                            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_usuario) ?>">
                            
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="protocolo" name="protocolo" 
                                           placeholder="Ex: P2025000001" value="<?= htmlspecialchars($protocolo_busca) ?>" 
                                           pattern="[PR][0-9]{10}" maxlength="11" required>
                                    <label for="protocolo" class="text-muted">Número do Protocolo</label>
                                </div>
                                <div class="form-text text-white-50 mt-2">
                                    Digite o protocolo recebido no momento da solicitação (Ex: P2025000001)
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-light btn-lg w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-search me-2"></i> Consultar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados da Busca -->
        <?php if ($error_message): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($pedido): ?>
        <!-- Dados do Pedido -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-file-earmark-text"></i> Protocolo: <?= htmlspecialchars($pedido['protocolo']) ?>
                            </h5>
                            <span class="badge bg-<?= getStatusColor($pedido['status']) ?> fs-6">
                                <?= getStatusText($pedido['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Informações Gerais -->
                        <div class="info-grid mb-4">
                            <div>
                                <strong>Requerente:</strong><br>
                                <?= htmlspecialchars($pedido['requerente_nome']) ?>
                            </div>
                            <div>
                                <strong>Órgão:</strong><br>
                                <?= htmlspecialchars($pedido['orgao_nome']) ?>
                                <?php if ($pedido['orgao_sigla']): ?>
                                    (<?= htmlspecialchars($pedido['orgao_sigla']) ?>)
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong>Data da Solicitação:</strong><br>
                                <?= $pedido['data_cadastro_formatada'] ?>
                            </div>
                            <div>
                                <strong>Prazo Limite:</strong><br>
                                <span class="<?= isPrazoVencido($pedido['data_limite']) ? 'text-danger' : 'text-success' ?>">
                                    <?= $pedido['data_limite_formatada'] ?>
                                    <?php if (isPrazoVencido($pedido['data_limite'])): ?>
                                        <i class="bi bi-exclamation-triangle"></i>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Assunto e Descrição -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary">Assunto:</h6>
                                <p class="mb-3"><?= htmlspecialchars($pedido['assunto']) ?></p>
                                
                                <h6 class="text-primary">Descrição:</h6>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($pedido['descricao'])) ?></p>
                            </div>
                        </div>

                        <!-- Resposta (se houver) -->
                        <?php if (!empty($pedido['resposta'])): ?>
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="bi bi-chat-left-text"></i> Resposta:
                            </h6>
                            <p class="mb-2"><?= nl2br(htmlspecialchars($pedido['resposta'])) ?></p>
                            <?php if ($pedido['data_resposta']): ?>
                            <small class="text-muted">
                                Respondido em: <?= date('d/m/Y H:i', strtotime($pedido['data_resposta'])) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline de Tramitações -->
        <?php if (!empty($pedido['tramitacoes'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history"></i> Histórico de Tramitações
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">
                            <?php foreach (array_reverse($pedido['tramitacoes']) as $index => $tramitacao): ?>
                            <li class="timeline-item">
                                <div class="timeline-marker <?= $index === 0 ? 'success' : '' ?>"></div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1"><?= getStatusText($tramitacao['status_novo']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($tramitacao['data_tramitacao'])) ?>
                                        </small>
                                    </div>
                                    <?php if (!empty($tramitacao['observacoes'])): ?>
                                    <p class="text-muted mb-2"><?= htmlspecialchars($tramitacao['observacoes']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($tramitacao['usuario_nome'])): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> <?= htmlspecialchars($tramitacao['usuario_nome']) ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        
        <!-- Instruções quando não há busca -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">Digite o protocolo para consultar</h4>
                        <p class="text-muted">
                            O protocolo é fornecido no momento da criação da solicitação.<br>
                            Exemplo de formato: <code>P2025000001</code>
                        </p>
                        
                        <div class="mt-4">
                            <a href="novo-pedido-v2.php?tipo=<?= $tipo_usuario ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Fazer Nova Solicitação
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formatação do protocolo
            const protocoloInput = document.getElementById('protocolo');
            if (protocoloInput) {
                protocoloInput.addEventListener('input', function() {
                    let value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    
                    // Garantir que inicie com P ou R
                    if (value && !value.match(/^[PR]/)) {
                        value = 'P' + value;
                    }
                    
                    // Limitar a 11 caracteres
                    if (value.length > 11) {
                        value = value.substring(0, 11);
                    }
                    
                    this.value = value;
                });

                protocoloInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        this.form.submit();
                    }
                });
            }

            // Auto-focus no campo de protocolo se não houver resultado
            <?php if (empty($pedido) && empty($error_message)): ?>
            if (protocoloInput) {
                protocoloInput.focus();
            }
            <?php endif; ?>

            // Mostrar toast se há protocolo na URL
            const urlParams = new URLSearchParams(window.location.search);
            const protocoloUrl = urlParams.get('protocolo');
            if (protocoloUrl && !<?= json_encode($pedido !== null) ?>) {
                ESICApp.showToast('Protocolo não encontrado. Verifique se está correto.', 'warning');
            }
        });
    </script>
</body>
</html>

<?php
// Funções auxiliares
function getStatusColor($status) {
    $colors = [
        'aguardando' => 'warning',
        'em_analise' => 'info',
        'respondido' => 'success',
        'negado' => 'danger',
        'parcialmente_atendido' => 'primary',
        'cancelado' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}

function getStatusText($status) {
    $texts = [
        'aguardando' => 'Aguardando Análise',
        'em_analise' => 'Em Análise',
        'respondido' => 'Respondido',
        'negado' => 'Negado',
        'parcialmente_atendido' => 'Parcialmente Atendido',
        'cancelado' => 'Cancelado'
    ];
    return $texts[$status] ?? 'Status Desconhecido';
}

function isPrazoVencido($dataLimite) {
    return strtotime($dataLimite) < strtotime('today');
}
?>