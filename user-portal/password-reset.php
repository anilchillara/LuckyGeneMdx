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
    <style>
        :root {
            --glass-dark: rgba(10, 31, 68, 0.8);
            --glass-border: rgba(255, 255, 255, 0.12);
            --teal-bright: #00E0C6;
            --text-white: #ffffff;
            --text-dim: #94a3b8;
            --input-bg: rgba(15, 23, 42, 0.6);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0f172a; font-family: 'Inter', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }

        .video-bg { position: fixed; inset: 0; z-index: 0; }
        .video-bg video { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.3) blur(10px); }
        
        .glass-card {
            position: relative; z-index: 10; width: 100%; max-width: 450px;
            background: var(--glass-dark); backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border); border-radius: 28px;
            padding: 2.5rem; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .logo-section { text-align: center; margin-bottom: 2rem; }
        .logo-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
        h1 { color: var(--text-white); font-size: 1.5rem; font-family: 'Poppins'; }
        p { color: var(--text-dim); font-size: 0.9rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; color: var(--text-dim); font-size: 0.7rem; text-transform: uppercase; margin-bottom: 6px; font-weight: 700; }
        input { width: 100%; padding: 12px 16px; background: var(--input-bg); border: 1px solid var(--glass-border); border-radius: 12px; color: white; transition: 0.3s; }
        input:focus { outline: none; border-color: var(--teal-bright); }

        .btn-primary { width: 100%; padding: 14px; background: linear-gradient(135deg, #00B3A4, #00E0C6); border: none; border-radius: 12px; color: white; font-weight: 700; cursor: pointer; margin-top: 1rem; }
        
        .status-msg { padding: 12px; border-radius: 12px; margin-bottom: 1rem; text-align: center; font-size: 0.85rem; }
        .error { background: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.2); }
        .success { background: rgba(16, 185, 129, 0.1); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.2); }

        .step-content { display: none; }
        .step-content.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="video-bg">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="glass-card">
        <div style="margin-bottom: 20px;">
            <a href="../index.php" style="color: var(--text-dim); text-decoration: none; font-size: 0.8rem;">✕ Close and Exit</a>
        </div>
        <div class="logo-section">
            <div class="logo-icon">🔑</div>
            <h1 id="title">Account Recovery</h1>
            <p id="desc">Verify your identity to reset password</p>
        </div>

        <?php if ($error): ?>
            <div class="status-msg error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="status-msg success">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" id="resetForm">
            <div id="step-1" class="step-content active">
                <div class="form-group">
                    <label>Account Email</label>
                    <input type="email" name="email" id="email" placeholder="name@example.com" required>
                </div>
                <button type="button" class="btn-primary" onclick="showStep(2)">Verify Account →</button>
            </div>

            <div id="step-2" class="step-content">
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
                <hr style="border: 0; border-top: 1px solid var(--glass-border); margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label>New Security Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary">Reset & Sign In</button>
                <button type="button" onclick="showStep(1)" style="background:none; border:none; color:var(--text-dim); width:100%; margin-top:15px; cursor:pointer; font-size:0.8rem;">← Back</button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="login.php" style="color: var(--teal-bright); font-size: 0.85rem; text-decoration: none; font-weight: 600;">Return to Login</a>
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

            document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');
        }
    </script>
</body>
</html>