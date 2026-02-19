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
$loginType = $_POST['login_type'] ?? 'email';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();

    if ($loginType === 'order') {
        $orderNumber = trim($_POST['order_number'] ?? '');
        $password    = $_POST['password'] ?? '';
        if ($orderNumber && $password) {
            $result = $userModel->loginWithOrderId($orderNumber, $password);
            if ($result['success']) { header('Location: index.php'); exit; }
            else { $error = $result['message']; }
        } else { $error = 'Please enter both order number and password.'; }
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email && $password) {
            $result = $userModel->login($email, $password);
            if ($result['success']) { header('Location: index.php'); exit; }
            else { $error = $result['message']; }
        } else { $error = 'Please enter both email and password.'; }
    }
}

// Values to re-populate form after error
$postedEmail = htmlspecialchars($_POST['email'] ?? '');
$postedOrder = htmlspecialchars($_POST['order_number'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Portal Login – LuckyGeneMDx</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --glass-dark:   rgba(10, 31, 68, 0.82);
        --glass-border: rgba(255,255,255,0.12);
        --teal:         #00B3A4;
        --teal-bright:  #00E0C6;
        --teal-glow:    rgba(0,224,198,0.25);
        --text-white:   #ffffff;
        --text-dim:     #94a3b8;
        --input-bg:     rgba(15,23,42,0.6);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { height:100%; font-family:'Inter',sans-serif; overflow:hidden; }
    body { background:#0f172a; display:flex; align-items:center; justify-content:center; }

    /* Background */
    .video-bg { position:fixed; inset:0; z-index:0; overflow:hidden; }
    .video-bg video { width:100%; height:100%; object-fit:cover; filter:brightness(.35) blur(6px); }
    .video-bg::after { content:''; position:absolute; inset:0; background:radial-gradient(circle at 50% 50%, rgba(10,31,68,.3), rgba(10,31,68,.88)); pointer-events:none; }
    .particles { position:fixed; inset:0; z-index:1; pointer-events:none; }
    .particle { position:absolute; width:3px; height:3px; background:rgba(0,224,198,.4); border-radius:50%; animation:float linear infinite; }
    @keyframes float { 0%{transform:translateY(110vh) scale(0);opacity:0} 10%{opacity:.8} 90%{opacity:.8} 100%{transform:translateY(-10vh) scale(1);opacity:0} }

    /* Card */
    .login-container { position:relative; z-index:10; width:100%; max-width:440px; padding:1rem; }
    .glass-card { background:var(--glass-dark); backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px); border:1px solid var(--glass-border); border-radius:28px; padding:2.75rem 2.5rem; box-shadow:0 25px 60px rgba(0,0,0,.5); }
    .back-link { position:absolute; top:-40px; left:0; color:var(--text-dim); text-decoration:none; font-size:.84rem; display:flex; align-items:center; gap:5px; transition:.3s; }
    .back-link:hover { color:var(--teal-bright); }

    /* Logo */
    .logo-section { text-align:center; margin-bottom:1.75rem; }
    .logo-icon { font-size:3rem; margin-bottom:.6rem; display:inline-block; animation:pulse 3s ease-in-out infinite; }
    @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }
    .logo-section h1 { font-family:'Poppins',sans-serif; color:var(--teal-bright); font-size:1.65rem; font-weight:700; }
    .logo-section p { color:var(--text-dim); font-size:.9rem; margin-top:.2rem; }

    /* Tabs */
    .tab-nav { display:flex; background:rgba(0,0,0,.22); padding:4px; border-radius:14px; margin-bottom:1.75rem; gap:4px; }
    .tab-btn { flex:1; padding:10px 8px; text-align:center; color:var(--text-dim); font-size:.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; cursor:pointer; border-radius:10px; transition:.3s; }
    .tab-btn.active { color:var(--text-white); background:rgba(0,224,198,.18); box-shadow:0 2px 10px rgba(0,224,198,.2); }
    .tab-btn:hover:not(.active) { color:var(--text-white); }

    /* Inputs */
    .form-group { margin-bottom:1.4rem; }
    .form-group label { display:block; color:var(--text-dim); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:7px; }
    .input-wrapper { position:relative; }
    input { width:100%; padding:14px 16px; background:var(--input-bg); border:1.5px solid rgba(255,255,255,.07); border-radius:13px; color:var(--text-white); font-size:.95rem; font-family:'Inter',sans-serif; transition:.3s; }
    input:focus { outline:none; border-color:var(--teal-bright); background:rgba(15,23,42,.85); box-shadow:0 0 0 3px rgba(0,224,198,.1); }
    input.field-error { border-color:#ef4444 !important; box-shadow:0 0 0 3px rgba(239,68,68,.1) !important; }
    .pw-toggle { position:absolute; right:14px; top:50%; transform:translateY(-50%); cursor:pointer; color:var(--text-dim); user-select:none; transition:.2s; }
    .pw-toggle:hover { color:var(--text-white); }

    /* Buttons */
    .btn-primary { width:100%; padding:15px; background:linear-gradient(135deg,var(--teal) 0%,var(--teal-bright) 100%); border:none; border-radius:14px; color:#fff; font-weight:700; font-size:.95rem; cursor:pointer; transition:.3s; box-shadow:0 6px 24px var(--teal-glow); letter-spacing:.02em; }
    .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 32px rgba(0,224,198,.38); }
    .btn-primary:active { transform:translateY(0); }

    /* User preview */
    .user-preview { background:rgba(0,0,0,.2); padding:11px 16px; border-radius:14px; display:flex; justify-content:space-between; align-items:center; margin-bottom:1.4rem; border:1px dashed var(--glass-border); }
    .user-preview span { color:var(--text-white); font-size:.88rem; font-weight:500; overflow:hidden; text-overflow:ellipsis; max-width:260px; }
    .edit-btn { background:none; border:none; color:var(--teal-bright); font-size:.8rem; cursor:pointer; font-weight:600; padding:0; flex-shrink:0; }

    /* Error */
    .error-msg { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); border-radius:12px; padding:11px 14px; margin-bottom:1rem; color:#fca5a5; font-size:.84rem; text-align:center; }

    /* Divider + Social */
    .divider { display:flex; align-items:center; margin:1.4rem 0; color:var(--text-dim); font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; }
    .divider::before,.divider::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.09); }
    .divider span { padding:0 12px; }
    .social-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
    .social-btn { background:var(--input-bg); border:1px solid var(--glass-border); border-radius:13px; padding:12px; cursor:pointer; display:flex; justify-content:center; transition:.3s; }
    .social-btn:hover { background:rgba(255,255,255,.09); border-color:var(--teal-bright); }
    .social-btn svg { width:20px; height:20px; fill:white; }

    /* Steps */
    .step { display:none; }
    .step.active { display:block; animation:stepIn .38s ease forwards; }
    @keyframes stepIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

    /* Footer */
    .footer-section { text-align:center; margin-top:1.75rem; }
    .register-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 24px; border:1.5px solid rgba(0,224,198,.3); border-radius:50px; color:var(--teal-bright); text-decoration:none; font-size:.88rem; font-weight:600; transition:.3s; }
    .register-btn:hover { background:rgba(0,224,198,.1); transform:translateY(-2px); }
</style>
</head>
<body>

<div class="video-bg">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>
<div class="particles" id="particles"></div>

<div class="login-container">
    <div class="glass-card">
        <a href="../index.php" class="back-link">← Back to Main Site</a>

        <div class="logo-section">
            <div class="logo-icon">🧬</div>
            <h1 id="header-title">Patient Portal</h1>
            <p id="header-desc">Welcome back to LuckyGeneMDx</p>
        </div>

        <?php if ($error): ?>
        <div class="error-msg">⚠ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="auth-form">
            <input type="hidden" name="login_type" id="login_type" value="<?php echo htmlspecialchars($loginType); ?>">

            <!-- STEP 1: Identifier -->
            <div class="step <?php echo $error ? '' : 'active'; ?>" id="step-1">
                <div class="tab-nav">
                    <div class="tab-btn <?php echo $loginType !== 'order' ? 'active' : ''; ?>"
                         onclick="switchTab('email', this)">✉ Email Login</div>
                    <div class="tab-btn <?php echo $loginType === 'order' ? 'active' : ''; ?>"
                         onclick="switchTab('order', this)">🗂 Order Login</div>
                </div>

                <div id="email-group" class="form-group" <?php echo $loginType === 'order' ? 'style="display:none"' : ''; ?>>
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email-input"
                               placeholder="name@example.com" autocomplete="email"
                               value="<?php echo $postedEmail; ?>">
                    </div>
                </div>

                <div id="order-group" class="form-group" <?php echo $loginType !== 'order' ? 'style="display:none"' : ''; ?>>
                    <label>Order Number</label>
                    <div class="input-wrapper">
                        <input type="text" name="order_number" id="order-input"
                               placeholder="LGM-2024-XXXXX"
                               value="<?php echo $postedOrder; ?>">
                    </div>
                </div>

                <button type="button" class="btn-primary" onclick="goToStep2()">Continue →</button>

                <div class="divider"><span>or continue with</span></div>
                <div class="social-grid">
                    <button type="button" class="social-btn" title="Google">
                        <svg viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-1.92 5.36-7.84 5.36-5.12 0-9.28-4.24-9.28-9.48s4.16-9.48 9.28-9.48c2.92 0 4.88 1.2 6 2.28l2.56-2.48C18.92 1.92 15.96 0 12.48 0 5.52 0 0 5.52 0 12.48s5.52 12.48 12.48 12.48c7.28 0 12.12-5.12 12.12-12.32 0-.84-.08-1.48-.2-2.12h-11.92z"/></svg>
                    </button>
                    <button type="button" class="social-btn" title="Apple">
                        <svg viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05 1.78-3.14 1.72-1.09-.06-1.49-.72-2.74-.72-1.25 0-1.7.7-2.71.75-1.02.05-2.2-.95-3.18-1.89-2-1.92-3.53-5.41-3.53-8.7 0-5.32 3.44-8.13 6.71-8.13 1.73 0 3.37.59 4.41 1.25 1.04.66 1.83.66 2.87 0 1.04-.66 2.68-1.25 4.41-1.25 1.54 0 3.01.55 4.14 1.56-4.6 2.13-3.87 8.71.74 10.46-.86 2.12-1.99 4.01-2.98 4.95zM12.04 4.54c-.11-2.43 1.89-4.54 4.31-4.54.12 2.54-2.11 4.75-4.31 4.54z"/></svg>
                    </button>
                    <button type="button" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24h11.495v-8.791h-2.96v-3.429h2.96v-2.528c0-2.937 1.793-4.535 4.412-4.535 1.254 0 2.333.093 2.646.136v3.069h-1.815c-1.424 0-1.7.677-1.7 1.67v2.188h3.398l-.441 3.429h-2.957V24h6.09C23.407 24 24 23.407 24 22.676V1.325C24 .593 23.407 0 22.675 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Password -->
            <div class="step <?php echo $error ? 'active' : ''; ?>" id="step-2">
                <div class="user-preview">
                    <span id="display-identifier"><?php echo $postedEmail ?: $postedOrder; ?></span>
                    <button type="button" class="edit-btn" onclick="goToStep1()">Edit</button>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password-input"
                               placeholder="••••••••" autocomplete="current-password">
                        <span class="pw-toggle" onclick="togglePass()">👁</span>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Authorize & Sign In</button>
                <a href="password-reset.php" style="display:block;text-align:center;margin-top:1.4rem;color:var(--text-dim);text-decoration:none;font-size:.84rem;">Forgot Password?</a>
            </div>
        </form>

        <div class="footer-section">
            <p style="color:var(--text-dim);font-size:.84rem;margin-bottom:.9rem">Don't have an account?</p>
            <a href="register.php" class="register-btn">Create Secure Account →</a>
        </div>
    </div>
</div>

<script>
(function(){
    const c = document.getElementById('particles');
    for(let i = 0; i < 10; i++){
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `left:${Math.random()*100}%;width:${2+Math.random()*3}px;height:${2+Math.random()*3}px;animation-duration:${15+Math.random()*20}s;animation-delay:${-Math.random()*20}s`;
        c.appendChild(p);
    }
})();

function switchTab(type, el) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('login_type').value = type;
    document.getElementById('email-group').style.display = type === 'email' ? 'block' : 'none';
    document.getElementById('order-group').style.display = type === 'order' ? 'block' : 'none';
}

function goToStep2() {
    const type   = document.getElementById('login_type').value;
    const input  = type === 'email' ? document.getElementById('email-input') : document.getElementById('order-input');
    const val    = input.value.trim();

    if (!val) { input.classList.add('field-error'); input.focus(); return; }
    input.classList.remove('field-error');

    document.getElementById('display-identifier').textContent = val;
    document.getElementById('step-1').classList.remove('active');
    document.getElementById('step-2').classList.add('active');
    document.getElementById('header-title').textContent = 'Security Check';
    document.getElementById('header-desc').textContent  = 'Enter your password to continue';
    setTimeout(() => document.getElementById('password-input').focus(), 50);
}

function goToStep1() {
    document.getElementById('step-2').classList.remove('active');
    document.getElementById('step-1').classList.add('active');
    document.getElementById('header-title').textContent = 'Patient Portal';
    document.getElementById('header-desc').textContent  = 'Welcome back to LuckyGeneMDx';
}

function togglePass() {
    const p = document.getElementById('password-input');
    p.type  = p.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
