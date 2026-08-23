<?php
/**
 * Gift Kit Redemption API
 * Validates a gift token and links the kit to the authenticated user's account.
 */
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Must be logged in to redeem
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to claim a gift kit.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate CSRF
if (empty($input['csrf_token']) || !validateCSRFToken($input['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security validation failed.']);
    exit;
}

$giftToken = trim($input['gift_token'] ?? '');
if (empty($giftToken)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Gift token is required.']);
    exit;
}

$orderModel = new Order();
$result     = $orderModel->redeemGiftKit($giftToken, (int) $_SESSION['user_id']);

if ($result['success']) {
    echo json_encode([
        'success'     => true,
        'kit_id'      => $result['kit_id'],
        'kit_barcode' => $result['kit_barcode'],
        'message'     => $result['message'],
        'redirect'    => '../user-portal/orders.php',
    ]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $result['message']]);
}
