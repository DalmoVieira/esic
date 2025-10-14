<?php

// Application paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
define('LOG_PATH', STORAGE_PATH . '/logs');
define('CACHE_PATH', STORAGE_PATH . '/cache');

// Application settings
define('APP_NAME', 'E-SIC - Prefeitura de Rio Claro');
define('APP_VERSION', '2.0.0');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', $_ENV['APP_DEBUG'] ?? false);
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// Organization settings - Prefeitura de Rio Claro
define('ORG_NAME', 'Prefeitura Municipal de Rio Claro');
define('ORG_NAME_SHORT', 'PMRC');
define('ORG_ADDRESS', 'Av. João Portugal Baptista, 230');
define('ORG_CITY', 'Rio Claro');
define('ORG_STATE', 'RJ');
define('ORG_ZIP', '27.460-000');
define('ORG_EMAIL', 'pmrc@rioclaro.rj.gov.br');
define('ORG_PHONE', '(22) 0000-0000'); // Adicionar telefone real
define('ORG_WEBSITE', 'https://rioclaro.rj.gov.br');
define('ORG_LOGO', '/assets/images/logo-rioclaro.png'); // Logo fictício

// Database settings
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_DATABASE', $_ENV['DB_DATABASE'] ?? 'esic');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// File upload settings
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt']);

// Email settings
define('MAIL_HOST', $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com');
define('MAIL_PORT', $_ENV['MAIL_PORT'] ?? 587);
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME'] ?? '');
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD'] ?? '');
define('MAIL_ENCRYPTION', $_ENV['MAIL_ENCRYPTION'] ?? 'tls');
define('MAIL_FROM_ADDRESS', $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@rioclaro.rj.gov.br');
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME'] ?? 'E-SIC Rio Claro');

// LAI specific settings
define('DEFAULT_RESPONSE_DEADLINE', 20); // days
define('DEFAULT_EXTENSION_DEADLINE', 10); // days  
define('DEFAULT_RESOURCE_DEADLINE', 10); // days
define('DEFAULT_RESOURCE_ANALYSIS_DEADLINE', 5); // days
define('MAX_RESOURCE_INSTANCES', 3);

// Pagination
define('DEFAULT_PER_PAGE', 20);
define('MAX_PER_PAGE', 100);

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 1 hour

// Logging levels
define('LOG_LEVEL_ERROR', 'error');
define('LOG_LEVEL_WARNING', 'warning');  
define('LOG_LEVEL_INFO', 'info');
define('LOG_LEVEL_DEBUG', 'debug');

// System status
define('SYSTEM_STATUS_ACTIVE', 'active');
define('SYSTEM_STATUS_MAINTENANCE', 'maintenance');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_OPERATOR', 'operator');
define('ROLE_MANAGER', 'manager');
define('ROLE_CITIZEN', 'citizen');

// Pedido status
define('STATUS_RECEIVED', 'recebido');
define('STATUS_IN_ANALYSIS', 'em_analise');
define('STATUS_PENDING', 'pendente');
define('STATUS_ANSWERED', 'atendido');
define('STATUS_PARTIALLY_ANSWERED', 'parcialmente_atendido');
define('STATUS_DENIED', 'negado');
define('STATUS_NOT_ANSWERED', 'nao_respondido');
define('STATUS_IN_RESOURCE', 'em_recurso');
define('STATUS_EXPIRED', 'vencido');

// Resource status
define('RESOURCE_STATUS_WAITING', 'aguardando_analise');
define('RESOURCE_STATUS_IN_ANALYSIS', 'em_analise');
define('RESOURCE_STATUS_GRANTED', 'deferido');
define('RESOURCE_STATUS_DENIED', 'indeferido');
define('RESOURCE_STATUS_PARTIALLY_GRANTED', 'parcialmente_deferido');

// Create required directories if they don't exist
$directories = [
    STORAGE_PATH,
    UPLOAD_PATH,
    LOG_PATH,
    CACHE_PATH,
    UPLOAD_PATH . '/pedidos',
    UPLOAD_PATH . '/recursos',
    UPLOAD_PATH . '/public',
    UPLOAD_PATH . '/temp'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ERROR | E_WARNING);
    ini_set('display_errors', 0);
}

// Set default timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Set memory limit
ini_set('memory_limit', '256M');

// Set execution time limit
set_time_limit(300); // 5 minutes