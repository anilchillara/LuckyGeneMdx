<?php
require_once 'includes/config.php';

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit;
}

// 1. Sanitize and Capture Inputs
$first_name = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_SPECIAL_CHARS);
$last_name  = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_SPECIAL_CHARS);
$email      = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$role       = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
$subject    = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message    = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// 2. Validation
$errors = [];
if (!$email) $errors[] = "A valid email address is required.";
if (empty($message)) $errors[] = "Please enter your message.";

if (!empty($errors)) {
    // In a real app, you'd pass these back via session to display on contact.php
    die("Validation Error: " . implode(", ", $errors));
}

// 3. Determine Routing (MDx Triage Logic)
// In a clinical environment, Provider emails often go to a different team than Patients.
$to_email = "support@luckygenemdx.com"; // Default
if ($role === 'provider') {
    $to_email = "providers@luckygenemdx.com";
} elseif ($role === 'partner') {
    $to_email = "partnerships@luckygenemdx.com";
}

// 4. Construct the Professional Email Header
$full_name = $first_name . " " . $last_name;
$email_subject = "[MDx Inquiry] $subject - from $full_name";

$email_body = "
<html>
<head>
    <style>
        .header { background: #0A1F44; color: white; padding: 20px; font-family: sans-serif; }
        .content { padding: 20px; font-family: sans-serif; line-height: 1.6; color: #333; }
        .meta { font-size: 12px; color: #777; border-top: 1px solid #eee; margin-top: 20px; padding-top: 10px; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>LuckyGeneMDx Incoming Inquiry</h2>
    </div>
    <div class='content'>
        <p><strong>From:</strong> $full_name ($email)</p>
        <p><strong>Role:</strong> " . ucfirst($role) . "</p>
        <p><strong>Subject:</strong> $subject</p>
        <hr>
        <p><strong>Message:</strong><br>$message</p>
        
        <div class='meta'>
            Sent via LuckyGeneMDx Contact Portal<br>
            Timestamp: " . date('Y-m-d H:i:s') . "
        </div>
    </div>
</body>
</html>
";

// Headers for HTML Email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: LuckyGeneMDx Portal <noreply@luckygenemdx.com>" . "\r\n";
$headers .= "Reply-To: $email" . "\r\n";

// 5. Send Email (Note: mail() requires a configured SMTP server)
$success = mail($to_email, $email_subject, $email_body, $headers);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiry Received | LuckyGeneMDx</title>
    <link rel="stylesheet" href="css/main.css">
    <style>
        .success-page { text-align: center; padding: 100px 20px; font-family: 'Poppins', sans-serif; }
        .success-icon { font-size: 4rem; color: #00B3A4; margin-bottom: 20px; }
        .btn-home { display: inline-block; margin-top: 30px; padding: 12px 25px; background: #0A1F44; color: white; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="success-page">
        <div class="success-icon">✔️</div>
        <h1>Message Received</h1>
        <p>Thank you, <?php echo $first_name; ?>. Our clinical team has received your inquiry.</p>
        <p>We typically respond to <?php echo ($role === 'provider') ? 'healthcare providers' : 'inquiries'; ?> within 1 business day.</p>
        <a href="index.php" class="btn-home">Return to Homepage</a>
    </div>
</body>
</html>