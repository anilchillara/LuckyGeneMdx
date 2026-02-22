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
                $check = $db->prepare("SELECT 1 FROM site_settings WHERE setting_key = ?");
                $update = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = ?");
                $insert = $db->prepare("INSERT INTO site_settings (setting_key, value) VALUES (?, ?)");

                foreach ($_POST['settings'] as $key => $value) {
                    $check->execute([$key]);
                    if ($check->fetch()) {
                        $update->execute([$value, $key]);
                    } else {
                        $insert->execute([$key, $value]);
                    }
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
    } elseif (isset($_POST['save_navbar'])) {
        try {
            $db->beginTransaction();
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $id => $item) {
                    $isActive = isset($item['is_active']) ? 1 : 0;
                    $stmt = $db->prepare("UPDATE navbar_items SET label = ?, url = ?, display_order = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([trim($item['label']), trim($item['url']), (int)$item['display_order'], $isActive, $id]);
                }
            }
            $db->commit();
            $success = "Navbar settings updated successfully.";
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Error updating settings: " . $e->getMessage();
        }
    } elseif (isset($_POST['add_nav_item'])) {
        try {
            $stmt = $db->prepare("INSERT INTO navbar_items (label, url, display_order, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute([trim($_POST['new_label']), trim($_POST['new_url']), (int)$_POST['new_order']]);
            $success = "New item added successfully.";
        } catch (Exception $e) {
            $error = "Error adding item: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_nav_item'])) {
        try {
            $stmt = $db->prepare("DELETE FROM navbar_items WHERE id = ?");
            $stmt->execute([(int)$_POST['delete_id']]);
            $success = "Item deleted successfully.";
        } catch (Exception $e) {
            $error = "Error deleting item: " . $e->getMessage();
        }
    } elseif (isset($_POST['move_nav_item'])) {
        try {
            $id = (int)$_POST['move_id'];
            $dir = $_POST['move_dir'];
            $stmt = $db->prepare("SELECT id, display_order FROM navbar_items WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($current) {
                $operator = ($dir === 'up') ? '<' : '>';
                $order = ($dir === 'up') ? 'DESC' : 'ASC';
                $stmt = $db->prepare("SELECT id, display_order FROM navbar_items WHERE display_order $operator ? ORDER BY display_order $order LIMIT 1");
                $stmt->execute([$current['display_order']]);
                $swap = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($swap) {
                    $db->beginTransaction();
                    $db->prepare("UPDATE navbar_items SET display_order = ? WHERE id = ?")->execute([$swap['display_order'], $current['id']]);
                    $db->prepare("UPDATE navbar_items SET display_order = ? WHERE id = ?")->execute([$current['display_order'], $swap['id']]);
                    $db->commit();
                    $success = "Item reordered successfully.";
                }
            }
        } catch (Exception $e) {
            $error = "Error moving item: " . $e->getMessage();
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

// Fetch Navbar Items
$navItems = [];
try {
    $stmt = $db->query("SELECT * FROM navbar_items ORDER BY display_order ASC");
    $navItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { /* Ignore table missing error */ }

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

        /* Navbar Settings Styles */
        .btn-xs { padding: 0 5px; font-size: 0.7rem; line-height: 1.2; border: 1px solid #ddd; background: #fff; cursor: pointer; }
        .btn-xs:hover { background: #f0f0f0; }
        .form-control-sm { padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
    </style>
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="brand">
        <span>🧬</span> <?php echo htmlspecialchars(SITE_NAME); ?> <span class="admin-badge">Admin</span>
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
                <button class="tab-btn" onclick="openTab(event, 'navbar')">Navbar</button>
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
                        <div class="form-group">
                            <label class="form-label" style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:normal;">
                                <input type="hidden" name="settings[show_cta]" value="0">
                                <input type="checkbox" name="settings[show_cta]" value="1" <?php echo ($settings['show_cta'] ?? 1) ? 'checked' : ''; ?>>
                                Show CTA Section on Pages
                            </label>
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

            <div id="navbar" class="tab-content">
                <div class="card" style="margin-bottom: 2rem;">
                    <h2>Navigation Menu</h2>
                    <form method="POST">
                        <input type="hidden" name="save_navbar" value="1">
                        <div style="overflow-x: auto; margin-bottom: 1rem;">
                            <table class="info-table">
                                <thead>
                                    <tr>
                                        <th width="100">Order</th>
                                        <th width="80">Section</th>
                                        <th>Label</th>
                                        <th>URL</th>
                                        <th width="80" class="text-center">Active</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($navItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div style="display:flex; align-items:center;">
                                                <input type="number" name="items[<?php echo $item['id']; ?>][display_order]" value="<?php echo $item['display_order']; ?>" class="form-control-sm" style="width:50px; margin-right:5px;">
                                                <div style="display:flex; flex-direction:column;">
                                                    <button type="submit" name="move_nav_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='up';" class="btn-xs" title="Move Up">▲</button>
                                                    <button type="submit" name="move_nav_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='down';" class="btn-xs" title="Move Down">▼</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (isset($item['section']) && $item['section'] === 'actions'): ?>
                                                <span style="background:#e2e3e5; padding:2px 6px; border-radius:4px; font-size:0.75rem;">Actions</span>
                                            <?php else: ?>
                                                <span style="background:#d1e7dd; padding:2px 6px; border-radius:4px; font-size:0.75rem;">Main</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><input type="text" name="items[<?php echo $item['id']; ?>][label]" value="<?php echo htmlspecialchars($item['label']); ?>" class="form-control-sm"></td>
                                        <td><input type="text" name="items[<?php echo $item['id']; ?>][url]" value="<?php echo htmlspecialchars($item['url']); ?>" class="form-control-sm"></td>
                                        <td class="text-center">
                                            <input type="checkbox" name="items[<?php echo $item['id']; ?>][is_active]" value="1" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <button type="submit" name="delete_nav_item" value="1" onclick="document.getElementById('delete_id_input').value='<?php echo $item['id']; ?>'; return confirm('Are you sure?');" class="btn btn-outline btn-sm" style="color: #dc3545; border-color: #dc3545;">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="delete_id" id="delete_id_input" value="">
                        <input type="hidden" name="move_id" id="move_id_input" value="">
                        <input type="hidden" name="move_dir" id="move_dir_input" value="">
                        <button type="submit" class="btn">Save Changes</button>
                    </form>
                    
                    <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--glass-border);">
                    
                    <h3>Add New Item</h3>
                    <form method="POST" style="display: grid; grid-template-columns: 1fr 2fr 2fr auto; gap: 10px; align-items: end;">
                        <input type="hidden" name="add_nav_item" value="1">
                        <div class="form-group mb-0"><label class="font-sm">Order</label><input type="number" name="new_order" class="form-control-sm" value="<?php echo count($navItems) + 1; ?>" required></div>
                        <div class="form-group mb-0"><label class="font-sm">Label</label><input type="text" name="new_label" class="form-control-sm" placeholder="e.g. Blog" required></div>
                        <div class="form-group mb-0"><label class="font-sm">URL</label><input type="text" name="new_url" class="form-control-sm" placeholder="e.g. blog.php" required></div>
                        <button type="submit" class="btn btn-primary btn-sm" style="height: 32px;">Add Item</button>
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