<?php
define('LuckyGenesMDx', true);
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

$userModel = new User();
$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefs = [
        'notify_email_orders' => isset($_POST['notify_email_orders']) ? 1 : 0,
        'notify_email_results' => isset($_POST['notify_email_results']) ? 1 : 0,
        'notify_email_marketing' => isset($_POST['notify_email_marketing']) ? 1 : 0
    ];
    
    if ($userModel->updateNotificationPreferences($userId, $prefs)) {
        $success = 'Preferences updated successfully.';
    } else {
        $error = 'Failed to update preferences. Please try again.';
    }
}

$prefs = $userModel->getNotificationPreferences($userId);
$user = $userModel->getUserById($userId);
$initials = strtoupper(substr($user['full_name'], 0, 1));
if (strpos($user['full_name'], ' ') !== false) {
    $initials .= strtoupper(substr(explode(' ', $user['full_name'])[1], 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Preferences - LuckyGenesMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="header-section">
            <h1>Notifications</h1>
            <p>Manage how we communicate with you.</p>
        </div>

        <div class="grid">
            <div class="col-span-8">
                <?php if ($success): ?>
                    <div class="glass-card-teal-left p-3 mb-3 text-teal"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="glass-card-error p-3 mb-3 text-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="card">
                    <form method="POST" action="">
                        <h3 class="mb-3">Email Notifications</h3>
                        
                        <div class="form-group mb-4">
                            <div class="form-checkbox">
                                <input type="checkbox" id="notify_email_orders" name="notify_email_orders" value="1" <?php echo $prefs['notify_email_orders'] ? 'checked' : ''; ?>>
                                <div>
                                    <label for="notify_email_orders" class="mb-0 font-semibold">Order Updates</label>
                                    <p class="font-sm text-dark-gray mb-0">Receive emails about your order status, shipping, and delivery.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-checkbox">
                                <input type="checkbox" id="notify_email_results" name="notify_email_results" value="1" <?php echo $prefs['notify_email_results'] ? 'checked' : ''; ?>>
                                <div>
                                    <label for="notify_email_results" class="mb-0 font-semibold">Test Results</label>
                                    <p class="font-sm text-dark-gray mb-0">Get notified immediately when your results are ready to view.</p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-checkbox">
                                <input type="checkbox" id="notify_email_marketing" name="notify_email_marketing" value="1" <?php echo $prefs['notify_email_marketing'] ? 'checked' : ''; ?>>
                                <div>
                                    <label for="notify_email_marketing" class="mb-0 font-semibold">News & Updates</label>
                                    <p class="font-sm text-dark-gray mb-0">Stay informed about new tests, health tips, and company news.</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Preferences</button>
                    </form>
                </div>
            </div>

            <div class="col-span-4">
                <div class="card">
                    <h3 class="mb-2">Contact Info</h3>
                    <p class="font-sm text-dark-gray mb-3">Notifications will be sent to:</p>
                    
                    <div class="mb-3">
                        <div class="stat-lbl">Email</div>
                        <div class="font-semibold"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    
                    <a href="profile.php" class="btn btn-outline w-100">Update Contact Info</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../js/main.js"></script>
</body>
</html>