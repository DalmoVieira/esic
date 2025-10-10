-- =======================================================
-- SISTEMA E-SIC - Lei de Acesso à Informação
-- Esquema do Banco de Dados MySQL
-- =======================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS esic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE esic_db;

-- =======================================================
-- TABELA: usuarios (Administradores/Operadores)
-- =======================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    cargo VARCHAR(100),
    unidade VARCHAR(100),
    nivel_acesso ENUM('admin', 'operador', 'gestor') DEFAULT 'operador',
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    token_verificacao VARCHAR(100),
    token_reset_senha VARCHAR(100),
    reset_senha_expira DATETIME,
    ultimo_login DATETIME,
    tentativas_login INT DEFAULT 0,
    bloqueado_ate DATETIME NULL,
    google_id VARCHAR(100),
    govbr_id VARCHAR(100),
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_nivel (nivel_acesso),
    INDEX idx_ativo (ativo)
);

-- =======================================================
-- TABELA: pedidos (Solicitações de Informação)
-- =======================================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(20) NOT NULL UNIQUE,
    nome_solicitante VARCHAR(100) NOT NULL,
    email_solicitante VARCHAR(100) NOT NULL,
    telefone_solicitante VARCHAR(20),
    cpf_solicitante VARCHAR(14),
    endereco_solicitante TEXT,
    assunto VARCHAR(200) NOT NULL,
    descricao TEXT NOT NULL,
    forma_recebimento ENUM('email', 'presencial', 'correio') DEFAULT 'email',
    endereco_resposta TEXT,
    categoria VARCHAR(100),
    subcategoria VARCHAR(100),
    unidade_responsavel VARCHAR(100),
    status ENUM('pendente', 'em_andamento', 'respondido', 'negado', 'recurso') DEFAULT 'pendente',
    prioridade ENUM('normal', 'alta', 'urgente') DEFAULT 'normal',
    prazo_resposta DATE,
    data_resposta DATETIME,
    resposta TEXT,
    resposta_usuario_id INT,
    observacoes TEXT,
    arquivo_anexo VARCHAR(255),
    arquivo_resposta VARCHAR(255),
    visualizado BOOLEAN DEFAULT FALSE,
    ip_solicitante VARCHAR(45),
    user_agent TEXT,
    origem ENUM('site', 'presencial', 'telefone', 'email') DEFAULT 'site',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (resposta_usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_protocolo (protocolo),
    INDEX idx_email (email_solicitante),
    INDEX idx_status (status),
    INDEX idx_data (created_at),
    INDEX idx_prazo (prazo_resposta),
    INDEX idx_unidade (unidade_responsavel)
);

-- =======================================================
-- TABELA: recursos (Recursos Administrativos)
-- =======================================================
CREATE TABLE recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    protocolo_recurso VARCHAR(20) NOT NULL UNIQUE,
    tipo ENUM('primeira_instancia', 'segunda_instancia', 'cgu') DEFAULT 'primeira_instancia',
    justificativa TEXT NOT NULL,
    status ENUM('pendente', 'em_andamento', 'deferido', 'indeferido') DEFAULT 'pendente',
    prazo_resposta DATE,
    data_resposta DATETIME,
    resposta TEXT,
    resposta_usuario_id INT,
    observacoes TEXT,
    arquivo_anexo VARCHAR(255),
    arquivo_resposta VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (resposta_usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_pedido (pedido_id),
    INDEX idx_protocolo (protocolo_recurso),
    INDEX idx_status (status),
    INDEX idx_tipo (tipo)
);

-- =======================================================
-- TABELA: auth_logs (Logs de Autenticação)
-- =======================================================
CREATE TABLE auth_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    email VARCHAR(100),
    tipo_evento ENUM('login', 'logout', 'tentativa_falha', 'reset_senha', 'bloqueio', 'desbloqueio') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    sucesso BOOLEAN DEFAULT FALSE,
    detalhes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_email (email),
    INDEX idx_tipo (tipo_evento),
    INDEX idx_data (created_at),
    INDEX idx_ip (ip_address)
);

-- =======================================================
-- TABELA: configuracoes (Configurações do Sistema)
-- =======================================================
CREATE TABLE configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    tipo ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    descricao TEXT,
    categoria VARCHAR(50),
    editavel BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_chave (chave),
    INDEX idx_categoria (categoria)
);

-- =======================================================
-- TABELA: notificacoes (Sistema de Notificações)
-- =======================================================
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('pedido_novo', 'pedido_resposta', 'recurso_novo', 'prazo_vencendo', 'sistema') NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensagem TEXT NOT NULL,
    destinatario_email VARCHAR(100),
    usuario_id INT,
    pedido_id INT,
    recurso_id INT,
    enviado BOOLEAN DEFAULT FALSE,
    tentativas_envio INT DEFAULT 0,
    data_envio DATETIME,
    erro_envio TEXT,
    lido BOOLEAN DEFAULT FALSE,
    data_leitura DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (recurso_id) REFERENCES recursos(id) ON DELETE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_enviado (enviado),
    INDEX idx_usuario (usuario_id),
    INDEX idx_pedido (pedido_id),
    INDEX idx_data (created_at)
);

-- =======================================================
-- TABELA: templates_email (Templates de Email)
-- =======================================================
CREATE TABLE templates_email (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    assunto VARCHAR(200) NOT NULL,
    corpo TEXT NOT NULL,
    variaveis JSON,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo)
);

-- =======================================================
-- TABELA: historico_pedidos (Histórico de Alterações)
-- =======================================================
CREATE TABLE historico_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    status_anterior VARCHAR(50),
    status_novo VARCHAR(50),
    observacoes TEXT,
    dados_alterados JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_pedido (pedido_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_data (created_at)
);

-- =======================================================
-- TABELA: estatisticas (Estatísticas do Sistema)
-- =======================================================
CREATE TABLE estatisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_referencia DATE NOT NULL,
    pedidos_total INT DEFAULT 0,
    pedidos_pendentes INT DEFAULT 0,
    pedidos_respondidos INT DEFAULT 0,
    pedidos_negados INT DEFAULT 0,
    recursos_total INT DEFAULT 0,
    tempo_medio_resposta DECIMAL(5,2),
    unidade VARCHAR(100),
    categoria VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_estatistica (data_referencia, unidade, categoria),
    INDEX idx_data (data_referencia),
    INDEX idx_unidade (unidade)
);

-- =======================================================
-- INSERIR DADOS INICIAIS
-- =======================================================

-- Inserir usuário administrador padrão
INSERT INTO usuarios (nome, email, senha, nivel_acesso, ativo, email_verificado) 
VALUES ('Administrador', 'admin@esic.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE, TRUE);

-- Configurações iniciais do sistema
INSERT INTO configuracoes (chave, valor, tipo, descricao, categoria) VALUES
('site_nome', 'Sistema E-SIC', 'string', 'Nome do sistema', 'geral'),
('site_descricao', 'Sistema Eletrônico do Serviço de Informação ao Cidadão', 'string', 'Descrição do sistema', 'geral'),
('orgao_nome', 'Órgão Público', 'string', 'Nome do órgão', 'geral'),
('orgao_endereco', 'Endereço do órgão', 'string', 'Endereço completo', 'geral'),
('orgao_telefone', '(11) 0000-0000', 'string', 'Telefone de contato', 'geral'),
('orgao_email', 'contato@orgao.gov.br', 'string', 'Email de contato', 'geral'),
('prazo_resposta_dias', '20', 'number', 'Prazo padrão para resposta (dias)', 'prazos'),
('prazo_recurso_dias', '10', 'number', 'Prazo adicional para recurso (dias)', 'prazos'),
('email_smtp_host', 'smtp.gmail.com', 'string', 'Servidor SMTP', 'email'),
('email_smtp_port', '587', 'number', 'Porta SMTP', 'email'),
('email_smtp_user', '', 'string', 'Usuário SMTP', 'email'),
('email_smtp_pass', '', 'string', 'Senha SMTP', 'email'),
('email_from_name', 'Sistema E-SIC', 'string', 'Nome do remetente', 'email'),
('email_from_address', 'noreply@esic.gov.br', 'string', 'Email do remetente', 'email'),
('uploads_max_size', '10', 'number', 'Tamanho máximo de arquivo (MB)', 'uploads'),
('uploads_tipos_permitidos', 'pdf,doc,docx,jpg,jpeg,png', 'string', 'Tipos de arquivo permitidos', 'uploads'),
('google_client_id', '', 'string', 'Google OAuth Client ID', 'oauth'),
('google_client_secret', '', 'string', 'Google OAuth Client Secret', 'oauth'),
('govbr_client_id', '', 'string', 'Gov.br OAuth Client ID', 'oauth'),
('govbr_client_secret', '', 'string', 'Gov.br OAuth Client Secret', 'oauth'),
('sistema_manutencao', 'false', 'boolean', 'Sistema em manutenção', 'sistema'),
('logs_retention_dias', '365', 'number', 'Dias para manter logs', 'sistema');

-- Templates de email padrão
INSERT INTO templates_email (nome, assunto, corpo, variaveis) VALUES
('pedido_confirmacao', 
 'Confirmação de Pedido - Protocolo {{protocolo}}',
 '<h2>Pedido Registrado com Sucesso</h2><p>Seu pedido foi registrado com o protocolo <strong>{{protocolo}}</strong>.</p><p><strong>Assunto:</strong> {{assunto}}</p><p><strong>Prazo de resposta:</strong> {{prazo_resposta}}</p><p>Você pode acompanhar o andamento através do nosso site.</p>',
 '["protocolo", "assunto", "prazo_resposta", "nome_solicitante"]'),
 
('pedido_resposta',
 'Resposta do seu pedido - Protocolo {{protocolo}}',
 '<h2>Seu pedido foi respondido</h2><p>O pedido de protocolo <strong>{{protocolo}}</strong> foi respondido.</p><p><strong>Resposta:</strong></p><div>{{resposta}}</div><p>Caso não concorde com a resposta, você pode interpor recurso.</p>',
 '["protocolo", "resposta", "nome_solicitante"]'),
 
('novo_pedido_admin',
 'Novo pedido cadastrado - Protocolo {{protocolo}}',
 '<h2>Novo Pedido Cadastrado</h2><p>Um novo pedido foi cadastrado no sistema:</p><p><strong>Protocolo:</strong> {{protocolo}}</p><p><strong>Solicitante:</strong> {{nome_solicitante}}</p><p><strong>Assunto:</strong> {{assunto}}</p>',
 '["protocolo", "nome_solicitante", "assunto", "email_solicitante"]');

-- =======================================================
-- VIEWS PARA RELATÓRIOS
-- =======================================================

-- View para estatísticas gerais
CREATE VIEW vw_estatisticas_gerais AS
SELECT 
    DATE(created_at) as data,
    COUNT(*) as total_pedidos,
    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN status = 'respondido' THEN 1 ELSE 0 END) as respondidos,
    SUM(CASE WHEN status = 'negado' THEN 1 ELSE 0 END) as negados,
    AVG(CASE 
        WHEN data_resposta IS NOT NULL 
        THEN DATEDIFF(data_resposta, created_at) 
        ELSE NULL 
    END) as tempo_medio_resposta
FROM pedidos 
GROUP BY DATE(created_at)
ORDER BY data DESC;

-- View para pedidos com atraso
CREATE VIEW vw_pedidos_atrasados AS
SELECT 
    p.*,
    DATEDIFF(CURRENT_DATE, p.prazo_resposta) as dias_atraso
FROM pedidos p
WHERE p.status IN ('pendente', 'em_andamento') 
AND p.prazo_resposta < CURRENT_DATE;

-- =======================================================
-- PROCEDURES E FUNCTIONS
-- =======================================================

DELIMITER //

-- Procedure para gerar protocolo automático
CREATE FUNCTION gerar_protocolo() 
RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE novo_protocolo VARCHAR(20);
    DECLARE contador INT DEFAULT 1;
    DECLARE data_hoje VARCHAR(8);
    
    SET data_hoje = DATE_FORMAT(CURDATE(), '%Y%m%d');
    
    -- Buscar próximo número sequencial do dia
    SELECT IFNULL(MAX(CAST(SUBSTRING(protocolo, -4) AS UNSIGNED)), 0) + 1 
    INTO contador
    FROM pedidos 
    WHERE protocolo LIKE CONCAT('ESIC-', data_hoje, '-%');
    
    SET novo_protocolo = CONCAT('ESIC-', data_hoje, '-', LPAD(contador, 4, '0'));
    
    RETURN novo_protocolo;
END //

-- Procedure para calcular prazo de resposta
CREATE FUNCTION calcular_prazo_resposta(data_pedido DATE, dias_uteis INT)
RETURNS DATE
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE data_final DATE;
    DECLARE contador INT DEFAULT 0;
    DECLARE data_atual DATE;
    
    SET data_atual = data_pedido;
    
    WHILE contador < dias_uteis DO
        SET data_atual = DATE_ADD(data_atual, INTERVAL 1 DAY);
        
        -- Pular finais de semana (sábado = 7, domingo = 1)
        IF DAYOFWEEK(data_atual) NOT IN (1, 7) THEN
            SET contador = contador + 1;
        END IF;
    END WHILE;
    
    SET data_final = data_atual;
    
    RETURN data_final;
END //

DELIMITER ;

-- =======================================================
-- TRIGGERS
-- =======================================================

DELIMITER //

-- Trigger para gerar protocolo automático
CREATE TRIGGER tr_pedido_protocolo 
BEFORE INSERT ON pedidos
FOR EACH ROW
BEGIN
    IF NEW.protocolo IS NULL OR NEW.protocolo = '' THEN
        SET NEW.protocolo = gerar_protocolo();
    END IF;
    
    IF NEW.prazo_resposta IS NULL THEN
        SET NEW.prazo_resposta = calcular_prazo_resposta(CURDATE(), 20);
    END IF;
END //

-- Trigger para histórico de alterações
CREATE TRIGGER tr_pedido_historico 
AFTER UPDATE ON pedidos
FOR EACH ROW
BEGIN
    INSERT INTO historico_pedidos (
        pedido_id, 
        usuario_id, 
        acao, 
        status_anterior, 
        status_novo,
        observacoes,
        dados_alterados
    ) VALUES (
        NEW.id,
        NEW.resposta_usuario_id,
        'status_alterado',
        OLD.status,
        NEW.status,
        CASE 
            WHEN OLD.status != NEW.status THEN CONCAT('Status alterado de ', OLD.status, ' para ', NEW.status)
            ELSE 'Pedido atualizado'
        END,
        JSON_OBJECT(
            'status_anterior', OLD.status,
            'status_novo', NEW.status,
            'resposta_anterior', OLD.resposta,
            'resposta_nova', NEW.resposta
        )
    );
END //

DELIMITER ;

-- =======================================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- =======================================================

-- Índices compostos para consultas frequentes
CREATE INDEX idx_pedidos_status_data ON pedidos(status, created_at);
CREATE INDEX idx_pedidos_unidade_status ON pedidos(unidade_responsavel, status);
CREATE INDEX idx_notificacoes_enviado_tipo ON notificacoes(enviado, tipo);
CREATE INDEX idx_historico_pedido_data ON historico_pedidos(pedido_id, created_at);

-- =======================================================
-- COMENTÁRIOS DAS TABELAS
-- =======================================================

ALTER TABLE usuarios COMMENT = 'Usuários administradores e operadores do sistema';
ALTER TABLE pedidos COMMENT = 'Pedidos de acesso à informação dos cidadãos';
ALTER TABLE recursos COMMENT = 'Recursos administrativos contra respostas';
ALTER TABLE auth_logs COMMENT = 'Logs de autenticação e segurança';
ALTER TABLE configuracoes COMMENT = 'Configurações gerais do sistema';
ALTER TABLE notificacoes COMMENT = 'Sistema de notificações por email';
ALTER TABLE templates_email COMMENT = 'Templates para emails automáticos';
ALTER TABLE historico_pedidos COMMENT = 'Histórico de alterações nos pedidos';
ALTER TABLE estatisticas COMMENT = 'Estatísticas consolidadas do sistema';

-- =======================================================
-- FIM DO SCHEMA
-- =======================================================