<?php
define('LuckyGenesMDx', true);
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
            if ($result['success']) {
                if (isset($_POST['remember'])) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), session_id(), time() + (86400 * 30), $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
                header('Location: index.php');
                exit;
            }
            else { $error = $result['message']; }
        } else { $error = 'Please enter both order number and password.'; }
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email && $password) {
            $result = $userModel->login($email, $password);
            if ($result['success']) {
                if (isset($_POST['remember'])) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), session_id(), time() + (86400 * 30), $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                }
                header('Location: index.php');
                exit;
            }
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
<title>Patient Portal Login – LuckyGenesMDx</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/portal.css">
<link rel="stylesheet" href="../css/custom.css">
</head>
<body class="auth-body">

<div class="auth-bg-video">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>

<div class="auth-card">
    <a href="../index.php" class="back-link">← Back to Main Site</a>

    <div class="auth-header">
        <img src="../assets/images/logo_small.png" alt="Logo" class="auth-logo">
        <h1 id="header-title">Patient Portal</h1>
        <p id="header-desc">Welcome back to <?php echo htmlspecialchars(SITE_NAME); ?></p>
    </div>

    <?php if ($error): ?>
    <div class="msg msg-error">⚠ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" id="auth-form">
        <input type="hidden" name="login_type" id="login_type" value="<?php echo htmlspecialchars($loginType); ?>">

        <!-- STEP 1: Identifier -->
        <div class="step <?php echo $error ? '' : 'active'; ?>" id="step-1" style="<?php echo $error ? 'display:none' : 'display:block'; ?>">
            <div class="auth-toggle-group">
                <button type="button" class="btn btn-outline btn-full <?php echo $loginType !== 'order' ? 'active' : ''; ?>" onclick="switchTab('email', this)">Email Login</button>
                <button type="button" class="btn btn-outline btn-full <?php echo $loginType === 'order' ? 'active' : ''; ?>" onclick="switchTab('order', this)">Order Login</button>
            </div>

            <div id="email-group" class="form-group" <?php echo $loginType === 'order' ? 'style="display:none"' : ''; ?>>
                <label>Email Address</label>
                <input type="email" name="email" id="email-input" placeholder="name@example.com" autocomplete="email" value="<?php echo $postedEmail; ?>">
            </div>

            <div id="order-group" class="form-group" <?php echo $loginType !== 'order' ? 'style="display:none"' : ''; ?>>
                <label>Order Number</label>
                <input type="text" name="order_number" id="order-input" placeholder="LGM-2024-XXXXX" value="<?php echo $postedOrder; ?>">
            </div>

            <button type="button" class="btn btn-full" onclick="goToStep2()">Continue →</button>
        </div>

        <!-- STEP 2: Password -->
        <div class="step <?php echo $error ? 'active' : ''; ?>" id="step-2" style="<?php echo $error ? 'display:block' : 'display:none'; ?>">
            <div class="identifier-display">
                <span id="display-identifier" class="font-weight-600"><?php echo $postedEmail ?: $postedOrder; ?></span>
                <button type="button" onclick="goToStep1()" class="btn-link">Edit</button>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password-input" placeholder="••••••••" autocomplete="current-password" class="pr-40">
                    <button type="button" onclick="togglePassword('password-input')" class="password-toggle" title="Show Password">👁️</button>
                </div>
            </div>
            <div class="form-group mb-1">
                <input type="checkbox" name="remember" id="remember" class="form-checkbox">
                <label for="remember" class="form-label-inline">Remember me</label>
            </div>
            <button type="submit" class="btn btn-full">Sign In</button>
            <a href="password-reset.php" class="auth-link">Forgot Password?</a>
            <a href="forgot-username.php" class="auth-link-secondary">Forgot Email?</a>
        </div>
    </form>

    <div class="auth-footer">
        <p>Don't have an account?</p>
        <a href="register.php" class="btn btn-outline">Create Account</a>
    </div>
</div>

<script>
function switchTab(type, el) {
    // Simple tab switch logic
    document.getElementById('login_type').value = type;
    document.getElementById('email-group').style.display = type === 'email' ? 'block' : 'none';
    document.getElementById('order-group').style.display = type === 'order' ? 'block' : 'none';
}

function goToStep2() {
    const type   = document.getElementById('login_type').value;
    const input  = type === 'email' ? document.getElementById('email-input') : document.getElementById('order-input');
    const val    = input.value.trim();

    if (!val) { input.focus(); return; }

    document.getElementById('display-identifier').textContent = val;
    document.getElementById('step-1').style.display = 'none';
    document.getElementById('step-2').style.display = 'block';
    document.getElementById('header-title').textContent = 'Security Check';
    document.getElementById('header-desc').textContent  = 'Enter your password to continue';
    setTimeout(() => document.getElementById('password-input').focus(), 50);
}

function goToStep1() {
    document.getElementById('step-2').style.display = 'none';
    document.getElementById('step-1').style.display = 'block';
    document.getElementById('header-title').textContent = 'Patient Portal';
    document.getElementById('header-desc').textContent  = 'Welcome back to <?php echo htmlspecialchars(SITE_NAME); ?>';
}

function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
