<?php
/**
 * Razorpay Create Order API
 * Step 1 of 2: Creates a Razorpay order and returns order_id to the frontend.
 * The frontend then opens the Razorpay checkout modal with this order_id.
 */
define('LuckyGenes', true);
require_once '../includes/config.php';
session_start();

header('Content-Type: application/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Parse JSON body
$input = json_decode(file_get_contents('php://input'), true);

// Validate CSRF token
if (empty($input['csrf_token']) || !validateCSRFToken($input['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security validation failed.']);
    exit;
}

// Check Razorpay keys are configured
if (empty(RAZORPAY_KEY_ID) || empty(RAZORPAY_KEY_SECRET)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Payment gateway not configured. Please contact support.']);
    exit;
}

// Amount in paise (Razorpay requires smallest currency unit)
$amount_inr   = KIT_PRICE;
$amount_paise = (int) round($amount_inr * 100);

// Generate a unique receipt ID
$receipt = 'LGM_' . date('ymd') . '_' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

// Build Razorpay order payload
$orderData = [
    'amount'          => $amount_paise,
    'currency'        => 'INR',
    'receipt'         => $receipt,
    'payment_capture' => 1,
    'notes'           => [
        'site' => SITE_NAME,
        'kit'  => 'Comprehensive Carrier Screening Kit'
    ]
];

// Call Razorpay Orders API
$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($orderData),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    error_log("Razorpay cURL error: " . $curlError);
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Payment gateway connection failed. Please try again.']);
    exit;
}

$razorpayOrder = json_decode($response, true);

if ($httpCode !== 200 || empty($razorpayOrder['id'])) {
    $errMsg = $razorpayOrder['error']['description'] ?? 'Unknown error';
    error_log("Razorpay order creation failed (HTTP $httpCode): $errMsg | Response: $response");
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Could not initiate payment: ' . $errMsg]);
    exit;
}

// Store the Razorpay order ID in session to validate during verification
$_SESSION['pending_razorpay_order_id'] = $razorpayOrder['id'];
$_SESSION['pending_razorpay_receipt']  = $receipt;

echo json_encode([
    'success'            => true,
    'razorpay_order_id'  => $razorpayOrder['id'],
    'amount'             => $amount_paise,
    'currency'           => 'INR',
    'key_id'             => RAZORPAY_KEY_ID,
    'site_name'          => SITE_NAME,
]);
