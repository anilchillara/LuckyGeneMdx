<?php

/**
 * .env Loader for Hostinger & Local Dev
 */
function loadEnv($path) {
    if (!file_exists($path)) return false;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || strpos($line, '=') === false) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim(trim($value), '"\'');
        
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value; 
    }
    return true;
}

// --- Environment Selection Logic ---
// Define the base path to your environment files
$basePath = __DIR__ . '/../';

// --- Environment Selection Logic ---
// 1. Check for .env.local first
if (file_exists($basePath . '.env.local')) {
    $envFile = '.env.local';
    $isLocal = true;
} 
// 2. If .env.local is missing, fall back to .env.prod
elseif (file_exists($basePath . '.env.prod')) {
    $envFile = '.env.prod';
    $isLocal = false;
} 
else {
    // Fail-safe: Ensure the application doesn't run without configuration
    error_log("Critical: No environment file (.env.local or .env.prod) found at " . $basePath);
    die("Configuration Error: Environment file missing.");
}

loadEnv(__DIR__ . '/../' . $envFile);

// Set ENVIRONMENT constant immediately to use in the logger
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: ($isLocal ? 'development' : 'production'));

/**
 * Environment Logger
 * Prints to screen in dev, writes to file in prod.
 */
if (ENVIRONMENT === 'development') {
    // Hidden in page source as a comment, but visible if you "View Source"
    echo "\n";
} else {
    // Silently log to the PHP error log on the server
    error_log("APP INFO: Environment loaded from {$envFile}");
}
// -----------------------------------

// Prevent direct access
if (!defined('luckygenemdx')) {
    die('Direct access not permitted');
}

// Application Constants
define('SITE_URL', getenv('SITE_URL') ?: 'https://luckygenemdx.com');
define('SITE_NAME', getenv('SITE_NAME') ?: 'LuckyGeneMDx');

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'luckygenemdx_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Security Settings
define('SESSION_TIMEOUT', 1800);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900);
define('PASSWORD_MIN_LENGTH', 8);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5242880);
define('ALLOWED_FILE_TYPES', ['pdf']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Encryption Settings
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY'));

// Email Configuration
define('MAIL_HOST', getenv('SMTP_HOST'));
define('MAIL_PORT', getenv('SMTP_PORT') ?: 587);
define('MAIL_USERNAME', getenv('SMTP_USER'));
define('MAIL_PASSWORD', getenv('SMTP_PASS'));
define('MAIL_FROM', getenv('EMAIL_FROM'));
define('MAIL_FROM_NAME', getenv('EMAIL_FROM_NAME'));

define('BASE_URL', getenv('BASE_URL'));

// Application Settings
define('KIT_PRICE', 99.00);
define('CURRENCY', 'USD');
define('RESULTS_PROCESSING_DAYS', '14-21');

// Error Reporting logic
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// Security Headers Function
function setSecurityHeaders() {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    if (ENVIRONMENT === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

// CSRF Functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}