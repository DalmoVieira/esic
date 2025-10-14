<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Redefinir Senha'; ?> - Sistema E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 8px;
            transition: all 0.3s;
        }
        .password-strength.weak { background: #dc3545; }
        .password-strength.medium { background: #ffc107; }
        .password-strength.strong { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Redefinir Senha</h3>
                            <p class="text-muted">Digite sua nova senha</p>
                        </div>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/auth/reset-password/<?php echo htmlspecialchars($token); ?>" novalidate>
                            <input type="hidden" name="<?php echo $_ENV['CSRF_TOKEN_NAME'] ?? '_token'; ?>" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-key me-2"></i>Nova Senha
                                </label>
                                <div class="position-relative">
                                    <input 
                                        type="password" 
                                        class="form-control form-control-lg" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Digite sua nova senha"
                                        required
                                        minlength="6"
                                        autocomplete="new-password"
                                    >
                                    <button type="button" class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2 border-0" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <div class="form-text">
                                    <small>A senha deve ter pelo menos 6 caracteres</small>
                                </div>
                                <div class="invalid-feedback">
                                    A senha deve ter pelo menos 6 caracteres.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="fas fa-check-double me-2"></i>Confirmar Senha
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Confirme sua nova senha"
                                    required
                                    autocomplete="new-password"
                                >
                                <div class="invalid-feedback">
                                    As senhas devem ser iguais.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    Redefinir Senha
                                </button>
                            </div>

                            <div class="text-center">
                                <div class="row">
                                    <div class="col">
                                        <a href="/auth/login" class="text-decoration-none">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Voltar ao Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-white">
                        <i class="fas fa-shield-alt me-2"></i>
                        Sua nova senha será criptografada de forma segura
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            strengthBar.classList.remove('weak', 'medium', 'strong');
            
            if (password.length === 0) {
                strengthBar.style.width = '0%';
            } else if (strength <= 2) {
                strengthBar.classList.add('weak');
                strengthBar.style.width = '33%';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                strengthBar.style.width = '66%';
            } else {
                strengthBar.classList.add('strong');
                strengthBar.style.width = '100%';
            }
        });

        // Form validation
        (function() {
            'use strict';
            
            const form = document.querySelector('form');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            
            function validatePasswords() {
                const isValid = passwordInput.value === confirmPasswordInput.value && passwordInput.value.length >= 6;
                
                if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('As senhas não coincidem');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
                
                return isValid;
            }
            
            passwordInput.addEventListener('input', validatePasswords);
            confirmPasswordInput.addEventListener('input', validatePasswords);
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity() || !validatePasswords()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        })();
    </script>
</body>
</html>