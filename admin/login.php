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
            
            // Check for lockout
            $stmt = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = :username AND ip_address = :ip AND success = 0 AND attempted_at > (NOW() - INTERVAL " . (int)LOCKOUT_TIME . " SECOND)");
            $stmt->execute([':username' => $username, ':ip' => $_SERVER['REMOTE_ADDR']]);
            if ($stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS) {
                $error = 'Too many failed attempts. Please try again in 15 minutes.';
            } else {
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
                    
                    // Handle Remember Me
                    if (isset($_POST['remember'])) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), session_id(), time() + (86400 * 30), $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                    }

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
                $log = "INSERT INTO login_attempts (email, ip_address, success, attempted_at) VALUES (:username, :ip, 0, NOW())";
                $stmt_log = $db->prepare($log);
                $stmt_log->execute([
                    ':username' => $username,
                    ':ip' => $_SERVER['REMOTE_ADDR']
                ]);
            }
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
    <title>Admin Login - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="auth-body">

    <div class="auth-bg-video">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="auth-card">
        <a href="../index.php" style="display:block; margin-bottom: 1rem; font-size: 0.9rem;">← Back to Main Site</a>

        <div style="text-align:center; margin-bottom: 2rem;">
            <div style="font-size: 3rem;">🧬</div>
            <h1><?php echo htmlspecialchars(SITE_NAME); ?> <span class="admin-badge">Admin</span></h1>
            <p>Secure System Access</p>
        </div>
            
        <?php if ($error): ?>
            <div class="msg msg-error" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autofocus
                    autocomplete="username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        autocomplete="current-password"
                        style="padding-right: 40px;"
                    >
                    <button type="button" onclick="togglePassword('password')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem; color: var(--text-secondary);" title="Show Password">👁️</button>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 8px;">
                <label for="remember" style="display: inline; font-weight: normal;">Remember me</label>
            </div>

            <button type="submit" class="btn btn-full" style="margin-top: 1.5rem;">
                Sign In
            </button>
            <a href="password-reset.php" style="display:block; text-align:center; margin-top:1rem; font-size:0.85rem;">Forgot Password?</a>
        </form>
        
        <div style="text-align: center; margin-top: 2rem;">
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Authorized personnel only. All activities are logged.</p>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
