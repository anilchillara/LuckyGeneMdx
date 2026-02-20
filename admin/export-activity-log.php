<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=activity_log_' . date('Y-m-d_His') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel compatibility
fwrite($output, "\xEF\xBB\xBF");

// Add CSV header row
fputcsv($output, ['Log ID', 'Date', 'Admin User', 'Action', 'Entity Type', 'Entity ID', 'Details', 'IP Address']);

// Fetch logs
try {
    $sql = "SELECT l.log_id, l.created_at, a.username, l.action, l.entity_type, l.entity_id, l.details, l.ip_address 
            FROM activity_log l 
            LEFT JOIN admins a ON l.admin_id = a.admin_id 
            ORDER BY l.created_at DESC";
    
    $stmt = $db->query($sql);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format date for better readability if needed, or keep raw
        // $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
        
        // Handle null username (system actions)
        if (empty($row['username'])) {
            $row['username'] = 'System/Unknown';
        }
        
        fputcsv($output, $row);
    }
} catch (PDOException $e) {
    // In a download script, we can't easily show an HTML error, so we might just log it or output a CSV error row
    fputcsv($output, ['Error', 'Could not export logs: ' . $e->getMessage()]);
}

fclose($output);
exit;