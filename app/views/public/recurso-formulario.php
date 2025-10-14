<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Recurso - Sistema E-SIC'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-section {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
            color: white;
            padding: 2rem 0;
        }
        .form-card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .form-section {
            border-bottom: 1px solid #e9ecef;
            padding: 2rem 0;
        }
        .form-section:last-child {
            border-bottom: none;
        }
        .section-title {
            color: #dc3545;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #dc3545;
        }
        .required {
            color: #dc3545;
        }
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .original-pedido {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 1rem;
            border-radius: 0 8px 8px 0;
        }
        .character-count {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: #dc3545;
            background: #fff5f5;
        }
        .file-upload-area.dragover {
            border-color: #dc3545;
            background: #fff5f5;
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
                        <a class="nav-link" href="/novo-pedido">Novo Pedido</a>
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

    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">
                        <i class="fas fa-gavel me-2"></i>
                        Formulário de Recurso
                    </h1>
                    <p class="mb-0 opacity-75">
                        Protocolo: #<?php echo htmlspecialchars($pedido['protocolo']); ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-danger fs-6 px-3 py-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Prazo: 10 dias
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Início</a></li>
                <li class="breadcrumb-item"><a href="/acompanhar">Acompanhar</a></li>
                <li class="breadcrumb-item"><a href="/pedido/<?php echo $pedido['protocolo']; ?>">Protocolo <?php echo $pedido['protocolo']; ?></a></li>
                <li class="breadcrumb-item active">Recurso</li>
            </ol>
        </nav>

        <!-- Information Alert -->
        <div class="alert alert-warning d-flex align-items-start mb-4">
            <i class="fas fa-info-circle me-3 mt-1"></i>
            <div>
                <h6 class="alert-heading mb-2">Importante - Direito de Recurso</h6>
                <p class="mb-0">
                    Conforme a Lei nº 12.527/2011, você tem direito de entrar com recurso contra a decisão
                    no prazo de 10 (dez) dias contados da ciência da decisão. O recurso será analisado por
                    autoridade hierarquicamente superior àquela que proferiu a decisão impugnada.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Original Request Info -->
                <div class="card form-card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Informações do Pedido Original
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="original-pedido">
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Protocolo:</strong> <?php echo htmlspecialchars($pedido['protocolo']); ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($pedido['created_at'])); ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Assunto:</strong><br>
                                <?php echo htmlspecialchars($pedido['assunto']); ?>
                            </div>
                            <div class="mb-3">
                                <strong>Status Atual:</strong>
                                <span class="badge bg-danger ms-2">
                                    <?php 
                                    $statusTexts = [
                                        'negado' => 'Negado',
                                        'parcial' => 'Atendido Parcialmente'
                                    ];
                                    echo $statusTexts[$pedido['status']] ?? 'Status Desconhecido';
                                    ?>
                                </span>
                            </div>
                            <?php if (!empty($pedido['justificativa_negacao'])): ?>
                            <div>
                                <strong>Justificativa da Negação:</strong><br>
                                <em><?php echo nl2br(htmlspecialchars($pedido['justificativa_negacao'])); ?></em>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Resource Form -->
                <div class="card form-card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Formulário de Recurso
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="recursoForm" method="POST" action="/recurso/<?php echo $pedido['protocolo']; ?>" 
                              enctype="multipart/form-data" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">

                            <!-- Seção 1: Dados do Requerente -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-user me-2"></i>
                                    1. Identificação do Requerente
                                </h4>
                                
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label for="nome_requerente" class="form-label">
                                            Nome Completo <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nome_requerente" 
                                               name="nome_requerente" 
                                               value="<?php echo htmlspecialchars($pedido['nome_requerente'] ?? ''); ?>"
                                               readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cpf_cnpj" class="form-label">
                                            CPF/CNPJ <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="cpf_cnpj" 
                                               name="cpf_cnpj" 
                                               value="<?php echo htmlspecialchars($pedido['cpf_cnpj'] ?? ''); ?>"
                                               readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            E-mail <span class="required">*</span>
                                        </label>
                                        <input type="email" class="form-control" id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($pedido['email_requerente'] ?? ''); ?>"
                                               readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="tel" class="form-control" id="telefone" 
                                               name="telefone" 
                                               value="<?php echo htmlspecialchars($pedido['telefone'] ?? ''); ?>"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 2: Tipo de Recurso -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-list-ul me-2"></i>
                                    2. Tipo de Recurso
                                </h4>

                                <div class="mb-3">
                                    <label class="form-label">Selecione o tipo de recurso <span class="required">*</span></label>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_recurso" 
                                               id="recurso_negacao" value="negacao_total" required>
                                        <label class="form-check-label" for="recurso_negacao">
                                            <strong>Recurso contra negação total</strong><br>
                                            <small class="text-muted">O pedido foi completamente negado</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_recurso" 
                                               id="recurso_parcial" value="atendimento_parcial">
                                        <label class="form-check-label" for="recurso_parcial">
                                            <strong>Recurso contra atendimento parcial</strong><br>
                                            <small class="text-muted">O pedido foi atendido apenas parcialmente</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_recurso" 
                                               id="recurso_prazo" value="descumprimento_prazo">
                                        <label class="form-check-label" for="recurso_prazo">
                                            <strong>Recurso por descumprimento de prazo</strong><br>
                                            <small class="text-muted">O prazo legal de resposta foi ultrapassado</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_recurso" 
                                               id="recurso_outros" value="outros">
                                        <label class="form-check-label" for="recurso_outros">
                                            <strong>Outros motivos</strong><br>
                                            <small class="text-muted">Outras irregularidades no atendimento</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 3: Justificativa -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-align-left me-2"></i>
                                    3. Justificativa do Recurso
                                </h4>

                                <div class="info-box">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    <strong>Dica:</strong> Explique detalhadamente os motivos pelos quais você discorda 
                                    da decisão. Cite dispositivos legais, se conhecer, e apresente argumentos claros 
                                    e objetivos.
                                </div>

                                <div class="mb-3">
                                    <label for="justificativa" class="form-label">
                                        Justificativa detalhada do recurso <span class="required">*</span>
                                    </label>
                                    <textarea class="form-control" id="justificativa" name="justificativa" 
                                              rows="8" required maxlength="3000"
                                              placeholder="Descreva detalhadamente os motivos do seu recurso, apresentando argumentos que justifiquem a revisão da decisão..."></textarea>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Mínimo: 50 caracteres</small>
                                        <small class="character-count">
                                            <span id="justificativaCount">0</span>/3000 caracteres
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="fundamentacao_legal" class="form-label">
                                        Fundamentação Legal (opcional)
                                    </label>
                                    <textarea class="form-control" id="fundamentacao_legal" 
                                              name="fundamentacao_legal" rows="4" maxlength="1000"
                                              placeholder="Se conhecer, cite os dispositivos legais que fundamentam seu recurso (Lei 12.527/2011, Constituição Federal, etc.)"></textarea>
                                    <small class="text-muted">
                                        Cite artigos da Lei de Acesso à Informação ou outras normas aplicáveis
                                    </small>
                                </div>
                            </div>

                            <!-- Seção 4: Documentos -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-paperclip me-2"></i>
                                    4. Documentos Anexos
                                </h4>

                                <div class="mb-3">
                                    <label class="form-label">Anexar documentos (opcional)</label>
                                    <div class="file-upload-area" id="fileUploadArea">
                                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                        <p class="mb-2">
                                            <strong>Clique aqui ou arraste arquivos</strong>
                                        </p>
                                        <p class="text-muted mb-0">
                                            Formatos aceitos: PDF, DOC, DOCX, JPG, PNG<br>
                                            Tamanho máximo: 10MB por arquivo
                                        </p>
                                        <input type="file" id="arquivos" name="arquivos[]" 
                                               multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                                               style="display: none;">
                                    </div>
                                    <div id="filesList" class="mt-3"></div>
                                </div>
                            </div>

                            <!-- Seção 5: Forma de Resposta -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-reply me-2"></i>
                                    5. Forma de Resposta ao Recurso
                                </h4>

                                <div class="mb-3">
                                    <label class="form-label">Como deseja receber a resposta? <span class="required">*</span></label>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_resposta" 
                                               id="resposta_email" value="email" checked>
                                        <label class="form-check-label" for="resposta_email">
                                            <i class="fas fa-envelope me-2"></i>
                                            Por e-mail (recomendado)
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_resposta" 
                                               id="resposta_sistema" value="sistema">
                                        <label class="form-check-label" for="resposta_sistema">
                                            <i class="fas fa-desktop me-2"></i>
                                            Consulta pelo sistema (site)
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_resposta" 
                                               id="resposta_fisico" value="fisico">
                                        <label class="form-check-label" for="resposta_fisico">
                                            <i class="fas fa-building me-2"></i>
                                            Retirada no órgão público
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção 6: Confirmação -->
                            <div class="form-section">
                                <h4 class="section-title">
                                    <i class="fas fa-check-circle me-2"></i>
                                    6. Confirmação e Envio
                                </h4>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="declaracao" 
                                               name="declaracao" required>
                                        <label class="form-check-label" for="declaracao">
                                            <strong>Declaro que:</strong> As informações prestadas são verdadeiras 
                                            e estou ciente de que a falsidade de declarações é crime previsto 
                                            no Código Penal Brasileiro. <span class="required">*</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Prazo de Análise:</strong> O recurso será analisado no prazo de 
                                    5 (cinco) dias, podendo ser prorrogado por mais 5 dias mediante justificativa.
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/pedido/<?php echo $pedido['protocolo']; ?>" 
                                       class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Enviar Recurso
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Ajuda sobre Recursos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-primary">O que é um recurso?</h6>
                            <p class="small mb-0">
                                É o direito de questionar uma decisão que negou total ou parcialmente 
                                seu pedido de informação.
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">Qual o prazo?</h6>
                            <p class="small mb-0">
                                Você tem 10 dias corridos contados da ciência da decisão para entrar 
                                com o recurso.
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">Quem analisa?</h6>
                            <p class="small mb-0">
                                Uma autoridade hierarquicamente superior àquela que tomou a 
                                decisão original.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Legal References -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-balance-scale me-2"></i>
                            Amparo Legal
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <strong>Art. 15</strong> - Lei 12.527/2011<br>
                                <small class="text-muted">Direito de recurso</small>
                            </li>
                            <li class="mb-2">
                                <strong>Art. 16</strong> - Lei 12.527/2011<br>
                                <small class="text-muted">Prazo e competência</small>
                            </li>
                            <li class="mb-2">
                                <strong>Art. 5º, XXXIII</strong> - CF/88<br>
                                <small class="text-muted">Direito fundamental</small>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Contact -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-headset me-2"></i>
                            Precisa de Ajuda?
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-3">
                            Em caso de dúvidas sobre como preencher este formulário:
                        </p>
                        <div class="d-grid gap-2">
                            <a href="mailto:esic@orgao.gov.br" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope me-2"></i>
                                E-mail
                            </a>
                            <a href="tel:08006420001" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-phone me-2"></i>
                                0800 642-0001
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
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
                        <li><a href="/novo-pedido" class="text-light">Novo Pedido</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counter
        document.getElementById('justificativa').addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('justificativaCount').textContent = count;
            
            // Visual feedback
            if (count < 50) {
                this.classList.add('border-warning');
                this.classList.remove('border-success');
            } else {
                this.classList.remove('border-warning');
                this.classList.add('border-success');
            }
        });

        // File upload handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('arquivos');
        const filesList = document.getElementById('filesList');

        fileUploadArea.addEventListener('click', () => fileInput.click());

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            updateFilesList();
        });

        fileInput.addEventListener('change', updateFilesList);

        function updateFilesList() {
            const files = Array.from(fileInput.files);
            filesList.innerHTML = '';

            files.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'alert alert-info alert-dismissible fade show';
                fileItem.innerHTML = `
                    <i class="fas fa-file-alt me-2"></i>
                    <strong>${file.name}</strong>
                    <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                    <button type="button" class="btn-close" onclick="removeFile(${index})"></button>
                `;
                filesList.appendChild(fileItem);
            });
        }

        function removeFile(index) {
            const dt = new DataTransfer();
            const files = Array.from(fileInput.files);
            
            files.forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            fileInput.files = dt.files;
            updateFilesList();
        }

        // Form validation
        document.getElementById('recursoForm').addEventListener('submit', function(e) {
            const justificativa = document.getElementById('justificativa').value;
            
            if (justificativa.length < 50) {
                e.preventDefault();
                alert('A justificativa deve ter pelo menos 50 caracteres.');
                document.getElementById('justificativa').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>