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
<style>
    :root { --teal:#00B3A4; --teal-bright:#00E0C6; --text-white:#fff; --text-dim:#94a3b8; --glass-dark:rgba(10,31,68,.82); --glass-border:rgba(255,255,255,.12); }
    *{margin:0;padding:0;box-sizing:border-box}
    html,body{height:100%;font-family:'Inter',sans-serif;background:#0f172a}
    body{display:flex;align-items:center;justify-content:center;padding:1rem}

    .video-bg{position:fixed;inset:0;z-index:0}
    .video-bg video{width:100%;height:100%;object-fit:cover;filter:brightness(.3) blur(8px)}
    .video-bg::after{content:'';position:absolute;inset:0;background:rgba(10,20,50,.7)}

    .card{position:relative;z-index:10;background:var(--glass-dark);backdrop-filter:blur(24px);border:1px solid var(--glass-border);border-radius:28px;padding:3rem 2.5rem;max-width:440px;width:100%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,.5)}
    .icon{font-size:4rem;margin-bottom:1.25rem;display:inline-block;animation:pulse 2s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.06)}}
    h1{font-family:'Poppins',sans-serif;color:var(--text-white);font-size:1.5rem;margin-bottom:.75rem}
    p{color:var(--text-dim);font-size:.9rem;line-height:1.7;margin-bottom:1.5rem}
    .btn{display:inline-block;padding:14px 32px;background:linear-gradient(135deg,var(--teal),var(--teal-bright));color:#fff;text-decoration:none;border-radius:14px;font-weight:700;font-size:.93rem;transition:.3s;box-shadow:0 6px 24px rgba(0,224,198,.25)}
    .btn:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,224,198,.38)}
    .btn-outline{display:inline-block;margin-top:.75rem;padding:12px 28px;border:1.5px solid rgba(0,224,198,.3);color:var(--teal-bright);text-decoration:none;border-radius:14px;font-size:.88rem;font-weight:600;transition:.3s}
    .btn-outline:hover{background:rgba(0,224,198,.1)}
    .alert-box{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:12px 16px;margin-bottom:1.5rem;color:#fca5a5;font-size:.84rem}
</style>
</head>
<body>
<div class="video-bg">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>

<div class="card">
    <?php if ($success): ?>
        <div class="icon">✅</div>
        <h1>Email Verified!</h1>
        <p>Your account is now active. You can log in to the LuckyGeneMDx Patient Portal.</p>
        <a href="login.php" class="btn">Sign In to Your Account</a>

    <?php elseif ($expired): ?>
        <div class="icon">⏰</div>
        <h1>Link Expired</h1>
        <p>Your verification link has expired. Request a new one below — it's free and takes a second.</p>
        <div class="alert-box"><?php echo $message; ?></div>
        <a href="resend-verification.php" class="btn">Request New Link</a>
        <br>
        <a href="register.php" class="btn-outline">Start Over</a>

    <?php else: ?>
        <div class="icon">❌</div>
        <h1>Verification Failed</h1>
        <p>This link is invalid or has already been used.</p>
        <div class="alert-box"><?php echo $message; ?></div>
        <a href="login.php" class="btn">Go to Login</a>
        <br>
        <a href="resend-verification.php" class="btn-outline">Resend Verification Email</a>
    <?php endif; ?>
</div>
</body>
</html>
