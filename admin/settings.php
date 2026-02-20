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
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - LuckyGeneMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Page specific styles */
        .tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--glass-border);
        }
        .tab-btn {
            padding: 1rem 2rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        .tab-btn:hover { color: var(--ms-blue); }
        .tab-btn.active {
            color: var(--ms-blue);
            border-bottom-color: var(--ms-blue);
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Table styling for System Info */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 1rem; border-bottom: 1px solid var(--glass-border); }
        .info-table tr:last-child td { border-bottom: none; }
        .info-table strong { color: var(--text-primary); }
    </style>
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="brand">
        <span>🧬</span> LuckyGeneMDx <span class="admin-badge">Admin</span>
      </a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link">Orders</a>
        <a href="users.php" class="nav-link">Users</a>
        <a href="upload-results.php" class="nav-link">Upload Results</a>
        <a href="activity-log.php" class="nav-link">Activity Log</a>
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
            <div>
                <h1>System Settings</h1>
                <p>Configure site behavior and security</p>
            </div>
        </div>

            <?php if (isset($success)): ?>
                <div class="msg msg-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="msg msg-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-btn active" onclick="openTab(event, 'general')">General</button>
                <button class="tab-btn" onclick="openTab(event, 'email')">Email</button>
                <button class="tab-btn" onclick="openTab(event, 'security')">Security</button>
                <button class="tab-btn" onclick="openTab(event, 'system')">System Info</button>
            </div>

            <div id="general" class="tab-content active">
                <div class="card" style="margin-bottom: 2rem;">
                    <h2>General Configuration</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_general">
                        <div class="form-group">
                            <label class="form-label">Site Name</label>
                            <input type="text" name="settings[site_name]" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'LuckyGeneMDx'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site URL</label>
                            <input type="url" name="settings[site_url]" value="<?php echo htmlspecialchars($settings['site_url'] ?? SITE_URL); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Support Email</label>
                            <input type="email" name="settings[support_email]" value="<?php echo htmlspecialchars($settings['support_email'] ?? 'support@luckygenemdx.com'); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kit Price (USD)</label>
                            <input type="number" name="settings[kit_price]" step="0.01" value="<?php echo htmlspecialchars($settings['kit_price'] ?? '99.00'); ?>">
                        </div>
                        <button type="submit" class="btn">Save Changes</button>
                    </form>
                </div>

                <div class="card">
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
                <div class="card">
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