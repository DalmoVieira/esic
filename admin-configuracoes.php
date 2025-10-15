<?php
session_start();
require_once 'app/config/Database.php';

// Verificar permissão de administrador
$tipo_usuario = $_GET['tipo'] ?? '';
if ($tipo_usuario !== 'administrador') {
    header('Location: login.php');
    exit;
}

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Buscar configurações
    $stmt = $pdo->query("SELECT * FROM configuracoes WHERE categoria IN ('email', 'geral') ORDER BY categoria, chave");
    $configuracoes = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações de Email - E-SIC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark hero-gradient">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php?tipo=administrador">
                <i class="bi bi-gear"></i> Configurações
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php?tipo=administrador">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="text-primary fw-bold mb-4">
                    <i class="bi bi-envelope-gear"></i> Configurações de Email e Notificações
                </h2>
            </div>
        </div>

        <!-- Mensagens -->
        <div id="alertContainer"></div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#smtp" type="button">
                    <i class="bi bi-server"></i> Servidor SMTP
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notificacoes" type="button">
                    <i class="bi bi-bell"></i> Notificações
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#teste" type="button">
                    <i class="bi bi-send-check"></i> Testar Email
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- SMTP -->
            <div class="tab-pane fade show active" id="smtp">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Configurações do Servidor SMTP</h5>
                    </div>
                    <div class="card-body">
                        <form id="formSMTP">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="smtp_host" class="form-label">Servidor SMTP</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                           placeholder="smtp.gmail.com" required>
                                    <div class="form-text">Ex: smtp.gmail.com, smtp.office365.com, localhost</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="smtp_port" class="form-label">Porta</label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                           placeholder="587" required>
                                    <div class="form-text">587 (TLS) ou 465 (SSL)</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_user" class="form-label">Usuário SMTP</label>
                                    <input type="text" class="form-control" id="smtp_user" name="smtp_user" 
                                           placeholder="email@dominio.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_pass" class="form-label">Senha SMTP</label>
                                    <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" 
                                           placeholder="••••••••">
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> Deixe em branco para manter a senha atual
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="mb-3">Informações do Remetente</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="from_email" class="form-label">Email Remetente</label>
                                    <input type="email" class="form-control" id="from_email" name="from_email" 
                                           placeholder="noreply@rioclaro.sp.gov.br" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="from_name" class="form-label">Nome do Remetente</label>
                                    <input type="text" class="form-control" id="from_name" name="from_name" 
                                           placeholder="E-SIC Rio Claro" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="base_url" class="form-label">URL Base do Sistema</label>
                                <input type="url" class="form-control" id="base_url" name="base_url" 
                                       placeholder="https://esic.rioclaro.sp.gov.br" required>
                                <div class="form-text">URL completa para links nos emails</div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Salvar Configurações
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notificações -->
            <div class="tab-pane fade" id="notificacoes">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Gerenciar Notificações Automáticas</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Configure quais notificações serão enviadas automaticamente aos cidadãos.
                        </div>

                        <form id="formNotificacoes">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notificacoes_ativas" checked>
                                <label class="form-check-label" for="notificacoes_ativas">
                                    <strong>Ativar sistema de notificações por email</strong>
                                </label>
                            </div>

                            <hr>

                            <h6>Tipos de Notificações:</h6>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_novo_pedido" checked>
                                <label class="form-check-label" for="notif_novo_pedido">
                                    Novo pedido criado (confirmação)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_mudanca_status" checked>
                                <label class="form-check-label" for="notif_mudanca_status">
                                    Mudança de status
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_resposta" checked>
                                <label class="form-check-label" for="notif_resposta">
                                    Pedido respondido
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_prazo_proximo" checked>
                                <label class="form-check-label" for="notif_prazo_proximo">
                                    Prazo próximo do vencimento (5 dias)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_prazo_vencido" checked>
                                <label class="form-check-label" for="notif_prazo_vencido">
                                    Prazo vencido
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notif_novo_recurso" checked>
                                <label class="form-check-label" for="notif_novo_recurso">
                                    Novo recurso criado
                                </label>
                            </div>

                            <hr>

                            <h6>Configurações de Cron:</h6>
                            <p class="text-muted">
                                Para envio automático de notificações, configure o cron para executar:
                            </p>
                            <div class="bg-dark text-light p-3 rounded mb-3">
                                <code>0 8 * * * php /caminho/para/esic/cron/notificacoes.php</code>
                            </div>
                            <small class="text-muted">
                                Esse comando executará o script diariamente às 8h da manhã.
                            </small>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Salvar Preferências
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Teste -->
            <div class="tab-pane fade" id="teste">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Testar Envio de Email</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Antes de testar, certifique-se de salvar as configurações SMTP.
                        </div>

                        <form id="formTeste">
                            <div class="mb-3">
                                <label for="email_teste" class="form-label">Email de Destino</label>
                                <input type="email" class="form-control" id="email_teste" name="email_teste" 
                                       placeholder="seu@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="tipo_email" class="form-label">Tipo de Email</label>
                                <select class="form-select" id="tipo_email" name="tipo_email" required>
                                    <option value="">Selecione...</option>
                                    <option value="teste">Email de Teste Simples</option>
                                    <option value="novo_pedido">Confirmação de Novo Pedido</option>
                                    <option value="resposta">Notificação de Resposta</option>
                                    <option value="prazo_proximo">Alerta de Prazo Próximo</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Enviar Email de Teste
                            </button>
                        </form>

                        <div id="resultadoTeste" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Carregar configurações ao inicializar
        window.addEventListener('DOMContentLoaded', carregarConfiguracoes);

        async function carregarConfiguracoes() {
            try {
                const response = await fetch('api/configuracoes.php?action=listar&categoria=email');
                const data = await response.json();
                
                if (data.success) {
                    Object.entries(data.data).forEach(([chave, valor]) => {
                        const input = document.getElementById(chave);
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = valor === 'true' || valor === '1';
                            } else {
                                input.value = valor;
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Erro ao carregar configurações:', error);
            }
        }

        // Salvar configurações SMTP
        document.getElementById('formSMTP')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'salvar_smtp');
            
            try {
                const response = await fetch('api/configuracoes.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    ESICApp.showToast('Configurações salvas com sucesso!', 'success');
                } else {
                    ESICApp.showToast(data.message, 'danger');
                }
            } catch (error) {
                ESICApp.showToast('Erro ao salvar: ' + error.message, 'danger');
            }
        });

        // Testar email
        document.getElementById('formTeste')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'testar_email');
            
            const btn = e.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            
            try {
                const response = await fetch('api/configuracoes.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                const resultado = document.getElementById('resultadoTeste');
                if (data.success) {
                    resultado.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            Email enviado com sucesso! Verifique sua caixa de entrada.
                        </div>
                    `;
                } else {
                    resultado.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle"></i>
                            Erro ao enviar: ${data.message}
                        </div>
                    `;
                }
            } catch (error) {
                document.getElementById('resultadoTeste').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i>
                        Erro: ${error.message}
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send"></i> Enviar Email de Teste';
            }
        });
    </script>
</body>
</html>