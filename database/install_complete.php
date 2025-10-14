<?php

/**
 * Script de instalação completa do E-SIC
 */

require_once __DIR__ . '/../config/constants.php';

echo "=== Instalação Completa do E-SIC ===\n\n";

try {
    // Conectar ao banco
    $pdo = new PDO('mysql:host=localhost;dbname=esic', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Conectado ao banco de dados\n\n";
    
    // Criar todas as tabelas
    echo "Criando estrutura do banco...\n";
    
    // Tabela de órgãos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `orgaos` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabela orgaos criada\n";
    
    // Tabela de usuários
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
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
            KEY `orgao_id` (`orgao_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabela usuarios criada\n";
    
    // Tabela de pedidos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pedidos` (
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
            KEY `prazo_atendimento` (`prazo_atendimento`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabela pedidos criada\n";
    
    // Tabela de recursos
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `recursos` (
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
            KEY `status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabela recursos criada\n";
    
    // Tabela de histórico
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `pedido_historico` (
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
            KEY `data_acao` (`data_acao`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Tabela pedido_historico criada\n";
    
    // Inserir dados iniciais
    echo "\nInserindo dados iniciais...\n";
    
    // Configurações
    $pdo->exec("
        INSERT IGNORE INTO `configuracoes` (`chave`, `valor`, `descricao`, `tipo`, `categoria`) VALUES
        ('sistema_nome', 'E-SIC - Sistema Eletrônico de Informações ao Cidadão', 'Nome do sistema', 'text', 'geral'),
        ('sistema_email', 'esic@exemplo.gov.br', 'Email principal do sistema', 'text', 'geral'),
        ('sistema_telefone', '(11) 1234-5678', 'Telefone de contato', 'text', 'geral'),
        ('prazo_padrao_dias', '20', 'Prazo padrão para resposta (dias)', 'number', 'geral')
    ");
    echo "✓ Configurações inseridas\n";
    
    // Categorias
    $pdo->exec("
        INSERT IGNORE INTO `categorias` (`nome`, `descricao`, `icone`, `prazo_dias`, `ordem`) VALUES
        ('Informações Gerais', 'Solicitações de informações gerais da administração pública', 'fas fa-info-circle', 20, 1),
        ('Contratos e Licitações', 'Informações sobre contratos, licitações e processos de compra', 'fas fa-file-contract', 20, 2),
        ('Recursos Humanos', 'Informações sobre servidores, folha de pagamento e concursos', 'fas fa-users', 20, 3),
        ('Orçamento e Finanças', 'Informações orçamentárias, financeiras e contábeis', 'fas fa-dollar-sign', 20, 4)
    ");
    echo "✓ Categorias inseridas\n";
    
    // Órgãos
    $pdo->exec("
        INSERT IGNORE INTO `orgaos` (`nome`, `sigla`, `email`) VALUES
        ('Secretaria Municipal de Administração', 'SMA', 'administracao@exemplo.gov.br'),
        ('Secretaria Municipal de Finanças', 'SMF', 'financas@exemplo.gov.br'),
        ('Gabinete do Prefeito', 'GAB', 'gabinete@exemplo.gov.br')
    ");
    echo "✓ Órgãos inseridos\n";
    
    // Usuário administrador
    $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("
        INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `perfil`) VALUES
        (?, ?, ?, ?)
    ")->execute(['Administrador', 'admin@exemplo.gov.br', $senhaHash, 'administrador']);
    echo "✓ Usuário administrador criado\n";
    
    // Verificar tabelas criadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\n=== Status Final ===\n";
    echo "Tabelas criadas: " . count($tables) . "\n";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $stmt->fetchColumn();
        echo "- $table: $count registros\n";
    }
    
    echo "\n=== Instalação Completa! ===\n";
    echo "Credenciais do administrador:\n";
    echo "Email: admin@exemplo.gov.br\n";
    echo "Senha: admin123\n\n";
    echo "⚠ IMPORTANTE: Altere a senha após o primeiro login!\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}