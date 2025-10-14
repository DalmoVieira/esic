<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-person-plus me-2"></i>
            Novo Usuário
        </h1>
        <p class="text-muted mb-0">Cadastrar novo usuário do sistema</p>
    </div>
    <div>
        <a href="<?= url('/admin/usuarios') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Voltar à Lista
        </a>
    </div>
</div>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= url('/admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= url('/admin/usuarios') ?>">Usuários</a></li>
        <li class="breadcrumb-item active">Novo Usuário</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Main Form -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Dados do Usuário
                </h5>
            </div>
            <div class="card-body">
                <form id="usuarioForm" method="POST" action="/admin/usuarios/create" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    
                    <!-- Seção: Dados Pessoais -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-person me-2"></i>
                            Dados Pessoais
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">
                                    Nome Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       required maxlength="255" 
                                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="cpf" class="form-label">
                                    CPF <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="cpf" name="cpf" 
                                       required pattern="[0-9]{3}\.?[0-9]{3}\.?[0-9]{3}-?[0-9]{2}"
                                       value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    E-mail <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       required maxlength="255"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" 
                                       maxlength="20"
                                       value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Foto do Perfil</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" 
                                   accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">Formatos aceitos: JPG, JPEG, PNG. Tamanho máximo: 2MB.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <!-- Seção: Dados do Sistema -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-gear me-2"></i>
                            Dados do Sistema
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="usuario_login" class="form-label">
                                    Nome de Usuário <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="usuario_login" name="usuario_login" 
                                       required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_.-]+"
                                       value="<?= htmlspecialchars($_POST['usuario_login'] ?? '') ?>">
                                <div class="form-text">Apenas letras, números, pontos, hífens e underscores.</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    Perfil de Acesso <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Selecione o perfil...</option>
                                    <option value="usuario" <?= ($_POST['role'] ?? '') === 'usuario' ? 'selected' : '' ?>>
                                        Usuário - Acesso básico
                                    </option>
                                    <option value="operador" <?= ($_POST['role'] ?? '') === 'operador' ? 'selected' : '' ?>>
                                        Operador - Visualizar e responder pedidos
                                    </option>
                                    <option value="analista" <?= ($_POST['role'] ?? '') === 'analista' ? 'selected' : '' ?>>
                                        Analista - Gerenciar pedidos e recursos
                                    </option>
                                    <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                        Administrador - Acesso completo
                                    </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="senha" class="form-label">
                                    Senha <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="senha" name="senha" 
                                           required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('senha')">
                                        <i class="bi bi-eye" id="senha-icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Mínimo 6 caracteres.</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirmar_senha" class="form-label">
                                    Confirmar Senha <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_senha" 
                                           name="confirmar_senha" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirmar_senha')">
                                        <i class="bi bi-eye" id="confirmar_senha-icon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="setor" class="form-label">Setor/Departamento</label>
                                <select class="form-select" id="setor" name="setor">
                                    <option value="">Selecione o setor...</option>
                                    <option value="Administração" <?= ($_POST['setor'] ?? '') === 'Administração' ? 'selected' : '' ?>>
                                        Administração
                                    </option>
                                    <option value="Recursos Humanos" <?= ($_POST['setor'] ?? '') === 'Recursos Humanos' ? 'selected' : '' ?>>
                                        Recursos Humanos
                                    </option>
                                    <option value="Financeiro" <?= ($_POST['setor'] ?? '') === 'Financeiro' ? 'selected' : '' ?>>
                                        Financeiro
                                    </option>
                                    <option value="Jurídico" <?= ($_POST['setor'] ?? '') === 'Jurídico' ? 'selected' : '' ?>>
                                        Jurídico
                                    </option>
                                    <option value="TI" <?= ($_POST['setor'] ?? '') === 'TI' ? 'selected' : '' ?>>
                                        Tecnologia da Informação
                                    </option>
                                    <option value="Transparência" <?= ($_POST['setor'] ?? '') === 'Transparência' ? 'selected' : '' ?>>
                                        Transparência e Acesso à Informação
                                    </option>
                                    <option value="Outro" <?= ($_POST['setor'] ?? '') === 'Outro' ? 'selected' : '' ?>>
                                        Outro
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="cargo" class="form-label">Cargo</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" 
                                       maxlength="100"
                                       value="<?= htmlspecialchars($_POST['cargo'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção: Configurações -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-toggles me-2"></i>
                            Configurações
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ativo" 
                                           name="ativo" value="1" checked>
                                    <label class="form-check-label" for="ativo">
                                        <strong>Usuário Ativo</strong><br>
                                        <small class="text-muted">Usuário pode fazer login no sistema</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notificacoes_email" 
                                           name="notificacoes_email" value="1" checked>
                                    <label class="form-check-label" for="notificacoes_email">
                                        <strong>Notificações por E-mail</strong><br>
                                        <small class="text-muted">Receber notificações por e-mail</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="alterar_senha_proximo_login" 
                                           name="alterar_senha_proximo_login" value="1">
                                    <label class="form-check-label" for="alterar_senha_proximo_login">
                                        <strong>Forçar Troca de Senha</strong><br>
                                        <small class="text-muted">Usuário deve alterar senha no próximo login</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enviar_credenciais" 
                                           name="enviar_credenciais" value="1" checked>
                                    <label class="form-check-label" for="enviar_credenciais">
                                        <strong>Enviar Credenciais por E-mail</strong><br>
                                        <small class="text-muted">Enviar login e senha por e-mail</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="limparFormulario()">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Limpar
                        </button>
                        <a href="<?= url('/admin/usuarios') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-person-plus me-1"></i>
                            Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Perfis de Acesso
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Usuário</h6>
                    <p class="small mb-0">Acesso básico apenas para consultar informações próprias.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-info">Operador</h6>
                    <p class="small mb-0">Pode visualizar e responder pedidos de informação.</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-warning">Analista</h6>
                    <p class="small mb-0">Gerencia pedidos, recursos e pode gerar relatórios.</p>
                </div>
                
                <div class="mb-0">
                    <h6 class="text-danger">Administrador</h6>
                    <p class="small mb-0">Acesso completo ao sistema, incluindo usuários e configurações.</p>
                </div>
            </div>
        </div>
        
        <!-- Password Strength -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check me-2"></i>
                    Segurança da Senha
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" id="passwordStrength" role="progressbar" 
                             style="width: 0%"></div>
                    </div>
                    <small class="text-muted" id="passwordStrengthText">Digite uma senha</small>
                </div>
                
                <div class="small">
                    <p class="mb-2"><strong>Recomendações:</strong></p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Mínimo 8 caracteres
                        </li>
                        <li class="mb-1">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Incluir letras maiúsculas e minúsculas
                        </li>
                        <li class="mb-1">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Incluir números
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-1"></i>
                            Incluir caracteres especiais
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="gerarSenhaAleatoria()">
                        <i class="bi bi-key me-1"></i>
                        Gerar Senha Aleatória
                    </button>
                    
                    <button class="btn btn-outline-info btn-sm" onclick="preencherDadosDemo()">
                        <i class="bi bi-magic me-1"></i>
                        Preencher Dados Demo
                    </button>
                    
                    <a href="<?= url('/admin/usuarios/import') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-upload me-1"></i>
                        Importar Usuários
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation and interaction
document.addEventListener('DOMContentLoaded', function() {
    // CPF mask
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        this.value = value;
    });
    
    // Phone mask
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
    
    // Username validation
    const usuarioLoginInput = document.getElementById('usuario_login');
    usuarioLoginInput.addEventListener('blur', function() {
        if (this.value) {
            checkUsernameAvailability(this.value);
        }
    });
    
    // Email validation
    const emailInput = document.getElementById('email');
    emailInput.addEventListener('blur', function() {
        if (this.value) {
            checkEmailAvailability(this.value);
        }
    });
    
    // Password strength
    const senhaInput = document.getElementById('senha');
    senhaInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        validatePasswordMatch();
    });
    
    // Confirm password validation
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    confirmarSenhaInput.addEventListener('input', validatePasswordMatch);
    
    // Form submission
    document.getElementById('usuarioForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });
});

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function checkPasswordStrength(password) {
    let strength = 0;
    let strengthText = 'Muito fraca';
    let strengthClass = 'bg-danger';
    
    if (password.length >= 6) strength += 20;
    if (password.length >= 8) strength += 20;
    if (/[a-z]/.test(password)) strength += 20;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/[0-9]/.test(password)) strength += 10;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 10;
    
    if (strength >= 80) {
        strengthText = 'Muito forte';
        strengthClass = 'bg-success';
    } else if (strength >= 60) {
        strengthText = 'Forte';
        strengthClass = 'bg-info';
    } else if (strength >= 40) {
        strengthText = 'Média';
        strengthClass = 'bg-warning';
    } else if (strength >= 20) {
        strengthText = 'Fraca';
        strengthClass = 'bg-warning';
    }
    
    const progressBar = document.getElementById('passwordStrength');
    const progressText = document.getElementById('passwordStrengthText');
    
    progressBar.style.width = strength + '%';
    progressBar.className = 'progress-bar ' + strengthClass;
    progressText.textContent = strengthText;
}

function validatePasswordMatch() {
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    
    if (confirmarSenha && senha !== confirmarSenha) {
        confirmarSenhaInput.classList.add('is-invalid');
        confirmarSenhaInput.nextElementSibling.textContent = 'As senhas não coincidem';
        return false;
    } else {
        confirmarSenhaInput.classList.remove('is-invalid');
        confirmarSenhaInput.nextElementSibling.textContent = '';
        return true;
    }
}

function checkUsernameAvailability(username) {
    fetch('/admin/usuarios/check-username', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({username: username})
    })
    .then(response => response.json())
    .then(data => {
        const input = document.getElementById('usuario_login');
        if (!data.available) {
            input.classList.add('is-invalid');
            input.nextElementSibling.nextElementSibling.textContent = 'Nome de usuário já existe';
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            input.nextElementSibling.nextElementSibling.textContent = '';
        }
    });
}

function checkEmailAvailability(email) {
    fetch('/admin/usuarios/check-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({email: email})
    })
    .then(response => response.json())
    .then(data => {
        const input = document.getElementById('email');
        if (!data.available) {
            input.classList.add('is-invalid');
            input.nextElementSibling.textContent = 'E-mail já está em uso';
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            input.nextElementSibling.textContent = '';
        }
    });
}

function validateCpf(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    remainder = 11 - (sum % 11);
    if (remainder === 10 || remainder === 11) remainder = 0;
    
    return remainder === parseInt(cpf.charAt(10));
}

function validateForm() {
    let valid = true;
    
    // Clear all previous validation
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    // Validate required fields
    const requiredFields = ['nome', 'cpf', 'email', 'usuario_login', 'role', 'senha', 'confirmar_senha'];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            field.nextElementSibling.textContent = 'Campo obrigatório';
            valid = false;
        }
    });
    
    // Validate CPF
    const cpfField = document.getElementById('cpf');
    if (cpfField.value && !validateCpf(cpfField.value)) {
        cpfField.classList.add('is-invalid');
        cpfField.nextElementSibling.textContent = 'CPF inválido';
        valid = false;
    }
    
    // Validate password match
    if (!validatePasswordMatch()) {
        valid = false;
    }
    
    return valid;
}

function gerarSenhaAleatoria() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let password = '';
    
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    document.getElementById('senha').value = password;
    document.getElementById('confirmar_senha').value = password;
    
    checkPasswordStrength(password);
    validatePasswordMatch();
}

function preencherDadosDemo() {
    if (confirm('Deseja preencher o formulário com dados de demonstração?')) {
        document.getElementById('nome').value = 'João da Silva Santos';
        document.getElementById('cpf').value = '123.456.789-00';
        document.getElementById('email').value = 'joao.santos@exemplo.com';
        document.getElementById('telefone').value = '(11) 99999-9999';
        document.getElementById('usuario_login').value = 'joao.santos';
        document.getElementById('role').value = 'operador';
        document.getElementById('setor').value = 'Administração';
        document.getElementById('cargo').value = 'Assistente Administrativo';
        
        gerarSenhaAleatoria();
    }
}

function limparFormulario() {
    if (confirm('Deseja limpar todos os campos do formulário?')) {
        document.getElementById('usuarioForm').reset();
        
        // Clear validation classes
        document.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
        
        // Reset password strength
        document.getElementById('passwordStrength').style.width = '0%';
        document.getElementById('passwordStrengthText').textContent = 'Digite uma senha';
    }
}
</script>

<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.is-valid {
    border-color: #198754;
}

.is-invalid {
    border-color: #dc3545;
}

.progress {
    background-color: #e9ecef;
}

.avatar-preview {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #dee2e6;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .d-flex.justify-content-end {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .d-flex.justify-content-end .btn {
        width: 100%;
    }
}
</style>