<?php
/**
 * CORRECT Admin Authentication Pattern
 * Use this at the top of ALL admin pages
 */

define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

// Check admin authentication - USE THIS CHECK
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Optional: Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

// Get database connection
$db = Database::getInstance()->getConnection();

// Available session variables:
// $_SESSION['admin_id'] - Admin user ID
// $_SESSION['admin_username'] - Admin username  
// $_SESSION['admin_role'] - Admin role (super_admin, admin, etc.)
// $_SESSION['last_activity'] - Last activity timestamp

$adminName = $_SESSION['admin_username'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'admin';

// Your page code here...
?>
