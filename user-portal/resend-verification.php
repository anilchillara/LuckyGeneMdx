<?php
define('LuckyGenesMDx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

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
<title>Resend Verification – LuckyGenesMDx</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/main.css">
</head>
<body class="auth-body">

<div class="auth-card">
    <div class="text-center mb-4">
        <a href="login.php" class="text-dark-gray" style="font-size: 0.9rem;">← Back to Login</a>
    </div>

    <div class="mb-4">
        <img src="../assets/images/logo_small.png" alt="Logo" style="height: 48px; margin-bottom: 1rem;">
        <h1 class="font-xl mb-2">Resend Verification</h1>
        <p class="auth-title">Enter your email to receive a new verification link</p>
    </div>

    <?php if ($message): ?>
    <div class="<?php echo $isError ? 'glass-card-error text-error' : 'glass-card-teal-left text-teal'; ?> p-3 mb-3"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!$done): ?>
    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?php echo $prefillEmail; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Send Verification Email</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>