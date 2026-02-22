<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();
$userModel = new User();
$userId = $_SESSION['user_id'];

$success = '';
$error = '';

$user_data = $userModel->getUserById($userId);

$initials  = strtoupper(substr($user_data['full_name'],0,1));
if (strpos($user_data['full_name'],' ')!==false) $initials .= strtoupper(substr(explode(' ',$user_data['full_name'])[1],0,1));

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $data = [
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone'])
    ];
    
    $result = $userModel->updateProfile($userId, $data);
    
    if ($result['success']) {
        $_SESSION['user_name'] = $data['full_name'];
        $success = 'Profile updated successfully.';
        $user_data = $userModel->getUserById($userId);
    } else {
        $error = $result['message'];
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $result = $userModel->changePassword($userId, $currentPassword, $newPassword);
        if ($result['success']) {
            $success = 'Password changed successfully.';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - LuckyGeneMDx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <nav class="navbar">
      <a href="../index.php" class="brand"><span>🧬</span> LuckyGeneMDx</a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link">My Orders</a>
        <a href="results.php" class="nav-link">Results</a>
        <a href="settings.php" class="nav-link active">Settings</a>
      </div>
      <div class="user-menu">
        <button id="theme-toggle" class="btn btn-outline btn-sm" style="border:none; font-size:1.2rem; padding:4px 8px; margin-right:5px; background:transparent;">🌙</button>
        <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
        <a href="logout.php" class="btn btn-outline btn-sm">Sign Out</a>
      </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Account Settings</h1>
            <p>Manage your personal information and security preferences.</p>
        </div>

        <div class="grid">
            
            <div class="col-span-12">
                <?php if($success): ?><div class="msg msg-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
                <?php if($error): ?><div class="msg msg-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            </div>

            <div class="col-span-8">
                    
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                            <h3>Personal Information</h3>
                            <p>Update your contact details.</p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" 
                                    value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" 
                                    value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled 
                                    style="background:#777777; color:#AAAAAA;">
                                <small style="display:block; margin-top:4px; color:#605e5c;">Contact support to change email.</small>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" 
                                    value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" required>
                            </div>
                            
                            <button type="submit" class="btn">Save Changes</button>
                        </form>
                    </div>

                    <div class="card">
                        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem;">
                            <h3>Security</h3>
                            <p>Update your password.</p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required minlength="8">
                                <small style="display:block; margin-top:4px; color:#605e5c;">Min. 8 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-outline">Update Password</button>
                        </form>
                    </div>

            </div>

            <div class="col-span-4">
                    <div class="card" style="position:sticky; top:5rem;">
                        <h4 style="margin-bottom: 1rem;">Account Summary</h4>
                        <ul style="list-style:none; padding:0; margin:0; font-size:0.9rem;">
                            <li style="display:flex; justify-content:space-between; padding:0.75rem 0; border-bottom:1px solid rgba(0,0,0,0.05);">
                                <span style="color:#909090;">Member Since</span>
                                <span style="font-weight:600;"><?php echo date('Y', strtotime($user_data['created_at'] ?? '')); ?></span>
                            </li>
                            <li style="display:flex; justify-content:space-between; padding:0.75rem 0; border-bottom:1px solid rgba(0,0,0,0.05);">
                                <span style="color:#909090;">Last Login</span>
                                <span style="font-weight:600;">
                                    <?php echo $user_data['last_login'] ? date('M j', strtotime($user_data['last_login'])) : 'N/A'; ?>
                                </span>
                            </li>
                        </ul>

                        <div style="margin-top: 2rem;">
                            <h5 style="margin-bottom: 0.5rem;">Data Privacy</h5>
                            <p style="font-size: 0.85rem; color: #909090; margin-bottom: 1rem;">
                                Your genetic data is encrypted and stored securely. We do not sell your data.
                            </p>
                            <a href="../privacy-policy.php" style="font-size: 0.85rem; text-decoration: underline;">Read Privacy Policy</a>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script>
        const toggle = document.getElementById('theme-toggle');
        const body = document.body;
        
        if (localStorage.getItem('portal_theme') === 'dark') {
            body.classList.add('dark-theme');
            toggle.textContent = '☀️';
        }

        toggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('portal_theme', isDark ? 'dark' : 'light');
            toggle.textContent = isDark ? '☀️' : '🌙';
        });
    </script>
</body>
</html>
