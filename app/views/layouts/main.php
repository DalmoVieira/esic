<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema E-SIC' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #1565c0;
            --accent-color: #ff6f00;
            --success-color: #2e7d32;
            --warning-color: #f57c00;
            --danger-color: #d32f2f;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.25rem;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
        }
        
        .card-hover {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 40px 0 20px 0;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        
        .nav-link.active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
        }
        
        .protocol-display {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 1.1em;
            color: var(--primary-color);
        }
        
        .deadline-warning {
            color: var(--warning-color);
            font-weight: bold;
        }
        
        .deadline-danger {
            color: var(--danger-color);
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }
            
            .sidebar {
                min-height: auto;
            }
        }
        
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Custom animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form enhancements */
        .form-floating label {
            color: #6c757d;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
        }
        
        /* Alert styles */
        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 500;
        }
        
        .alert-dismissible .btn-close {
            padding: 1rem 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= url('/') ?>">
                <i class="bi bi-file-text-fill me-2"></i>
                Sistema E-SIC
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentUrl === '/' ? 'active' : '' ?>" href="<?= url('/') ?>">
                            <i class="bi bi-house-fill me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/pedido') === 0 ? 'active' : '' ?>" href="<?= url('/pedido/novo') ?>">
                            <i class="bi bi-plus-circle-fill me-1"></i>Novo Pedido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentUrl === '/acompanhar' ? 'active' : '' ?>" href="<?= url('/acompanhar') ?>">
                            <i class="bi bi-search me-1"></i>Acompanhar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentUrl === '/transparencia' ? 'active' : '' ?>" href="<?= url('/transparencia') ?>">
                            <i class="bi bi-graph-up me-1"></i>Transparência
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn && isset($user)): ?>
                        <?php if (in_array($user['tipo'], ['administrador', 'operador'])): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/admin/dashboard') ?>">
                                    <i class="bi bi-speedometer2 me-1"></i>Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($user['nome']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/auth/login') ?>">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($messages) && !empty($messages)): ?>
        <div class="container mt-3">
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?= $message['type'] === 'success' ? 'check-circle-fill' : ($message['type'] === 'error' ? 'exclamation-triangle-fill' : 'info-circle-fill') ?> me-2"></i>
                    <?= htmlspecialchars($message['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-shrink-0">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema E-SIC</h5>
                    <p class="mb-2">Sistema Eletrônico do Serviço de Informação ao Cidadão</p>
                    <p class="mb-2">Implementação da Lei nº 12.527/2011 (Lei de Acesso à Informação)</p>
                </div>
                <div class="col-md-3">
                    <h6>Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/') ?>" class="text-light text-decoration-none">Início</a></li>
                        <li><a href="<?= url('/transparencia') ?>" class="text-light text-decoration-none">Transparência</a></li>
                        <li><a href="<?= url('/sobre') ?>" class="text-light text-decoration-none">Sobre a LAI</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Contato</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i><?= $config['contatos']['email'] ?? 'contato@orgao.gov.br' ?></li>
                        <li><i class="bi bi-telephone me-2"></i><?= $config['contatos']['telefone'] ?? '(XX) XXXX-XXXX' ?></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> Sistema E-SIC. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Desenvolvido em conformidade com a Lei de Acesso à Informação</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            form.classList.add('was-validated');
            return form.checkValidity();
        }

        // Protocol formatter
        function formatProtocol(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            if (value.length >= 9) {
                value = value.substring(0, 9) + '-' + value.substring(9, 13);
            }
            input.value = 'ESIC-' + value;
        }

        // Loading state helper
        function setLoading(buttonId, loading = true) {
            const button = document.getElementById(buttonId);
            if (!button) return;
            
            if (loading) {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || 'Enviar';
            }
        }

        // Copy to clipboard helper
        function copyToClipboard(text, element) {
            navigator.clipboard.writeText(text).then(function() {
                const originalText = element.innerHTML;
                element.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copiado!';
                element.classList.add('btn-success');
                element.classList.remove('btn-outline-primary');
                
                setTimeout(function() {
                    element.innerHTML = originalText;
                    element.classList.remove('btn-success');
                    element.classList.add('btn-outline-primary');
                }, 2000);
            });
        }

        // File upload preview
        function previewFiles(input) {
            const preview = document.getElementById(input.id + '-preview');
            if (!preview) return;
            
            preview.innerHTML = '';
            
            for (let file of input.files) {
                const div = document.createElement('div');
                div.className = 'alert alert-info d-flex justify-content-between align-items-center';
                div.innerHTML = `
                    <span><i class="bi bi-file-earmark me-2"></i>${file.name} (${formatFileSize(file.size)})</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('${input.id}', '${file.name}')">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                preview.appendChild(div);
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeFile(inputId, fileName) {
            // Implementation for removing files from input
            console.log('Remove file:', fileName, 'from', inputId);
        }
    </script>
</body>
</html>