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
<style>
    :root{--teal:#00B3A4;--teal-bright:#00E0C6;--text-white:#fff;--text-dim:#94a3b8;--glass-dark:rgba(10,31,68,.82);--glass-border:rgba(255,255,255,.12);--input-bg:rgba(15,23,42,.6)}
    *{margin:0;padding:0;box-sizing:border-box}
    html,body{height:100%;font-family:'Inter',sans-serif;background:#0f172a}
    body{display:flex;align-items:center;justify-content:center;padding:1rem}
    .video-bg{position:fixed;inset:0;z-index:0}
    .video-bg video{width:100%;height:100%;object-fit:cover;filter:brightness(.3) blur(8px)}
    .video-bg::after{content:'';position:absolute;inset:0;background:rgba(10,20,50,.7)}
    .card{position:relative;z-index:10;background:var(--glass-dark);backdrop-filter:blur(24px);border:1px solid var(--glass-border);border-radius:28px;padding:2.75rem 2.5rem;max-width:420px;width:100%;box-shadow:0 25px 60px rgba(0,0,0,.5)}
    .logo-section{text-align:center;margin-bottom:1.75rem}
    .logo-icon{font-size:2.6rem;margin-bottom:.5rem;display:inline-block;animation:pulse 3s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
    h1{font-family:'Poppins',sans-serif;color:var(--teal-bright);font-size:1.4rem;font-weight:700}
    .logo-section p{color:var(--text-dim);font-size:.88rem;margin-top:.3rem}
    .form-group{margin-bottom:1.25rem}
    label{display:block;color:var(--text-dim);font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}
    input[type=email]{width:100%;padding:14px 16px;background:var(--input-bg);border:1.5px solid rgba(255,255,255,.07);border-radius:13px;color:#fff;font-size:.95rem;font-family:'Inter',sans-serif;transition:.3s}
    input[type=email]:focus{outline:none;border-color:var(--teal-bright);box-shadow:0 0 0 3px rgba(0,224,198,.1)}
    .btn{width:100%;padding:14px;background:linear-gradient(135deg,var(--teal),var(--teal-bright));border:none;border-radius:14px;color:#fff;font-weight:700;font-size:.93rem;cursor:pointer;transition:.3s;box-shadow:0 6px 24px rgba(0,224,198,.25)}
    .btn:hover{transform:translateY(-2px)}
    .msg{border-radius:12px;padding:11px 14px;margin-bottom:1rem;font-size:.84rem;text-align:center}
    .msg.error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
    .msg.success{background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.3);color:#86efac}
    .back-link{display:block;text-align:center;margin-top:1.25rem;color:var(--text-dim);text-decoration:none;font-size:.84rem;transition:.3s}
    .back-link:hover{color:var(--teal-bright)}
</style>
</head>
<body>
<div class="video-bg">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>
<div class="card">
    <div class="logo-section">
        <div class="logo-icon">📧</div>
        <h1>Resend Verification</h1>
        <p>Enter your email to receive a new verification link</p>
    </div>

    <?php if ($message): ?>
    <div class="msg <?php echo $isError ? 'error' : 'success'; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if (!$done): ?>
    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@example.com"
                   value="<?php echo $prefillEmail; ?>" required>
        </div>
        <button type="submit" class="btn">Send Verification Email</button>
    </form>
    <?php endif; ?>

    <a href="login.php" class="back-link">← Back to Login</a>
</div>
</body>
</html>
