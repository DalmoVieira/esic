<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Recuperar Senha'; ?> - Sistema E-SIC</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-key fa-3x text-primary mb-3"></i>
                            <h3 class="fw-bold">Recuperar Senha</h3>
                            <p class="text-muted">Digite seu email para receber instruções de recuperação</p>
                        </div>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/auth/forgot-password" novalidate>
                            <input type="hidden" name="<?php echo $_ENV['CSRF_TOKEN_NAME'] ?? '_token'; ?>" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control form-control-lg" 
                                    id="email" 
                                    name="email" 
                                    placeholder="Digite seu email"
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                    required
                                    autocomplete="email"
                                >
                                <div class="invalid-feedback">
                                    Por favor, digite um email válido.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Enviar Instruções
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
                        <i class="fas fa-info-circle me-2"></i>
                        As instruções serão enviadas para o email cadastrado
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação de formulário
        (function() {
            'use strict';
            
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        })();
    </script>
</body>
</html>