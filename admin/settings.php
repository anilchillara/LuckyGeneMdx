<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_general':
                $stmt = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = ?");
                foreach ($_POST['settings'] as $key => $value) {
                    $stmt->execute([$value, $key]);
                }
                $success = "General settings updated successfully!";
                break;
                
            case 'update_email':
                $stmt = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = ?");
                foreach ($_POST['email_settings'] as $key => $value) {
                    $stmt->execute([$value, $key]);
                }
                $success = "Email settings updated successfully!";
                break;
                
            case 'change_password':
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$hashed, $_SESSION['admin_id']]);
                    $success = "Password changed successfully!";
                } else {
                    $error = "Passwords do not match!";
                }
                break;
                
            case 'update_maintenance':
                $mode = isset($_POST['maintenance_mode']) ? 1 : 0;
                $stmt = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = 'maintenance_mode'");
                $stmt->execute([$mode]);
                $success = "Maintenance mode " . ($mode ? "enabled" : "disabled") . "!";
                break;
        }
    }
}

// Get current settings
$settings = [];
$result = $db->query("SELECT setting_key, value FROM site_settings");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['value'];
}

// Get system info
$system_info = [
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'database' => $db->getAttribute(PDO::ATTR_SERVER_VERSION),
    'upload_max' => ini_get('upload_max_filesize'),
    'post_max' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
];

$adminName = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - LuckyGeneMDx Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        /* Shared layout from orders.php */
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px;
            background: var(--color-primary-deep-blue);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar-header h2 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .admin-sidebar-user { font-size: 0.85rem; opacity: 0.8; }
        .admin-nav { margin-top: 2rem; }
        .admin-nav-item {
            display: block;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
            text-decoration: none;
        }
        .admin-nav-item:hover, .admin-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--color-medical-teal);
        }
        .admin-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            background: var(--color-light-gray);
        }
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        
        /* Settings Specific Card Styling */
        .settings-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .settings-card h2 { 
            font-size: 1.25rem; 
            color: var(--color-primary-deep-blue); 
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--color-medium-gray);
        }

        /* Tabs styling from settings.php integrated with orders.php colors */
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--color-medium-gray);
        }
        .tab-btn {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-dark-gray);
            border-bottom: 3px solid transparent;
            transition: all var(--transition-fast);
        }
        .tab-btn:hover { color: var(--color-medical-teal); }
        .tab-btn.active {
            color: var(--color-medical-teal);
            border-bottom-color: var(--color-medical-teal);
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Table styling for System Info */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 1rem; border-bottom: 1px solid var(--color-medium-gray); }
        .info-table tr:last-child td { border-bottom: none; }
        .info-table strong { color: var(--color-primary-deep-blue); }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-sm);
            margin-bottom: 2rem;
        }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include 'sidenav.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <h1 style="margin-bottom: 0.25rem;">System Settings</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">Configure site behavior and security</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-btn active" onclick="openTab(event, 'general')">General</button>
                <button class="tab-btn" onclick="openTab(event, 'email')">Email</button>
                <button class="tab-btn" onclick="openTab(event, 'security')">Security</button>
                <button class="tab-btn" onclick="openTab(event, 'system')">System Info</button>
            </div>

            <div id="general" class="tab-content active">
                <div class="settings-card">
                    <h2>General Configuration</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_general">
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="settings[site_name]" class="form-input" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'LuckyGeneMDx'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site URL</label>
                            <input type="url" name="settings[site_url]" class="form-input" value="<?php echo htmlspecialchars($settings['site_url'] ?? SITE_URL); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Support Email</label>
                            <input type="email" name="settings[support_email]" class="form-input" value="<?php echo htmlspecialchars($settings['support_email'] ?? 'support@luckygenemdx.com'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kit Price (USD)</label>
                            <input type="number" name="settings[kit_price]" step="0.01" class="form-input" value="<?php echo htmlspecialchars($settings['kit_price'] ?? '99.00'); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <div class="settings-card">
                    <h2>Maintenance Mode</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_maintenance">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="maintenance_mode" <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Enable Maintenance Mode (Admin only access)</span>
                        </label>
                        <button type="submit" class="btn btn-outline" style="margin-top: 1.5rem;">Update Status</button>
                    </form>
                </div>
            </div>

            <div id="system" class="tab-content">
                <div class="settings-card">
                    <h2>Server Information</h2>
                    <table class="info-table">
                        <?php foreach ($system_info as $key => $val): ?>
                            <tr>
                                <td><strong><?php echo ucwords(str_replace('_', ' ', $key)); ?></strong></td>
                                <td><?php echo htmlspecialchars($val); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            </main>
    </div>

    <script>
        function openTab(evt, tabName) {
            const contents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < contents.length; i++) contents[i].classList.remove('active');
            
            const btns = document.getElementsByClassName('tab-btn');
            for (let i = 0; i < btns.length; i++) btns[i].classList.remove('active');
            
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>