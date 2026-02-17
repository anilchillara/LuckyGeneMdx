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
$success = '';
$registrationType = $_GET['type'] ?? 'new'; // 'new' or 'with_order'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registrationType = $_POST['registration_type'] ?? 'new';
    $userModel = new User();
    $db = Database::getInstance()->getConnection();
    
    if ($registrationType === 'with_order') {
        // Register with existing order
        $orderNumber = trim($_POST['order_number'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dob = trim($_POST['dob'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate password match
        if ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        } else {
            try {
                // Check if order exists
                $stmt = $db->prepare("
                    SELECT order_id, user_id, guest_email, guest_name 
                    FROM orders 
                    WHERE order_number = :order_number
                ");
                $stmt->execute([':order_number' => $orderNumber]);
                $order = $stmt->fetch();
                
                if (!$order) {
                    $error = 'Order number not found. Please check and try again.';
                } elseif ($order['user_id']) {
                    $error = 'This order is already linked to an account. Please <a href="login.php">login</a> instead.';
                } else {
                    // Verify email matches (if guest email exists)
                    if ($order['guest_email'] && strtolower($order['guest_email']) !== strtolower($email)) {
                        $error = 'Email does not match the order. Please use the email from your order confirmation.';
                    } else {
                        // Create user account
                        $userData = [
                            'email' => $email,
                            'password' => $password,
                            'full_name' => $fullName,
                            'phone' => $phone,
                            'dob' => $dob
                        ];
                        
                        $result = $userModel->register($userData);
                        
                        if ($result['success']) {
                            $userId = $result['user_id'];
                            
                            // Link order to user
                            $stmt = $db->prepare("
                                UPDATE orders 
                                SET user_id = :user_id,
                                    guest_email = NULL,
                                    guest_name = NULL
                                WHERE order_id = :order_id
                            ");
                            $stmt->execute([
                                ':user_id' => $userId,
                                ':order_id' => $order['order_id']
                            ]);
                            
                            // Log the user in
                            $_SESSION['user_id'] = $userId;
                            $_SESSION['user_name'] = $fullName;
                            $_SESSION['last_activity'] = time();
                            
                            header('Location: index.php?registered=1&linked=1');
                            exit;
                        } else {
                            $error = $result['message'];
                        }
                    }
                }
            } catch(PDOException $e) {
                error_log("Registration with order error: " . $e->getMessage());
                $error = 'Registration failed. Please try again.';
            }
        }
    } else {
        // New registration without order
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dob = trim($_POST['dob'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate password match
        if ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        } else {
            $userData = [
                'email' => $email,
                'password' => $password,
                'full_name' => $fullName,
                'phone' => $phone,
                'dob' => $dob
            ];
            
            $result = $userModel->register($userData);
            
            if ($result['success']) {
                // Log the user in
                $_SESSION['user_id'] = $result['user_id'];
                $_SESSION['user_name'] = $fullName;
                $_SESSION['last_activity'] = time();
                
                // Redirect to order page or dashboard
                $redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header("Location: {$redirectTo}?registered=1");
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - LuckyGeneMDx Patient Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        /* PATIENET PORTAL SPECIFIC STYLES  */
        .welcome-banner {
            background: var(--gradient-hero);
            color: white;
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
        }
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
            padding: 2rem 1rem;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 550px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-logo {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .register-logo .logo-text {
            background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .register-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .register-tab {
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
            background: white;
        }
        .register-tab.active {
            color: white;
            background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
            border-color: #00B3A4;
            transform: scale(1.02);
        }
        .register-tab:hover:not(.active) {
            border-color: #00B3A4;
            color: #00B3A4;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-error a {
            color: #c33;
            text-decoration: underline;
            font-weight: 600;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .info-box {
            background: rgba(0, 179, 164, 0.1);
            border-left: 3px solid #00B3A4;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .register-card {
                padding: 2rem 1.5rem;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .register-tabs {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    🧬 <span class="logo-text">LuckyGeneMDx</span>
                </div>
                <h1 style="color: #0A1F44; margin-bottom: 0.5rem; font-size: 2rem;">
                    Create Your Account
                </h1>
                <p style="color: #666;">Join the LuckyGeneMDx Patient Portal</p>
            </div>
            
            <div class="register-tabs">
                <div class="register-tab <?php echo $registrationType === 'new' ? 'active' : ''; ?>" 
                     onclick="switchTab('new')">
                    <strong>✨ New Account</strong><br>
                    <small style="font-size: 0.85rem;">Register to order kit</small>
                </div>
                <div class="register-tab <?php echo $registrationType === 'with_order' ? 'active' : ''; ?>" 
                     onclick="switchTab('with_order')">
                    <strong>📦 Have Order?</strong><br>
                    <small style="font-size: 0.85rem;">Link existing order</small>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert-error">
                    ⚠️ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- New Registration Tab -->
            <div id="new-tab" class="tab-content <?php echo $registrationType === 'new' ? 'active' : ''; ?>">
                <div class="info-box">
                    ✓ Create an account to order your screening kit<br>
                    ✓ Access results and manage orders online<br>
                    ✓ Secure portal for your genetic information
                </div>
                
                <form method="POST" action="" id="newRegisterForm">
                    <input type="hidden" name="registration_type" value="new">
                    
                    <div class="form-group">
                        <label for="full_name_new" class="form-label required">Full Name</label>
                        <input type="text" id="full_name_new" name="full_name" class="form-input" 
                            value="<?php echo $registrationType === 'new' ? htmlspecialchars($_POST['full_name'] ?? '') : ''; ?>" 
                            required placeholder="John Doe">
                    </div>
                    
                    <div class="form-group">
                        <label for="email_new" class="form-label required">Email Address</label>
                        <input type="email" id="email_new" name="email" class="form-input" 
                            value="<?php echo $registrationType === 'new' ? htmlspecialchars($_POST['email'] ?? '') : ''; ?>" 
                            required placeholder="john@example.com">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone_new" class="form-label required">Phone Number</label>
                            <input type="tel" id="phone_new" name="phone" class="form-input" 
                                value="<?php echo $registrationType === 'new' ? htmlspecialchars($_POST['phone'] ?? '') : ''; ?>" 
                                required placeholder="(555) 123-4567">
                        </div>
                        
                        <div class="form-group">
                            <label for="dob_new" class="form-label required">Date of Birth</label>
                            <input type="date" id="dob_new" name="dob" class="form-input" 
                                value="<?php echo $registrationType === 'new' ? htmlspecialchars($_POST['dob'] ?? '') : ''; ?>" 
                                required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password_new" class="form-label required">Password</label>
                            <input type="password" id="password_new" name="password" class="form-input" 
                                required minlength="8" autocomplete="new-password">
                            <small style="color: #666;">Min. 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password_new" class="form-label required">Confirm Password</label>
                            <input type="password" id="confirm_password_new" name="confirm_password" 
                                class="form-input" required autocomplete="new-password">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: start; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" required style="margin-top: 0.25rem;">
                            <span style="font-size: 0.9rem;">
                                I agree to the <a href="../terms-of-service.php" target="_blank" style="color: #00B3A4;">Terms of Service</a> 
                                and <a href="../privacy-policy.php" target="_blank" style="color: #00B3A4;">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1rem; background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);">
                        Create Account →
                    </button>
                </form>
            </div>
            
            <!-- Register with Order Tab -->
            <div id="with_order-tab" class="tab-content <?php echo $registrationType === 'with_order' ? 'active' : ''; ?>">
                <div class="info-box">
                    ✓ Already ordered a kit? Create your account here<br>
                    ✓ Your order number is in your confirmation email<br>
                    ✓ Link your order and access your results
                </div>
                
                <form method="POST" action="" id="orderRegisterForm">
                    <input type="hidden" name="registration_type" value="with_order">
                    
                    <div class="form-group">
                        <label for="order_number" class="form-label required">Order Number</label>
                        <input type="text" id="order_number" name="order_number" class="form-input" 
                            value="<?php echo $registrationType === 'with_order' ? htmlspecialchars($_POST['order_number'] ?? '') : ''; ?>" 
                            required placeholder="LGM-2024-00001" style="font-family: monospace; font-size: 1.05rem;">
                        <small style="color: #666;">Found in your order confirmation email</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_order" class="form-label required">Email Address</label>
                        <input type="email" id="email_order" name="email" class="form-input" 
                            value="<?php echo $registrationType === 'with_order' ? htmlspecialchars($_POST['email'] ?? '') : ''; ?>" 
                            required placeholder="john@example.com">
                        <small style="color: #666;">Use the email from your order</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name_order" class="form-label required">Full Name</label>
                        <input type="text" id="full_name_order" name="full_name" class="form-input" 
                            value="<?php echo $registrationType === 'with_order' ? htmlspecialchars($_POST['full_name'] ?? '') : ''; ?>" 
                            required placeholder="John Doe">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone_order" class="form-label required">Phone Number</label>
                            <input type="tel" id="phone_order" name="phone" class="form-input" 
                                value="<?php echo $registrationType === 'with_order' ? htmlspecialchars($_POST['phone'] ?? '') : ''; ?>" 
                                required placeholder="(555) 123-4567">
                        </div>
                        
                        <div class="form-group">
                            <label for="dob_order" class="form-label required">Date of Birth</label>
                            <input type="date" id="dob_order" name="dob" class="form-input" 
                                value="<?php echo $registrationType === 'with_order' ? htmlspecialchars($_POST['dob'] ?? '') : ''; ?>" 
                                required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password_order" class="form-label required">Create Password</label>
                            <input type="password" id="password_order" name="password" class="form-input" 
                                required minlength="8" autocomplete="new-password">
                            <small style="color: #666;">Min. 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password_order" class="form-label required">Confirm Password</label>
                            <input type="password" id="confirm_password_order" name="confirm_password" 
                                class="form-input" required autocomplete="new-password">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: start; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" required style="margin-top: 0.25rem;">
                            <span style="font-size: 0.9rem;">
                                I agree to the <a href="../terms-of-service.php" target="_blank" style="color: #00B3A4;">Terms of Service</a> 
                                and <a href="../privacy-policy.php" target="_blank" style="color: #00B3A4;">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1rem; background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);">
                        Create Account & Link Order →
                    </button>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e0e0e0;">
                <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                    Already have an account?
                </p>
                <a href="login.php" class="btn btn-outline" style="display: inline-block; padding: 0.75rem 2rem;">
                    Sign In →
                </a>
            </div>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="../index.php" style="color: #666; font-size: 0.9rem; text-decoration: none;">
                    ← Back to Website
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tabName) {
            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('type', tabName);
            window.history.pushState({}, '', url);
            
            // Update tabs
            document.querySelectorAll('.register-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Activate selected tab
            event.target.closest('.register-tab').classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        }
        
        // Password validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const password = this.querySelector('[name="password"]').value;
                const confirm = this.querySelector('[name="confirm_password"]').value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password must be at least 8 characters!');
                    return false;
                }
            });
        });
        
        // Phone number formatting
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);
                
                if (value.length >= 6) {
                    value = `(${value.slice(0,3)}) ${value.slice(3,6)}-${value.slice(6)}`;
                } else if (value.length >= 3) {
                    value = `(${value.slice(0,3)}) ${value.slice(3)}`;
                }
                
                e.target.value = value;
            });
        });
    </script>
</body>
</html>
