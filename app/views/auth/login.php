<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
            <div class="w-100" style="max-width: 400px;">
                <!-- Logo and Title -->
                <div class="text-center mb-5">
                    <div class="mb-4">
                        <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-2">Acesso ao Sistema</h2>
                    <p class="text-muted">Entre com suas credenciais para acessar</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" method="POST" action="<?= url('/auth/login') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="email" class="form-control form-control-lg" 
                                   id="email" name="email" 
                                   value="<?= htmlspecialchars($old_data['email'] ?? '') ?>"
                                   required>
                            <label for="email">
                                <i class="bi bi-envelope me-2"></i>Email
                            </label>
                            <div class="invalid-feedback">
                                Por favor, informe um email válido.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="password" class="form-control form-control-lg" 
                                   id="senha" name="senha" required>
                            <label for="senha">
                                <i class="bi bi-lock me-2"></i>Senha
                            </label>
                            <div class="invalid-feedback">
                                Por favor, informe sua senha.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lembrar" name="lembrar" value="1">
                            <label class="form-check-label" for="lembrar">
                                Lembrar de mim
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Entrar
                        </button>
                    </div>
                </form>
                
                <!-- OAuth Login -->
                <div class="text-center mb-4">
                    <div class="position-relative">
                        <hr>
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">
                            ou entre com
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
                
                <!-- Links -->
                <div class="text-center">
                    <a href="<?= url('/auth/recuperar-senha') ?>" 
                       class="text-decoration-none me-3">
                        Esqueceu a senha?
                    </a>
                    <a href="<?= url('/auth/registro') ?>" 
                       class="text-decoration-none">
                        Criar conta
                    </a>
                </div>
                
                <!-- Public Access -->
                <div class="mt-5 pt-4 border-top">
                    <div class="text-center">
                        <p class="text-muted mb-3">Acesso público</p>
                        <div class="d-grid gap-2">
                            <a href="<?= url('/pedido/novo') ?>" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                Fazer Pedido de Informação
                            </a>
                            <a href="<?= url('/pedido/consultar') ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-search me-2"></i>
                                Consultar Pedido
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Information -->
        <div class="col-lg-6 bg-primary d-none d-lg-flex align-items-center justify-content-center text-white p-5">
            <div class="text-center">
                <div class="mb-5">
                    <i class="bi bi-info-circle-fill" style="font-size: 5rem; opacity: 0.8;"></i>
                </div>
                
                <h1 class="fw-bold mb-4">Sistema E-SIC</h1>
                <h4 class="mb-4 opacity-75">Serviço de Informação ao Cidadão</h4>
                
                <div class="row text-center mb-5">
                    <div class="col-4">
                        <div class="mb-3">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6>24/7</h6>
                        <p class="small opacity-75">Disponível sempre</p>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <i class="bi bi-shield-check" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6>Seguro</h6>
                        <p class="small opacity-75">Dados protegidos</p>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <i class="bi bi-speedometer2" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6>Rápido</h6>
                        <p class="small opacity-75">Resposta em 20 dias</p>
                    </div>
                </div>
                
                <blockquote class="blockquote">
                    <p class="mb-0 opacity-75">
                        "O acesso à informação é um direito fundamental 
                        do cidadão e dever do Estado."
                    </p>
                    <footer class="blockquote-footer mt-2">
                        <cite title="Lei 12.527/2011">Lei de Acesso à Informação</cite>
                    </footer>
                </blockquote>
                
                <div class="mt-5">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border border-white border-opacity-25 rounded p-3 h-100">
                                <h3 class="fw-bold"><?= number_format($stats['total_pedidos'] ?? 0) ?></h3>
                                <p class="mb-0 small opacity-75">Pedidos atendidos</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-white border-opacity-25 rounded p-3 h-100">
                                <h3 class="fw-bold"><?= number_format($stats['tempo_medio_resposta'] ?? 15) ?></h3>
                                <p class="mb-0 small opacity-75">Dias médios de resposta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.form-floating > label {
    opacity: 0.7;
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    opacity: 1;
}

@media (max-width: 991.98px) {
    .container-fluid {
        background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%);
    }
    
    .col-lg-6:first-child {
        background: white;
        margin: 2rem;
        border-radius: 1rem;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
}

.position-relative hr {
    margin: 1rem 0;
}

.blockquote {
    border-left: 3px solid rgba(255,255,255,0.3);
    padding-left: 1rem;
}

.border-white {
    transition: all 0.3s ease;
}

.border-white:hover {
    background-color: rgba(255,255,255,0.1);
}
</style>

<script>
// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const form = this;
    const email = document.getElementById('email');
    const senha = document.getElementById('senha');
    
    let valid = true;
    
    // Reset validation states
    form.querySelectorAll('.form-control').forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
    
    // Email validation
    if (!email.value.trim() || !email.checkValidity()) {
        email.classList.add('is-invalid');
        valid = false;
    } else {
        email.classList.add('is-valid');
    }
    
    // Password validation
    if (!senha.value.trim()) {
        senha.classList.add('is-invalid');
        valid = false;
    } else {
        senha.classList.add('is-valid');
    }
    
    if (!valid) {
        e.preventDefault();
    } else {
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Entrando...';
        
        // Reset button after some time if form submission fails
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }, 5000);
    }
});

// Real-time validation
document.getElementById('email').addEventListener('input', function() {
    this.classList.remove('is-invalid', 'is-valid');
    if (this.value.trim() && this.checkValidity()) {
        this.classList.add('is-valid');
    }
});

document.getElementById('senha').addEventListener('input', function() {
    this.classList.remove('is-invalid', 'is-valid');
    if (this.value.trim()) {
        this.classList.add('is-valid');
    }
});

// Auto-focus on email field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});
</script>