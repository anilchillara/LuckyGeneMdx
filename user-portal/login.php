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
        
        html, body { 
            height: 100%; 
            font-family: 'Inter', sans-serif; 
            overflow: hidden;
        }

        body {
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* ── VIDEO BACKGROUND ─────────────────────── */
        .video-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .video-bg video {
            width: 100%; height: 100%;
            object-fit: cover;
            filter: brightness(0.35) blur(6px);
        }

        .video-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, 
                rgba(10, 31, 68, 0.3) 0%, 
                rgba(10, 31, 68, 0.85) 100%);
            pointer-events: none;
        }

        /* ── FLOATING PARTICLES ────────────────────── */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(0, 224, 198, 0.4);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 15s; }
        .particle:nth-child(2) { left: 30%; animation-delay: 2s; animation-duration: 18s; }
        .particle:nth-child(3) { left: 50%; animation-delay: 4s; animation-duration: 22s; }
        .particle:nth-child(4) { left: 70%; animation-delay: 1s; animation-duration: 19s; }
        .particle:nth-child(5) { left: 90%; animation-delay: 3s; animation-duration: 16s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }

        /* ── GLASS CARD ────────────────────────────── */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .glass-card {
            background: var(--glass-dark);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5),
                        0 0 80px rgba(0, 224, 198, 0.08);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── LOGO HEADER ───────────────────────────── */
        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: inline-block;
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logo-section h1 {
            font-family: 'Poppins', sans-serif;
            color: var(--teal-bright);
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        /* ── TAB NAVIGATION ────────────────────────── */
        .tab-nav {
            display: flex;
            background: var(--tab-bg);
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 2rem;
            position: relative;
        }

        .tab-btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            position: relative;
            z-index: 1;
        }

        .tab-btn.active {
            color: var(--text-white);
            background: rgba(0, 224, 198, 0.15);
            box-shadow: 0 2px 8px rgba(0, 224, 198, 0.2),
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        /* ── ERROR MESSAGE ─────────────────────────── */
        .error-msg {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        .error-msg::before {
            content: '⚠️';
            font-size: 1.25rem;
        }

        .error-msg span {
            color: #fca5a5;
            font-size: 0.9rem;
            flex: 1;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        /* ── FORM STYLES ───────────────────────────── */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            color: var(--text-dim);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px 18px;
            background: var(--input-bg);
            border: 1.5px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            color: var(--text-white);
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input.has-toggle {
            padding-right: 50px;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--teal-bright);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 3px rgba(0, 224, 198, 0.1),
                        0 4px 12px rgba(0, 224, 198, 0.15);
        }

        /* ── PASSWORD TOGGLE ───────────────────────── */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dim);
            transition: color 0.2s;
            user-select: none;
        }

        .password-toggle:hover {
            color: var(--teal-bright);
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        /* ── SUBMIT BUTTON ─────────────────────────── */
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--teal) 0%, var(--teal-bright) 100%);
            border: none;
            border-radius: 14px;
            color: var(--text-white);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            margin-top: 0.5rem;
            box-shadow: 0 6px 24px var(--teal-glow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 224, 198, 0.35);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* ── LINKS ─────────────────────────────────── */
        .forgot-pass {
            display: block;
            margin-top: 1.25rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-pass:hover {
            color: var(--teal-bright);
        }

        /* ── DIVIDER ───────────────────────────────── */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            margin: 2.5rem 0;
        }

        /* ── FOOTER SECTION ────────────────────────── */
        .footer-section {
            text-align: center;
        }

        .footer-text {
            color: var(--text-dim);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .register-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border: 1.5px solid rgba(0, 224, 198, 0.3);
            border-radius: 50px;
            color: var(--teal-bright);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            background: rgba(0, 224, 198, 0.05);
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            border-color: var(--teal-bright);
            background: rgba(0, 224, 198, 0.12);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 224, 198, 0.2);
        }

        .exit-link {
            display: inline-block;
            margin-top: 1.5rem;
            color: var(--text-dim);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .exit-link:hover {
            color: var(--text-white);
        }

        /* ── RESPONSIVE ────────────────────────────── */
        @media (max-width: 768px) {
            .glass-card {
                padding: 2.5rem 2rem;
                border-radius: 24px;
            }

            .logo-section h1 {
                font-size: 1.5rem;
            }

            .tab-btn {
                font-size: 0.85rem;
                padding: 10px;
            }

            .form-group input {
                padding: 14px 16px;
                font-size: 0.95rem;
            }

            .submit-btn {
                padding: 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 0.75rem;
            }

            .glass-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }

            .logo-icon {
                font-size: 2.5rem;
            }

            .logo-section h1 {
                font-size: 1.35rem;
            }

            .tab-nav {
                padding: 4px;
            }

            .tab-btn {
                font-size: 0.8rem;
                padding: 9px;
            }

            .form-group {
                margin-bottom: 1.25rem;
            }

            .form-group input {
                padding: 13px 14px;
            }
        }

        /* ── LANDSCAPE MOBILE ──────────────────────── */
        @media (max-height: 600px) and (orientation: landscape) {
            .glass-card {
                max-height: 90vh;
                overflow-y: auto;
                padding: 1.5rem;
            }

            .logo-section {
                margin-bottom: 1.5rem;
            }

            .tab-nav {
                margin-bottom: 1.5rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .divider {
                margin: 1.5rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-bg">
        <video autoplay muted loop playsinline preload="auto">
            <source src="../assets/video/My580.webm" type="video/webm">
            <source src="../assets/video/My580.mp4" type="video/mp4">
        </video>
    </div>

    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Login Card -->
    <div class="login-container">
        <div class="glass-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">🧬</div>
                <h1>Patient Portal</h1>
                <p>Welcome back to LuckyGeneMDx</p>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-nav">
                <div class="tab-btn <?php echo $loginType === 'email' ? 'active' : ''; ?>" 
                     onclick="switchTab('email', this)">
                    Email Login
                </div>
                <div class="tab-btn <?php echo $loginType === 'order' ? 'active' : ''; ?>" 
                     onclick="switchTab('order', this)">
                    Order Login
                </div>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="error-msg">
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Email Login Tab -->
            <div id="email-tab" class="tab-content <?php echo $loginType === 'email' ? 'active' : ''; ?>">
                <form method="POST">
                    <input type="hidden" name="login_type" value="email">
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="name@example.com" 
                            required 
                            autocomplete="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="password" 
                                id="email-password"
                                class="has-toggle"
                                placeholder="••••••••" 
                                required
                                autocomplete="current-password"
                            >
                            <span class="password-toggle" onclick="togglePassword('email-password')">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg class="eye-off-icon" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Sign In Securely</button>
                    <a href="password-reset.php" class="forgot-pass">Forgot your password?</a>
                </form>
            </div>

            <!-- Order Login Tab -->
            <div id="order-tab" class="tab-content <?php echo $loginType === 'order' ? 'active' : ''; ?>">
                <form method="POST">
                    <input type="hidden" name="login_type" value="order">
                    
                    <div class="form-group">
                        <label>Order Number</label>
                        <input 
                            type="text" 
                            name="order_number" 
                            placeholder="LGM240214ABC123" 
                            required
                            autocomplete="off"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label>Checkout Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                name="password" 
                                id="order-password"
                                class="has-toggle"
                                placeholder="••••••••" 
                                required
                                autocomplete="current-password"
                            >
                            <span class="password-toggle" onclick="togglePassword('order-password')">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg class="eye-off-icon" style="display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Access Results</button>
                    <a href="#" class="forgot-pass" style="opacity: 0; pointer-events: none;">Spacer</a>
                </form>
            </div>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Footer Section -->
            <div class="footer-section">
                <p class="footer-text">New to our laboratory?</p>
                <a href="register.php" class="register-btn">
                    <span>Create Secure Account</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                <a href="../index.php" class="exit-link">← Return to Homepage</a>
            </div>
        </div>
    </div>

    <script>
        // Tab Switching
        function switchTab(type, el) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById(type + '-tab').classList.add('active');
        }

        // Password Toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling;
            const eyeIcon = toggle.querySelector('.eye-icon');
            const eyeOffIcon = toggle.querySelector('.eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }

        // Auto-hide error after 5 seconds
        const errorMsg = document.querySelector('.error-msg');
        if (errorMsg) {
            setTimeout(() => {
                errorMsg.style.transition = 'opacity 0.5s ease';
                errorMsg.style.opacity = '0';
                setTimeout(() => errorMsg.remove(), 500);
            }, 5000);
        }
    </script>
</body>
</html>