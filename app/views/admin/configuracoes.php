<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-primary mb-2">
            <i class="bi bi-gear me-2"></i>
            Configurações do Sistema
        </h1>
        <p class="text-muted mb-0">Configurações gerais do sistema E-SIC</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="exportarConfiguracoes()">
            <i class="bi bi-download me-1"></i>
            Backup Config
        </button>
        <button class="btn btn-warning" onclick="resetarConfiguracoes()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Restaurar Padrões
        </button>
    </div>
</div>

<!-- Configuration Tabs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <ul class="nav nav-tabs card-header-tabs" id="configTabs">
            <li class="nav-item">
                <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral">
                    <i class="bi bi-gear me-2"></i>Geral
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="lai-tab" data-bs-toggle="tab" data-bs-target="#lai">
                    <i class="bi bi-file-text me-2"></i>Lei de Acesso
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email">
                    <i class="bi bi-envelope me-2"></i>E-mail
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="seguranca-tab" data-bs-toggle="tab" data-bs-target="#seguranca">
                    <i class="bi bi-shield-check me-2"></i>Segurança
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="integracao-tab" data-bs-toggle="tab" data-bs-target="#integracao">
                    <i class="bi bi-puzzle me-2"></i>Integrações
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="manutencao-tab" data-bs-toggle="tab" data-bs-target="#manutencao">
                    <i class="bi bi-tools me-2"></i>Manutenção
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <form id="configuracaoForm" method="POST" action="/admin/configuracoes" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="tab-content">
                <!-- Geral Tab -->
                <div class="tab-pane fade show active" id="geral">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="text-primary mb-3">Informações do Órgão</h5>
                            
                            <div class="mb-3">
                                <label for="nome_orgao" class="form-label">Nome do Órgão <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome_orgao" name="nome_orgao" 
                                       value="<?= htmlspecialchars($config['nome_orgao'] ?? '') ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sigla_orgao" class="form-label">Sigla</label>
                                    <input type="text" class="form-control" id="sigla_orgao" name="sigla_orgao" 
                                           value="<?= htmlspecialchars($config['sigla_orgao'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cnpj_orgao" class="form-label">CNPJ</label>
                                    <input type="text" class="form-control" id="cnpj_orgao" name="cnpj_orgao" 
                                           value="<?= htmlspecialchars($config['cnpj_orgao'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="endereco_orgao" class="form-label">Endereço Completo</label>
                                <textarea class="form-control" id="endereco_orgao" name="endereco_orgao" rows="3"><?= htmlspecialchars($config['endereco_orgao'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefone_orgao" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone_orgao" name="telefone_orgao" 
                                           value="<?= htmlspecialchars($config['telefone_orgao'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email_orgao" class="form-label">E-mail Oficial</label>
                                    <input type="email" class="form-control" id="email_orgao" name="email_orgao" 
                                           value="<?= htmlspecialchars($config['email_orgao'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="site_orgao" class="form-label">Site Oficial</label>
                                    <input type="url" class="form-control" id="site_orgao" name="site_orgao" 
                                           value="<?= htmlspecialchars($config['site_orgao'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="horario_funcionamento" class="form-label">Horário de Funcionamento</label>
                                    <input type="text" class="form-control" id="horario_funcionamento" name="horario_funcionamento" 
                                           value="<?= htmlspecialchars($config['horario_funcionamento'] ?? '') ?>"
                                           placeholder="Ex: Segunda à Sexta, 8h às 17h">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <h5 class="text-primary mb-3">Logotipo</h5>
                            
                            <div class="text-center mb-3">
                                <?php if (!empty($config['logo_orgao'])): ?>
                                <img src="<?= htmlspecialchars($config['logo_orgao']) ?>" 
                                     alt="Logo do Órgão" class="img-fluid mb-2" 
                                     style="max-height: 150px;" id="logoPreview">
                                <?php else: ?>
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center mb-2" 
                                     style="height: 150px;" id="logoPreview">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="logo_orgao" class="form-label">Upload Logo</label>
                                <input type="file" class="form-control" id="logo_orgao" name="logo_orgao" 
                                       accept="image/*" onchange="previewLogo(this)">
                                <div class="form-text">Formatos: PNG, JPG, SVG. Tamanho máximo: 2MB.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control" id="favicon" name="favicon" 
                                       accept="image/x-icon,image/png">
                                <div class="form-text">Formato ICO ou PNG 32x32px.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Lei de Acesso Tab -->
                <div class="tab-pane fade" id="lai">
                    <h5 class="text-primary mb-3">Configurações da Lei de Acesso à Informação</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prazo_resposta" class="form-label">Prazo de Resposta (dias)</label>
                            <input type="number" class="form-control" id="prazo_resposta" name="prazo_resposta" 
                                   value="<?= htmlspecialchars($config['prazo_resposta'] ?? '20') ?>" 
                                   min="1" max="60">
                            <div class="form-text">Padrão LAI: 20 dias úteis</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prazo_recurso" class="form-label">Prazo para Recurso (dias)</label>
                            <input type="number" class="form-control" id="prazo_recurso" name="prazo_recurso" 
                                   value="<?= htmlspecialchars($config['prazo_recurso'] ?? '10') ?>" 
                                   min="1" max="30">
                            <div class="form-text">Padrão LAI: 10 dias corridos</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prazo_analise_recurso" class="form-label">Prazo Análise Recurso (dias)</label>
                            <input type="number" class="form-control" id="prazo_analise_recurso" name="prazo_analise_recurso" 
                                   value="<?= htmlspecialchars($config['prazo_analise_recurso'] ?? '5') ?>" 
                                   min="1" max="15">
                            <div class="form-text">Padrão LAI: 5 dias úteis</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_instancias" class="form-label">Máximo de Instâncias</label>
                            <select class="form-select" id="max_instancias" name="max_instancias">
                                <option value="2" <?= ($config['max_instancias'] ?? '3') === '2' ? 'selected' : '' ?>>2 Instâncias</option>
                                <option value="3" <?= ($config['max_instancias'] ?? '3') === '3' ? 'selected' : '' ?>>3 Instâncias</option>
                                <option value="4" <?= ($config['max_instancias'] ?? '3') === '4' ? 'selected' : '' ?>>4 Instâncias</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="texto_info_lai" class="form-label">Texto Informativo LAI</label>
                        <textarea class="form-control" id="texto_info_lai" name="texto_info_lai" rows="4"><?= htmlspecialchars($config['texto_info_lai'] ?? '') ?></textarea>
                        <div class="form-text">Texto exibido na página inicial sobre a Lei de Acesso</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="permitir_anonimo" 
                                       name="permitir_anonimo" value="1" 
                                       <?= (!empty($config['permitir_anonimo'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="permitir_anonimo">
                                    Permitir pedidos anônimos
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="validar_cpf" 
                                       name="validar_cpf" value="1" 
                                       <?= (!empty($config['validar_cpf'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="validar_cpf">
                                    Validar CPF obrigatoriamente
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Email Tab -->
                <div class="tab-pane fade" id="email">
                    <h5 class="text-primary mb-3">Configurações de E-mail</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="smtp_host" class="form-label">Servidor SMTP</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                   value="<?= htmlspecialchars($config['smtp_host'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="smtp_porta" class="form-label">Porta SMTP</label>
                            <input type="number" class="form-control" id="smtp_porta" name="smtp_porta" 
                                   value="<?= htmlspecialchars($config['smtp_porta'] ?? '587') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="smtp_usuario" class="form-label">Usuário SMTP</label>
                            <input type="text" class="form-control" id="smtp_usuario" name="smtp_usuario" 
                                   value="<?= htmlspecialchars($config['smtp_usuario'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="smtp_senha" class="form-label">Senha SMTP</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="smtp_senha" name="smtp_senha" 
                                       value="<?= htmlspecialchars($config['smtp_senha'] ?? '') ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('smtp_senha')">
                                    <i class="bi bi-eye" id="smtp_senha-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email_remetente" class="form-label">E-mail Remetente</label>
                            <input type="email" class="form-control" id="email_remetente" name="email_remetente" 
                                   value="<?= htmlspecialchars($config['email_remetente'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nome_remetente" class="form-label">Nome do Remetente</label>
                            <input type="text" class="form-control" id="nome_remetente" name="nome_remetente" 
                                   value="<?= htmlspecialchars($config['nome_remetente'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smtp_ssl" 
                                       name="smtp_ssl" value="1" 
                                       <?= (!empty($config['smtp_ssl'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="smtp_ssl">
                                    Usar SSL/TLS
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn btn-outline-info" onclick="testarEmail()">
                                <i class="bi bi-envelope-check me-1"></i>
                                Testar Configuração
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Segurança Tab -->
                <div class="tab-pane fade" id="seguranca">
                    <h5 class="text-primary mb-3">Configurações de Segurança</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sessao_timeout" class="form-label">Timeout de Sessão (minutos)</label>
                            <input type="number" class="form-control" id="sessao_timeout" name="sessao_timeout" 
                                   value="<?= htmlspecialchars($config['sessao_timeout'] ?? '60') ?>" min="5" max="480">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_tentativas_login" class="form-label">Máx. Tentativas Login</label>
                            <input type="number" class="form-control" id="max_tentativas_login" name="max_tentativas_login" 
                                   value="<?= htmlspecialchars($config['max_tentativas_login'] ?? '5') ?>" min="3" max="10">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bloqueio_tempo" class="form-label">Tempo Bloqueio (minutos)</label>
                            <input type="number" class="form-control" id="bloqueio_tempo" name="bloqueio_tempo" 
                                   value="<?= htmlspecialchars($config['bloqueio_tempo'] ?? '15') ?>" min="5" max="120">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="complexidade_senha" class="form-label">Complexidade da Senha</label>
                            <select class="form-select" id="complexidade_senha" name="complexidade_senha">
                                <option value="baixa" <?= ($config['complexidade_senha'] ?? 'media') === 'baixa' ? 'selected' : '' ?>>Baixa (6+ caracteres)</option>
                                <option value="media" <?= ($config['complexidade_senha'] ?? 'media') === 'media' ? 'selected' : '' ?>>Média (8+ caracteres, números)</option>
                                <option value="alta" <?= ($config['complexidade_senha'] ?? 'media') === 'alta' ? 'selected' : '' ?>>Alta (8+ caracteres, números, símbolos)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="force_https" 
                                       name="force_https" value="1" 
                                       <?= (!empty($config['force_https'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="force_https">
                                    Forçar HTTPS
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="log_tentativas" 
                                       name="log_tentativas" value="1" 
                                       <?= (!empty($config['log_tentativas'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="log_tentativas">
                                    Log de Tentativas
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="captcha_ativo" 
                                       name="captcha_ativo" value="1" 
                                       <?= (!empty($config['captcha_ativo'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="captcha_ativo">
                                    CAPTCHA Ativo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Integrações Tab -->
                <div class="tab-pane fade" id="integracao">
                    <h5 class="text-primary mb-3">Integrações Externas</h5>
                    
                    <!-- Google OAuth -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-google me-2"></i>
                                Google OAuth
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="google_client_id" class="form-label">Client ID</label>
                                    <input type="text" class="form-control" id="google_client_id" name="google_client_id" 
                                           value="<?= htmlspecialchars($config['google_client_id'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="google_client_secret" class="form-label">Client Secret</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="google_client_secret" 
                                               name="google_client_secret" 
                                               value="<?= htmlspecialchars($config['google_client_secret'] ?? '') ?>">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="togglePassword('google_client_secret')">
                                            <i class="bi bi-eye" id="google_client_secret-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="google_oauth_ativo" 
                                       name="google_oauth_ativo" value="1" 
                                       <?= (!empty($config['google_oauth_ativo'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="google_oauth_ativo">
                                    Ativar login com Google
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gov.br -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-shield-check me-2"></i>
                                Gov.br (Acesso Cidadão)
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="govbr_client_id" class="form-label">Client ID</label>
                                    <input type="text" class="form-control" id="govbr_client_id" name="govbr_client_id" 
                                           value="<?= htmlspecialchars($config['govbr_client_id'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="govbr_client_secret" class="form-label">Client Secret</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="govbr_client_secret" 
                                               name="govbr_client_secret" 
                                               value="<?= htmlspecialchars($config['govbr_client_secret'] ?? '') ?>">
                                        <button class="btn btn-outline-secondary" type="button" 
                                                onclick="togglePassword('govbr_client_secret')">
                                            <i class="bi bi-eye" id="govbr_client_secret-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="govbr_ativo" 
                                       name="govbr_ativo" value="1" 
                                       <?= (!empty($config['govbr_ativo'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="govbr_ativo">
                                    Ativar login com Gov.br
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- API Externa -->
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-cloud-arrow-up me-2"></i>
                                API Externa / Webhook
                            </h6>
                            
                            <div class="mb-3">
                                <label for="webhook_url" class="form-label">URL do Webhook</label>
                                <input type="url" class="form-control" id="webhook_url" name="webhook_url" 
                                       value="<?= htmlspecialchars($config['webhook_url'] ?? '') ?>"
                                       placeholder="https://exemplo.com/webhook">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="api_token" class="form-label">Token de Autenticação</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="api_token" name="api_token" 
                                               value="<?= htmlspecialchars($config['api_token'] ?? '') ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="gerarToken()">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="webhook_ativo" 
                                               name="webhook_ativo" value="1" 
                                               <?= (!empty($config['webhook_ativo'])) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="webhook_ativo">
                                            Ativar Webhook
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Manutenção Tab -->
                <div class="tab-pane fade" id="manutencao">
                    <h5 class="text-primary mb-3">Ferramentas de Manutenção</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-info">
                                        <i class="bi bi-database me-2"></i>
                                        Banco de Dados
                                    </h6>
                                    <p class="card-text small text-muted mb-3">
                                        Ferramentas para manutenção do banco de dados.
                                    </p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="backupBanco()">
                                            <i class="bi bi-download me-1"></i>
                                            Backup Banco
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="limparLogs()">
                                            <i class="bi bi-trash me-1"></i>
                                            Limpar Logs Antigos
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="otimizarBanco()">
                                            <i class="bi bi-speedometer2 me-1"></i>
                                            Otimizar Tabelas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-warning">
                                        <i class="bi bi-folder me-2"></i>
                                        Arquivos
                                    </h6>
                                    <p class="card-text small text-muted mb-3">
                                        Gerenciamento de arquivos e cache do sistema.
                                    </p>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="limparCache()">
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                            Limpar Cache
                                        </button>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="verificarArquivos()">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Verificar Integridade
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="compactarLogs()">
                                            <i class="bi bi-archive me-1"></i>
                                            Compactar Logs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="modo_manutencao" 
                                       name="modo_manutencao" value="1" 
                                       <?= (!empty($config['modo_manutencao'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="modo_manutencao">
                                    <strong>Modo Manutenção</strong><br>
                                    <small class="text-muted">Sistema ficará indisponível para usuários</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="debug_ativo" 
                                       name="debug_ativo" value="1" 
                                       <?= (!empty($config['debug_ativo'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="debug_ativo">
                                    <strong>Modo Debug</strong><br>
                                    <small class="text-muted">Exibir informações de debug</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensagem_manutencao" class="form-label">Mensagem de Manutenção</label>
                        <textarea class="form-control" id="mensagem_manutencao" name="mensagem_manutencao" rows="3"><?= htmlspecialchars($config['mensagem_manutencao'] ?? 'Sistema em manutenção. Tente novamente em alguns minutos.') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="border-top pt-3 mt-4">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetarFormulario()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Restaurar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Salvar Configurações
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Form handling
document.getElementById('configuracaoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    submitBtn.disabled = true;
    
    fetch('/admin/configuracoes', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Configurações salvas com sucesso!', 'success');
        } else {
            showNotification('Erro ao salvar: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showNotification('Erro ao salvar configurações.', 'danger');
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Logo preview
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-fluid" style="max-height: 150px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Password toggle
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Token generator
function gerarToken() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let token = '';
    for (let i = 0; i < 32; i++) {
        token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('api_token').value = token;
}

// Test functions
function testarEmail() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Testando...';
    button.disabled = true;
    
    fetch('/admin/configuracoes/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('E-mail de teste enviado com sucesso!', 'success');
        } else {
            showNotification('Erro no teste: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showNotification('Erro ao testar e-mail.', 'danger');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Maintenance functions
function backupBanco() {
    if (confirm('Deseja gerar um backup completo do banco de dados?')) {
        window.open('/admin/configuracoes/backup-db', '_blank');
    }
}

function limparLogs() {
    if (confirm('Deseja limpar logs com mais de 30 dias?')) {
        fetch('/admin/configuracoes/clear-logs', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'danger');
        });
    }
}

function otimizarBanco() {
    if (confirm('Deseja otimizar as tabelas do banco de dados?')) {
        fetch('/admin/configuracoes/optimize-db', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'danger');
        });
    }
}

function limparCache() {
    if (confirm('Deseja limpar todo o cache do sistema?')) {
        fetch('/admin/configuracoes/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'danger');
        });
    }
}

function verificarArquivos() {
    fetch('/admin/configuracoes/check-integrity', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'warning');
    });
}

function compactarLogs() {
    if (confirm('Deseja compactar os arquivos de log antigos?')) {
        fetch('/admin/configuracoes/compress-logs', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'danger');
        });
    }
}

function exportarConfiguracoes() {
    window.open('/admin/configuracoes/export', '_blank');
}

function resetarConfiguracoes() {
    if (confirm('Deseja restaurar todas as configurações para os valores padrão? Esta ação não pode ser desfeita.')) {
        fetch('/admin/configuracoes/reset', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification('Erro ao resetar: ' + data.message, 'danger');
            }
        });
    }
}

function resetarFormulario() {
    if (confirm('Deseja descartar todas as alterações não salvas?')) {
        location.reload();
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// CNPJ mask
document.getElementById('cnpj_orgao').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    this.value = value;
});

// Phone mask
document.getElementById('telefone_orgao').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    } else {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    this.value = value;
});
</script>

<style>
.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    background-color: transparent;
    border-bottom: 2px solid #0d6efd;
    color: #0d6efd;
    font-weight: 500;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .nav-tabs {
        flex-wrap: wrap;
    }
    
    .nav-tabs .nav-link {
        font-size: 0.875rem;
        padding: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>