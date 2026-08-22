<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/GoogleDriveService.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    die('Unauthorized');
}

if (!isset($_GET['order_id'])) {
    header('HTTP/1.1 400 Bad Request');
    die('Order ID is required');
}

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id'];

// Verify the user owns this order
$stmt = $db->prepare("SELECT order_number FROM orders WHERE order_id = :order_id AND user_id = :user_id");
$stmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
$order = $stmt->fetch();

if (!$order) {
    header('HTTP/1.1 403 Forbidden');
    die('Forbidden: You do not have access to this order.');
}

$orderNumber = $order['order_number'];
$pdfFileName = $orderNumber . '.pdf';

try {
    $driveService = new GoogleDriveService();
    $file = $driveService->findFileByName($pdfFileName);

    if (!$file) {
        header('HTTP/1.1 404 Not Found');
        die('Report not yet available for this order. Please check back later.');
    }

    // Fetch the file content
    $content = $driveService->downloadFile($file->getId());

    // Serve the file
    $isDownload = isset($_GET['download']) && $_GET['download'] == '1';
    $disposition = $isDownload ? 'attachment' : 'inline';

    header('Content-Type: application/pdf');
    header('Content-Disposition: ' . $disposition . '; filename="' . $pdfFileName . '"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    echo $content;
    exit;

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    error_log("Google Drive API Error: " . $e->getMessage());
    if (ENVIRONMENT === 'development') {
        die("Error fetching report: " . $e->getMessage());
    } else {
        die("An error occurred while fetching the report. Please contact support.");
    }
}
