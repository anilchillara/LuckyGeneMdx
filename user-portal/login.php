<?php
define('LuckyGenes', true);
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
<title>Patient Portal Login – LuckyGenes</title>
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
        <h1 class="font-xl mb-2" id="header-title">Patient Portal</h1>
        <p class="auth-title" id="header-desc">Welcome back to <?php echo htmlspecialchars(SITE_NAME); ?></p>
    </div>

    <?php if ($error): ?>
    <div class="glass-card-error p-3 mb-3 text-error">⚠ <?php echo htmlspecialchars($error); ?></div>
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
                <input type="email" name="email" id="email-input" placeholder="name@example.com" autocomplete="email" value="<?php echo $postedEmail; ?>" class="form-control">
            </div>

            <div id="order-group" class="form-group" <?php echo $loginType !== 'order' ? 'style="display:none"' : ''; ?>>
                <label>Order Number</label>
                <input type="text" name="order_number" id="order-input" placeholder="LGM-2024-XXXXX" value="<?php echo $postedOrder; ?>" class="form-control">
            </div>

            <button type="button" class="btn btn-primary btn-full" onclick="goToStep2()">Continue →</button>
        </div>

        <!-- STEP 2: Password -->
        <div class="step <?php echo $error ? 'active' : ''; ?>" id="step-2" style="<?php echo $error ? 'display:block' : 'display:none'; ?>">
            <div class="identifier-display">
                <span id="display-identifier" class="font-semibold"><?php echo $postedEmail ?: $postedOrder; ?></span>
                <button type="button" onclick="goToStep1()" class="text-teal" style="background:none; border:none; cursor:pointer;">Edit</button>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password-input" placeholder="••••••••" autocomplete="current-password" class="form-control">
            </div>
            <div class="form-group mb-1">
                <div class="form-checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
            <div class="mt-3 text-center">
                <a href="password-reset.php" class="text-teal font-sm">Forgot Password?</a>
                <span class="mx-2 text-dark-gray">|</span>
                <a href="forgot-username.php" class="text-teal font-sm">Forgot Email?</a>
            </div>
        </div>
    </form>

    <div class="mt-4 text-center">
        <p class="text-dark-gray mb-2">Don't have an account?</p>
        <a href="register.php" class="btn btn-outline btn-full">Create Account</a>
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
</script>
</body>
</html>
