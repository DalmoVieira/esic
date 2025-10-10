<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="mb-4">
                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                </div>
                <h1 class="fw-bold text-primary mb-3">Criar Conta</h1>
                <p class="lead text-muted">
                    Registre-se para gerenciar seus pedidos de informação
                </p>
            </div>

            <!-- Registration Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <form id="registroForm" method="POST" action="<?= url('/auth/registro') ?>" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-person me-2"></i>
                                Dados Pessoais
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?= htmlspecialchars($old_data['nome'] ?? '') ?>" required>
                                        <label for="nome">Nome Completo *</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe seu nome completo.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="cpf" name="cpf" 
                                               value="<?= htmlspecialchars($old_data['cpf'] ?? '') ?>">
                                        <label for="cpf">CPF</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe um CPF válido.
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
                                        <select class="form-select" id="tipo_pessoa" name="tipo_pessoa" required>
                                            <option value="">Selecione...</option>
                                            <option value="fisica" <?= ($old_data['tipo_pessoa'] ?? '') === 'fisica' ? 'selected' : '' ?>>
                                                Pessoa Física
                                            </option>
                                            <option value="juridica" <?= ($old_data['tipo_pessoa'] ?? '') === 'juridica' ? 'selected' : '' ?>>
                                                Pessoa Jurídica
                                            </option>
                                        </select>
                                        <label for="tipo_pessoa">Tipo de Pessoa *</label>
                                        <div class="invalid-feedback">
                                            Por favor, selecione o tipo de pessoa.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-key me-2"></i>
                                Dados da Conta
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?= htmlspecialchars($old_data['email'] ?? '') ?>" required>
                                        <label for="email">Email *</label>
                                        <div class="invalid-feedback">
                                            Por favor, informe um email válido.
                                        </div>
                                        <div class="form-text">
                                            Este será seu login e onde enviaremos as notificações
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="senha" name="senha" 
                                               minlength="8" required>
                                        <label for="senha">Senha *</label>
                                        <div class="invalid-feedback">
                                            A senha deve ter pelo menos 8 caracteres.
                                        </div>
                                    </div>
                                    
                                    <!-- Password Strength Indicator -->
                                    <div class="mt-2">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" id="password-strength" 
                                                 style="width: 0%" role="progressbar"></div>
                                        </div>
                                        <small class="text-muted" id="password-help">
                                            Mínimo 8 caracteres
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="confirmar_senha" 
                                               name="confirmar_senha" required>
                                        <label for="confirmar_senha">Confirmar Senha *</label>
                                        <div class="invalid-feedback">
                                            As senhas não coincidem.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Privacy -->
                        <div class="mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="aceito_termos" 
                                               name="aceito_termos" required>
                                        <label class="form-check-label" for="aceito_termos">
                                            Aceito os <a href="<?= url('/termos') ?>" target="_blank" class="text-decoration-none">
                                                Termos de Uso
                                            </a> e a <a href="<?= url('/privacidade') ?>" target="_blank" class="text-decoration-none">
                                                Política de Privacidade
                                            </a> *
                                        </label>
                                        <div class="invalid-feedback">
                                            Você deve aceitar os termos para continuar.
                                        </div>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="aceito_newsletter" 
                                               name="aceito_newsletter" value="1">
                                        <label class="form-check-label" for="aceito_newsletter">
                                            Desejo receber atualizações e notificações por email
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>
                                Criar Conta
                            </button>
                        </div>
                    </form>
                    
                    <!-- OAuth Registration -->
                    <div class="text-center mb-4">
                        <div class="position-relative">
                            <hr>
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                                ou registre-se com
                            </span>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <a href="<?= url('/auth/oauth/google') ?>" 
                               class="btn btn-outline-danger w-100">
                                <i class="bi bi-google me-2"></i>
                                Google
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= url('/auth/oauth/govbr') ?>" 
                               class="btn btn-outline-success w-100">
                                <i class="bi bi-shield-check me-2"></i>
                                Gov.br
                            </a>
                        </div>
                    </div>
                    
                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-muted">
                            Já possui uma conta? 
                            <a href="<?= url('/auth/login') ?>" class="text-decoration-none">
                                Faça login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Benefits Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-star me-2"></i>
                        Vantagens de ter uma conta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Acompanhe todos os seus pedidos
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Receba notificações automáticas
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Histórico completo de solicitações
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Dados salvos para próximos pedidos
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Interpor recursos facilmente
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    Painel personalizado
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-floating > label {
    opacity: 0.7;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select:focus ~ label,
.form-floating > .form-select:not([value=""]) ~ label {
    opacity: 1;
}

.progress-bar {
    transition: all 0.3s ease;
}

.password-weak { background-color: #dc3545 !important; }
.password-fair { background-color: #fd7e14 !important; }
.password-good { background-color: #ffc107 !important; }
.password-strong { background-color: #198754 !important; }

@media (max-width: 768px) {
    .card-body {
        padding: 2rem 1.5rem !important;
    }
}
</style>

<script>
// Form validation
document.getElementById('registroForm').addEventListener('submit', function(e) {
    const form = this;
    let valid = true;
    
    // Reset validation states
    form.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
    
    // Required fields validation
    const requiredFields = ['nome', 'email', 'senha', 'confirmar_senha', 'tipo_pessoa'];
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim() || !field.checkValidity()) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.add('is-valid');
        }
    });
    
    // CPF validation (if provided)
    const cpf = document.getElementById('cpf');
    if (cpf.value.trim() && !isValidCPF(cpf.value)) {
        cpf.classList.add('is-invalid');
        valid = false;
    } else if (cpf.value.trim()) {
        cpf.classList.add('is-valid');
    }
    
    // Password confirmation
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');
    if (senha.value !== confirmarSenha.value) {
        confirmarSenha.classList.add('is-invalid');
        valid = false;
    }
    
    // Terms acceptance
    const aceitoTermos = document.getElementById('aceito_termos');
    if (!aceitoTermos.checked) {
        aceitoTermos.classList.add('is-invalid');
        valid = false;
    }
    
    if (!valid) {
        e.preventDefault();
    } else {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando conta...';
        
        // Reset button after some time if form submission fails
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }, 5000);
    }
});

// Password strength indicator
document.getElementById('senha').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('password-strength');
    const helpText = document.getElementById('password-help');
    
    let strength = 0;
    let feedback = '';
    
    // Length check
    if (password.length >= 8) strength += 25;
    else feedback = 'Mínimo 8 caracteres';
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength += 25;
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength += 25;
    
    // Number or special character check
    if (/[\d\W]/.test(password)) strength += 25;
    
    // Update progress bar
    strengthBar.style.width = strength + '%';
    strengthBar.className = 'progress-bar';
    
    if (strength < 50) {
        strengthBar.classList.add('password-weak');
        if (!feedback) feedback = 'Senha fraca';
    } else if (strength < 75) {
        strengthBar.classList.add('password-fair');
        if (!feedback) feedback = 'Senha razoável';
    } else if (strength < 100) {
        strengthBar.classList.add('password-good');
        if (!feedback) feedback = 'Senha boa';
    } else {
        strengthBar.classList.add('password-strong');
        if (!feedback) feedback = 'Senha forte';
    }
    
    helpText.textContent = feedback;
    
    // Validation state
    if (password.length >= 8) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else {
        this.classList.remove('is-valid');
    }
});

// Confirm password validation
document.getElementById('confirmar_senha').addEventListener('input', function() {
    const senha = document.getElementById('senha').value;
    if (this.value && this.value === senha) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else if (this.value) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
    }
});

// CPF mask and validation
document.getElementById('cpf').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
    
    // Validate CPF
    if (value.length === 14) {
        if (isValidCPF(value)) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    } else {
        this.classList.remove('is-valid', 'is-invalid');
    }
});

// Phone mask
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
    e.target.value = value;
});

// Email validation
document.getElementById('email').addEventListener('input', function() {
    if (this.value.trim() && this.checkValidity()) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    } else if (this.value.trim()) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
    }
});

// CPF validation function
function isValidCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let digit1 = 11 - (sum % 11);
    if (digit1 === 10 || digit1 === 11) digit1 = 0;
    
    if (parseInt(cpf.charAt(9)) !== digit1) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    let digit2 = 11 - (sum % 11);
    if (digit2 === 10 || digit2 === 11) digit2 = 0;
    
    return parseInt(cpf.charAt(10)) === digit2;
}

// Auto-focus on first field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nome').focus();
});
</script>