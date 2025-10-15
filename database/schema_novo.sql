-- =============================================
-- Sistema E-SIC - Schema do Banco de Dados
-- Prefeitura Municipal de Rio Claro - RJ
-- VERSÃO 2.0 - COMPLETA COM SISTEMA DE PEDIDOS
-- =============================================

-- Criar banco se não existir
CREATE DATABASE IF NOT EXISTS esic_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE esic_db;

-- =============================================
-- TABELA: usuarios
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    cpf_cnpj VARCHAR(18) UNIQUE NOT NULL,
    telefone VARCHAR(15),
    tipo_pessoa ENUM('fisica', 'juridica') NOT NULL DEFAULT 'fisica',
    tipo_usuario ENUM('cidadao', 'funcionario', 'administrador') NOT NULL DEFAULT 'cidadao',
    senha_hash VARCHAR(255) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    email_verificado TINYINT(1) DEFAULT 0,
    token_verificacao VARCHAR(100),
    token_recuperacao VARCHAR(100),
    token_expiracao DATETIME,
    ultimo_login DATETIME,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_cpf_cnpj (cpf_cnpj),
    INDEX idx_tipo_usuario (tipo_usuario),
    INDEX idx_ativo (ativo)
);

-- =============================================
-- TABELA: orgaos_setores
-- =============================================
CREATE TABLE IF NOT EXISTS orgaos_setores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    sigla VARCHAR(10),
    descricao TEXT,
    responsavel_id INT,
    email VARCHAR(100),
    telefone VARCHAR(15),
    prazo_resposta INT DEFAULT 20, -- dias úteis
    ativo TINYINT(1) DEFAULT 1,
    ordem_exibicao INT DEFAULT 0,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_ativo (ativo),
    INDEX idx_ordem (ordem_exibicao)
);

-- =============================================
-- TABELA: pedidos
-- =============================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(20) UNIQUE NOT NULL,
    requerente_id INT NOT NULL,
    orgao_id INT,
    
    -- Dados do pedido
    assunto VARCHAR(200) NOT NULL,
    descricao TEXT NOT NULL,
    forma_recebimento ENUM('sistema', 'email', 'presencial', 'correio') DEFAULT 'sistema',
    
    -- Status e tramitação  
    status ENUM('aguardando', 'em_analise', 'respondido', 'negado', 'parcialmente_atendido', 'cancelado') DEFAULT 'aguardando',
    prioridade ENUM('normal', 'urgente') DEFAULT 'normal',
    
    -- Prazos
    data_limite DATE,
    data_resposta DATETIME,
    prazo_prorrogado TINYINT(1) DEFAULT 0,
    notificado_prazo_proximo TINYINT(1) DEFAULT 0,
    notificado_prazo_vencido TINYINT(1) DEFAULT 0,
    
    -- Resposta
    resposta TEXT,
    tipo_resposta ENUM('deferido', 'indeferido', 'parcial') NULL,
    motivo_negativa TEXT,
    responsavel_resposta_id INT,
    
    -- Dados de acesso
    informacao_classificada TINYINT(1) DEFAULT 0,
    grau_sigilo ENUM('publico', 'reservado', 'secreto', 'ultrassecreto') DEFAULT 'publico',
    
    -- Metadados
    ip_origem VARCHAR(45),
    user_agent TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (requerente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orgao_id) REFERENCES orgaos_setores(id) ON DELETE SET NULL,
    FOREIGN KEY (responsavel_resposta_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_protocolo (protocolo),
    INDEX idx_requerente (requerente_id),
    INDEX idx_status (status),
    INDEX idx_data_cadastro (data_cadastro),
    INDEX idx_data_limite (data_limite),
    INDEX idx_orgao (orgao_id)
);

-- =============================================
-- TABELA: recursos
-- =============================================
CREATE TABLE IF NOT EXISTS recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(20) UNIQUE NOT NULL,
    pedido_id INT NOT NULL,
    requerente_id INT NOT NULL,
    
    -- Dados do recurso
    tipo ENUM('primeira_instancia', 'segunda_instancia', 'terceira_instancia') DEFAULT 'primeira_instancia',
    motivo ENUM('negativa_acesso', 'demora_resposta', 'resposta_incompleta', 'classificacao_indevida', 'outro') NOT NULL,
    justificativa TEXT NOT NULL,
    
    -- Status
    status ENUM('aguardando', 'em_analise', 'deferido', 'indeferido', 'cancelado') DEFAULT 'aguardando',
    
    -- Prazos
    data_limite DATE,
    data_decisao DATETIME,
    
    -- Decisão
    decisao TEXT,
    responsavel_decisao_id INT,
    
    -- Metadados
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (requerente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (responsavel_decisao_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_protocolo (protocolo),
    INDEX idx_pedido (pedido_id),
    INDEX idx_status (status),
    INDEX idx_tipo (tipo),
    INDEX idx_data_cadastro (data_cadastro)
);

-- =============================================
-- TABELA: anexos
-- =============================================
CREATE TABLE IF NOT EXISTS anexos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    recurso_id INT,
    
    -- Dados do arquivo
    nome_original VARCHAR(255) NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    tamanho INT NOT NULL,
    hash_arquivo VARCHAR(64),
    
    -- Classificação
    tipo ENUM('documento', 'imagem', 'audio', 'video', 'outro') DEFAULT 'documento',
    publico TINYINT(1) DEFAULT 1,
    
    -- Metadados
    usuario_upload_id INT NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (recurso_id) REFERENCES recursos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_upload_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    
    INDEX idx_pedido (pedido_id),
    INDEX idx_recurso (recurso_id),
    INDEX idx_tipo (tipo),
    INDEX idx_data_upload (data_upload)
);

-- =============================================
-- TABELA: tramitacoes
-- =============================================
CREATE TABLE IF NOT EXISTS tramitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    recurso_id INT,
    
    -- Dados da tramitação
    status_anterior VARCHAR(50),
    status_novo VARCHAR(50) NOT NULL,
    observacoes TEXT,
    
    -- Responsável
    usuario_id INT NOT NULL,
    orgao_origem_id INT,
    orgao_destino_id INT,
    
    -- Metadados
    data_tramitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (recurso_id) REFERENCES recursos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (orgao_origem_id) REFERENCES orgaos_setores(id) ON DELETE SET NULL,
    FOREIGN KEY (orgao_destino_id) REFERENCES orgaos_setores(id) ON DELETE SET NULL,
    
    INDEX idx_pedido (pedido_id),
    INDEX idx_recurso (recurso_id),
    INDEX idx_data_tramitacao (data_tramitacao)
);

-- =============================================
-- TABELA: logs_sistema
-- =============================================
CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    
    -- Dados do log
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(50),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    
    -- Contexto
    ip VARCHAR(45),
    user_agent TEXT,
    sessao_id VARCHAR(100),
    
    -- Metadados
    data_log TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    
    INDEX idx_usuario (usuario_id),
    INDEX idx_acao (acao),
    INDEX idx_data_log (data_log),
    INDEX idx_tabela (tabela_afetada)
);

-- =============================================
-- TABELA: configuracoes
-- =============================================
CREATE TABLE IF NOT EXISTS configuracoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    tipo ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    descricao VARCHAR(255),
    grupo VARCHAR(50) DEFAULT 'geral',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_chave (chave),
    INDEX idx_grupo (grupo)
);

-- =============================================
-- DADOS INICIAIS
-- =============================================

-- Usuário administrador padrão
INSERT IGNORE INTO usuarios (nome, email, cpf_cnpj, tipo_usuario, senha_hash, ativo, email_verificado) 
VALUES 
('Administrador Sistema', 'admin@rioclaro.rj.gov.br', '000.000.000-00', 'administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1),
('Funcionário Teste', 'funcionario@rioclaro.rj.gov.br', '111.111.111-11', 'funcionario', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Órgãos/Setores padrão
INSERT IGNORE INTO orgaos_setores (nome, sigla, descricao, prazo_resposta) VALUES
('Gabinete do Prefeito', 'GAB', 'Gabinete do Prefeito Municipal', 20),
('Secretaria de Administração', 'SEMAD', 'Secretaria Municipal de Administração', 20),
('Secretaria de Finanças', 'SEMFIN', 'Secretaria Municipal de Finanças', 15),
('Secretaria de Obras', 'SEMOB', 'Secretaria Municipal de Obras', 20),
('Secretaria de Saúde', 'SEMUS', 'Secretaria Municipal de Saúde', 15),
('Secretaria de Educação', 'SEMED', 'Secretaria Municipal de Educação', 15),
('Ouvidoria Municipal', 'OUVIDORIA', 'Ouvidoria Municipal', 10);

-- Configurações iniciais
INSERT IGNORE INTO configuracoes (chave, valor, tipo, descricao, grupo) VALUES
('sistema_nome', 'E-SIC Rio Claro', 'string', 'Nome do sistema', 'geral'),
('sistema_email', 'esic@rioclaro.rj.gov.br', 'string', 'Email principal do sistema', 'geral'),
('prazo_resposta_padrao', '20', 'number', 'Prazo padrão em dias úteis', 'prazos'),
('permitir_prorrogacao', 'true', 'boolean', 'Permitir prorrogação de prazos', 'prazos'),
('dias_prorrogacao', '10', 'number', 'Dias de prorrogação permitidos', 'prazos'),
('tamanho_max_anexo', '10485760', 'number', 'Tamanho máximo de anexo em bytes (10MB)', 'arquivos'),
('tipos_anexo_permitidos', '["pdf","doc","docx","jpg","jpeg","png","txt"]', 'json', 'Tipos de arquivo permitidos', 'arquivos'),
-- Configurações de email/SMTP
('smtp_host', 'localhost', 'string', 'Servidor SMTP', 'email'),
('smtp_port', '587', 'number', 'Porta SMTP', 'email'),
('smtp_user', '', 'string', 'Usuário SMTP', 'email'),
('smtp_pass', '', 'string', 'Senha SMTP', 'email'),
('from_email', 'noreply@rioclaro.sp.gov.br', 'string', 'Email remetente', 'email'),
('from_name', 'E-SIC Rio Claro', 'string', 'Nome do remetente', 'email'),
('base_url', 'http://localhost/esic', 'string', 'URL base do sistema', 'email'),
('notificacoes_ativas', 'true', 'boolean', 'Ativar notificações por email', 'email');