<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();

$token     = trim($_GET['token'] ?? '');
$userModel = new User();
$result    = $userModel->verifyEmailToken($token);

$success = $result['success'];
$message = $result['message'];
$expired = $result['expired'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $success ? 'Email Verified' : 'Verification Failed'; ?> – LuckyGeneMDx</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/portal.css">
</head>
<body class="auth-body">
<div class="auth-bg-video">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>

<div class="auth-card" style="text-align:center;">
    <?php if ($success): ?>
        <div style="font-size:4rem; margin-bottom:1rem;">✅</div>
        <h1>Email Verified!</h1>
        <p>Your account is now active. You can log in to the LuckyGeneMDx Patient Portal.</p>
        <a href="login.php" class="btn btn-full">Sign In to Your Account</a>

    <?php elseif ($expired): ?>
        <div style="font-size:4rem; margin-bottom:1rem;">⏰</div>
        <h1>Link Expired</h1>
        <p>Your verification link has expired. Request a new one below — it's free and takes a second.</p>
        <div class="msg msg-error"><?php echo $message; ?></div>
        <a href="resend-verification.php" class="btn btn-full">Request New Link</a>
        <br>
        <a href="register.php" class="btn btn-outline btn-full" style="margin-top:10px;">Start Over</a>

    <?php else: ?>
        <div style="font-size:4rem; margin-bottom:1rem;">❌</div>
        <h1>Verification Failed</h1>
        <p>This link is invalid or has already been used.</p>
        <div class="msg msg-error"><?php echo $message; ?></div>
        <a href="login.php" class="btn btn-full">Go to Login</a>
        <br>
        <a href="resend-verification.php" class="btn btn-outline btn-full" style="margin-top:10px;">Resend Verification Email</a>
    <?php endif; ?>
</div>
</body>
</html>
