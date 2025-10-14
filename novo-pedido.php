<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Solicitação - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #0d47a1, #1565c0);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-info-circle"></i> E-SIC
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">← Voltar ao Início</a>
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