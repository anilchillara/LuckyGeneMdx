<?php
// test-phpmailer.php
// Run this script to verify PHPMailer is installed and loading correctly.

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHPMailer Installation Check</h1>";

// 1. Check for Autoloader or Manual Files
$autoloadPath = __DIR__ . '/vendor/autoload.php';
$manualPath1  = __DIR__ . '/includes/phpmailer/src/PHPMailer.php';

if (file_exists($autoloadPath)) {
    echo "<p style='color:green'><strong>✅ Composer autoloader found.</strong></p>";
    require_once $autoloadPath;
} elseif (file_exists($manualPath1)) {
    echo "<p style='color:green'><strong>✅ Manual PHPMailer found (lowercase).</strong></p>";
    require_once __DIR__ . '/includes/phpmailer/src/Exception.php';
    require_once __DIR__ . '/includes/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/includes/phpmailer/src/SMTP.php';
}  else {
    echo "<p style='color:red'><strong>❌ PHPMailer NOT found.</strong></p>";
    echo "<p>Checked paths:</p><ul>";
    echo "<li>Composer: <code>$autoloadPath</code></li>";
    echo "<li>Manual 1: <code>$manualPath1</code></li>";
    echo "</ul>";
    echo "<p>Please run <code>composer update</code> in your terminal OR download PHPMailer to <code>includes/</code>.</p>";
    exit;
}

// 2. Check if PHPMailer Class is defined
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color:green'><strong>✅ PHPMailer class loaded successfully.</strong></p>";
    
    // 3. Attempt Instantiation
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        echo "<p style='color:green'><strong>✅ PHPMailer instance created.</strong></p>";
        echo "<p>Version: " . htmlspecialchars($mail::VERSION) . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'><strong>❌ Instantiation failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color:red'><strong>❌ PHPMailer class NOT found.</strong></p>";
    echo "<p>The autoloader exists but the class could not be loaded. Check your <code>composer.json</code> and run <code>composer update</code>.</p>";
}

echo "<hr><p><a href='admin/settings.php'>Go to Admin Settings to test SMTP sending</a></p>";
?>