<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparação de Largura - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .demo-container {
            border: 2px solid #007bff;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(102, 126, 234, 0.1));
        }
        .comparison-table td {
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="bi bi-rulers"></i> Comparação de Largura
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success">
                    <h4 class="alert-heading">
                        <i class="bi bi-check-circle"></i> Padronização Concluída!
                    </h4>
                    <p>Todas as páginas agora usam a mesma largura (<code>.container</code>) para consistência visual.</p>
                </div>
            </div>
        </div>

        <!-- Demonstração Visual -->
        <div class="row mt-4">
            <div class="col-12">
                <h2 class="text-primary mb-4">
                    <i class="bi bi-layout-text-window-reverse"></i> Largura Padronizada
                </h2>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📏 Área de Conteúdo (.container)</h5>
                    </div>
                    <div class="card-body">
                        <div class="demo-container">
                            <div class="text-center">
                                <i class="bi bi-arrows-expand" style="font-size: 3rem; color: #007bff;"></i>
                                <p class="mt-3 mb-0"><strong>Esta é a largura padrão em todas as páginas</strong></p>
                                <p class="text-muted">Responsiva e consistente</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Comparação -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Páginas Atualizadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered comparison-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Página</th>
                                        <th>Tipo</th>
                                        <th>Antes</th>
                                        <th>Depois</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>dashboard.php</code></td>
                                        <td><span class="badge bg-info">Cidadão</span></td>
                                        <td><code>.container</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-success">✓ OK</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>novo-pedido-v2.php</code></td>
                                        <td><span class="badge bg-info">Cidadão</span></td>
                                        <td><code>.container</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-success">✓ OK</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>acompanhar-v2.php</code></td>
                                        <td><span class="badge bg-info">Cidadão</span></td>
                                        <td><code>.container</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-success">✓ OK</span></td>
                                    </tr>
                                    <tr>
                                        <td><code>recurso.php</code></td>
                                        <td><span class="badge bg-info">Cidadão</span></td>
                                        <td><code>.container</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-success">✓ OK</span></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><code>admin-pedidos.php</code></td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td><code>.container-fluid</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-warning">✓ CORRIGIDO</span></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><code>admin-configuracoes.php</code></td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td><code>.container-fluid</code></td>
                                        <td><code>.container</code></td>
                                        <td><span class="badge bg-warning">✓ CORRIGIDO</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Explicação Técnica -->
                <div class="card mt-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle"></i> Diferença Técnica
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <strong>ANTES: .container-fluid</strong>
                                    </div>
                                    <div class="card-body">
                                        <ul class="mb-0">
                                            <li>Largura: 100% da tela</li>
                                            <li>Margens laterais: mínimas</li>
                                            <li>Ocupa toda a largura disponível</li>
                                            <li>Pode ficar muito largo em telas grandes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <strong>DEPOIS: .container</strong>
                                    </div>
                                    <div class="card-body">
                                        <ul class="mb-0">
                                            <li>Largura: responsiva (max 1140px)</li>
                                            <li>Margens laterais: automáticas</li>
                                            <li>Centralizado na tela</li>
                                            <li>Melhor legibilidade e UX</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-lightbulb"></i> Larguras Responsivas do .container:</h6>
                            <ul class="mb-0">
                                <li><strong>Celular (< 576px):</strong> 100% da tela</li>
                                <li><strong>Tablet (≥ 576px):</strong> 540px</li>
                                <li><strong>Desktop (≥ 768px):</strong> 720px</li>
                                <li><strong>Desktop grande (≥ 992px):</strong> 960px</li>
                                <li><strong>Desktop extra (≥ 1200px):</strong> 1140px</li>
                                <li><strong>Desktop XXL (≥ 1400px):</strong> 1320px</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Benefícios -->
                <div class="card mt-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-trophy"></i> Benefícios da Padronização
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center mb-3">
                                    <i class="bi bi-eye" style="font-size: 3rem; color: #28a745;"></i>
                                    <h6 class="mt-2">Consistência Visual</h6>
                                    <p class="text-muted small">Mesma largura em todas as páginas</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center mb-3">
                                    <i class="bi bi-phone" style="font-size: 3rem; color: #28a745;"></i>
                                    <h6 class="mt-2">Responsividade</h6>
                                    <p class="text-muted small">Adapta-se a todos os tamanhos de tela</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center mb-3">
                                    <i class="bi bi-book" style="font-size: 3rem; color: #28a745;"></i>
                                    <h6 class="mt-2">Legibilidade</h6>
                                    <p class="text-muted small">Largura ideal para leitura de conteúdo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegação -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h5 class="mb-3">Testar Páginas Atualizadas</h5>
                        <div class="btn-group-vertical gap-2" role="group">
                            <a href="dashboard.php?tipo=cidadao" class="btn btn-outline-primary">
                                <i class="bi bi-house"></i> Dashboard Cidadão
                            </a>
                            <a href="novo-pedido-v2.php?tipo=cidadao" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle"></i> Novo Pedido
                            </a>
                            <a href="acompanhar-v2.php?tipo=cidadao" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Acompanhar
                            </a>
                            <a href="admin-pedidos.php?tipo=administrador" class="btn btn-outline-danger">
                                <i class="bi bi-gear"></i> Admin - Pedidos
                            </a>
                            <a href="admin-configuracoes.php?tipo=administrador" class="btn btn-outline-danger">
                                <i class="bi bi-envelope-gear"></i> Admin - Configurações
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-check-circle-fill text-success"></i> 
                Largura padronizada em todas as páginas!
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>