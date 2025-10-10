<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin - Sistema E-SIC' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --admin-primary: #1a365d;
            --admin-secondary: #2d3748;
            --admin-accent: #3182ce;
            --sidebar-width: 250px;
        }
        
        body {
            font-size: 0.875rem;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            width: var(--sidebar-width);
            background-color: var(--admin-secondary);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar .nav-link {
            color: #adb5bd;
            font-weight: 500;
            padding: 0.75rem 1rem;
            margin: 0.125rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--admin-accent);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 16px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
        }
        
        .main-content.sidebar-open {
            margin-left: var(--sidebar-width);
        }
        
        .navbar-admin {
            background-color: var(--admin-primary) !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .content-header {
            background-color: #fff;
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }
        
        .stats-card-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stats-card-warning {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .stats-card-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .stats-card-info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
        
        .card-custom {
            border-radius: 15px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .table-admin {
            background-color: #fff;
        }
        
        .table-admin thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        
        .btn-admin-primary {
            background-color: var(--admin-accent);
            border-color: var(--admin-accent);
            color: #fff;
        }
        
        .btn-admin-primary:hover {
            background-color: #2c5aa0;
            border-color: #2c5aa0;
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        @media (min-width: 768px) {
            .sidebar {
                position: relative;
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
            }
            
            .sidebar-toggle {
                display: none;
            }
        }
        
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar bg-dark" id="sidebar">
        <div class="position-sticky">
            <div class="px-3 py-3">
                <h6 class="text-muted text-uppercase fs-6 fw-bold mb-3">Menu Principal</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/dashboard') === 0 ? 'active' : '' ?>" href="<?= url('/admin/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/pedidos') === 0 ? 'active' : '' ?>" href="<?= url('/admin/pedidos') ?>">
                            <i class="bi bi-file-earmark-text"></i>Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/recursos') === 0 ? 'active' : '' ?>" href="<?= url('/admin/recursos') ?>">
                            <i class="bi bi-arrow-repeat"></i>Recursos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/relatorios') === 0 ? 'active' : '' ?>" href="<?= url('/admin/relatorios') ?>">
                            <i class="bi bi-graph-up"></i>Relatórios
                        </a>
                    </li>
                </ul>
                
                <?php if (isset($user) && $user['tipo'] === 'administrador'): ?>
                <h6 class="text-muted text-uppercase fs-6 fw-bold mb-3 mt-4">Administração</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/usuarios') === 0 ? 'active' : '' ?>" href="<?= url('/admin/usuarios') ?>">
                            <i class="bi bi-people"></i>Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentUrl, '/admin/configuracoes') === 0 ? 'active' : '' ?>" href="<?= url('/admin/configuracoes') ?>">
                            <i class="bi bi-gear"></i>Configurações
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
                
                <h6 class="text-muted text-uppercase fs-6 fw-bold mb-3 mt-4">Sistema</h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>Ver Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i>Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-admin">
            <div class="container-fluid">
                <button class="btn btn-outline-light sidebar-toggle me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                
                <a class="navbar-brand" href="<?= url('/admin/dashboard') ?>">
                    <i class="bi bi-shield-check me-2"></i>Admin E-SIC
                </a>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2 fs-5"></i>
                            <span><?= htmlspecialchars($user['nome'] ?? 'Usuário') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">
                                <?= htmlspecialchars($user['email'] ?? '') ?>
                            </h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/') ?>" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Ver Site Público
                            </a></li>
                            <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>">
                                <i class="bi bi-box-arrow-right me-2"></i>Sair
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <?php if (isset($messages) && !empty($messages)): ?>
            <div class="container-fluid mt-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?= $message['type'] === 'success' ? 'check-circle-fill' : ($message['type'] === 'error' ? 'exclamation-triangle-fill' : 'info-circle-fill') ?> me-2"></i>
                        <?= htmlspecialchars($message['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="container-fluid py-4">
            <?= $content ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('show');
                mainContent.classList.toggle('sidebar-open');
            }
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Initialize sidebar state for desktop
            if (window.innerWidth >= 768) {
                document.getElementById('mainContent').classList.add('sidebar-open');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                mainContent.classList.add('sidebar-open');
            } else {
                mainContent.classList.remove('sidebar-open');
            }
        });

        // Status badge helper
        function getStatusBadge(status) {
            const badges = {
                'pendente': '<span class="badge badge-status bg-warning">Pendente</span>',
                'em_andamento': '<span class="badge badge-status bg-info">Em Andamento</span>',
                'deferido': '<span class="badge badge-status bg-success">Deferido</span>',
                'indeferido': '<span class="badge badge-status bg-danger">Indeferido</span>',
                'parcialmente_deferido': '<span class="badge badge-status bg-primary">Parcialmente Deferido</span>'
            };
            return badges[status] || '<span class="badge badge-status bg-secondary">Desconhecido</span>';
        }

        // Date formatter
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Deadline checker
        function checkDeadline(deadlineString) {
            const deadline = new Date(deadlineString);
            const now = new Date();
            const diffTime = deadline - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays < 0) {
                return `<span class="deadline-danger">Vencido há ${Math.abs(diffDays)} dia(s)</span>`;
            } else if (diffDays <= 3) {
                return `<span class="deadline-warning">Vence em ${diffDays} dia(s)</span>`;
            } else {
                return `<span class="text-success">Vence em ${diffDays} dia(s)</span>`;
            }
        }

        // Loading overlay
        function showLoading(containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                const overlay = document.createElement('div');
                overlay.className = 'loading-overlay';
                overlay.id = containerId + '-loading';
                overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>';
                container.style.position = 'relative';
                container.appendChild(overlay);
            }
        }

        function hideLoading(containerId) {
            const loading = document.getElementById(containerId + '-loading');
            if (loading) {
                loading.remove();
            }
        }

        // Confirm actions
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }

        // Export functions
        function exportData(format, url) {
            showLoading('main-content');
            window.location.href = url + '?format=' + format;
            setTimeout(() => hideLoading('main-content'), 2000);
        }
    </script>
</body>
</html>