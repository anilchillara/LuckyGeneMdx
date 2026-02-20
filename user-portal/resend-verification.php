<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();

$message = '';
$isError = false;
$done    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $userModel = new User();
    $result    = $userModel->resendVerificationEmail($email);

    $message = $result['message'];
    $isError = !$result['success'];
    $done    = $result['success'];
}

$prefillEmail = htmlspecialchars($_GET['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resend Verification – LuckyGeneMDx</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/portal.css">
</head>
<body class="auth-body">
<div class="auth-bg-video">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>
<div class="auth-card">
    <div style="text-align:center; margin-bottom:1.5rem;">
        <div style="font-size:3rem;">📧</div>
        <h1>Resend Verification</h1>
        <p>Enter your email to receive a new verification link</p>
    </div>

    <?php if ($message): ?>
    <div class="msg <?php echo $isError ? 'msg-error' : 'msg-success'; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (!$done): ?>
    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@example.com"
                   value="<?php echo $prefillEmail; ?>" required>
        </div>
        <button type="submit" class="btn btn-full">Send Verification Email</button>
    </form>
    <?php endif; ?>

    <a href="login.php" class="btn btn-outline btn-full" style="margin-top:1rem;">← Back to Login</a>
</div>
</body>
</html>
