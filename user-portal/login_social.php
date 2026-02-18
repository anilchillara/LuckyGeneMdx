<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$loginType = 'email'; // Default

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginType = $_POST['login_type'] ?? 'email';
    $userModel = new User();
    
    if ($loginType === 'order') {
        $orderNumber = trim($_POST['order_number'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($orderNumber && $password) {
            $result = $userModel->loginWithOrderId($orderNumber, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else { $error = $result['message']; }
        } else { $error = 'Please enter both order number and password.'; }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email && $password) {
            $result = $userModel->login($email, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else { $error = $result['message']; }
        } else { $error = 'Please enter both email and password.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal Login - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-dark: rgba(10, 31, 68, 0.75);
            --glass-border: rgba(255, 255, 255, 0.12);
            --teal: #00B3A4;
            --teal-bright: #00E0C6;
            --teal-glow: rgba(0, 224, 198, 0.25);
            --navy: #0A1F44;
            --text-white: #ffffff;
            --text-dim: #94a3b8;
            --input-bg: rgba(15, 23, 42, 0.6);
            --tab-bg: rgba(0, 0, 0, 0.2);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; overflow: hidden; }
        body { background: #0f172a; display: flex; align-items: center; justify-content: center; position: relative; }

        /* VIDEO BACKGROUND & PARTICLES (Original Styles) */
        .video-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden; }
        .video-bg video { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.35) blur(6px); }
        .video-bg::after { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 50% 50%, rgba(10, 31, 68, 0.3) 0%, rgba(10, 31, 68, 0.85) 100%); pointer-events: none; }
        .particles { position: fixed; inset: 0; z-index: 1; pointer-events: none; overflow: hidden; }
        .particle { position: absolute; width: 3px; height: 3px; background: rgba(0, 224, 198, 0.4); border-radius: 50%; animation: float 20s infinite ease-in-out; }
        @keyframes float { 0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-100vh) scale(1); opacity: 0; } }

        /* GLASS CARD & ANIMATIONS */
        .login-container { position: relative; z-index: 10; width: 100%; max-width: 440px; padding: 1rem; }
        .glass-card { background: var(--glass-dark); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid var(--glass-border); border-radius: 28px; padding: 3rem 2.5rem; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5); transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        .logo-section { text-align: center; margin-bottom: 2rem; }
        .logo-icon { font-size: 3rem; margin-bottom: 0.75rem; display: inline-block; animation: pulse 3s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .logo-section h1 { font-family: 'Poppins', sans-serif; color: var(--teal-bright); font-size: 1.75rem; font-weight: 700; }
        .logo-section p { color: var(--text-dim); font-size: 0.95rem; }

        /* TAB NAVIGATION */
        .tab-nav { display: flex; background: var(--tab-bg); padding: 5px; border-radius: 14px; margin-bottom: 2rem; }
        .tab-btn { flex: 1; padding: 12px; text-align: center; color: var(--text-dim); font-size: 0.85rem; font-weight: 600; cursor: pointer; border-radius: 10px; transition: 0.3s; }
        .tab-btn.active { color: var(--text-white); background: rgba(0, 224, 198, 0.15); box-shadow: 0 2px 8px rgba(0, 224, 198, 0.2); }

        /* FORMS & INPUTS */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; color: var(--text-dim); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        input { width: 100%; padding: 15px 18px; background: var(--input-bg); border: 1.5px solid rgba(255, 255, 255, 0.06); border-radius: 14px; color: var(--text-white); font-size: 1rem; transition: 0.3s; }
        input:focus { outline: none; border-color: var(--teal-bright); background: rgba(15, 23, 42, 0.8); box-shadow: 0 0 0 3px rgba(0, 224, 198, 0.1); }

        .submit-btn { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--teal) 0%, var(--teal-bright) 100%); border: none; border-radius: 14px; color: var(--text-white); font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 6px 24px var(--teal-glow); }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0, 224, 198, 0.35); }

        /* SPLIT-STEP SPECIFIC UI */
        .social-divider { display: flex; align-items: center; margin: 1.5rem 0; color: var(--text-dim); font-size: 0.75rem; text-transform: uppercase; }
        .social-divider::before, .social-divider::after { content: ""; flex: 1; height: 1px; background: rgba(255,255,255,0.1); }
        .social-divider span { padding: 0 10px; }

        .social-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .social-btn { background: var(--input-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px; cursor: pointer; display: flex; justify-content: center; transition: 0.3s; }
        .social-btn:hover { background: rgba(255,255,255,0.1); border-color: var(--teal-bright); }
        .social-btn svg { width: 20px; height: 20px; fill: white; }

        .user-preview { background: rgba(0,0,0,0.2); padding: 12px 16px; border-radius: 14px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border: 1px dashed var(--glass-border); }
        #display-identifier { color: var(--text-white); font-size: 0.9rem; font-weight: 500; overflow: hidden; text-overflow: ellipsis; }
        .edit-btn { background: none; border: none; color: var(--teal-bright); font-size: 0.8rem; cursor: pointer; font-weight: 600; }

        .password-toggle { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-dim); }
        .error-msg { background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 12px; margin-bottom: 1rem; color: #fca5a5; font-size: 0.85rem; text-align: center; }

        @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        .step-active { animation: slideIn 0.4s ease forwards; }
        
        .footer-section { text-align: center; margin-top: 2rem; }
        .register-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; border: 1.5px solid rgba(0, 224, 198, 0.3); border-radius: 50px; color: var(--teal-bright); text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .register-btn:hover { background: rgba(0, 224, 198, 0.1); transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="video-bg">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="particles">
        <div class="particle" style="left:10%"></div><div class="particle" style="left:40%"></div><div class="particle" style="left:70%"></div>
    </div>

    <div class="login-container">
        <div class="glass-card">
            <div class="logo-section">
                <div class="logo-icon">🧬</div>
                <h1 id="header-title">Patient Portal</h1>
                <p id="header-desc">Welcome back to LuckyGeneMDx</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" id="auth-form">
                <input type="hidden" name="login_type" id="login_type" value="<?php echo $loginType; ?>">

                <div id="step-1" class="step-content">
                    <div class="tab-nav" id="login-tabs">
                        <div class="tab-btn <?php echo $loginType === 'email' ? 'active' : ''; ?>" onclick="switchTab('email', this)">Email Login</div>
                        <div class="tab-btn <?php echo $loginType === 'order' ? 'active' : ''; ?>" onclick="switchTab('order', this)">Order Login</div>
                    </div>

                    <div id="email-group" class="form-group" style="<?php echo $loginType === 'order' ? 'display:none' : ''; ?>">
                        <label>Email Address</label>
                        <input type="email" name="email" id="email-input" placeholder="name@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div id="order-group" class="form-group" style="<?php echo $loginType === 'email' ? 'display:none' : ''; ?>">
                        <label>Order Number</label>
                        <input type="text" name="order_number" id="order-input" placeholder="LGM240214ABC123">
                    </div>

                    <button type="button" class="submit-btn" onclick="validateAndGoToStep2()">Continue</button>

                    <div class="social-divider"><span>or continue with</span></div>
                    <div class="social-grid">
                        <button type="button" class="social-btn" title="Google"><svg viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-1.92 5.36-7.84 5.36-5.12 0-9.28-4.24-9.28-9.48s4.16-9.48 9.28-9.48c2.92 0 4.88 1.2 6 2.28l2.56-2.48C18.92 1.92 15.96 0 12.48 0 5.52 0 0 5.52 0 12.48s5.52 12.48 12.48 12.48c7.28 0 12.12-5.12 12.12-12.32 0-.84-.08-1.48-.2-2.12h-11.92z"/></svg></button>
                        <button type="button" class="social-btn" title="Apple"><svg viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05 1.78-3.14 1.72-1.09-.06-1.49-.72-2.74-.72-1.25 0-1.7.7-2.71.75-1.02.05-2.2-.95-3.18-1.89-2-1.92-3.53-5.41-3.53-8.7 0-5.32 3.44-8.13 6.71-8.13 1.73 0 3.37.59 4.41 1.25 1.04.66 1.83.66 2.87 0 1.04-.66 2.68-1.25 4.41-1.25 1.54 0 3.01.55 4.14 1.56-4.6 2.13-3.87 8.71.74 10.46-.86 2.12-1.99 4.01-2.98 4.95zM12.04 4.54c-.11-2.43 1.89-4.54 4.31-4.54.12 2.54-2.11 4.75-4.31 4.54z"/></svg></button>
                        <button type="button" class="social-btn" title="Facebook"><svg viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-8.791h-2.96v-3.429h2.96v-2.528c0-2.937 1.793-4.535 4.412-4.535 1.254 0 2.333.093 2.646.136v3.069h-1.815c-1.424 0-1.7.677-1.7 1.67v2.188h3.398l-.441 3.429h-2.957v8.791h6.09c.733 0 1.325-.593 1.325-1.324v-21.351c0-.732-.592-1.325-1.325-1.325z"/></svg></button>
                    </div>
                </div>

                <div id="step-2" class="step-content" style="display: none;">
                    <div class="user-preview">
                        <span id="display-identifier">user@example.com</span>
                        <button type="button" class="edit-btn" onclick="goToStep1()">Edit</button>
                    </div>

                    <div class="form-group">
                        <label>Security Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password-input" placeholder="••••••••">
                            <span class="password-toggle" onclick="togglePass()">👁️</span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Authorize & Sign In</button>
                    <a href="password-reset.php" style="display:block; text-align:center; margin-top:1.5rem; color:var(--text-dim); text-decoration:none; font-size:0.85rem;">Forgot Password?</a>
                </div>
            </form>

            <div class="footer-section">
                <p style="color:var(--text-dim); font-size:0.85rem; margin-bottom:1rem;">Don't have an account?</p>
                <a href="register.php" class="register-btn">Create Secure Account</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(type, el) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('login_type').value = type;
            document.getElementById('email-group').style.display = (type === 'email') ? 'block' : 'none';
            document.getElementById('order-group').style.display = (type === 'order') ? 'block' : 'none';
        }

        function validateAndGoToStep2() {
            const type = document.getElementById('login_type').value;
            const input = type === 'email' ? document.getElementById('email-input') : document.getElementById('order-input');
            
            if(!input.value) {
                input.style.borderColor = '#ef4444';
                return;
            }

            document.getElementById('display-identifier').innerText = input.value;
            document.getElementById('step-1').style.display = 'none';
            document.getElementById('header-title').innerText = "Security Check";
            document.getElementById('header-desc').innerText = "Please verify your password";
            
            const step2 = document.getElementById('step-2');
            step2.style.display = 'block';
            step2.classList.add('step-active');
        }

        function goToStep1() {
            document.getElementById('step-2').style.display = 'none';
            document.getElementById('step-1').style.display = 'block';
            document.getElementById('header-title').innerText = "Patient Portal";
            document.getElementById('header-desc').innerText = "Welcome back to LuckyGeneMDx";
            document.getElementById('step-1').classList.add('step-active');
        }

        function togglePass() {
            const p = document.getElementById('password-input');
            p.type = p.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>