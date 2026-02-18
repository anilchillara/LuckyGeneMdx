<?php
// define('luckygenemdx', true);
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
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .msg-box { padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 2rem; }
        .msg-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .msg-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .section-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-medium-gray); }
        .section-header h3 { margin-bottom: 0.25rem; }
        .section-header p { color: var(--color-dark-gray); font-size: 0.9rem; margin: 0; }

        .helper-text { font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.4rem; display: block; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="portal-page">
        <div class="portal-hero">
            <div class="container">
                <h1>Account Settings</h1>
                <p>Manage your personal information and security preferences.</p>
            </div>
        </div>

        <div class="container" style="max-width: 900px;">
            
            <?php if($success): ?><div class="msg-box msg-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
            <?php if($error): ?><div class="msg-box msg-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <div class="row">
                <div class="col col-2">
                    
                    <div class="content-card">
                        <div class="section-header">
                            <h3>Personal Information</h3>
                            <p>Update your contact details.</p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="form-group">
                                <label for="full_name" class="form-label required">Full Name</label>
                                <input type="text" id="full_name" name="full_name" class="form-input" 
                                    value="<?php echo htmlspecialchars($user_data['full_name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" class="form-input" 
                                    value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled 
                                    style="background:var(--color-light-gray); color:var(--color-dark-gray);">
                                <small class="helper-text">Contact support to change email.</small>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label required">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input" 
                                    value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>

                    <div class="content-card">
                        <div class="section-header">
                            <h3>Security</h3>
                            <p>Update your password.</p>
                        </div>
                        <form method="POST" action="">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="form-group">
                                <label for="current_password" class="form-label required">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password" class="form-label required">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-input" required minlength="8">
                                <small class="helper-text">Min. 8 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password" class="form-label required">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                            </div>
                            
                            <button type="submit" class="btn btn-outline">Update Password</button>
                        </form>
                    </div>

                </div>

                <div class="col col-2">
                    <div class="glass-card" style="position:sticky; top:2rem;">
                        <h4 style="margin-bottom: 1rem;">Account Summary</h4>
                        <ul style="list-style:none; padding:0; margin:0; font-size:0.9rem;">
                            <li style="display:flex; justify-content:space-between; padding:0.75rem 0; border-bottom:1px solid rgba(0,0,0,0.05);">
                                <span style="color:var(--color-dark-gray);">Member Since</span>
                                <span style="font-weight:600;"><?php echo date('Y', strtotime($user_data['created_at'] ?? '')); ?></span>
                            </li>
                            <li style="display:flex; justify-content:space-between; padding:0.75rem 0; border-bottom:1px solid rgba(0,0,0,0.05);">
                                <span style="color:var(--color-dark-gray);">Last Login</span>
                                <span style="font-weight:600;">
                                    <?php echo $user_data['last_login'] ? date('M j', strtotime($user_data['last_login'])) : 'N/A'; ?>
                                </span>
                            </li>
                        </ul>

                        <div style="margin-top: 2rem;">
                            <h5 style="margin-bottom: 0.5rem;">Data Privacy</h5>
                            <p style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 1rem;">
                                Your genetic data is encrypted and stored securely. We do not sell your data.
                            </p>
                            <a href="../privacy-policy.php" style="font-size: 0.85rem; text-decoration: underline;">Read Privacy Policy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
