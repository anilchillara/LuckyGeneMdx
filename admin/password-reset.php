<?php
define('LuckyGenesMDx', true);
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
    <title>Admin Password Reset - LuckyGenesMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="admin-login-body">

    <div class="admin-login-card">
        <div class="text-center mb-4">
            <a href="login.php" class="text-dark-gray" style="font-size: 0.9rem;">← Back to Login</a>
        </div>

        <div class="mb-4">
            <img src="../assets/images/logo_small.png" alt="Logo" style="height: 48px; margin-bottom: 1rem;">
            <h1 class="font-xl mb-2">Admin Recovery</h1>
            <p class="admin-login-title">Reset your administrative access</p>
        </div>
            
        <?php if ($error): ?>
            <div class="glass-card-error p-3 mb-3 text-error" role="alert">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="glass-card-teal-left p-3 mb-3 text-teal" role="alert">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required class="form-control">
            </div>
            <div class="form-group">
                <label for="secret_key">Recovery Key</label>
                <input type="password" id="secret_key" name="secret_key" required placeholder="Enter system recovery key" class="form-control">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" class="form-control">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</body>
</html>