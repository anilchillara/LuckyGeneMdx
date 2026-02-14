<?php
/**
 * LuckyGeneMdx Configuration File
 * Contains database, security, and application settings
 */

// Prevent direct access
if (!defined('luckygenemdx')) {
    die('Direct access not permitted');
}

// Environment Configuration
define('ENVIRONMENT', 'development'); // Change to 'production' when live
define('SITE_URL', 'https://luckygenemdx.com');
define('SITE_NAME', 'LuckyGeneMdx');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'luckygenemdx_db');
define('DB_USER', 'luckygenemdx');
define('DB_PASS', 'luckygenemdx');
define('DB_CHARSET', 'utf8mb4');

// Security Settings
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes
define('PASSWORD_MIN_LENGTH', 8);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Encryption Settings
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_KEY', 'your_32_character_encryption_key_here_changeme!'); // CHANGE THIS

// Email Configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@luckygenemdx.com');
define('SMTP_PASS', 'luckygenemdx');
define('EMAIL_FROM', 'support@luckygenemdx.com');
define('EMAIL_FROM_NAME', 'LuckyGeneMdx Support');

// Application Settings
define('KIT_PRICE', 99.00);
define('CURRENCY', 'USD');
define('RESULTS_PROCESSING_DAYS', '14-21');

// Error Reporting
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

// CSRF Token Generation
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Validation
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
