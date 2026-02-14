<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
session_start();
setSecurityHeaders();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/Database.php';
    
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        try {
            $db = Database::getInstance()->getConnection();
            $sql = "SELECT admin_id, username, password_hash, role, is_active FROM admins WHERE username = :username";
            $stmt = $db->prepare($sql);
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                if (!$admin['is_active']) {
                    $error = 'Account is disabled. Contact system administrator.';
                } else {
                    // Update last login
                    $update = "UPDATE admins SET last_login = NOW() WHERE admin_id = :admin_id";
                    $stmt_update = $db->prepare($update);
                    $stmt_update->execute([':admin_id' => $admin['admin_id']]);
                    
                    // Set session
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_role'] = $admin['role'];
                    $_SESSION['last_activity'] = time();
                    
                    session_regenerate_id(true);
                    
                    // Log activity
                    $log = "INSERT INTO activity_log (admin_id, action, ip_address) VALUES (:admin_id, 'login', :ip)";
                    $stmt_log = $db->prepare($log);
                    $stmt_log->execute([
                        ':admin_id' => $admin['admin_id'],
                        ':ip' => $_SERVER['REMOTE_ADDR']
                    ]);
                    
                    header('Location: index.php');
                    exit;
                }
            } else {
                $error = 'Invalid username or password.';
                
                // Log failed attempt
                $log = "INSERT INTO login_attempts (email, ip_address, success) VALUES (:username, :ip, 0)";
                $stmt_log = $db->prepare($log);
                $stmt_log->execute([
                    ':username' => $username,
                    ':ip' => $_SERVER['REMOTE_ADDR']
                ]);
            }
        } catch(PDOException $e) {
            error_log("Admin Login Error: " . $e->getMessage());
            $error = 'Login system error. Please try again.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LuckyGeneMdx</title>
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
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: var(--color-primary-deep-blue);
            margin-bottom: 0.5rem;
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
            <div class="login-header">
                <h1>🧬 Admin Login</h1>
                <p style="color: var(--color-dark-gray);">LuckyGeneMdx Administration</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert-error" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" data-validate>
                <div class="form-group">
                    <label for="username" class="form-label required">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        required 
                        autofocus
                        autocomplete="username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-full" style="margin-top: 1.5rem;">
                    Sign In
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-medium-gray);">
                <a href="../index.php" style="color: var(--color-dark-gray); font-size: 0.9rem;">
                    ← Back to Website
                </a>
            </div>
        </div>
    </div>
    
    <script src="../js/main.js"></script>
</body>
</html>
