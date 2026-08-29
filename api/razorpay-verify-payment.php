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

// === PROVISION KIT ===
// Determine gift options from form data
$isGift = !empty($formData['is_gift']) && $formData['is_gift'] === '1';
$giftOpts = null;
if ($isGift) {
    $giftOpts = [
        'is_gift'         => true,
        'recipient_email' => trim($formData['gift_recipient_email'] ?? ''),
        'recipient_name'  => trim($formData['gift_recipient_name']  ?? ''),
        'message'         => substr(trim($formData['gift_message'] ?? ''), 0, 500),
    ];
    // Validate recipient email
    if (empty($giftOpts['recipient_email']) || !filter_var($giftOpts['recipient_email'], FILTER_VALIDATE_EMAIL)) {
        error_log("Gift kit: invalid recipient email for order_id=" . $orderResult['order_id']);
        $isGift = false;
        $giftOpts = null;
    }
}

$kitResult = $orderModel->createKitForOrder((int) $orderResult['order_id'], $giftOpts);

if (!$kitResult['success']) {
    // Non-fatal: order exists, kit provisioning failed — log and continue
    error_log("WARN: Order created (order_id={$orderResult['order_id']}) but kit provisioning failed.");
}

// === SEND GIFT EMAIL (if applicable) ===
if ($isGift && !empty($kitResult['gift_token']) && !empty($giftOpts['recipient_email'])) {
    // Build claim URL
    $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $claimUrl = $scheme . '://' . $host . '/claim-gift.php?token=' . urlencode($kitResult['gift_token']);

    $recipientName  = !empty($giftOpts['recipient_name']) ? $giftOpts['recipient_name'] : 'there';
    $senderName     = $_SESSION['user_name'] ?? 'Someone';
    $personalNote   = !empty($giftOpts['message'])
        ? '<p style="font-style:italic;color:#555;">\"' . htmlspecialchars($giftOpts['message']) . '\"</p>'
        : '';

    $subject = $senderName . ' has gifted you a LuckyGenes Carrier Screening Kit!';
    $htmlBody = '
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;">
<h2 style="color:#00B3A4;">🎁 You\'ve received a gift!</h2>
<p>Hi ' . htmlspecialchars($recipientName) . ',</p>
<p><strong>' . htmlspecialchars($senderName) . '</strong> has gifted you a <strong>Comprehensive Carrier Screening Kit</strong> from LuckyGenes — a personalised genetic health test covering 300+ conditions.</p>
' . $personalNote . '
<p>To activate your kit and begin your screening journey, click the button below:</p>
<p style="text-align:center;margin:30px 0;">
  <a href="' . $claimUrl . '" style="background:#00B3A4;color:#fff;padding:14px 28px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;">Claim Your Gift Kit</a>
</p>
<p style="font-size:0.85rem;color:#888;">This link expires in 90 days. If you have any questions, contact us at <a href="mailto:' . SUPPORT_EMAIL . '">' . SUPPORT_EMAIL . '</a>.</p>
<hr>
<p style="font-size:0.8rem;color:#aaa;">LuckyGenes · Privacy-first genetic screening</p>
</body></html>';

    // Use PHPMailer if available, otherwise mail()
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->setFrom(SMTP_FROM, SITE_NAME);
            $mail->addAddress($giftOpts['recipient_email'], $recipientName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->send();
        } catch (Exception $e) {
            error_log("Gift email send failed: " . $e->getMessage());
        }
    } else {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . SITE_NAME . " <" . SMTP_FROM . ">\r\n";
        mail($giftOpts['recipient_email'], $subject, $htmlBody, $headers);
    }
}

echo json_encode([
    'success'       => true,
    'order_number'  => $orderResult['order_number'],
    'order_id'      => $orderResult['order_id'],
    'kit_barcode'   => $kitResult['kit_barcode'] ?? null,
    'is_gift'       => $isGift,
    'gift_sent_to'  => $isGift ? ($giftOpts['recipient_email'] ?? null) : null,
    'message'       => 'Payment successful and order placed!',
]);
