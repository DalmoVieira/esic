<?php
session_start();

// Verificar se está logado
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (empty($tipo_usuario) || $tipo_usuario === 'anonimo') {
    header('Location: login.php');
    exit;
}

// Buscar pedido se protocolo foi informado
$pedido = null;
$error_message = null;
$protocolo_pedido = $_GET['protocolo'] ?? '';

if (!empty($protocolo_pedido)) {
    try {
        $url = 'http://localhost/esic/api/pedidos.php?action=buscar&protocolo=' . urlencode($protocolo_pedido);
        $response = file_get_contents($url);
        $result = json_decode($response, true);
        
        if ($result && $result['success']) {
            $pedido = $result['data'];
            
            // Verificar se pode interpor recurso
            if (!in_array($pedido['status'], ['negado', 'parcialmente_atendido'])) {
                $error_message = 'Recurso só pode ser interposto contra pedidos negados ou parcialmente atendidos.';
                $pedido = null;
            }
        } else {
            $error_message = 'Pedido não encontrado';
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
    <title>Interpor Recurso - E-SIC Rio Claro</title>
    <meta name="description" content="Interpor recurso contra decisão - E-SIC Rio Claro">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .info-card {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            border-left: 4px solid #e17055;
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
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
                        <a class="nav-link" href="acompanhar-v2.php?tipo=<?= $tipo_usuario ?>">
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
                            <i class="bi bi-arrow-counterclockwise"></i> Interpor Recurso
                        </h2>
                        <p class="text-muted mb-0">Solicite revisão de decisão sobre pedido negado ou parcialmente atendido</p>
                    </div>
                    <div>
                        <a href="acompanhar-v2.php?tipo=<?= $tipo_usuario ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações sobre Recursos -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card info-card">
                    <div class="card-body">
                        <h6 class="card-title text-danger fw-bold">
                            <i class="bi bi-info-circle"></i> Sobre Recursos
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>1ª Instância:</strong> Revisão pela autoridade hierárquica superior</li>
                                    <li><strong>Prazo:</strong> 10 dias após negativa</li>
                                    <li><strong>Resposta:</strong> Até 5 dias úteis</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li>Recursos podem ser interpostos contra negativas ou informações incompletas</li>
                                    <li>Seja claro e objetivo na justificativa</li>
                                    <li>Você será notificado sobre a decisão</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($error_message): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$pedido && !$error_message): ?>
        <!-- Buscar Pedido -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informe o Protocolo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo_usuario) ?>">
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="protocolo" class="form-label">Protocolo do Pedido</label>
                                    <input type="text" class="form-control form-control-lg" id="protocolo" 
                                           name="protocolo" placeholder="Ex: P2025000001" 
                                           pattern="P[0-9]{10}" maxlength="11" required>
                                    <div class="form-text">
                                        Digite o protocolo do pedido que deseja recorrer
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-search"></i> Buscar Pedido
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>

        <!-- Dados do Pedido -->
        <?php if ($pedido): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Dados do Pedido Original</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Protocolo:</strong><br>
                                <?= htmlspecialchars($pedido['protocolo']) ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-danger"><?= htmlspecialchars($pedido['status']) ?></span>
                            </div>
                            <div class="col-12 mb-3">
                                <strong>Assunto:</strong><br>
                                <?= htmlspecialchars($pedido['assunto']) ?>
                            </div>
                            <?php if ($pedido['resposta']): ?>
                            <div class="col-12">
                                <strong>Resposta Recebida:</strong><br>
                                <div class="alert alert-light">
                                    <?= nl2br(htmlspecialchars($pedido['resposta'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Recurso -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Formulário de Recurso</h5>
                    </div>
                    <div class="card-body">
                        <form id="formRecurso" method="POST" action="api/recursos.php" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="criar">
                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                            <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario) ?>">

                            <!-- Tipo de Recurso -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-stack"></i> Tipo de Recurso
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tipo" class="form-label required-field">Instância do Recurso</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="primeira_instancia">1ª Instância (Autoridade Hierárquica Superior)</option>
                                        <option value="segunda_instancia">2ª Instância (Recurso à CGU)</option>
                                        <option value="terceira_instancia">3ª Instância (CMRI)</option>
                                    </select>
                                    <div class="form-text">
                                        Geralmente, inicia-se pela 1ª instância
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="motivo" class="form-label required-field">Motivo do Recurso</label>
                                    <select class="form-select" id="motivo" name="motivo" required>
                                        <option value="">Selecione...</option>
                                        <option value="negativa_acesso">Negativa de Acesso à Informação</option>
                                        <option value="demora_resposta">Demora Injustificada na Resposta</option>
                                        <option value="resposta_incompleta">Resposta Incompleta ou Insatisfatória</option>
                                        <option value="classificacao_indevida">Classificação Indevida de Sigilo</option>
                                        <option value="outro">Outro Motivo</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Selecione o motivo do recurso
                                    </div>
                                </div>
                            </div>

                            <!-- Justificativa -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-file-text"></i> Justificativa
                                    </h6>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="justificativa" class="form-label required-field">Justificativa Detalhada</label>
                                    <textarea class="form-control" id="justificativa" name="justificativa" 
                                              rows="8" maxlength="3000" required></textarea>
                                    <div class="char-counter mt-1">
                                        <span id="justificativa-count">0</span>/3000 caracteres
                                    </div>
                                    <div class="form-text">
                                        Explique detalhadamente os motivos pelos quais você está recorrendo da decisão. 
                                        Seja claro e objetivo, apresentando argumentos que fundamentem seu pedido de revisão.
                                    </div>
                                    <div class="invalid-feedback">
                                        A justificativa é obrigatória
                                    </div>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="row">
                                <div class="col-12">
                                    <hr class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="acompanhar-v2.php?tipo=<?= $tipo_usuario ?>&protocolo=<?= $pedido['protocolo'] ?>" 
                                           class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-send"></i> Enviar Recurso
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter
            const justificativa = document.getElementById('justificativa');
            if (justificativa) {
                const counter = document.getElementById('justificativa-count');
                
                justificativa.addEventListener('input', function() {
                    const count = this.value.length;
                    counter.textContent = count;
                    counter.className = count > 2700 ? 'char-counter text-danger' : 
                                       count > 2400 ? 'char-counter text-warning' : 'char-counter';
                });
            }

            // Form validation
            const form = document.getElementById('formRecurso');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!this.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                        ESICApp.showToast('Por favor, preencha todos os campos obrigatórios.', 'warning');
                    } else {
                        e.preventDefault();
                        
                        // Show loading
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Enviando...';
                        submitBtn.disabled = true;
                        
                        // Submit via fetch
                        const formData = new FormData(this);
                        
                        fetch(this.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                ESICApp.showToast('Recurso enviado com sucesso!', 'success');
                                setTimeout(() => {
                                    window.location.href = 'acompanhar-v2.php?tipo=<?= $tipo_usuario ?>&protocolo=' + data.data.protocolo_recurso;
                                }, 2000);
                            } else {
                                ESICApp.showToast(data.message || 'Erro ao enviar recurso', 'danger');
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            }
                        })
                        .catch(error => {
                            ESICApp.showToast('Erro ao enviar recurso: ' + error.message, 'danger');
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                    }
                    
                    this.classList.add('was-validated');
                });
            }

            // Protocolo formatting
            const protocoloInput = document.getElementById('protocolo');
            if (protocoloInput) {
                protocoloInput.addEventListener('input', function() {
                    let value = this.value.toUpperCase().replace(/[^P0-9]/g, '');
                    if (value && !value.startsWith('P')) {
                        value = 'P' + value;
                    }
                    if (value.length > 11) {
                        value = value.substring(0, 11);
                    }
                    this.value = value;
                });
            }
        });
    </script>
</body>
</html>