<?php
define('LUCKYGENEMXD', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

// Check user authentication
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

// Get user info
$user = $userModel->getUserById($userId);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $data = [
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone'])
    ];
    
    $result = $userModel->updateProfile($userId, $data);
    
    if ($result['success']) {
        $_SESSION['user_name'] = $data['full_name'];
        $success = 'Profile updated successfully!';
        $user = $userModel->getUserById($userId); // Refresh user data
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
    } elseif (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
    } else {
        $result = $userModel->changePassword($userId, $currentPassword, $newPassword);
        
        if ($result['success']) {
            $success = 'Password changed successfully!';
        } else {
            $error = $result['message'];
        }
    }
}

$userName = $user['full_name'];
$firstName = explode(' ', $userName)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - LuckyGeneMDx Patient Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .portal-wrapper { display: flex; min-height: 100vh; }
        .portal-sidebar {
            width: 260px; background: var(--color-primary-deep-blue); color: white; padding: 2rem 0;
            position: fixed; height: 100vh; overflow-y: auto;
        }
        .portal-sidebar-header { padding: 0 1.5rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .portal-sidebar-header h2 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .portal-sidebar-user { font-size: 0.85rem; opacity: 0.8; }
        .portal-nav { margin-top: 2rem; }
        .portal-nav-item {
            display: block; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8);
            transition: all var(--transition-fast); border-left: 3px solid transparent;
        }
        .portal-nav-item:hover, .portal-nav-item.active {
            background: rgba(255,255,255,0.1); color: white; border-left-color: var(--color-medical-teal);
        }
        .portal-main { flex: 1; margin-left: 260px; padding: 2rem; background: var(--color-light-gray); }
        .page-header {
            background: white; padding: 1.5rem 2rem; border-radius: var(--radius-md);
            margin-bottom: 2rem; box-shadow: var(--shadow-sm);
        }
        .content-card {
            background: white; padding: 2rem; border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm); margin-bottom: 2rem;
        }
        .alert-success {
            background: #d4edda; border: 1px solid #c3e6cb; color: #155724;
            padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;
            padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .portal-sidebar { display: none; }
            .portal-main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="portal-wrapper">
        <!-- INCLUDE RESPONSIVE SIDEBAR -->
        <?php include 'includes/portal-sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="portal-main">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
            <h1 style="color: white; margin-bottom: 0.5rem;">Account Settings</h1>
                <p style="opacity: 0.9; margin: 0">
                Manage your account information and preferences
                </p>
            </div>
            
            <?php if($success): ?>
                <div class="alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Profile Information -->
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem;">Profile Information</h3>
                <form method="POST" action="" data-validate>
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group">
                        <label for="full_name" class="form-label required">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-input" 
                            value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" class="form-input" 
                            value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <small style="color: var(--color-dark-gray);">Email cannot be changed. Contact support if you need to update it.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label required">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-input" data-validate="phone"
                            value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" class="form-input" 
                            value="<?php echo htmlspecialchars($user['dob']); ?>" disabled>
                        <small style="color: var(--color-dark-gray);">Date of birth cannot be changed for verification purposes.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            
            <!-- Change Password -->
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem;">Change Password</h3>
                <form method="POST" action="" data-validate>
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label required">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                            class="form-input" required autocomplete="current-password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="form-label required">New Password</label>
                        <input type="password" id="new_password" name="new_password" 
                            class="form-input" required minlength="8" data-validate="password"
                            autocomplete="new-password">
                        <small style="color: var(--color-dark-gray);">Minimum 8 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label required">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                            class="form-input" required data-validate="confirm-password"
                            autocomplete="new-password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
            
            <!-- Account Information -->
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem;">Account Information</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.25rem;">Account Created</div>
                        <div style="font-weight: 500;"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.25rem;">Last Login</div>
                        <div style="font-weight: 500;">
                            <?php echo $user['last_login'] ? date('F j, Y \a\t g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Privacy & Support -->
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem;">Privacy & Support</h3>
                <p style="margin-bottom: 1.5rem;">
                    Your genetic information is private and secure. We never share your data without your explicit consent.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="../privacy-policy.php" class="btn btn-outline" target="_blank">
                        Privacy Policy
                    </a>
                    <a href="mailto:support@luckygenemxd.com" class="btn btn-outline">
                        Contact Support
                    </a>
                    <a href="../terms-of-service.php" class="btn btn-outline" target="_blank">
                        Terms of Service
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../js/main.js"></script>
</body>
</html>
