<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Lei de Acesso à Informação'; ?> - E-SIC</title>
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
                        <a class="nav-link active" href="/lei-acesso-informacao">LAI</a>
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
                        <li class="breadcrumb-item active">Lei de Acesso à Informação</li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-balance-scale me-2"></i>
                            Lei de Acesso à Informação - LAI
                        </h1>
                    </div>
                    <div class="card-body">
                        <h2>O que é a LAI?</h2>
                        <p class="lead">
                            A Lei de Acesso à Informação (Lei nº 12.527/2011) regulamenta o direito constitucional 
                            de acesso às informações públicas. Essa norma entrou em vigor em 16 de maio de 2012 
                            e criou mecanismos que possibilitam, a qualquer pessoa, física ou jurídica, sem 
                            necessidade de apresentar motivo, o recebimento de informações públicas dos órgãos 
                            e entidades.
                        </p>

                        <h3>Princípios da LAI</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Publicidade como regra e sigilo como exceção</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Divulgação de informações de interesse público</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Utilização de meios de comunicação viabilizados pela tecnologia</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Fomento ao desenvolvimento da cultura de transparência</li>
                        </ul>

                        <h3>Quem pode solicitar?</h3>
                        <p>
                            Qualquer pessoa, física ou jurídica, pode apresentar pedido de acesso a informações 
                            aos órgãos e entidades referidos na LAI, por qualquer meio legítimo, devendo o pedido 
                            conter a identificação do requerente e a especificação da informação requerida.
                        </p>

                        <h3>Prazo de Resposta</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Prazo padrão:</strong> 20 dias
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <strong>Prorrogação:</strong> + 10 dias (com justificativa)
                                </div>
                            </div>
                        </div>

                        <h3>Recursos</h3>
                        <p>
                            Em caso de negativa de acesso ou não fornecimento das razões da negativa, você pode 
                            apresentar recurso no prazo de 10 (dez) dias a contar da sua ciência da decisão.
                        </p>

                        <div class="alert alert-primary">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> O serviço de busca e fornecimento da informação é gratuito, 
                            salvo nas hipóteses de reprodução de documentos pelo órgão ou entidade pública consultada, 
                            situação em que poderá ser cobrado exclusivamente o valor necessário ao ressarcimento 
                            do custo dos serviços e dos materiais utilizados.
                        </div>

                        <div class="text-center mt-4">
                            <a href="/novo-pedido" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>
                                Fazer Pedido
                            </a>
                            <a href="/acompanhar" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-search me-2"></i>
                                Acompanhar Pedido
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