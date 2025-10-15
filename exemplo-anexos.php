<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemplo - Sistema de Anexos - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="bi bi-file-earmark-text"></i> E-SIC - Exemplo de Anexos
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="text-primary fw-bold mb-4">
                    <i class="bi bi-paperclip"></i> Sistema de Anexos - Demonstração
                </h2>

                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> Como Usar</h5>
                    <p>Este é um exemplo de como integrar o sistema de anexos em suas páginas.</p>
                    <ol>
                        <li>Incluir o script <code>anexos.js</code></li>
                        <li>Criar um container div com ID</li>
                        <li>Inicializar a classe <code>ESICAnexos</code></li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Exemplo 1: Anexos de Pedido -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Exemplo 1: Anexos de um Pedido</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Protocolo: P2025000001</p>
                        
                        <!-- Container para anexos -->
                        <div id="anexosPedido"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exemplo 2: Anexos de Recurso -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Exemplo 2: Anexos de um Recurso</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Protocolo do Recurso: R2025000001</p>
                        
                        <!-- Container para anexos -->
                        <div id="anexosRecurso"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Código de Exemplo -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-code-slash"></i> Código de Exemplo</h5>
                    </div>
                    <div class="card-body">
                        <h6>HTML:</h6>
                        <pre class="bg-light p-3 rounded"><code>&lt;!-- Container para os anexos --&gt;
&lt;div id="anexosPedido"&gt;&lt;/div&gt;

&lt;!-- Script do componente --&gt;
&lt;script src="assets/js/anexos.js"&gt;&lt;/script&gt;</code></pre>

                        <h6>JavaScript:</h6>
                        <pre class="bg-light p-3 rounded"><code>&lt;script&gt;
    // Inicializar componente de anexos
    // Parâmetros: (tipoEntidade, entidadeId, selectorContainer)
    
    // Para um pedido com ID 1
    const anexosPedido = new ESICAnexos('pedido', 1, '#anexosPedido');
    
    // Para um recurso com ID 2
    const anexosRecurso = new ESICAnexos('recurso', 2, '#anexosRecurso');
&lt;/script&gt;</code></pre>

                        <h6>PHP - Integração na página de detalhes:</h6>
                        <pre class="bg-light p-3 rounded"><code>&lt;?php
// Exemplo em acompanhar-v2.php ou página de detalhes

// Buscar pedido
$stmt = $pdo-&gt;prepare("SELECT * FROM pedidos WHERE protocolo = ?");
$stmt-&gt;execute([$protocolo]);
$pedido = $stmt-&gt;fetch();

?&gt;

&lt;!-- Exibir detalhes do pedido --&gt;
&lt;h3&gt;Pedido &lt;?= $pedido['protocolo'] ?&gt;&lt;/h3&gt;

&lt;!-- Container de anexos --&gt;
&lt;div id="anexosContainer"&gt;&lt;/div&gt;

&lt;script src="assets/js/anexos.js"&gt;&lt;/script&gt;
&lt;script&gt;
    // Inicializar com o ID do pedido do PHP
    const anexos = new ESICAnexos('pedido', &lt;?= $pedido['id'] ?&gt;, '#anexosContainer');
&lt;/script&gt;</code></pre>

                        <hr>

                        <h6>API Endpoints Disponíveis:</h6>
                        <ul>
                            <li><code>api/anexos.php?action=upload</code> - Upload de arquivo (POST)</li>
                            <li><code>api/anexos.php?action=listar&tipo_entidade=pedido&entidade_id=1</code> - Listar anexos (GET)</li>
                            <li><code>api/anexos.php?action=download&anexo_id=1</code> - Download de arquivo (GET)</li>
                            <li><code>api/anexos.php?action=deletar</code> - Deletar anexo (POST)</li>
                        </ul>

                        <h6>Configurações:</h6>
                        <ul>
                            <li><strong>Tamanho máximo:</strong> 10MB por arquivo</li>
                            <li><strong>Extensões permitidas:</strong> PDF, DOC, DOCX, JPG, JPEG, PNG, TXT, ODT, XLS, XLSX</li>
                            <li><strong>Diretório:</strong> <code>uploads/</code></li>
                            <li><strong>Segurança:</strong> Validação de MIME type, nome único com hash</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/anexos.js"></script>
    
    <script>
        // Exemplo 1: Anexos de pedido (ID 1 - exemplo)
        // Você pode buscar o ID real do pedido via AJAX ou PHP
        const anexosPedido = new ESICAnexos('pedido', 1, '#anexosPedido');
        
        // Exemplo 2: Anexos de recurso (ID 1 - exemplo)
        const anexosRecurso = new ESICAnexos('recurso', 1, '#anexosRecurso');
        
        // Nota: Se não houver pedidos/recursos com ID 1, 
        // o componente mostrará "Nenhum anexo encontrado"
    </script>
</body>
</html>