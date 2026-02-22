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
    // Logging disabled to prevent "Headers already sent" errors
} else {
    // Silently log to the PHP error log on the server
    error_log("APP INFO: Environment loaded from {$envFile}");
}
// -----------------------------------

// Prevent direct access
if (!defined('luckygenemdx')) {
    die('Direct access not permitted');
}

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'luckygenemdx_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Load Dynamic Settings from Database
require_once __DIR__ . '/Database.php';
$dbSettings = [];
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT setting_key, value FROM site_settings");
    $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    // Fallback if DB connection fails or table missing
}

// Application Constants
define('SITE_URL', $dbSettings['site_url'] ?? (getenv('SITE_URL') ?: 'https://luckygenemdx.com'));
define('SITE_NAME', $dbSettings['site_name'] ?? (getenv('SITE_NAME') ?: 'LuckyGeneMDx'));
define('SUPPORT_EMAIL', $dbSettings['support_email'] ?? 'support@luckygenemdx.com');

// Access Control: Block direct access to disabled navbar pages
if (isset($db) && strpos($_SERVER['PHP_SELF'], '/admin/') === false) {
    try {
        $stmt = $db->prepare("SELECT url FROM navbar_items WHERE is_active = 0");
        $stmt->execute();
        $disabledPages = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($disabledPages)) {
            $currentScript = trim($_SERVER['PHP_SELF']);
            foreach ($disabledPages as $pageUrl) {
                // Check if current script ends with the disabled URL
                if (substr($currentScript, -strlen($pageUrl)) === $pageUrl) {
                    $boundary = substr($currentScript, -(strlen($pageUrl) + 1), 1);
                    if ($boundary === '/' || $boundary === false) {
                        if ($pageUrl === 'index.php') {
                            header("HTTP/1.1 503 Service Unavailable");
                            die("<h1>Service Unavailable</h1><p>This page is currently disabled.</p>");
                        } else {
                            $redirect = (strpos($currentScript, '/user-portal/') !== false) ? '../index.php' : 'index.php';
                            header("Location: " . $redirect);
                            exit;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) { /* Ignore access control errors */ }
}

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
define('KIT_PRICE', isset($dbSettings['kit_price']) ? (float)$dbSettings['kit_price'] : 99.00);
define('SHOW_CTA', isset($dbSettings['show_cta']) ? (bool)$dbSettings['show_cta'] : true);
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