<?php
/**
 * Configuração de Produção - E-SIC
 * IMPORTANTE: Altere as credenciais antes do deploy!
 */

return [
    'database' => [
        'host' => 'localhost', // Manter como localhost na Hostinger
        'dbname' => 'u123456789_esic', // ALTERAR: Nome do seu banco na Hostinger
        'username' => 'u123456789_user', // ALTERAR: Seu usuário do banco
        'password' => 'SUA_SENHA_SUPER_SEGURA_AQUI', // ALTERAR: Senha do banco
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    
    'app' => [
        'name' => 'E-SIC - Sistema de Informações ao Cidadão',
        'url' => 'https://seudominio.com.br', // ALTERAR: Seu domínio
        'debug' => false, // SEMPRE false em produção
        'environment' => 'production',
        'timezone' => 'America/Sao_Paulo',
        'charset' => 'UTF-8'
    ],
    
    'email' => [
        'smtp_host' => 'smtp.hostinger.com',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'smtp_user' => 'noreply@seudominio.com.br', // ALTERAR: Seu email
        'smtp_pass' => 'senha_do_email_aqui', // ALTERAR: Senha do email
        'from_email' => 'noreply@seudominio.com.br',
        'from_name' => 'E-SIC Sistema',
        'reply_to' => 'contato@seudominio.com.br'
    ],
    
    'security' => [
        'jwt_secret' => 'SUA_CHAVE_JWT_256_BITS_SUPER_SECRETA_AQUI', // ALTERAR: Gerar nova chave
        'session_name' => 'ESIC_SESSION',
        'session_lifetime' => 7200, // 2 horas
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos
        'csrf_token_name' => 'csrf_token'
    ],
    
    'upload' => [
        'max_size' => 10485760, // 10MB em bytes
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        'upload_path' => 'uploads/',
        'create_thumbs' => true
    ],
    
    'api' => [
        'rate_limit' => 100, // Requisições por minuto
        'version' => 'v1',
        'cors_enabled' => false,
        'allowed_origins' => []
    ],
    
    'logs' => [
        'enabled' => true,
        'level' => 'ERROR', // DEBUG, INFO, WARNING, ERROR
        'max_files' => 10,
        'path' => 'logs/'
    ],
    
    'cache' => [
        'enabled' => true,
        'driver' => 'file', // file, redis, memcached
        'ttl' => 3600, // 1 hora
        'path' => 'cache/'
    ],
    
    'backup' => [
        'enabled' => true,
        'frequency' => 'daily', // hourly, daily, weekly
        'keep_days' => 30,
        'compress' => true
    ]
];