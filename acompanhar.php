<?php
$tipo_usuario = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (empty($tipo_usuario) || $tipo_usuario === 'anonimo') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar Solicitação - E-SIC Rio Claro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=<?= $tipo_usuario ?>">
                <img src="assets/images/logo-pmrcrj.png" alt="Logo Prefeitura Municipal de Rio Claro" height="32" class="me-2" onerror="this.style.display='none'">
                E-SIC Rio Claro
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php?tipo=<?= $tipo_usuario ?>">← Voltar ao Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="bi bi-search"></i> Acompanhar Solicitação</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Consulte o status da sua solicitação informando o número do protocolo e seus dados:</p>
                        
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número do Protocolo *</label>
                                    <input type="text" class="form-control" placeholder="Ex: 2024123456789" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CPF/CNPJ *</label>
                                    <input type="text" class="form-control" placeholder="000.000.000-00" disabled>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-success" disabled>
                                    <i class="bi bi-search"></i> Consultar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Exemplo de resultado -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">📄 Exemplo de Consulta</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Sistema em Desenvolvimento:</strong> Abaixo temos um exemplo de como será exibido o status de uma solicitação.
                        </div>

                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-primary mb-1">Protocolo: 2024001234567</h6>
                                    <small class="text-muted">Solicitado em: 15/01/2024 14:30</small>
                                </div>
                                <span class="badge bg-warning text-dark">Em Análise</span>
                            </div>
                            
                            <p><strong>Assunto:</strong> Informações sobre contratos de limpeza</p>
                            <p class="mb-3"><strong>Descrição:</strong> Solicito informações sobre os contratos vigentes de limpeza predial do órgão...</p>

                            <!-- Timeline de status -->
                            <div class="timeline">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-success rounded-circle p-2 me-3">
                                        <i class="bi bi-check"></i>
                                    </div>
                                    <div>
                                        <strong>Solicitação Recebida</strong>
                                        <br><small class="text-muted">15/01/2024 14:30 - Protocolo gerado com sucesso</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-warning text-dark rounded-circle p-2 me-3">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div>
                                        <strong>Em Análise</strong>
                                        <br><small class="text-muted">16/01/2024 09:15 - Encaminhado para setor responsável</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2 opacity-50">
                                    <div class="badge bg-secondary rounded-circle p-2 me-3">
                                        <i class="bi bi-hourglass"></i>
                                    </div>
                                    <div>
                                        <strong>Aguardando Resposta</strong>
                                        <br><small class="text-muted">Prazo: até 05/02/2024</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 p-3 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-clock"></i>
                                <strong>Prazo:</strong> Sua solicitação deve ser respondida até <strong>05/02/2024</strong>
                                <br><small>Restam 15 dias úteis para resposta</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações sobre prazos -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6>⏰ Status das Solicitações</h6>
                        <div class="row text-center">
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-primary p-2 d-block">Recebida</span>
                                <small>Protocolo gerado</small>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-warning text-dark p-2 d-block">Em Análise</span>
                                <small>Sendo processada</small>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-info p-2 d-block">Respondida</span>
                                <small>Resposta enviada</small>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="badge bg-secondary p-2 d-block">Arquivada</span>
                                <small>Processo finalizado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>