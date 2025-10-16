<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-SIC Rio Claro</title>
    <meta name="description" content="Sistema de Login - E-SIC Prefeitura Municipal de Rio Claro - RJ">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body.hero-gradient {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            overflow-x: hidden;
        }
        
        .container-fluid.login-content {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .login-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-content {
            margin-top: 2rem;
            margin-bottom: 2rem;
            min-height: calc(100vh - 140px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: none;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Garantir que os modais fiquem acima de tudo */
        .modal {
            z-index: 1060 !important;
        }
        
        .modal-backdrop {
            z-index: 1050 !important;
        }
        
        @media (max-width: 768px) {
            .login-content {
                margin-top: 1rem;
                margin-bottom: 1rem;
                min-height: calc(100vh - 100px);
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .login-header .container {
                padding: 0.5rem 1rem;
            }
            
            .login-header .navbar-brand {
                font-size: 1rem;
            }
            
            .login-header small {
                display: none;
            }
            
            .col-lg-6 {
                padding: 2rem !important;
            }
            
            .login-card {
                margin: 0 !important;
            }
        }
        
        @media (min-width: 1200px) {
            .login-content {
                padding-left: 30px;
                padding-right: 30px;
            }
        }
    </style>
</head>
<body class="hero-gradient">
    <!-- Header -->
    <div class="login-header">
        <div class="container">
            <nav class="navbar navbar-dark">
                <span class="navbar-brand fw-bold">
                    <img src="assets/images/logo-rioclaro.svg" alt="Logo Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                    E-SIC Rio Claro
                </span>
                <div class="d-flex align-items-center text-white">
                    <small>
                        <i class="bi bi-telephone"></i> (24) 99828-1427 • 
                        <i class="bi bi-envelope"></i> pmrc@rioclaro.rj.gov.br
                    </small>
                </div>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid login-content">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11 col-md-12">
                <div class="row bg-white rounded-4 login-card overflow-hidden mx-auto">
                        <!-- Lado Esquerdo - Informações -->
                        <div class="col-lg-6 bg-light p-5 d-flex flex-column justify-content-center">
                            <div class="text-center mb-4">
                                <img src="assets/images/logo-rioclaro.svg" alt="Logo Prefeitura de Rio Claro" class="mb-3" style="max-height: 100px;" onerror="this.style.display='none'">
                                <h2 class="text-primary mb-2">E-SIC Rio Claro</h2>
                                <p class="text-muted lead">Sistema Eletrônico de Informações ao Cidadão</p>
                                <p class="text-muted">Prefeitura Municipal de Rio Claro - RJ</p>
                            </div>
                            
                            <div class="alert alert-info border-primary">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    <strong>Lei de Acesso à Informação</strong>
                                </div>
                                <p class="mb-2">Garantindo transparência e acesso às informações públicas conforme Lei nº 12.527/2011.</p>
                                <div class="row text-center mt-3">
                                    <div class="col-4">
                                        <i class="bi bi-shield-check text-success fs-3"></i>
                                        <small class="d-block text-muted">Seguro</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="bi bi-clock text-info fs-3"></i>
                                        <small class="d-block text-muted">24h</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="bi bi-people text-warning fs-3"></i>
                                        <small class="d-block text-muted">Cidadão</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6 class="text-primary mb-3">Sistema Oficial E-SIC:</h6>
                                <a href="https://gpi-services.cloud.el.com.br/rj-rioclaro-pm/e-sic/" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-up-right-square"></i> Acessar Sistema Oficial
                                </a>
                            </div>
                        </div>

                        <!-- Lado Direito - Login -->
                        <div class="col-lg-6 p-5 d-flex flex-column justify-content-center">
                            <div class="text-center mb-4">
                                <h3 class="text-primary mb-2">Acesso ao Sistema</h3>
                                <p class="text-muted">Escolha seu tipo de acesso</p>
                            </div>

                            <!-- Abas de Login -->
                            <ul class="nav nav-pills nav-justified mb-4" id="loginTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="cidadao-tab" data-bs-toggle="pill" data-bs-target="#cidadao" type="button" role="tab">
                                        <i class="bi bi-person"></i> Cidadão
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="interno-tab" data-bs-toggle="pill" data-bs-target="#interno" type="button" role="tab">
                                        <i class="bi bi-building"></i> Interno
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="loginTabsContent">
                                <!-- Login Cidadão -->
                                <div class="tab-pane fade show active" id="cidadao" role="tabpanel">
                                    <form id="formCidadao" class="needs-validation" novalidate>
                                        <div class="mb-3">
                                            <label for="tipoPessoa" class="form-label">
                                                <i class="bi bi-person-badge"></i> Tipo de Pessoa
                                            </label>
                                            <select class="form-select" id="tipoPessoa" required>
                                                <option value="">Selecione...</option>
                                                <option value="cpf">Pessoa Física (CPF)</option>
                                                <option value="cnpj">Pessoa Jurídica (CNPJ)</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                Por favor, selecione o tipo de pessoa.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="documentoCidadao" class="form-label">
                                                <i class="bi bi-person-vcard"></i> <span id="labelDocumento">CPF/CNPJ</span>
                                            </label>
                                            <input type="text" class="form-control" id="documentoCidadao" placeholder="000.000.000-00" required maxlength="18">
                                            <div class="invalid-feedback" id="feedbackDocumento">
                                                Por favor, informe um documento válido.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nomeCidadao" class="form-label">
                                                <i class="bi bi-person"></i> Nome Completo
                                            </label>
                                            <input type="text" class="form-control" id="nomeCidadao" placeholder="Seu nome completo" required>
                                            <div class="invalid-feedback">
                                                Por favor, informe seu nome completo.
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="emailCidadao" class="form-label">
                                                <i class="bi bi-envelope"></i> E-mail
                                            </label>
                                            <input type="email" class="form-control" id="emailCidadao" placeholder="seu@email.com" required>
                                            <div class="invalid-feedback">
                                                Por favor, informe um e-mail válido.
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">
                                            <i class="bi bi-box-arrow-in-right"></i> Acessar como Cidadão
                                        </button>
                                        <div class="text-center mb-3">
                                            <a href="#" class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalCadastro">
                                                <i class="bi bi-person-plus"></i> Cadastrar-se
                                            </a>
                                            <a href="#" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalRecuperar">
                                                <i class="bi bi-key"></i> Esqueci os dados
                                            </a>
                                        </div>
                                        <div class="text-center">
                                            <small class="text-muted">
                                                <i class="bi bi-shield-check"></i> 
                                                Acesso seguro e anônimo disponível
                                            </small>
                                        </div>
                                    </form>
                                </div>

                                <!-- Login Interno -->
                                <div class="tab-pane fade" id="interno" role="tabpanel">
                                    <form id="formInterno" class="needs-validation" novalidate>
                                        <div class="mb-3">
                                            <label for="usuarioInterno" class="form-label">
                                                <i class="bi bi-person-badge"></i> Usuário
                                            </label>
                                            <input type="text" class="form-control" id="usuarioInterno" placeholder="Seu usuário" required>
                                            <div class="invalid-feedback">
                                                Por favor, informe seu usuário.
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="senhaInterno" class="form-label">
                                                <i class="bi bi-key"></i> Senha
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="senhaInterno" placeholder="Sua senha" required>
                                                <button class="btn btn-outline-secondary" type="button" id="toggleSenha">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback">
                                                Por favor, informe sua senha.
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100 mb-3">
                                            <i class="bi bi-shield-lock"></i> Acessar Sistema Interno
                                        </button>
                                        <div class="text-center">
                                            <a href="#" class="text-muted text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalEsqueci">
                                                <small><i class="bi bi-question-circle"></i> Esqueci minha senha</small>
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Acesso Anônimo -->
                            <div class="border-top pt-4 mt-4">
                                <div class="text-center">
                                    <p class="text-muted mb-2">
                                        <small>Ou acesse sem cadastro:</small>
                                    </p>
                                    <a href="dashboard.php?tipo=anonimo" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-incognito"></i> Acesso Anônimo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cadastro Cidadão -->
    <div class="modal fade" id="modalCadastro" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Cadastro de Cidadão
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastro" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipoDocumentoCadastro" class="form-label">
                                        <i class="bi bi-person-badge"></i> Tipo de Pessoa *
                                    </label>
                                    <select class="form-select" id="tipoDocumentoCadastro" required>
                                        <option value="">Selecione...</option>
                                        <option value="cpf">Pessoa Física (CPF)</option>
                                        <option value="cnpj">Pessoa Jurídica (CNPJ)</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Selecione o tipo de documento.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="documentoCadastro" class="form-label">
                                        <i class="bi bi-person-vcard"></i> <span id="labelDocumentoCadastro">CPF/CNPJ</span> *
                                    </label>
                                    <input type="text" class="form-control" id="documentoCadastro" placeholder="000.000.000-00" required maxlength="18">
                                    <div class="invalid-feedback" id="feedbackDocumentoCadastro">
                                        Informe um documento válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomeCadastro" class="form-label">
                                        <i class="bi bi-person"></i> <span id="labelNomeCadastro">Nome Completo</span> *
                                    </label>
                                    <input type="text" class="form-control" id="nomeCadastro" placeholder="Nome completo ou Razão Social" required>
                                    <div class="invalid-feedback">
                                        Informe o nome completo.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emailCadastro" class="form-label">
                                        <i class="bi bi-envelope"></i> E-mail *
                                    </label>
                                    <input type="email" class="form-control" id="emailCadastro" placeholder="seu@email.com" required>
                                    <div class="invalid-feedback">
                                        Informe um e-mail válido.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefoneCadastro" class="form-label">
                                        <i class="bi bi-telephone"></i> Telefone
                                    </label>
                                    <input type="text" class="form-control" id="telefoneCadastro" placeholder="(00) 00000-0000" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cepCadastro" class="form-label">
                                        <i class="bi bi-geo-alt"></i> CEP
                                    </label>
                                    <input type="text" class="form-control" id="cepCadastro" placeholder="00000-000" maxlength="9">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="enderecoCadastro" class="form-label">
                                <i class="bi bi-house"></i> Endereço Completo
                            </label>
                            <textarea class="form-control" id="enderecoCadastro" rows="2" placeholder="Rua, número, bairro, cidade, estado"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="aceitoTermos" required>
                                <label class="form-check-label" for="aceitoTermos">
                                    Aceito os <a href="#" data-bs-toggle="modal" data-bs-target="#modalTermos">termos de uso</a> e política de privacidade *
                                </label>
                                <div class="invalid-feedback">
                                    Você deve aceitar os termos de uso.
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Seus dados serão utilizados apenas para identificação nas solicitações E-SIC e não serão compartilhados com terceiros.
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitCadastro()">
                        <i class="bi bi-person-check"></i> Cadastrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Recuperar Dados -->
    <div class="modal fade" id="modalRecuperar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-key"></i> Recuperar Dados de Acesso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formRecuperar" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="tipoRecuperacao" class="form-label">
                                <i class="bi bi-search"></i> Buscar por:
                            </label>
                            <select class="form-select" id="tipoRecuperacao" required>
                                <option value="">Selecione...</option>
                                <option value="cpf">CPF</option>
                                <option value="cnpj">CNPJ</option>
                                <option value="email">E-mail</option>
                            </select>
                            <div class="invalid-feedback">
                                Selecione como deseja buscar.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dadoRecuperacao" class="form-label">
                                <i class="bi bi-person-vcard"></i> <span id="labelRecuperacao">Dado para busca</span>
                            </label>
                            <input type="text" class="form-control" id="dadoRecuperacao" placeholder="Informe o dado para busca" required>
                            <div class="invalid-feedback">
                                Informe o dado para buscar seu cadastro.
                            </div>
                        </div>
                        <div class="alert alert-warning">
                            <small>
                                <i class="bi bi-exclamation-triangle"></i>
                                Os dados de acesso serão enviados para o e-mail cadastrado ou você pode entrar em contato conosco.
                            </small>
                        </div>
                        <div class="alert alert-info">
                            <strong>Contato direto:</strong><br>
                            <i class="bi bi-telephone"></i> (24) 99828-1427<br>
                            <i class="bi bi-envelope"></i> pmrc@rioclaro.rj.gov.br<br>
                            <small>Horário: 8h às 17h (dias úteis)</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="submitRecuperacao()">
                        <i class="bi bi-send"></i> Recuperar Dados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Termos de Uso -->
    <div class="modal fade" id="modalTermos" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-file-text"></i> Termos de Uso - E-SIC Rio Claro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <h6>1. Finalidade do Sistema</h6>
                    <p>O Sistema E-SIC destina-se ao recebimento e processamento de solicitações de informações públicas conforme a Lei nº 12.527/2011 (Lei de Acesso à Informação).</p>
                    
                    <h6>2. Uso dos Dados Pessoais</h6>
                    <p>Os dados fornecidos serão utilizados exclusivamente para:</p>
                    <ul>
                        <li>Identificação do solicitante</li>
                        <li>Comunicação sobre as solicitações</li>
                        <li>Cumprimento das obrigações legais</li>
                    </ul>
                    
                    <h6>3. Proteção de Dados</h6>
                    <p>A Prefeitura de Rio Claro compromete-se a:</p>
                    <ul>
                        <li>Manter a confidencialidade dos dados</li>
                        <li>Não compartilhar informações com terceiros</li>
                        <li>Garantir a segurança das informações</li>
                    </ul>
                    
                    <h6>4. Direitos do Cidadão</h6>
                    <p>Você tem direito a:</p>
                    <ul>
                        <li>Acessar seus dados</li>
                        <li>Solicitar correção</li>
                        <li>Solicitar exclusão</li>
                        <li>Retirar consentimento</li>
                    </ul>
                    
                    <h6>5. Contato</h6>
                    <p>Para exercer seus direitos ou tirar dúvidas:</p>
                    <p><strong>E-mail:</strong> pmrc@rioclaro.rj.gov.br<br>
                    <strong>Telefone:</strong> (24) 99828-1427</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="bi bi-check"></i> Entendi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Esqueci Senha (Funcionários) -->
    <div class="modal fade" id="modalEsqueci" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-key"></i> Recuperar Senha - Funcionários
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Para recuperar sua senha de funcionário, entre em contato com a administração:</p>
                    <div class="alert alert-info">
                        <strong>Contatos:</strong><br>
                        <i class="bi bi-telephone"></i> (24) 99828-1427<br>
                        <i class="bi bi-envelope"></i> pmrc@rioclaro.rj.gov.br<br>
                        <small>Horário: 8h às 17h (dias úteis)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Controle do tipo de pessoa e máscara de documento
            const tipoPessoa = document.getElementById('tipoPessoa');
            const documentoCidadao = document.getElementById('documentoCidadao');
            const labelDocumento = document.getElementById('labelDocumento');
            const feedbackDocumento = document.getElementById('feedbackDocumento');
            
            if (tipoPessoa && documentoCidadao) {
                tipoPessoa.addEventListener('change', function() {
                    const tipo = this.value;
                    if (tipo === 'cpf') {
                        labelDocumento.textContent = 'CPF';
                        documentoCidadao.placeholder = '000.000.000-00';
                        documentoCidadao.maxLength = 14;
                        feedbackDocumento.textContent = 'Por favor, informe um CPF válido.';
                    } else if (tipo === 'cnpj') {
                        labelDocumento.textContent = 'CNPJ';
                        documentoCidadao.placeholder = '00.000.000/0000-00';
                        documentoCidadao.maxLength = 18;
                        feedbackDocumento.textContent = 'Por favor, informe um CNPJ válido.';
                    }
                    documentoCidadao.value = '';
                });
                
                documentoCidadao.addEventListener('input', function() {
                    const tipo = tipoPessoa.value;
                    if (tipo === 'cpf') {
                        ESICApp.formatCPF(this);
                    } else if (tipo === 'cnpj') {
                        ESICApp.formatCNPJ(this);
                    }
                });
            }
            
            // Controles para modal de cadastro
            initCadastroControls();
            
            // Controles para modal de recuperação
            initRecuperacaoControls();

            // Toggle senha
            const toggleSenha = document.getElementById('toggleSenha');
            const senhaInput = document.getElementById('senhaInterno');
            if (toggleSenha && senhaInput) {
                toggleSenha.addEventListener('click', function() {
                    const type = senhaInput.type === 'password' ? 'text' : 'password';
                    senhaInput.type = type;
                    const icon = this.querySelector('i');
                    icon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
                });
            }

            // Form Cidadão
            const formCidadao = document.getElementById('formCidadao');
            if (formCidadao) {
                formCidadao.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (this.checkValidity()) {
                        const tipoDoc = document.getElementById('tipoPessoa').value;
                        const documento = document.getElementById('documentoCidadao').value;
                        const nome = document.getElementById('nomeCidadao').value;
                        const email = document.getElementById('emailCidadao').value;
                        
                        // Validar documento
                        if (!validarDocumento(documento, tipoDoc)) {
                            ESICApp.showToast('Documento inválido!', 'danger');
                            return;
                        }
                        
                        // Armazenar dados do cidadão na sessão
                        sessionStorage.setItem('usuario_tipo', 'cidadao');
                        sessionStorage.setItem('usuario_documento_tipo', tipoDoc);
                        sessionStorage.setItem('usuario_documento', documento);
                        sessionStorage.setItem('usuario_nome', nome);
                        sessionStorage.setItem('usuario_email', email);
                        
                        // Redirecionar para dashboard
                        window.location.href = 'dashboard.php?tipo=cidadao';
                    }
                    this.classList.add('was-validated');
                });
            }

            // Form Interno
            const formInterno = document.getElementById('formInterno');
            if (formInterno) {
                formInterno.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (this.checkValidity()) {
                        const usuario = document.getElementById('usuarioInterno').value;
                        const senha = document.getElementById('senhaInterno').value;
                        
                        // Validação simples (em produção usar autenticação real)
                        if ((usuario === 'admin' && senha === 'admin123') || 
                            (usuario === 'funcionario' && senha === 'func123')) {
                            
                            const tipo = usuario === 'admin' ? 'administrador' : 'funcionario';
                            
                            // Armazenar dados na sessão
                            sessionStorage.setItem('usuario_tipo', tipo);
                            sessionStorage.setItem('usuario_login', usuario);
                            
                            // Redirecionar para dashboard
                            window.location.href = 'dashboard.php?tipo=' + tipo;
                        } else {
                            ESICApp.showToast('Usuário ou senha incorretos!', 'danger');
                        }
                    }
                    this.classList.add('was-validated');
                });
            }
        });
        
        // Funções auxiliares
        function initCadastroControls() {
            const tipoDocCadastro = document.getElementById('tipoDocumentoCadastro');
            const documentoCadastro = document.getElementById('documentoCadastro');
            const labelDocCadastro = document.getElementById('labelDocumentoCadastro');
            const labelNomeCadastro = document.getElementById('labelNomeCadastro');
            const feedbackDocCadastro = document.getElementById('feedbackDocumentoCadastro');
            
            if (tipoDocCadastro && documentoCadastro) {
                tipoDocCadastro.addEventListener('change', function() {
                    const tipo = this.value;
                    if (tipo === 'cpf') {
                        labelDocCadastro.textContent = 'CPF';
                        labelNomeCadastro.textContent = 'Nome Completo';
                        documentoCadastro.placeholder = '000.000.000-00';
                        documentoCadastro.maxLength = 14;
                        feedbackDocCadastro.textContent = 'Informe um CPF válido.';
                    } else if (tipo === 'cnpj') {
                        labelDocCadastro.textContent = 'CNPJ';
                        labelNomeCadastro.textContent = 'Razão Social';
                        documentoCadastro.placeholder = '00.000.000/0000-00';
                        documentoCadastro.maxLength = 18;
                        feedbackDocCadastro.textContent = 'Informe um CNPJ válido.';
                    }
                    documentoCadastro.value = '';
                });
                
                documentoCadastro.addEventListener('input', function() {
                    const tipo = tipoDocCadastro.value;
                    if (tipo === 'cpf') {
                        ESICApp.formatCPF(this);
                    } else if (tipo === 'cnpj') {
                        ESICApp.formatCNPJ(this);
                    }
                });
            }
            
            // Máscara para telefone
            const telefoneCadastro = document.getElementById('telefoneCadastro');
            if (telefoneCadastro) {
                telefoneCadastro.addEventListener('input', function() {
                    ESICApp.formatPhone(this);
                });
            }
            
            // Máscara para CEP
            const cepCadastro = document.getElementById('cepCadastro');
            if (cepCadastro) {
                cepCadastro.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    this.value = value;
                });
                
                // Buscar endereço pelo CEP
                cepCadastro.addEventListener('blur', function() {
                    const cep = this.value.replace(/\D/g, '');
                    if (cep.length === 8) {
                        buscarCEP(cep);
                    }
                });
            }
        }
        
        function initRecuperacaoControls() {
            const tipoRecuperacao = document.getElementById('tipoRecuperacao');
            const dadoRecuperacao = document.getElementById('dadoRecuperacao');
            const labelRecuperacao = document.getElementById('labelRecuperacao');
            
            if (tipoRecuperacao && dadoRecuperacao) {
                tipoRecuperacao.addEventListener('change', function() {
                    const tipo = this.value;
                    dadoRecuperacao.value = '';
                    
                    switch(tipo) {
                        case 'cpf':
                            labelRecuperacao.textContent = 'CPF';
                            dadoRecuperacao.placeholder = '000.000.000-00';
                            dadoRecuperacao.maxLength = 14;
                            break;
                        case 'cnpj':
                            labelRecuperacao.textContent = 'CNPJ';
                            dadoRecuperacao.placeholder = '00.000.000/0000-00';
                            dadoRecuperacao.maxLength = 18;
                            break;
                        case 'email':
                            labelRecuperacao.textContent = 'E-mail';
                            dadoRecuperacao.placeholder = 'seu@email.com';
                            dadoRecuperacao.maxLength = 255;
                            break;
                    }
                });
                
                dadoRecuperacao.addEventListener('input', function() {
                    const tipo = tipoRecuperacao.value;
                    if (tipo === 'cpf') {
                        ESICApp.formatCPF(this);
                    } else if (tipo === 'cnpj') {
                        ESICApp.formatCNPJ(this);
                    }
                });
            }
        }
        
        function validarDocumento(documento, tipo) {
            if (tipo === 'cpf') {
                return validarCPF(documento);
            } else if (tipo === 'cnpj') {
                return validarCNPJ(documento);
            }
            return false;
        }
        
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
            
            let soma = 0;
            for (let i = 0; i < 9; i++) {
                soma += parseInt(cpf.charAt(i)) * (10 - i);
            }
            let resto = 11 - (soma % 11);
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(9))) return false;
            
            soma = 0;
            for (let i = 0; i < 10; i++) {
                soma += parseInt(cpf.charAt(i)) * (11 - i);
            }
            resto = 11 - (soma % 11);
            if (resto === 10 || resto === 11) resto = 0;
            return resto === parseInt(cpf.charAt(10));
        }
        
        function validarCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g, '');
            if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) return false;
            
            let tamanho = cnpj.length - 2;
            let numeros = cnpj.substring(0, tamanho);
            let digitos = cnpj.substring(tamanho);
            let soma = 0;
            let pos = tamanho - 7;
            
            for (let i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado !== parseInt(digitos.charAt(0))) return false;
            
            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;
            
            for (let i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2) pos = 9;
            }
            
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            return resultado === parseInt(digitos.charAt(1));
        }
        
        function buscarCEP(cep) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        ESICApp.showToast('CEP não encontrado!', 'warning');
                        return;
                    }
                    
                    const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                    document.getElementById('enderecoCadastro').value = endereco;
                })
                .catch(() => {
                    ESICApp.showToast('Erro ao buscar CEP. Tente novamente.', 'danger');
                });
        }
        
        function submitCadastro() {
            const form = document.getElementById('formCadastro');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const tipoDoc = document.getElementById('tipoDocumentoCadastro').value;
            const documento = document.getElementById('documentoCadastro').value;
            
            if (!validarDocumento(documento, tipoDoc)) {
                ESICApp.showToast('Documento inválido!', 'danger');
                return;
            }
            
            // Simular cadastro (em produção, enviar para backend)
            const dadosCadastro = {
                tipo_documento: tipoDoc,
                documento: documento,
                nome: document.getElementById('nomeCadastro').value,
                email: document.getElementById('emailCadastro').value,
                telefone: document.getElementById('telefoneCadastro').value,
                cep: document.getElementById('cepCadastro').value,
                endereco: document.getElementById('enderecoCadastro').value
            };
            
            // Armazenar no localStorage temporariamente
            let usuarios = JSON.parse(localStorage.getItem('usuarios_esic') || '[]');
            usuarios.push(dadosCadastro);
            localStorage.setItem('usuarios_esic', JSON.stringify(usuarios));
            
            ESICApp.showToast('Cadastro realizado com sucesso!', 'success');
            
            // Fechar modal e preencher campos de login
            bootstrap.Modal.getInstance(document.getElementById('modalCadastro')).hide();
            
            setTimeout(() => {
                document.getElementById('tipoPessoa').value = tipoDoc;
                document.getElementById('tipoPessoa').dispatchEvent(new Event('change'));
                document.getElementById('documentoCidadao').value = documento;
                document.getElementById('nomeCidadao').value = dadosCadastro.nome;
                document.getElementById('emailCidadao').value = dadosCadastro.email;
            }, 500);
        }
        
        function submitRecuperacao() {
            const form = document.getElementById('formRecuperar');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            
            const tipo = document.getElementById('tipoRecuperacao').value;
            const dado = document.getElementById('dadoRecuperacao').value;
            
            // Validar documento se for CPF ou CNPJ
            if ((tipo === 'cpf' || tipo === 'cnpj') && !validarDocumento(dado, tipo)) {
                ESICApp.showToast('Documento inválido!', 'danger');
                return;
            }
            
            // Simular busca (em produção, enviar para backend)
            const usuarios = JSON.parse(localStorage.getItem('usuarios_esic') || '[]');
            let usuarioEncontrado = null;
            
            usuarios.forEach(user => {
                if ((tipo === 'cpf' || tipo === 'cnpj') && user.documento === dado) {
                    usuarioEncontrado = user;
                } else if (tipo === 'email' && user.email === dado) {
                    usuarioEncontrado = user;
                }
            });
            
            if (usuarioEncontrado) {
                ESICApp.showToast('Dados enviados para seu e-mail cadastrado!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('modalRecuperar')).hide();
            } else {
                ESICApp.showToast('Cadastro não encontrado. Verifique os dados ou entre em contato.', 'warning');
            }
        }
    </script>
</body>
</html>