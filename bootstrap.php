<?php

// PSR-4 Autoload function
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = 'App\\';
    
    // Check if class uses our namespace
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    
    // Remove prefix and convert to file path
    $relative_class = substr($class, strlen($prefix));
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    
    // Build full file path
    $file_path = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $file;
    
    if (file_exists($file_path)) {
        require_once $file_path;
    }
});

// Load configuration constants
require_once __DIR__ . '/config/constants.php';

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

// Start session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Set error handling
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('America/Sao_Paulo');