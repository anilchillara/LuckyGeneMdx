<?php
define('LuckyGenesMDx', true);
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
    // Fetch current settings for change comparison
    $currentSettings = [];
    $stmt = $db->query("SELECT setting_key, value FROM site_settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $currentSettings[$row['setting_key']] = $row['value'];
    }

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_general':
                $check = $db->prepare("SELECT 1 FROM site_settings WHERE setting_key = ?");
                $update = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = ?");
                $insert = $db->prepare("INSERT INTO site_settings (setting_key, value) VALUES (?, ?)");

                $changes = [];
                foreach ($_POST['settings'] as $key => $value) {
                    $oldValue = $currentSettings[$key] ?? '';
                    if ($oldValue != $value) {
                        $changes[] = "$key: '$oldValue' -> '$value'";
                    }

                    $check->execute([$key]);
                    if ($check->fetch()) {
                        $update->execute([$value, $key]);
                    } else {
                        $insert->execute([$key, $value]);
                    }
                }
                $success = "General settings updated successfully!";
                // Log Activity
                $details = empty($changes) ? "No changes made" : "Updated: " . implode(', ', $changes);
                $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, details, ip_address) VALUES (?, 'update_general_settings', ?, ?)");
                $stmt->execute([$_SESSION['admin_id'], $details, $_SERVER['REMOTE_ADDR']]);
                break;
                
            case 'update_email':
                $check = $db->prepare("SELECT 1 FROM site_settings WHERE setting_key = ?");
                $update = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = ?");
                $insert = $db->prepare("INSERT INTO site_settings (setting_key, value) VALUES (?, ?)");

                $changes = [];
                foreach ($_POST['email_settings'] as $key => $value) {
                    $oldValue = $currentSettings[$key] ?? '';
                    if ($oldValue != $value) {
                        if (strpos($key, 'pass') !== false) {
                            $changes[] = "$key changed";
                        } else {
                            $changes[] = "$key: '$oldValue' -> '$value'";
                        }
                    }

                    $check->execute([$key]);
                    if ($check->fetch()) {
                        $update->execute([$value, $key]);
                    } else {
                        $insert->execute([$key, $value]);
                    }
                }
                $success = "Email settings updated successfully!";
                // Log Activity
                $details = empty($changes) ? "No changes made" : "Updated email settings: " . implode(', ', $changes);
                $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, details, ip_address) VALUES (?, 'update_email_settings', ?, ?)");
                $stmt->execute([$_SESSION['admin_id'], $details, $_SERVER['REMOTE_ADDR']]);
                break;
                
            case 'test_email':
                require_once '../includes/User.php';
                $userModel = new User();
                $testTo = $_POST['test_recipient'] ?? '';
                
                if (filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
                    $result = $userModel->sendTestEmail($testTo);
                    if ($result['success']) {
                        $success = $result['message'];
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = "Invalid email address for testing.";
                }
                break;
                
            case 'change_password':
                if ($_POST['new_password'] === $_POST['confirm_password']) {
                    $hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$hashed, $_SESSION['admin_id']]);
                    $success = "Password changed successfully!";
                    // Log Activity
                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, details, ip_address) VALUES (?, 'change_admin_password', 'Changed admin password', ?)");
                    $stmt->execute([$_SESSION['admin_id'], $_SERVER['REMOTE_ADDR']]);
                } else {
                    $error = "Passwords do not match!";
                }
                break;
                
            case 'update_maintenance':
                $oldMode = $currentSettings['maintenance_mode'] ?? 0;
                $mode = isset($_POST['maintenance_mode']) ? 1 : 0;
                $stmt = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = 'maintenance_mode'");
                $stmt->execute([$mode]);
                $success = "Maintenance mode " . ($mode ? "enabled" : "disabled") . "!";
                // Log Activity
                $details = "Maintenance mode changed from " . ($oldMode ? 'On' : 'Off') . " to " . ($mode ? 'On' : 'Off');
                $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, details, ip_address) VALUES (?, 'update_maintenance_mode', ?, ?)");
                $stmt->execute([$_SESSION['admin_id'], $details, $_SERVER['REMOTE_ADDR']]);
                break;
        }
    } elseif (isset($_POST['save_navbar'])) {
        try {
            // Fetch current items for comparison
            $currentItems = [];
            $stmt = $db->query("SELECT * FROM navbar_items");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $currentItems[$row['id']] = $row;
            }

            $db->beginTransaction();
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $id => $item) {
                    $isActive = isset($item['is_active']) ? 1 : 0;
                    $label = trim($item['label']);
                    $url = trim($item['url']);
                    $order = (int)$item['display_order'];

                    $stmt = $db->prepare("UPDATE navbar_items SET label = ?, url = ?, display_order = ?, is_active = ? WHERE id = ?");
                    $stmt->execute([$label, $url, $order, $isActive, $id]);

                    // Log individual changes
                    if (isset($currentItems[$id])) {
                        $old = $currentItems[$id];
                        $changes = [];
                        
                        if ($old['label'] !== $label) $changes[] = "Label: '{$old['label']}' -> '{$label}'";
                        if ($old['url'] !== $url) $changes[] = "URL: '{$old['url']}' -> '{$url}'";
                        if ((int)$old['display_order'] !== $order) $changes[] = "Order: {$old['display_order']} -> {$order}";
                        if ((int)$old['is_active'] !== $isActive) $changes[] = "Active: " . ($old['is_active'] ? 'Yes' : 'No') . " -> " . ($isActive ? 'Yes' : 'No');

                        if (!empty($changes)) {
                            $details = "Updated navbar item '{$label}': " . implode(', ', $changes);
                            $logStmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'update_navbar_item', 'navbar_item', ?, ?, ?)");
                            $logStmt->execute([$_SESSION['admin_id'], $id, $details, $_SERVER['REMOTE_ADDR']]);
                        }
                    }
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
            // Log Activity
            $newId = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'add_navbar_item', 'navbar_item', ?, ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], $newId, "Added navbar item: " . trim($_POST['new_label']), $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            $error = "Error adding item: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_nav_item'])) {
        try {
            $stmt = $db->prepare("SELECT label FROM navbar_items WHERE id = ?");
            $stmt->execute([(int)$_POST['delete_id']]);
            $label = $stmt->fetchColumn();

            $stmt = $db->prepare("DELETE FROM navbar_items WHERE id = ?");
            $stmt->execute([(int)$_POST['delete_id']]);
            $success = "Item deleted successfully.";
            // Log Activity
            $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'delete_navbar_item', 'navbar_item', ?, ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], (int)$_POST['delete_id'], "Deleted navbar item: " . ($label ?: 'Unknown'), $_SERVER['REMOTE_ADDR']]);
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
                    // Log Activity
                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'reorder_navbar_item', 'navbar_item', ?, ?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], $id, "Moved item " . $dir, $_SERVER['REMOTE_ADDR']]);
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
    <title>System Settings - LuckyGenesMDx Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .tab-container { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .btn-xs { padding: 2px 6px; font-size: 0.7rem; line-height: 1; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1>System Settings</h1>
                <p>Configure site behavior and security</p>
            </div>
        </div>

            <?php if (isset($success)): ?>
                <div class="glass-card-teal-left p-3 mb-3 text-teal"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="glass-card-error p-3 mb-3 text-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tab-container">
                <button class="tab-btn active" onclick="openTab(event, 'general')">General</button>
                <button class="tab-btn" onclick="openTab(event, 'email')">Email</button>
                <button class="tab-btn" onclick="openTab(event, 'security')">Security</button>
                <button class="tab-btn" onclick="openTab(event, 'navbar')">Navbar</button>
                <button class="tab-btn" onclick="openTab(event, 'system')">System Info</button>
            </div>

            <div id="general" class="tab-content active">
                <div class="admin-card mb-4">
                    <h2 class="mb-3">General Configuration</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_general">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="settings[site_name]" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'LuckyGenesMDx'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Site URL</label>
                            <input type="url" name="settings[site_url]" value="<?php echo htmlspecialchars($settings['site_url'] ?? SITE_URL); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Base URL</label>
                            <input type="url" name="settings[base_url]" value="<?php echo htmlspecialchars($settings['base_url'] ?? BASE_URL); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Kit Price (USD)</label>
                            <input type="number" name="settings[kit_price]" step="0.01" value="<?php echo htmlspecialchars($settings['kit_price'] ?? '99.00'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Actual Price (USD) <span class="font-sm text-dark-gray">(Strike-through price)</span></label>
                            <input type="number" name="settings[actual_price]" step="0.01" value="<?php echo htmlspecialchars($settings['actual_price'] ?? '249.00'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <div class="form-checkbox">
                                <input type="hidden" name="settings[show_cta]" value="0">
                                <input type="checkbox" id="show_cta" name="settings[show_cta]" value="1" <?php echo ($settings['show_cta'] ?? 1) ? 'checked' : ''; ?>>
                                <label for="show_cta">Show CTA Section on Pages</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>

                <div class="admin-card">
                    <h2 class="mb-3">Maintenance Mode</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_maintenance">
                        <div class="form-checkbox">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                            <label for="maintenance_mode">Enable Maintenance Mode (Admin only access)</label>
                        </div>
                        <button type="submit" class="btn btn-outline mt-1-5">Update Status</button>
                    </form>
                </div>
            </div>

            <div id="email" class="tab-content">
                <div class="admin-card">
                    <h2 class="mb-3">Email Configuration</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_email">
                        
                        <div class="form-group">
                            <label>From Email</label>
                            <input type="email" name="email_settings[from_email]" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" placeholder="noreply@LuckyGenesMDx.com" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>From Name</label>
                            <input type="text" name="email_settings[from_name]" value="<?php echo htmlspecialchars($settings['from_name'] ?? 'LuckyGenesMDx'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Support Email</label>
                            <input type="email" name="email_settings[support_email]" value="<?php echo htmlspecialchars($settings['support_email'] ?? 'support@LuckyGenesMDx.com'); ?>" class="form-control">
                        </div>
                        
                        <hr style="border: 0; border-top: 1px solid var(--color-border); margin: 2rem 0;">
                        <h3 class="mb-3">SMTP Settings</h3>
                        
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" name="email_settings[smtp_host]" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="number" name="email_settings[smtp_port]" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>SMTP Security</label>
                            <select name="email_settings[smtp_security]" class="form-select">
                                <option value="tls" <?php echo ($settings['smtp_security'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($settings['smtp_security'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="" <?php echo ($settings['smtp_security'] ?? '') === '' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="text" name="email_settings[smtp_username]" value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" name="email_settings[smtp_password]" value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Email Settings</button>
                    </form>
                    
                    <hr style="border: 0; border-top: 1px solid var(--color-border); margin: 2rem 0;">
                    
                    <h3 class="mb-3">Test Configuration</h3>
                    <p class="font-sm text-dark-gray mb-3">
                        Send a test email to verify your SMTP settings. <strong>Please save any changes above before testing.</strong>
                    </p>
                    
                    <form method="POST" class="test-email-form">
                        <input type="hidden" name="action" value="test_email">
                        <div class="form-group flex-1 mb-0">
                            <label>Recipient Email</label>
                            <input type="email" name="test_recipient" value="<?php echo htmlspecialchars($settings['support_email'] ?? ''); ?>" required placeholder="email@example.com" class="form-control mb-3">
                        </div>
                        <button type="submit" class="btn btn-outline">Send Test Email</button>
                    </form>
                </div>
            </div>

            <div id="navbar" class="tab-content">
                <div class="admin-card mb-4">
                    <h2 class="mb-3">Navigation Menu</h2>
                    <form method="POST">
                        <input type="hidden" name="save_navbar" value="1">
                        <div class="table-responsive mb-3">
                            <table class="admin-table">
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
                                            <div style="display:flex; align-items:center; gap:5px;">
                                                <input type="number" name="items[<?php echo $item['id']; ?>][display_order]" value="<?php echo $item['display_order']; ?>" class="form-control" style="width:60px; padding:4px;">
                                                <div style="display:flex; flex-direction:column;">
                                                    <button type="submit" name="move_nav_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='up';" class="btn btn-outline btn-xs" title="Move Up">▲</button>
                                                    <button type="submit" name="move_nav_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='down';" class="btn btn-outline btn-xs" title="Move Down">▼</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (isset($item['section']) && $item['section'] === 'actions'): ?>
                                                <span class="badge-grey">Actions</span>
                                            <?php else: ?>
                                                <span class="badge-green">Main</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><input type="text" name="items[<?php echo $item['id']; ?>][label]" value="<?php echo htmlspecialchars($item['label']); ?>" class="form-control" style="padding:6px;"></td>
                                        <td><input type="text" name="items[<?php echo $item['id']; ?>][url]" value="<?php echo htmlspecialchars($item['url']); ?>" class="form-control" style="padding:6px;"></td>
                                        <td class="text-center">
                                            <input type="checkbox" name="items[<?php echo $item['id']; ?>][is_active]" value="1" <?php echo $item['is_active'] ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <button type="submit" name="delete_nav_item" value="1" onclick="document.getElementById('delete_id_input').value='<?php echo $item['id']; ?>'; return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="delete_id" id="delete_id_input" value="">
                        <input type="hidden" name="move_id" id="move_id_input" value="">
                        <input type="hidden" name="move_dir" id="move_dir_input" value="">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                    
                    <hr style="border: 0; border-top: 1px solid var(--color-border); margin: 2rem 0;">
                    
                    <h3 class="mb-3">Add New Item</h3>
                    <form method="POST" class="nav-item-form">
                        <input type="hidden" name="add_nav_item" value="1">
                        <div class="form-group mb-2"><label class="font-sm">Order</label><input type="number" name="new_order" class="form-control" value="<?php echo count($navItems) + 1; ?>" required></div>
                        <div class="form-group mb-2"><label class="font-sm">Label</label><input type="text" name="new_label" class="form-control" placeholder="e.g. Blog" required></div>
                        <div class="form-group mb-3"><label class="font-sm">URL</label><input type="text" name="new_url" class="form-control" placeholder="e.g. blog.php" required></div>
                        <button type="submit" class="btn btn-primary btn-sm">Add Item</button>
                    </form>
                </div>
            </div>

            <div id="system" class="tab-content">
                <div class="admin-card">
                    <h2 class="mb-3">Server Information</h2>
                    <div class="table-responsive">
                        <table class="admin-table">
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