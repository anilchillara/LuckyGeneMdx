<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
session_start();
setSecurityHeaders();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $secretKey = $_POST['secret_key'] ?? ''; // Added a simple secret key check for admin reset security

    if ($username && $newPassword && $confirmPassword && $secretKey) {
        if ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($secretKey !== 'AdminSecret123') { // In production, this should be a robust verification method
            $error = "Invalid recovery key.";
        } else {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT admin_id FROM admins WHERE username = :username");
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch();

                if ($admin) {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $update = $db->prepare("UPDATE admins SET password_hash = :hash WHERE admin_id = :id");
                    $update->execute([':hash' => $hashed, ':id' => $admin['admin_id']]);
                    
                    $success = "Password reset successfully. Redirecting to login...";
                    header("refresh:2;url=login.php");
                } else {
                    $error = "Admin user not found.";
                }
            } catch (PDOException $e) {
                error_log("Admin Reset Error: " . $e->getMessage());
                $error = "System error. Please try again.";
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Reset - LuckyGeneMDx</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="auth-body">
    <div class="auth-bg-video">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="auth-card">
        <a href="login.php" style="display:block; margin-bottom: 1rem; font-size: 0.9rem;">← Back to Login</a>

        <div style="text-align:center; margin-bottom: 2rem;">
            <div style="font-size: 3rem;">🔐</div>
            <h1>Admin Recovery</h1>
            <p>Reset your administrative access</p>
        </div>
            
        <?php if ($error): ?>
            <div class="msg msg-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="msg msg-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="secret_key">Recovery Key</label>
                <input type="password" id="secret_key" name="secret_key" required placeholder="Enter system recovery key">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-full" style="margin-top: 1.5rem;">Reset Password</button>
        </form>
    </div>
</body>
</html>