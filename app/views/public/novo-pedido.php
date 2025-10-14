<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Novo Pedido - Sistema E-SIC'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 30px;
        }
        .required {
            color: #dc3545;
        }
        .help-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-university me-2"></i>
                Sistema E-SIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/novo-pedido">Novo Pedido</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acompanhar">Acompanhar</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Entrar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item active">Novo Pedido</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 text-primary">
                    <i class="fas fa-plus-circle me-2"></i>
                    Novo Pedido de Acesso à Informação
                </h1>
                <p class="lead">Solicite informações públicas de acordo com a Lei de Acesso à Informação (LAI)</p>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?php echo $message['type'] === 'error' ? 'danger' : $message['type']; ?> alert-dismissible fade show">
                    <?php echo $message['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="/novo-pedido" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <!-- Dados Pessoais -->
            <div class="form-section">
                <h3 class="h4 text-primary mb-4">
                    <i class="fas fa-user me-2"></i>
                    Dados Pessoais
                </h3>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome Completo <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required 
                               value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>">
                        <div class="invalid-feedback">Nome é obrigatório</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">E-mail <span class="required">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <div class="invalid-feedback">E-mail válido é obrigatório</div>
                        <div class="help-text">Você receberá atualizações sobre seu pedido neste e-mail</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cpf" class="form-label">CPF <span class="required">*</span></label>
                        <input type="text" class="form-control" id="cpf" name="cpf" required
                               value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>"
                               data-mask="000.000.000-00">
                        <div class="invalid-feedback">CPF válido é obrigatório</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone"
                               value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                               data-mask="(00) 00000-0000">
                        <div class="help-text">Opcional - para contato em caso de necessidade</div>
                    </div>
                </div>
            </div>

            <!-- Dados do Pedido -->
            <div class="form-section">
                <h3 class="h4 text-primary mb-4">
                    <i class="fas fa-file-alt me-2"></i>
                    Dados do Pedido
                </h3>
                
                <div class="mb-4">
                    <label for="assunto" class="form-label">Assunto <span class="required">*</span></label>
                    <input type="text" class="form-control" id="assunto" name="assunto" required
                           value="<?php echo htmlspecialchars($_POST['assunto'] ?? ''); ?>"
                           placeholder="Resumo do que você está solicitando">
                    <div class="invalid-feedback">Assunto é obrigatório</div>
                </div>
                
                <div class="mb-4">
                    <label for="descricao" class="form-label">Descrição Detalhada <span class="required">*</span></label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="6" required
                              placeholder="Descreva detalhadamente a informação que você está solicitando. Seja específico para facilitar o atendimento."><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                    <div class="invalid-feedback">Descrição é obrigatória</div>
                    <div class="help-text">
                        Seja específico sobre:
                        • Que tipo de informação você precisa
                        • Período ou data específica (se aplicável)
                        • Setor ou área relacionada
                        • Qualquer detalhe que possa ajudar na localização da informação
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="forma_resposta" class="form-label">Forma de Resposta Desejada <span class="required">*</span></label>
                    <select class="form-select" id="forma_resposta" name="forma_resposta" required>
                        <option value="">Selecione uma opção</option>
                        <option value="email" <?php echo ($_POST['forma_resposta'] ?? '') === 'email' ? 'selected' : ''; ?>>
                            Por e-mail
                        </option>
                        <option value="sistema" <?php echo ($_POST['forma_resposta'] ?? '') === 'sistema' ? 'selected' : ''; ?>>
                            Consulta pelo sistema
                        </option>
                        <option value="fisico" <?php echo ($_POST['forma_resposta'] ?? '') === 'fisico' ? 'selected' : ''; ?>>
                            Retirada física
                        </option>
                    </select>
                    <div class="invalid-feedback">Selecione uma forma de resposta</div>
                </div>
            </div>

            <!-- Anexos -->
            <div class="form-section">
                <h3 class="h4 text-primary mb-4">
                    <i class="fas fa-paperclip me-2"></i>
                    Anexos (Opcional)
                </h3>
                
                <div class="mb-3">
                    <label for="anexos" class="form-label">Arquivos de Apoio</label>
                    <input type="file" class="form-control" id="anexos" name="anexos[]" multiple
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                    <div class="help-text">
                        Você pode anexar documentos que ajudem a esclarecer seu pedido.
                        Formatos aceitos: PDF, DOC, DOCX, JPG, PNG, TXT (máx. 5MB por arquivo)
                    </div>
                </div>
            </div>

            <!-- Termos -->
            <div class="form-section">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termos" name="termos" required>
                    <label class="form-check-label" for="termos">
                        Declaro que as informações fornecidas são verdadeiras e estou ciente de que:
                        <ul class="mt-2 mb-0">
                            <li>Este pedido será processado conforme a Lei de Acesso à Informação (Lei 12.527/2011)</li>
                            <li>O prazo para resposta é de até 20 dias, prorrogáveis por mais 10 dias</li>
                            <li>Informações pessoais e sigilosas não serão fornecidas</li>
                            <li>Posso acompanhar o status do pedido usando o protocolo gerado</li>
                        </ul>
                    </label>
                    <div class="invalid-feedback">Você deve aceitar os termos</div>
                </div>
            </div>

            <!-- Botões -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/" class="btn btn-outline-secondary me-md-2">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>
                    Enviar Pedido
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema E-SIC</h5>
                    <p>Lei de Acesso à Informação - Lei nº 12.527/2011</p>
                </div>
                <div class="col-md-6">
                    <h6>Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="/lei-acesso-informacao" class="text-light">Lei de Acesso à Informação</a></li>
                        <li><a href="/transparencia" class="text-light">Transparência</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Máscaras
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara CPF
            const cpfInput = document.getElementById('cpf');
            cpfInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                this.value = value;
            });

            // Máscara Telefone
            const telefoneInput = document.getElementById('telefone');
            telefoneInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                } else {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }
                this.value = value;
            });

            // Validação do formulário
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>