<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="fw-bold text-primary mb-3">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    Novo Pedido de Informação
                </h1>
                <p class="lead text-muted">
                    Preencha o formulário abaixo para solicitar informações públicas
                </p>
            </div>

            <!-- Progress Steps -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="step-item active">
                            <div class="step-circle">1</div>
                            <div class="step-label">Dados Pessoais</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item">
                            <div class="step-circle">2</div>
                            <div class="step-label">Solicitação</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item">
                            <div class="step-circle">3</div>
                            <div class="step-label">Confirmação</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <form id="pedidoForm" method="POST" action="<?= url('/pedido/criar') ?>" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <!-- Step 1: Personal Data -->
                        <div class="step-content" id="step1">
                            <h4 class="mb-4">
                                <i class="bi bi-person-fill text-primary me-2"></i>
                                Dados Pessoais
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nome" name="nome_solicitante" 
                                               value="<?= htmlspecialchars($old_data['nome_solicitante'] ?? '') ?>" required>
                                        <label for="nome">Nome Completo *</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe seu nome completo.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($old_data['email'] ?? '') ?>" required>
                                        <label for="email">Email *</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe um email válido.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="telefone" name="telefone" 
                                               value="<?= htmlspecialchars($old_data['telefone'] ?? '') ?>">
                                        <label for="telefone">Telefone</label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cpf" name="cpf" 
                                               value="<?= htmlspecialchars($old_data['cpf'] ?? '') ?>">
                                        <label for="cpf">CPF</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" onclick="nextStep()">
                                    Próximo <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 2: Request Details -->
                        <div class="step-content d-none" id="step2">
                            <h4 class="mb-4">
                                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                Detalhes da Solicitação
                            </h4>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="assunto" name="assunto" 
                                               value="<?= htmlspecialchars($old_data['assunto'] ?? '') ?>" required>
                                        <label for="assunto">Assunto da Solicitação *</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe o assunto da sua solicitação.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="descricao" name="descricao" 
                                                  style="height: 150px" required><?= htmlspecialchars($old_data['descricao'] ?? '') ?></textarea>
                                        <label for="descricao">Descrição Detalhada *</label>
                                        <div class="invalid-feedback">
                                            Por favor, descreva detalhadamente sua solicitação.
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        Seja específico sobre as informações que você deseja obter. 
                                        Quanto mais detalhada for sua solicitação, mais precisa será a resposta.
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Forma de Resposta Preferida</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_resposta" 
                                               id="resposta_email" value="email" checked>
                                        <label class="form-check-label" for="resposta_email">
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="forma_resposta" 
                                               id="resposta_sistema" value="sistema">
                                        <label class="form-check-label" for="resposta_sistema">
                                            <i class="bi bi-globe me-2"></i>Consulta no Sistema
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="anexos" class="form-label">Anexos (opcional)</label>
                                    <input class="form-control" type="file" id="anexos" name="anexos[]" 
                                           multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt"
                                           onchange="previewFiles(this)">
                                    <div class="form-text">
                                        Formatos aceitos: PDF, DOC, DOCX, JPG, PNG, TXT (máx. 10MB cada)
                                    </div>
                                    <div id="anexos-preview" class="mt-2"></div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                    <i class="bi bi-arrow-left me-2"></i>Anterior
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep()">
                                    Próximo <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Confirmation -->
                        <div class="step-content d-none" id="step3">
                            <h4 class="mb-4">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Confirmação dos Dados
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">Dados Pessoais</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1"><strong>Nome:</strong> <span id="confirm-nome">-</span></p>
                                            <p class="mb-1"><strong>Email:</strong> <span id="confirm-email">-</span></p>
                                            <p class="mb-1"><strong>Telefone:</strong> <span id="confirm-telefone">-</span></p>
                                            <p class="mb-0"><strong>CPF:</strong> <span id="confirm-cpf">-</span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card bg-light border-0 mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Solicitação</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1"><strong>Assunto:</strong> <span id="confirm-assunto">-</span></p>
                                            <p class="mb-1"><strong>Descrição:</strong> <span id="confirm-descricao">-</span></p>
                                            <p class="mb-0"><strong>Forma de Resposta:</strong> <span id="confirm-resposta">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="card border-warning mb-4">
                                <div class="card-body">
                                    <h6 class="text-warning">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Termos e Condições
                                    </h6>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="aceito_termos" required>
                                        <label class="form-check-label" for="aceito_termos">
                                            Declaro que as informações fornecidas são verdadeiras e estou ciente de que:
                                        </label>
                                    </div>
                                    <ul class="list-unstyled mt-2 ms-4 small text-muted">
                                        <li>• O prazo de resposta é de até 20 dias úteis</li>
                                        <li>• Posso interpor recurso em caso de negativa</li>
                                        <li>• Os dados pessoais serão tratados conforme a LGPD</li>
                                        <li>• O pedido deve se referir a informações públicas</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                    <i class="bi bi-arrow-left me-2"></i>Anterior
                                </button>
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                    <i class="bi bi-send me-2"></i>Enviar Pedido
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Information Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Informações Importantes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Prazos Legais</h6>
                            <ul class="list-unstyled small">
                                <li>• Resposta: até 20 dias úteis</li>
                                <li>• Prorrogação: mais 10 dias (justificado)</li>
                                <li>• Recurso: até 10 dias após resposta</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Tipos de Informação</h6>
                            <ul class="list-unstyled small">
                                <li>• Dados sobre políticas públicas</li>
                                <li>• Informações orçamentárias</li>
                                <li>• Contratos e licitações</li>
                                <li>• Estrutura organizacional</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.step-item.active .step-circle {
    background-color: var(--bs-primary);
}

.step-item.completed .step-circle {
    background-color: var(--bs-success);
}

.step-label {
    font-size: 0.875rem;
    color: #6c757d;
    text-align: center;
}

.step-item.active .step-label {
    color: var(--bs-primary);
    font-weight: 500;
}

.step-line {
    height: 2px;
    background-color: #dee2e6;
    flex: 1;
    margin: 0 1rem;
    align-self: flex-start;
    margin-top: 19px;
}

.step-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

@media (max-width: 768px) {
    .step-line {
        display: none;
    }
    
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-item {
        flex-direction: row;
        justify-content: flex-start;
        width: 100%;
    }
    
    .step-circle {
        margin-bottom: 0;
        margin-right: 1rem;
    }
}
</style>

<script>
let currentStep = 1;
const totalSteps = 3;

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            hideStep(currentStep);
            currentStep++;
            showStep(currentStep);
            updateProgressBar();
            
            if (currentStep === 3) {
                updateConfirmation();
            }
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        hideStep(currentStep);
        currentStep--;
        showStep(currentStep);
        updateProgressBar();
    }
}

function showStep(step) {
    document.getElementById(`step${step}`).classList.remove('d-none');
}

function hideStep(step) {
    document.getElementById(`step${step}`).classList.add('d-none');
}

function updateProgressBar() {
    // Update step indicators
    const stepItems = document.querySelectorAll('.step-item');
    stepItems.forEach((item, index) => {
        item.classList.remove('active', 'completed');
        if (index + 1 === currentStep) {
            item.classList.add('active');
        } else if (index + 1 < currentStep) {
            item.classList.add('completed');
        }
    });
}

function validateCurrentStep() {
    const form = document.getElementById('pedidoForm');
    const currentStepElement = document.getElementById(`step${currentStep}`);
    const inputs = currentStepElement.querySelectorAll('input[required], textarea[required]');
    
    let valid = true;
    inputs.forEach(input => {
        if (!input.checkValidity()) {
            valid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    form.classList.add('was-validated');
    return valid;
}

function updateConfirmation() {
    document.getElementById('confirm-nome').textContent = 
        document.getElementById('nome').value || '-';
    document.getElementById('confirm-email').textContent = 
        document.getElementById('email').value || '-';
    document.getElementById('confirm-telefone').textContent = 
        document.getElementById('telefone').value || 'Não informado';
    document.getElementById('confirm-cpf').textContent = 
        document.getElementById('cpf').value || 'Não informado';
    document.getElementById('confirm-assunto').textContent = 
        document.getElementById('assunto').value || '-';
    document.getElementById('confirm-descricao').textContent = 
        document.getElementById('descricao').value.substring(0, 100) + 
        (document.getElementById('descricao').value.length > 100 ? '...' : '') || '-';
    
    const formaResposta = document.querySelector('input[name="forma_resposta"]:checked');
    document.getElementById('confirm-resposta').textContent = 
        formaResposta ? (formaResposta.value === 'email' ? 'Email' : 'Consulta no Sistema') : '-';
}

// Form submission
document.getElementById('pedidoForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    
    // Reset button if form has errors (will be handled by page reload)
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }, 5000);
});

// CPF mask
document.getElementById('cpf').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
});

// Phone mask
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
    e.target.value = value;
});
</script>