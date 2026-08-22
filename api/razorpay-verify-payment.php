<?php
/**
 * Razorpay Verify Payment API
 * Step 2 of 2: Verifies the payment signature from Razorpay,
 * then creates the order record in the database.
 */
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
require_once '../includes/Order.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate CSRF
if (empty($input['csrf_token']) || !validateCSRFToken($input['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security validation failed.']);
    exit;
}

// Required Razorpay fields
$razorpay_payment_id = trim($input['razorpay_payment_id'] ?? '');
$razorpay_order_id   = trim($input['razorpay_order_id'] ?? '');
$razorpay_signature  = trim($input['razorpay_signature'] ?? '');

if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing payment verification data.']);
    exit;
}

// Match against session-stored order ID to prevent tampering
if (empty($_SESSION['pending_razorpay_order_id']) || $_SESSION['pending_razorpay_order_id'] !== $razorpay_order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID mismatch. Please try again.']);
    exit;
}

// === SIGNATURE VERIFICATION ===
// Razorpay signs: razorpay_order_id + "|" + razorpay_payment_id
$expectedSignature = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    RAZORPAY_KEY_SECRET
);

if (!hash_equals($expectedSignature, $razorpay_signature)) {
    error_log("Razorpay signature mismatch. Expected: $expectedSignature | Got: $razorpay_signature");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payment verification failed. Please contact support.']);
    exit;
}

// Payment is verified — clear session pending data
unset($_SESSION['pending_razorpay_order_id'], $_SESSION['pending_razorpay_receipt']);

// === CREATE DB ORDER ===
$isLoggedIn = isset($_SESSION['user_id']);
$formData   = $input['form_data'] ?? [];

$data = [
    'address_line1'      => trim($formData['address_line1'] ?? ''),
    'address_line2'      => trim($formData['address_line2'] ?? ''),
    'city'               => trim($formData['city'] ?? ''),
    'state'              => trim($formData['state'] ?? ''),
    'zip'                => trim($formData['zip'] ?? ''),
    'razorpay_payment_id'=> $razorpay_payment_id,
    'razorpay_order_id'  => $razorpay_order_id,
];

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
} else {
    // Guest: register user first
    $data['full_name'] = trim($formData['full_name'] ?? '');
    $data['email']     = trim($formData['email'] ?? '');
    $data['phone']     = trim($formData['phone'] ?? '');
    $data['dob']       = trim($formData['dob'] ?? '');
    $data['password']  = $formData['password'] ?? '';

    if (empty($data['full_name']) || empty($data['email']) || strlen($data['password']) < 8) {
        echo json_encode(['success' => false, 'message' => 'Missing required user information.']);
        exit;
    }

    $userModel  = new User();
    $userResult = $userModel->register($data);

    if (!$userResult['success']) {
        echo json_encode(['success' => false, 'message' => $userResult['message']]);
        exit;
    }

    $userId = $userResult['user_id'];

    // Auto-login
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_email'] = $data['email'];
    $_SESSION['user_name']  = $data['full_name'];
    $_SESSION['last_activity'] = time();
}

// Create order record
$orderModel  = new Order();
$orderResult = $orderModel->createOrder($userId, $data);

if (!$orderResult['success']) {
    // Payment was captured but DB order failed — log it urgently
    error_log("CRITICAL: Payment captured (payment_id=$razorpay_payment_id) but order creation failed: " . $orderResult['message']);
    echo json_encode([
        'success' => false,
        'message' => 'Payment received but order recording failed. Please contact support with your payment ID: ' . $razorpay_payment_id
    ]);
    exit;
}

echo json_encode([
    'success'      => true,
    'order_number' => $orderResult['order_number'],
    'order_id'     => $orderResult['order_id'],
    'message'      => 'Payment successful and order placed!'
]);
