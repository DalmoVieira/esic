-- Database: esic_system
-- Charset: utf8mb4_unicode_ci

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `pedido_historico`;
DROP TABLE IF EXISTS `recursos`;
DROP TABLE IF EXISTS `pedidos`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `orgaos`;
DROP TABLE IF EXISTS `categorias`;
DROP TABLE IF EXISTS `configuracoes`;

-- Tabela de configurações do sistema
CREATE TABLE `configuracoes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chave` varchar(100) NOT NULL,
    `valor` text,
    `descricao` text,
    `tipo` enum('text','textarea','number','boolean','select') DEFAULT 'text',
    `opcoes` text,
    `categoria` varchar(50) DEFAULT 'geral',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de categorias de pedidos
CREATE TABLE `categorias` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `descricao` text,
    `icone` varchar(50) DEFAULT NULL,
    `cor` varchar(7) DEFAULT '#007bff',
    `prazo_dias` int(11) DEFAULT 20,
    `ativo` tinyint(1) DEFAULT 1,
    `ordem` int(11) DEFAULT 0,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de órgãos/unidades
CREATE TABLE `orgaos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(150) NOT NULL,
    `sigla` varchar(10),
    `descricao` text,
    `email` varchar(100),
    `telefone` varchar(20),
    `endereco` text,
    `responsavel` varchar(100),
    `ativo` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de usuários do sistema
CREATE TABLE `usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `senha` varchar(255) NOT NULL,
    `cpf` varchar(11),
    `telefone` varchar(20),
    `cargo` varchar(100),
    `orgao_id` int(11),
    `perfil` enum('administrador','operador','consulta') DEFAULT 'operador',
    `ativo` tinyint(1) DEFAULT 1,
    `ultimo_login` timestamp NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `orgao_id` (`orgao_id`),
    CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`orgao_id`) REFERENCES `orgaos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de pedidos de informação
CREATE TABLE `pedidos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `protocolo` varchar(20) NOT NULL,
    `nome_solicitante` varchar(100) NOT NULL,
    `email_solicitante` varchar(100) NOT NULL,
    `cpf_solicitante` varchar(11),
    `cnpj_solicitante` varchar(14),
    `telefone_solicitante` varchar(20),
    `endereco_solicitante` text,
    `cep_solicitante` varchar(8),
    `cidade_solicitante` varchar(100),
    `estado_solicitante` varchar(2),
    `categoria_id` int(11) NOT NULL,
    `orgao_id` int(11),
    `assunto` varchar(200) NOT NULL,
    `descricao_pedido` text NOT NULL,
    `justificativa` text,
    `forma_recebimento` enum('email','postal','presencial') DEFAULT 'email',
    `endereco_postal` text,
    `tipo_pessoa` enum('fisica','juridica') DEFAULT 'fisica',
    `anexos` json,
    `ip_solicitante` varchar(45),
    `user_agent` text,
    `status` enum('pendente','em_andamento','atendido','negado','recurso') DEFAULT 'pendente',
    `prazo_atendimento` date,
    `data_resposta` timestamp NULL,
    `resposta` text,
    `anexos_resposta` json,
    `usuario_responsavel_id` int(11),
    `observacoes_internas` text,
    `prioridade` enum('baixa','normal','alta','urgente') DEFAULT 'normal',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `protocolo` (`protocolo`),
    KEY `categoria_id` (`categoria_id`),
    KEY `orgao_id` (`orgao_id`),
    KEY `usuario_responsavel_id` (`usuario_responsavel_id`),
    KEY `status` (`status`),
    KEY `prazo_atendimento` (`prazo_atendimento`),
    CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
    CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`orgao_id`) REFERENCES `orgaos` (`id`) ON DELETE SET NULL,
    CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`usuario_responsavel_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de recursos
CREATE TABLE `recursos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `protocolo` varchar(20) NOT NULL,
    `pedido_id` int(11) NOT NULL,
    `tipo` enum('primeira_instancia','segunda_instancia','terceira_instancia') DEFAULT 'primeira_instancia',
    `motivo` text NOT NULL,
    `justificativa` text NOT NULL,
    `anexos` json,
    `status` enum('pendente','em_andamento','deferido','indeferido') DEFAULT 'pendente',
    `prazo_resposta` date,
    `data_resposta` timestamp NULL,
    `resposta` text,
    `anexos_resposta` json,
    `usuario_responsavel_id` int(11),
    `observacoes_internas` text,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `protocolo` (`protocolo`),
    KEY `pedido_id` (`pedido_id`),
    KEY `usuario_responsavel_id` (`usuario_responsavel_id`),
    KEY `status` (`status`),
    CONSTRAINT `recursos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `recursos_ibfk_2` FOREIGN KEY (`usuario_responsavel_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de histórico dos pedidos
CREATE TABLE `pedido_historico` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `pedido_id` int(11) NOT NULL,
    `usuario_id` int(11),
    `acao` varchar(50) NOT NULL,
    `descricao` text,
    `dados_anteriores` json,
    `dados_novos` json,
    `ip` varchar(45),
    `user_agent` text,
    `data_acao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `pedido_id` (`pedido_id`),
    KEY `usuario_id` (`usuario_id`),
    KEY `data_acao` (`data_acao`),
    CONSTRAINT `pedido_historico_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pedido_historico_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir dados iniciais

-- Configurações do sistema
INSERT INTO `configuracoes` (`chave`, `valor`, `descricao`, `tipo`, `categoria`) VALUES
('sistema_nome', 'E-SIC - Sistema Eletrônico de Informações ao Cidadão', 'Nome do sistema', 'text', 'geral'),
('sistema_email', 'esic@exemplo.gov.br', 'Email principal do sistema', 'text', 'geral'),
('sistema_telefone', '(11) 1234-5678', 'Telefone de contato', 'text', 'geral'),
('prazo_padrao_dias', '20', 'Prazo padrão para resposta (dias)', 'number', 'geral'),
('prazo_recurso_dias', '10', 'Prazo padrão para recurso (dias)', 'number', 'geral'),
('tamanho_max_arquivo', '10485760', 'Tamanho máximo de arquivo em bytes (10MB)', 'number', 'uploads'),
('tipos_arquivo_permitidos', 'pdf,doc,docx,jpg,jpeg,png,gif,txt,zip,rar', 'Tipos de arquivo permitidos', 'text', 'uploads'),
('smtp_host', 'localhost', 'Servidor SMTP', 'text', 'email'),
('smtp_port', '587', 'Porta SMTP', 'number', 'email'),
('smtp_usuario', '', 'Usuário SMTP', 'text', 'email'),
('smtp_senha', '', 'Senha SMTP', 'text', 'email'),
('smtp_seguranca', 'tls', 'Segurança SMTP (tls/ssl)', 'select', 'email'),
('manter_logs_dias', '365', 'Manter logs por quantos dias', 'number', 'sistema'),
('debug_mode', '0', 'Modo debug ativo', 'boolean', 'sistema');

-- Categorias padrão
INSERT INTO `categorias` (`nome`, `descricao`, `icone`, `prazo_dias`, `ordem`) VALUES
('Informações Gerais', 'Solicitações de informações gerais da administração pública', 'fas fa-info-circle', 20, 1),
('Contratos e Licitações', 'Informações sobre contratos, licitações e processos de compra', 'fas fa-file-contract', 20, 2),
('Recursos Humanos', 'Informações sobre servidores, folha de pagamento e concursos', 'fas fa-users', 20, 3),
('Orçamento e Finanças', 'Informações orçamentárias, financeiras e contábeis', 'fas fa-dollar-sign', 20, 4),
('Obras e Serviços', 'Informações sobre obras públicas e prestação de serviços', 'fas fa-hard-hat', 20, 5),
('Saúde', 'Informações relacionadas à área da saúde', 'fas fa-heartbeat', 20, 6),
('Educação', 'Informações sobre políticas e programas educacionais', 'fas fa-graduation-cap', 20, 7),
('Meio Ambiente', 'Informações sobre políticas ambientais e licenciamento', 'fas fa-leaf', 20, 8),
('Segurança Pública', 'Informações sobre segurança pública e políticas de segurança', 'fas fa-shield-alt', 20, 9),
('Outras', 'Outras informações não categorizadas', 'fas fa-question-circle', 20, 10);

-- Órgãos padrão
INSERT INTO `orgaos` (`nome`, `sigla`, `descricao`, `email`) VALUES
('Secretaria Municipal de Administração', 'SMA', 'Órgão responsável pela administração geral', 'administracao@exemplo.gov.br'),
('Secretaria Municipal de Finanças', 'SMF', 'Órgão responsável pelas finanças municipais', 'financas@exemplo.gov.br'),
('Secretaria Municipal de Saúde', 'SMS', 'Órgão responsável pela saúde pública', 'saude@exemplo.gov.br'),
('Secretaria Municipal de Educação', 'SME', 'Órgão responsável pela educação municipal', 'educacao@exemplo.gov.br'),
('Secretaria Municipal de Obras', 'SMO', 'Órgão responsável pelas obras públicas', 'obras@exemplo.gov.br'),
('Gabinete do Prefeito', 'GAB', 'Gabinete do Prefeito Municipal', 'gabinete@exemplo.gov.br');

-- Usuário administrador padrão
-- Senha: admin123 (hash MD5: 0192023a7bbd73250516f069df18b500)
INSERT INTO `usuarios` (`nome`, `email`, `senha`, `perfil`) VALUES
('Administrador', 'admin@exemplo.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador');