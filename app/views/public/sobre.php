<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sobre o Sistema'; ?> - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-university me-2"></i>
                E-SIC
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/novo-pedido">Novo Pedido</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/acompanhar">Acompanhar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/sobre">Sobre</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Sobre</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Sobre o Sistema E-SIC
                        </h1>
                    </div>
                    <div class="card-body">
                        <h2>O que é o E-SIC?</h2>
                        <p class="lead">
                            O Sistema Eletrônico do Serviço de Informação ao Cidadão (E-SIC) é uma plataforma 
                            digital que permite a qualquer pessoa, física ou jurídica, encaminhar pedidos de 
                            acesso à informação para órgãos e entidades do Poder Executivo Federal.
                        </p>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                                        <h5>Disponível 24h</h5>
                                        <p>O sistema funciona 24 horas por dia, 7 dias por semana.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                                        <h5>Seguro</h5>
                                        <p>Suas informações são protegidas e mantidas em sigilo.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="mt-4">Como funciona?</h3>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <span class="text-white fw-bold fs-4">1</span>
                                    </div>
                                </div>
                                <h5>Faça seu pedido</h5>
                                <p>Cadastre-se e envie sua solicitação de informação.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <span class="text-white fw-bold fs-4">2</span>
                                    </div>
                                </div>
                                <h5>Acompanhe o status</h5>
                                <p>Receba um protocolo e acompanhe o andamento.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <span class="text-white fw-bold fs-4">3</span>
                                    </div>
                                </div>
                                <h5>Receba a resposta</h5>
                                <p>Obtenha a informação solicitada no prazo legal.</p>
                            </div>
                        </div>

                        <?php if (isset($orgao)): ?>
                        <div class="alert alert-light mt-4">
                            <h4><i class="fas fa-building me-2"></i>Sobre o Órgão</h4>
                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($orgao['nome']); ?></p>
                            <p><strong>CNPJ:</strong> <?php echo htmlspecialchars($orgao['cnpj']); ?></p>
                            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($orgao['endereco']); ?></p>
                            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($orgao['telefone']); ?></p>
                            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($orgao['email']); ?></p>
                        </div>
                        <?php endif; ?>

                        <h3>Legislação</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-file-alt me-2"></i>
                                Lei nº 12.527, de 18 de novembro de 2011 - Lei de Acesso à Informação
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-file-alt me-2"></i>
                                Decreto nº 7.724, de 16 de maio de 2012 - Regulamenta a LAI
                            </li>
                        </ul>

                        <div class="text-center mt-4">
                            <a href="/novo-pedido" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>
                                Fazer Pedido
                            </a>
                            <a href="/lei-acesso-informacao" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-balance-scale me-2"></i>
                                Saiba mais sobre a LAI
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>