<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$loginType = 'email'; // 'email' or 'order'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginType = $_POST['login_type'] ?? 'email';
    $userModel = new User();
    
    if ($loginType === 'order') {
        // Login with order ID
        $orderNumber = trim($_POST['order_number'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($orderNumber && $password) {
            $result = $userModel->loginWithOrderId($orderNumber, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
            }
        } else {
            $error = 'Please enter both order number and password.';
        }
    } else {
        // Login with email
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($email && $password) {
            $result = $userModel->login($email, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
            }
        } else {
            $error = 'Please enter both email and password.';
        }
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
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-hero);
            padding: 2rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            max-width: 500px;
            width: 100%;
        }
        .login-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--color-medium-gray);
        }
        .login-tab {
            flex: 1;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            font-weight: 500;
            color: var(--color-dark-gray);
            transition: all var(--transition-fast);
        }
        .login-tab.active {
            color: var(--color-medical-teal);
            border-bottom-color: var(--color-medical-teal);
        }
        .login-tab:hover {
            color: var(--color-medical-teal);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="color: var(--color-primary-deep-blue); margin-bottom: 0.5rem;">🧬 Patient Portal</h1>
                <p style="color: var(--color-dark-gray);">Access your results and manage your account</p>
            </div>
            
            <div class="login-tabs">
                <div class="login-tab <?php echo $loginType === 'email' ? 'active' : ''; ?>" onclick="switchTab('email')">
                    Login with Email
                </div>
                <div class="login-tab <?php echo $loginType === 'order' ? 'active' : ''; ?>" onclick="switchTab('order')">
                    Login with Order
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Email Login Tab -->
            <div id="email-tab" class="tab-content <?php echo $loginType === 'email' ? 'active' : ''; ?>">
                <form method="POST" action="">
                    <input type="hidden" name="login_type" value="email">
                    
                    <div class="form-group">
                        <label for="email" class="form-label required">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required
                            autofocus
                            value="<?php echo $loginType === 'email' ? htmlspecialchars($_POST['email'] ?? '') : ''; ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password_email" class="form-label required">Password</label>
                        <input 
                            type="password" 
                            id="password_email" 
                            name="password" 
                            class="form-input" 
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                        Sign In
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="password-reset.php" style="color: var(--color-dark-gray); font-size: 0.9rem;">
                        Forgot password?
                    </a>
                </div>
            </div>
            
            <!-- Order Login Tab -->
            <div id="order-tab" class="tab-content <?php echo $loginType === 'order' ? 'active' : ''; ?>">
                <form method="POST" action="">
                    <input type="hidden" name="login_type" value="order">
                    
                    <div class="form-group">
                        <label for="order_number" class="form-label required">Order Number</label>
                        <input 
                            type="text" 
                            id="order_number" 
                            name="order_number" 
                            class="form-input" 
                            placeholder="LGM240214ABC123"
                            required
                            value="<?php echo $loginType === 'order' ? htmlspecialchars($_POST['order_number'] ?? '') : ''; ?>"
                        >
                        <small style="color: var(--color-dark-gray);">Found in your order confirmation email</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_order" class="form-label required">Password</label>
                        <input 
                            type="password" 
                            id="password_order" 
                            name="password" 
                            class="form-input" 
                            required
                        >
                        <small style="color: var(--color-dark-gray);">The password you created during checkout</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                        Sign In
                    </button>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-medium-gray);">
                <p style="color: var(--color-dark-gray); font-size: 0.9rem; margin-bottom: 1rem;">
                    Don't have an account yet?
                </p>
                <a href="register.php" class="btn btn-outline">
                    Register Now
                </a>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="../index.php" style="color: var(--color-dark-gray); font-size: 0.9rem;">
                    ← Back to Website
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tabName) {
            // Update tabs
            document.querySelectorAll('.login-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            event.target.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        }
    </script>
</body>
</html>
