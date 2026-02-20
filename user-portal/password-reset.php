<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();

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
    <title>Reset Security Credentials - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body class="auth-body">

    <div class="auth-bg-video">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="auth-card">
        <div style="margin-bottom: 20px;">
            <a href="../index.php" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem;">✕ Close and Exit</a>
        </div>
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="font-size:3rem;">🔑</div>
            <h1 id="title">Account Recovery</h1>
            <p id="desc">Verify your identity to reset password</p>
        </div>

        <?php if ($error): ?>
            <div class="msg msg-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="msg msg-success">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" id="resetForm">
            <div id="step-1" class="step-content active" style="display:block;">
                <div class="form-group">
                    <label>Account Email</label>
                    <input type="email" name="email" id="email" placeholder="name@example.com" required>
                </div>
                <button type="button" class="btn btn-full" onclick="showStep(2)">Verify Account →</button>
            </div>

            <div id="step-2" class="step-content" style="display:none;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="dob">
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" id="phone" placeholder="(555) 000-0000">
                    </div>
                </div>
                <div class="form-group">
                    <label>New Security Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-full">Reset & Sign In</button>
                <button type="button" onclick="showStep(1)" class="btn btn-outline btn-full" style="margin-top:10px;">← Back</button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="login.php" class="btn btn-outline">Return to Login</a>
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