<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

$userModel = new User();
$user = $userModel->getUserById($_SESSION['user_id']);
$initials = strtoupper(substr($user['full_name'], 0, 1));
if (strpos($user['full_name'], ' ') !== false) {
    $initials .= strtoupper(substr(explode(' ', $user['full_name'])[1], 0, 1));
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($subject) || empty($message)) {
        $error = 'Please fill in all fields.';
    } else {
        // Send email to support
        $to = defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : 'support@LuckyGenes.com';
        $email_subject = "[Portal Support] $subject - " . $user['full_name'];
        $body = "User: {$user['full_name']} (ID: {$user['user_id']})\nEmail: {$user['email']}\n\nMessage:\n$message";
        $headers = "From: noreply@LuckyGenes.com\r\n";
        $headers .= "Reply-To: {$user['email']}";
        
        // Using mail() for simplicity here, consistent with process-contact.php
        if (mail($to, $email_subject, $body, $headers)) {
            $success = 'Your message has been sent. Our support team will respond shortly.';
        } else {
            $error = 'There was a problem sending your message. Please try again later.';
        }
    }
}

$faqs = [
    ['q' => 'How long does it take to get results?', 'a' => 'Results are typically available within 14-21 days after our laboratory receives your sample. You will receive an email notification when your report is ready.'],
    ['q' => 'How do I download my report?', 'a' => 'Navigate to the "Results" tab in your dashboard. You can view your report online or download a PDF copy to share with your healthcare provider.'],
    ['q' => 'Can I share my results with my doctor?', 'a' => 'Yes. We recommend downloading the PDF version of your report from the Results page and emailing it directly to your doctor, or printing a copy for your next appointment.'],
    ['q' => 'What if I lose my kit or it is damaged?', 'a' => 'Please contact us immediately using the form on this page. We can invalidate the barcode of the lost kit and ship a replacement to you.'],
    ['q' => 'Is my genetic data secure?', 'a' => 'Absolutely. We use bank-grade encryption for all data transmission and storage. Your genetic information is de-identified during processing and is never sold to third parties.']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - LuckyGenes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="header-section">
            <h1>Support Center</h1>
            <p>Get help with your orders, results, and account.</p>
        </div>

        <div class="grid">
            <!-- FAQ Section -->
            <div class="col-span-8">
                <div class="card">
                    <h3 class="mb-3">Frequently Asked Questions</h3>
                    <div class="faq-list">
                        <?php foreach ($faqs as $index => $faq): ?>
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFaq(this)">
                                <?php echo htmlspecialchars($faq['q']); ?>
                                <span class="plus-icon">+</span>
                            </div>
                            <div class="faq-answer">
                                <?php echo htmlspecialchars($faq['a']); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-span-4">
                <div class="card sticky-top">
                    <h3 class="mb-2">Contact Support</h3>
                    <p class="text-dark-gray font-sm mb-3">Can't find what you're looking for? Send us a message.</p>
                    
                    <?php if ($success): ?>
                        <div class="glass-card-teal-left p-3 mb-3 text-teal font-sm"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="glass-card-error p-3 mb-3 text-error font-sm"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select name="subject" id="subject" class="form-select" required>
                                <option value="">Select a topic...</option>
                                <option value="Order Status">Order Status</option>
                                <option value="Results Inquiry">Results Inquiry</option>
                                <option value="Billing">Billing Question</option>
                                <option value="Technical Support">Technical Issue</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea name="message" id="message" rows="5" class="form-control" placeholder="How can we help you?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../js/main.js"></script>
    <script>
        function toggleFaq(element) {
            element.parentElement.classList.toggle('active');
        }
    </script>
</body>
</html>