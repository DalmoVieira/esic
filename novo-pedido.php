<?php
session_start();

// Verificar se está logado
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (empty($tipo_usuario) || $tipo_usuario === 'anonimo') {
    header('Location: login.php');
    exit;
}

// Incluir dependências
require_once 'app/config/Database.php';

try {
    $db = Database::getInstance();
    
    // Buscar órgãos ativos para o select
    $orgaos = $db->select("SELECT id, nome, sigla FROM orgaos_setores WHERE ativo = 1 ORDER BY ordem_exibicao, nome");
    
} catch (Exception $e) {
    $error_message = "Erro ao carregar dados: " . $e->getMessage();
    $orgaos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido - E-SIC Rio Claro</title>
    <meta name="description" content="Submeter nova solicitação de informação - E-SIC Rio Claro">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-floating textarea {
            min-height: 120px;
        }
        .char-counter {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .char-counter.warning {
            color: #fd7e14;
        }
        .char-counter.danger {
            color: #dc3545;
        }
        .info-card {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-left: 4px solid var(--primary-color);
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                <img src="assets/images/logo-pmrcrj.png" alt="Logo Prefeitura Municipal de Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="novo-pedido.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-plus-circle"></i> Novo Pedido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="acompanhar.php?tipo=<?= $tipo_usuario ?>">
                            <i class="bi bi-search"></i> Acompanhar
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nova Solicitação de Informação</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Sistema em Desenvolvimento:</strong> Esta funcionalidade será implementada em breve. 
                            Por enquanto, esta é uma demonstração da interface.
                        </div>

                        <form>
                            <h6 class="text-primary mb-3">Dados do Solicitante</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome Completo *</label>
                                    <input type="text" class="form-control" placeholder="Seu nome completo" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CPF/CNPJ *</label>
                                    <input type="text" class="form-control" placeholder="000.000.000-00" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" placeholder="seu@email.com" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control" placeholder="(11) 99999-9999" disabled>
                                </div>
                            </div>

                            <hr>
                            <h6 class="text-primary mb-3">Informações Solicitadas</h6>
                            <div class="mb-3">
                                <label class="form-label">Categoria da Solicitação *</label>
                                <select class="form-select" disabled>
                                    <option>Selecione uma categoria</option>
                                    <option>Informações Gerais</option>
                                    <option>Contratos e Licitações</option>
                                    <option>Recursos Humanos</option>
                                    <option>Orçamento e Finanças</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição da Informação Solicitada *</label>
                                <textarea class="form-control" rows="4" placeholder="Descreva detalhadamente as informações que você deseja obter..." disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Forma de Recebimento *</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="forma_recebimento" id="email" disabled>
                                        <label class="form-check-label" for="email">Por email</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="forma_recebimento" id="postal" disabled>
                                        <label class="form-check-label" for="postal">Via postal</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancelar</button>
                                <button type="submit" class="btn btn-primary" disabled>
                                    <i class="bi bi-send"></i> Enviar Solicitação
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h6>📋 Informações Importantes</h6>
                        <ul class="small">
                            <li>O prazo de resposta é de até 20 dias, prorrogáveis por mais 10 dias</li>
                            <li>Você receberá um protocolo para acompanhar sua solicitação</li>
                            <li>Todas as informações são protegidas pela Lei de Acesso à Informação</li>
                            <li>Em caso de dúvidas, consulte nossa seção de Perguntas Frequentes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>