<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/SessionManager.php';

SessionManager::start();
SessionManager::requireAdmin();

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
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
            $message = "Navbar settings updated successfully.";
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Error updating settings: " . $e->getMessage();
        }
    } elseif (isset($_POST['add_item'])) {
        try {
            $stmt = $db->prepare("INSERT INTO navbar_items (label, url, display_order, is_active) VALUES (?, ?, ?, 1)");
            $stmt->execute([trim($_POST['new_label']), trim($_POST['new_url']), (int)$_POST['new_order']]);
            $message = "New item added successfully.";
        } catch (Exception $e) {
            $error = "Error adding item: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_item'])) {
        try {
            $stmt = $db->prepare("DELETE FROM navbar_items WHERE id = ?");
            $stmt->execute([(int)$_POST['delete_id']]);
            $message = "Item deleted successfully.";
        } catch (Exception $e) {
            $error = "Error deleting item: " . $e->getMessage();
        }
    } elseif (isset($_POST['move_item'])) {
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
                    $message = "Item reordered successfully.";
                }
            }
        } catch (Exception $e) {
            $error = "Error moving item: " . $e->getMessage();
        }
    }
}

// Fetch Items
$navItems = [];
try {
    $stmt = $db->query("SELECT * FROM navbar_items ORDER BY display_order ASC");
    $navItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Navbar Settings | Admin Panel</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .admin-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; }
        .form-control-sm { padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        .btn-sm { padding: 5px 10px; font-size: 0.85rem; }
        .status-toggle { cursor: pointer; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .btn-xs { padding: 0 5px; font-size: 0.7rem; line-height: 1.2; border: 1px solid #ddd; background: #fff; cursor: pointer; }
        .btn-xs:hover { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="glass-card p-4">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2>Navbar Settings</h2>
                <a href="settings.php" class="btn btn-outline btn-sm">Back to Settings</a>
            </div>

            <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

            <form method="POST">
                <div class="table-wrapper">
                    <table>
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
                                            <button type="submit" name="move_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='up';" class="btn-xs" title="Move Up">▲</button>
                                            <button type="submit" name="move_item" value="1" onclick="document.getElementById('move_id_input').value='<?php echo $item['id']; ?>'; document.getElementById('move_dir_input').value='down';" class="btn-xs" title="Move Down">▼</button>
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
                                    <input type="checkbox" name="items[<?php echo $item['id']; ?>][is_active]" value="1" <?php echo $item['is_active'] ? 'checked' : ''; ?> class="status-toggle">
                                </td>
                                <td>
                                    <button type="submit" name="delete_item" value="1" onclick="document.getElementById('delete_id_input').value='<?php echo $item['id']; ?>'; return confirm('Are you sure?');" class="btn btn-outline btn-sm" style="color: #dc3545; border-color: #dc3545;">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="delete_id" id="delete_id_input" value="">
                <input type="hidden" name="move_id" id="move_id_input" value="">
                <input type="hidden" name="move_dir" id="move_dir_input" value="">
                <button type="submit" name="save_changes" class="btn btn-primary">Save Changes</button>
            </form>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

            <h3>Add New Item</h3>
            <form method="POST" style="display: grid; grid-template-columns: 1fr 2fr 2fr auto; gap: 10px; align-items: end;">
                <div class="form-group mb-0"><label class="font-sm">Order</label><input type="number" name="new_order" class="form-control-sm" value="<?php echo count($navItems) + 1; ?>" required></div>
                <div class="form-group mb-0"><label class="font-sm">Label</label><input type="text" name="new_label" class="form-control-sm" placeholder="e.g. Blog" required></div>
                <div class="form-group mb-0"><label class="font-sm">URL</label><input type="text" name="new_url" class="form-control-sm" placeholder="e.g. blog.php" required></div>
                <button type="submit" name="add_item" class="btn btn-primary btn-sm" style="height: 32px;">Add Item</button>
            </form>
        </div>
    </div>
</body>
</html>