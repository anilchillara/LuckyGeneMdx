<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    $email = trim($_POST['email'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    // In a real production environment, you would use a token-based email reset.
    // This logic follows a "Security Question" style reset based on user info.
    if ($email && $dob && $phone && $newPassword) {
        $result = $userModel->resetPasswordVerifyInfo($email, $dob, $phone, $newPassword);
        if ($result['success']) {
            $success = "Password updated successfully. Redirecting to login...";
            header("refresh:3;url=login.php");
        } else {
            $error = $result['message'];
        }
    } else {
        $error = "Please fill in all security verification fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Security Credentials - LuckyGenes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="../index.php" class="text-dark-gray" style="font-size: 0.9rem;">← Back to Main Site</a>
        </div>
        <div class="mb-4">
            <img src="../assets/images/logo_small.png" alt="Logo" style="height: 48px; margin-bottom: 1rem;">
            <h1 class="font-xl mb-2" id="title">Account Recovery</h1>
            <p class="auth-title" id="desc">Verify your identity to reset password</p>
        </div>

        <?php if ($error): ?>
            <div class="glass-card-error p-3 mb-3 text-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="glass-card-teal-left p-3 mb-3 text-teal">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" id="resetForm">
            <div id="step-1" class="step-content active d-block">
                <div class="form-group">
                    <label>Account Email</label>
                    <input type="email" name="email" id="email" placeholder="name@example.com" required class="form-control">
                </div>
                <button type="button" class="btn btn-primary btn-full" onclick="showStep(2)">Verify Account →</button>
            </div>

            <div id="step-2" class="step-content" style="display:none;">
                <div class="form-row mb-1">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" id="phone" placeholder="(555) 000-0000" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>New Security Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="••••••••" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-full">Reset & Sign In</button>
                <button type="button" onclick="showStep(1)" class="btn btn-outline btn-full mt-10">← Back</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <a href="login.php" class="btn btn-outline btn-full">Return to Login</a>
        </div>
    </div>

    <script>
        function showStep(step) {
            if (step === 2) {
                const email = document.getElementById('email').value;
                if (!email) {
                    alert("Please enter your email address first.");
                    return;
                }
                document.getElementById('title').innerText = "Identity Check";
                document.getElementById('desc').innerText = "Confirm details for " + email;
            } else {
                document.getElementById('title').innerText = "Account Recovery";
                document.getElementById('desc').innerText = "Verify your identity to reset password";
            }

            document.querySelectorAll('.step-content').forEach(s => s.style.display = 'none');
            document.getElementById('step-' + step).style.display = 'block';
        }
    </script>
</body>
</html>