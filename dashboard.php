<?php
session_start();

// Verificar se veio com parâmetro de tipo
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';

// Se não tiver tipo, redirecionar para login
if (empty($tipo_usuario)) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-SIC Rio Claro</title>
    <meta name="description" content="Sistema Eletrônico de Informações ao Cidadão - Prefeitura Municipal de Rio Claro - RJ">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <?php if ($tipo_usuario !== 'anonimo'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="novo-pedido-v2.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-plus-circle"></i> Novo Pedido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="acompanhar-v2.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-search"></i> Acompanhar
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="transparencia.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-eye"></i> Transparência
                        </a>
                    </li>
                    <?php if ($tipo_usuario === 'administrador' || $tipo_usuario === 'funcionario'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> Gerenciar
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-text"></i> Pedidos</a></li>
                            <?php if ($tipo_usuario === 'administrador'): ?>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-people"></i> Usuários</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-bar-chart"></i> Relatórios</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <span id="nomeUsuario">
                                <?php 
                                switch($tipo_usuario) {
                                    case 'cidadao': echo 'Cidadão'; break;
                                    case 'funcionario': echo 'Funcionário'; break;
                                    case 'administrador': echo 'Administrador'; break;
                                    case 'anonimo': echo 'Visitante'; break;
                                    default: echo 'Usuário'; break;
                                }
                                ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($tipo_usuario !== 'anonimo'): ?>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header com informações do usuário -->
    <div class="bg-white border-bottom">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1 text-primary">
                        <i class="bi bi-speedometer2"></i> Dashboard - 
                        <?php 
                        switch($tipo_usuario) {
                            case 'cidadao': echo 'Área do Cidadão'; break;
                            case 'funcionario': echo 'Área do Funcionário'; break;
                            case 'administrador': echo 'Área do Administrador'; break;
                            case 'anonimo': echo 'Acesso Público'; break;
                        }
                        ?>
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar"></i> <?= date('d/m/Y H:i:s') ?> • 
                        <i class="bi bi-building"></i> Prefeitura Municipal de Rio Claro - RJ
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-<?= $tipo_usuario === 'administrador' ? 'danger' : ($tipo_usuario === 'funcionario' ? 'warning' : 'primary') ?> fs-6">
                        <?= strtoupper($tipo_usuario === 'anonimo' ? 'visitante' : $tipo_usuario) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container mt-4">
        <!-- Alertas baseados no tipo de usuário -->
        <?php if ($tipo_usuario === 'anonimo'): ?>
        <div class="alert alert-info">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle me-2"></i>
                <div>
                    <strong>Acesso Anônimo</strong><br>
                    <small>Você está navegando sem identificação. Para fazer solicitações, faça login como cidadão.</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($tipo_usuario === 'cidadao'): ?>
        <div class="alert alert-success">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle me-2"></i>
                <div>
                    <strong>Bem-vindo, Cidadão!</strong><br>
                    <small>Você pode fazer solicitações de informações e acompanhar seus pedidos.</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cards de Ação Principal -->
        <div class="row">
            <?php if ($tipo_usuario !== 'anonimo'): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-plus-circle text-primary mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Nova Solicitação</h5>
                        <p class="card-text">Faça uma nova solicitação de informações públicas conforme a Lei de Acesso à Informação (LAI).</p>
                        <a href="novo-pedido-v2.php?tipo=<?= $tipo_usuario ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Fazer Solicitação
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-search text-info mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Acompanhar Pedido</h5>
                        <p class="card-text">Consulte o andamento da sua solicitação usando o número do protocolo e seu CPF ou CNPJ.</p>
                        <a href="acompanhar-v2.php?tipo=<?= $tipo_usuario ?>" class="btn btn-outline-success">
                            <i class="bi bi-search"></i> Acompanhar
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-eye text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Portal da Transparência</h5>
                        <p class="card-text">Acesse informações sobre gastos públicos, contratos e dados da administração municipal.</p>
                        <a href="transparencia.php?tipo=<?= $tipo_usuario ?>" class="btn btn-outline-success">
                            <i class="bi bi-eye"></i> Ver Transparência
                        </a>
                    </div>
                </div>
            </div>

            <?php if ($tipo_usuario === 'administrador' || $tipo_usuario === 'funcionario'): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-file-text text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Gerenciar Pedidos</h5>
                        <p class="card-text">Visualize e gerencie as solicitações de informações dos cidadãos.</p>
                        <a href="#" class="btn btn-outline-warning">
                            <i class="bi bi-file-text"></i> Gerenciar
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($tipo_usuario === 'administrador'): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-bar-chart text-danger mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Relatórios</h5>
                        <p class="card-text">Acesse relatórios e estatísticas do sistema E-SIC.</p>
                        <a href="#" class="btn btn-outline-danger">
                            <i class="bi bi-bar-chart"></i> Relatórios
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 text-center card-hover">
                    <div class="card-body">
                        <i class="bi bi-people text-info mb-3" style="font-size: 3rem;"></i>
                        <h5 class="card-title">Gerenciar Usuários</h5>
                        <p class="card-text">Administre usuários do sistema e permissões de acesso.</p>
                        <a href="admin.php?tipo=administrador" class="btn btn-outline-info">
                            <i class="bi bi-people"></i> Usuários
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Estatísticas rápidas -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="text-primary mb-3">
                    <i class="bi bi-speedometer"></i> Estatísticas do Sistema
                </h5>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-file-text fs-2 mb-2"></i>
                        <h4>124</h4>
                        <small>Pedidos Totais</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-2 mb-2"></i>
                        <h4>89</h4>
                        <small>Atendidos</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-2 mb-2"></i>
                        <h4>23</h4>
                        <small>Em Andamento</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-week fs-2 mb-2"></i>
                        <h4>12</h4>
                        <small>Este Mês</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações sobre a LAI -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-primary">
                            <i class="bi bi-info-circle"></i> Lei de Acesso à Informação - LAI
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <p class="mb-2">A LAI (Lei nº 12.527/2011) garante o acesso às informações públicas e estabelece que:</p>
                                <ul class="mb-0">
                                    <li>Toda pessoa tem direito de receber informações dos órgãos públicos</li>
                                    <li>As informações devem ser fornecidas de forma gratuita</li>
                                    <li>O prazo de resposta é de até 20 dias (prorrogável por mais 10)</li>
                                    <li>O sigilo só é permitido em casos específicos previstos em lei</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-primary text-white rounded p-3">
                                    <i class="bi bi-shield-check fs-1 mb-2"></i>
                                    <h6>Transparência Garantida</h6>
                                    <small>Lei nº 12.527/2011</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h6>Prefeitura Municipal de Rio Claro - RJ</h6>
                    <p class="mb-2">
                        <i class="bi bi-geo-alt"></i> Av. João Baptista Portugal, 230<br>
                        <i class="bi bi-telephone"></i> (24) 99828-1427<br>
                        <i class="bi bi-envelope"></i> pmrc@rioclaro.rj.gov.br
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Links Úteis</h6>
                    <ul class="list-unstyled">
                        <li><a href="https://gpi-services.cloud.el.com.br/rj-rioclaro-pm/e-sic/" target="_blank" class="text-white-50">Sistema Oficial E-SIC</a></li>
                        <li><a href="#" class="text-white-50">Manual do E-SIC</a></li>
                        <li><a href="#" class="text-white-50">Perguntas Frequentes</a></li>
                        <li><a href="#" class="text-white-50">Legislação</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2025 Prefeitura Municipal de Rio Claro - RJ. Sistema E-SIC para transparência pública e acesso à informação.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se há dados do usuário no sessionStorage
            const tipoUsuario = sessionStorage.getItem('usuario_tipo');
            const nomeUsuario = sessionStorage.getItem('usuario_nome');
            
            // Atualizar nome do usuário se disponível
            if (nomeUsuario && tipoUsuario === 'cidadao') {
                const nomeElement = document.getElementById('nomeUsuario');
                if (nomeElement) {
                    nomeElement.textContent = nomeUsuario;
                }
            }
            
            // Mostrar toast de boas-vindas
            const urlParams = new URLSearchParams(window.location.search);
            const tipo = urlParams.get('tipo');
            
            if (tipo) {
                let mensagem = '';
                switch(tipo) {
                    case 'cidadao':
                        mensagem = 'Bem-vindo à área do cidadão!';
                        break;
                    case 'funcionario':
                        mensagem = 'Acesso de funcionário autorizado!';
                        break;
                    case 'administrador':
                        mensagem = 'Bem-vindo, administrador!';
                        break;
                    case 'anonimo':
                        mensagem = 'Navegação anônima ativada!';
                        break;
                }
                
                if (mensagem) {
                    setTimeout(() => {
                        ESICApp.showToast(mensagem, 'success');
                    }, 1000);
                }
            }
        });
    </script>
</body>
</html>