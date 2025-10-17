<?php
session_start();

// Verificar se está logado
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (empty($tipo_usuario) || $tipo_usuario === 'anonimo') {
    header('Location: login.php');
    exit;
}

// Incluir dependências
require_once 'app/config/Database.php';

try {
    $db = Database::getInstance();
    
    // Buscar órgãos ativos para o select
    $orgaos = $db->select("SELECT id, nome, sigla FROM orgaos_setores WHERE ativo = 1 ORDER BY ordem_exibicao, nome");
    
} catch (Exception $e) {
    $error_message = "Erro ao carregar dados: " . $e->getMessage();
    $orgaos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido - E-SIC Rio Claro</title>
    <meta name="description" content="Submeter nova solicitação de informação - E-SIC Rio Claro">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-floating textarea {
            min-height: 120px;
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .char-counter.warning {
            color: #fd7e14;
        }
        .char-counter.danger {
            color: #dc3545;
        }
        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-left: 4px solid var(--primary-color);
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
                <img src="assets/images/logo-pmrcrj.png" alt="Logo Prefeitura Municipal de Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
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
                        <a class="nav-link active" href="novo-pedido.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-plus-circle"></i> Novo Pedido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="acompanhar.php?tipo=<?= $tipo_usuario ?>">
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
                            <i class="bi bi-plus-circle"></i> Nova Solicitação de Informação
                        </h2>
                        <p class="text-muted mb-0">Solicite informações públicas conforme a Lei de Acesso à Informação</p>
                    </div>
                    <div>
                        <a href="dashboard.php?tipo=<?= $tipo_usuario ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações Importantes -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card info-card">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="bi bi-info-circle"></i> Informações Importantes
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li>Prazo de resposta: <strong>até 20 dias úteis</strong></li>
                                    <li>Prorrogação: <strong>mais 10 dias</strong> (se necessário)</li>
                                    <li>Campos com <span class="text-danger">*</span> são obrigatórios</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li>Seja específico na sua solicitação</li>
                                    <li>Anexos permitidos: PDF, DOC, JPG, PNG (max 10MB)</li>
                                    <li>Você receberá um protocolo para acompanhamento</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                        </div>
                        <?php endif; ?>

                        <form id="formNovoPedido" method="POST" action="api/pedidos.php" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="criar">
                            <input type="hidden" name="tipo_usuario" value="<?= htmlspecialchars($tipo_usuario) ?>">

                            <!-- Dados do Requerente -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-person"></i> Dados do Requerente
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="100">
                                        <label for="nome" class="required-field">Nome Completo</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe seu nome completo.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" required maxlength="150">
                                        <label for="email" class="required-field">E-mail</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe um e-mail válido.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required maxlength="18">
                                        <label for="cpf_cnpj" class="required-field">CPF ou CNPJ</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe um CPF ou CNPJ válido.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="telefone" name="telefone" maxlength="15">
                                        <label for="telefone">Telefone (opcional)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dados da Solicitação -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-file-text"></i> Dados da Solicitação
                                    </h5>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="orgao_id" name="orgao_id" required>
                                            <option value="">Selecione o órgão...</option>
                                            <?php foreach ($orgaos as $orgao): ?>
                                            <option value="<?= $orgao['id'] ?>">
                                                <?= htmlspecialchars($orgao['nome']) ?> 
                                                <?php if ($orgao['sigla']): ?>(<?= htmlspecialchars($orgao['sigla']) ?>)<?php endif; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="orgao_id" class="required-field">Órgão Responsável</label>
                                        <div class="invalid-feedback">
                                            Por favor, selecione o órgão responsável.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-select" id="forma_recebimento" name="forma_recebimento" required>
                                            <option value="sistema">Pelo sistema (E-SIC)</option>
                                            <option value="email">Por e-mail</option>
                                            <option value="presencial">Retirada presencial</option>
                                            <option value="correio">Pelos Correios</option>
                                        </select>
                                        <label for="forma_recebimento" class="required-field">Forma de Recebimento</label>
                                        <div class="invalid-feedback">
                                            Por favor, selecione como deseja receber a resposta.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="assunto" name="assunto" required maxlength="200">
                                        <label for="assunto" class="required-field">Assunto</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe o assunto da solicitação.
                                        </div>
                                        <div class="char-counter mt-1">
                                            <span id="assunto-count">0</span>/200 caracteres
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="descricao" name="descricao" required maxlength="2000" style="min-height: 150px"></textarea>
                                        <label for="descricao" class="required-field">Descrição Detalhada</label>
                                        <div class="invalid-feedback">
                                            Por favor, descreva detalhadamente sua solicitação.
                                        </div>
                                        <div class="char-counter mt-1">
                                            <span id="descricao-count">0</span>/2000 caracteres
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        Seja específico sobre as informações que deseja obter. Quanto mais detalhada for sua solicitação, mais precisa será a resposta.
                                    </div>
                                </div>
                            </div>

                            <!-- Anexos -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-paperclip"></i> Anexos (Opcional)
                                    </h5>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-3">
                                        <input class="form-control" type="file" id="anexos" name="anexos[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                                        <div class="form-text">
                                            Tipos permitidos: PDF, DOC, DOCX, JPG, PNG, TXT. Tamanho máximo por arquivo: 10MB.
                                        </div>
                                    </div>
                                    <div id="anexos-preview" class="row g-2"></div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="row">
                                <div class="col-12">
                                    <hr class="mb-4">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="bi bi-arrow-left"></i> Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-send"></i> Enviar Solicitação
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const assunto = document.getElementById('assunto');
            const assuntoCount = document.getElementById('assunto-count');
            const descricao = document.getElementById('descricao');
            const descricaoCount = document.getElementById('descricao-count');

            assunto.addEventListener('input', function() {
                const count = this.value.length;
                assuntoCount.textContent = count;
                assuntoCount.className = count > 180 ? 'char-counter danger' : count > 150 ? 'char-counter warning' : 'char-counter';
            });

            descricao.addEventListener('input', function() {
                const count = this.value.length;
                descricaoCount.textContent = count;
                descricaoCount.className = count > 1800 ? 'char-counter danger' : count > 1500 ? 'char-counter warning' : 'char-counter';
            });

            // CPF/CNPJ formatting
            const cpfCnpj = document.getElementById('cpf_cnpj');
            cpfCnpj.addEventListener('input', function() {
                ESICApp.formatCpfCnpj(this);
            });

            // Phone formatting
            const telefone = document.getElementById('telefone');
            telefone.addEventListener('input', function() {
                this.value = ESICApp.formatPhone(this.value);
            });

            // File preview
            const anexos = document.getElementById('anexos');
            const preview = document.getElementById('anexos-preview');
            
            anexos.addEventListener('change', function() {
                preview.innerHTML = '';
                
                Array.from(this.files).forEach((file, index) => {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const isValidSize = file.size <= 10 * 1024 * 1024; // 10MB
                    
                    const col = document.createElement('div');
                    col.className = 'col-md-6 col-lg-4';
                    
                    col.innerHTML = `
                        <div class="card ${isValidSize ? '' : 'border-danger'}">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark me-2"></i>
                                    <div class="flex-grow-1 small">
                                        <div class="fw-semibold text-truncate" title="${file.name}">${file.name}</div>
                                        <div class="text-muted">${fileSize} MB</div>
                                    </div>
                                    ${!isValidSize ? '<i class="bi bi-exclamation-triangle text-danger" title="Arquivo muito grande"></i>' : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    preview.appendChild(col);
                });
            });

            // Form validation
            const form = document.getElementById('formNovoPedido');
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    ESICApp.showToast('Por favor, preencha todos os campos obrigatórios.', 'warning');
                } else {
                    // Validate file sizes
                    const files = anexos.files;
                    let hasInvalidFiles = false;
                    
                    Array.from(files).forEach(file => {
                        if (file.size > 10 * 1024 * 1024) {
                            hasInvalidFiles = true;
                        }
                    });
                    
                    if (hasInvalidFiles) {
                        e.preventDefault();
                        e.stopPropagation();
                        ESICApp.showToast('Alguns arquivos excedem o tamanho máximo de 10MB.', 'danger');
                        return;
                    }
                    
                    // Show loading
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Enviando...';
                    submitBtn.disabled = true;
                }
                
                this.classList.add('was-validated');
            });

            // Auto-save draft (opcional)
            let saveTimeout;
            form.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    // Implementar salvamento de rascunho
                }, 5000);
            });
        });
    </script>
</body>
</html>