<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
session_start();

// Log logout activity
if (isset($_SESSION['admin_id'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO activity_log (admin_id, action, ip_address) VALUES (:admin_id, 'logout', :ip)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':admin_id' => $_SESSION['admin_id'],
            ':ip' => $_SERVER['REMOTE_ADDR']
        ]);
    } catch(PDOException $e) {
        error_log("Logout Log Error: " . $e->getMessage());
    }
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: login.php?logged_out=1');
exit;
