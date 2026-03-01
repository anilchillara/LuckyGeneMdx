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

// Search Logic
$search = trim($_GET['search'] ?? '');
$whereClause = '';
$params = [];

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $deleteId = (int)$_POST['delete_id'];
        
        // Get email for logging
        $stmt = $db->prepare("SELECT email FROM interest_list WHERE id = ?");
        $stmt->execute([$deleteId]);
        $email = $stmt->fetchColumn();

        $stmt = $db->prepare("DELETE FROM interest_list WHERE id = ?");
        $stmt->execute([$deleteId]);
        
        // Log Activity
        $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'delete_interest_subscriber', 'interest_list', ?, ?, ?)");
        $stmt->execute([$_SESSION['admin_id'], $deleteId, "Deleted subscriber: " . ($email ?: 'Unknown'), $_SERVER['REMOTE_ADDR']]);

        $success = "Subscriber deleted successfully.";
    } catch (Exception $e) {
        $error = "Error deleting subscriber: " . $e->getMessage();
    }
}

// Handle Bulk Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete'])) {
    if (!empty($_POST['bulk_ids']) && is_array($_POST['bulk_ids'])) {
        try {
            $ids = array_map('intval', $_POST['bulk_ids']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $db->prepare("DELETE FROM interest_list WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $count = $stmt->rowCount();
            
            $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, details, ip_address) VALUES (?, 'bulk_delete_interest_subscriber', 'interest_list', ?, ?)");
            $stmt->execute([$_SESSION['admin_id'], "Bulk deleted $count subscribers", $_SERVER['REMOTE_ADDR']]);
            $success = "$count subscribers deleted successfully.";
        } catch (Exception $e) {
            $error = "Error deleting subscribers: " . $e->getMessage();
        }
    }
}

if ($search) {
    // Use unique parameter names to avoid PDO issues with non-emulated prepares
    $whereClause = "WHERE name LIKE :s1 OR email LIKE :s2 OR phone LIKE :s3 OR role LIKE :s4";
    $params[':s1'] = "%$search%";
    $params[':s2'] = "%$search%";
    $params[':s3'] = "%$search%";
    $params[':s4'] = "%$search%";
}

// Handle Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=interest_list_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Interest', 'Newsletter', 'Date Joined']);
    
    $stmt = $db->prepare("SELECT id, name, email, phone, role, interest, newsletter_opt_in, created_at FROM interest_list $whereClause ORDER BY created_at DESC");
    $stmt->execute($params);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $db->prepare("SELECT COUNT(*) FROM interest_list $whereClause");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Fetch data
$stmt = $db->prepare("SELECT * FROM interest_list $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$adminName = $_SESSION['admin_username'] ?? 'Admin';
$initials = strtoupper(substr($adminName, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest List - LuckyGenesMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .filters-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .filters-form { flex-direction: column; align-items: stretch; }
            .filters-form .btn { width: 100%; margin-top: 0.5rem; }
            .filters-form a.btn { text-align: center; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="brand">
        <img src="../assets/images/logo_small.png" alt="Logo" style="height: 32px; width: auto;"> <?php echo htmlspecialchars(SITE_NAME); ?> <span class="admin-badge">Admin</span>
      </a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link">Orders</a>
        <a href="Users.php" class="nav-link">Users</a>
        <a href="interest-list.php" class="nav-link active">Interest List</a>
        <a href="upload-results.php" class="nav-link">Upload Results</a>
        <a href="activity-log.php" class="nav-link">Activity Log</a>
        <a href="settings.php" class="nav-link">Settings</a>
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
                <h1>Interest List</h1>
                <p><?php echo number_format($total); ?> subscribers</p>
            </div>
            <a href="?export=csv<?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-primary">Export CSV</a>
        </div>

        <?php if (isset($success)): ?><div class="msg msg-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
        <?php if (isset($error)): ?><div class="msg msg-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <!-- Search -->
        <div class="card" style="margin-bottom: 2rem;">
            <form method="GET" class="filters-form">
                <div class="form-group" style="flex:1; margin-bottom:0;">
                    <input type="text" name="search" placeholder="Search by name, email, phone, or role..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn">Search</button>
                <?php if ($search): ?>
                    <a href="interest-list.php" class="btn btn-outline">Reset Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <form method="POST" id="bulkForm">
        <div class="card" style="padding:0; overflow:hidden;">
            <?php if (empty($subscribers)): ?>
                <div style="text-align:center; padding:4rem 2rem;">
                    <div style="font-size:4rem; margin-bottom:1rem; opacity:0.3;">📋</div>
                    <h3>No subscribers yet</h3>
                    <p style="color:var(--text-secondary);">People who join the interest list will appear here.</p>
                </div>
            <?php else: ?>
                <div style="padding: 1rem; border-bottom: 1px solid var(--glass-border); background: var(--glass-hover); display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <button type="submit" name="bulk_delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the selected subscribers?');">Delete Selected</button>
                    <span style="font-size: 0.85rem; color: var(--text-secondary);">Select items below to perform bulk actions</span>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAll"></th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Interest</th>
                                <th>Newsletter</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscribers as $sub): ?>
                            <tr>
                                <td><input type="checkbox" name="bulk_ids[]" value="<?php echo $sub['id']; ?>" class="row-checkbox"></td>
                                <td style="white-space:nowrap; color:var(--text-secondary); font-size:0.85rem;">
                                    <?php echo date('M j, Y', strtotime($sub['created_at'])); ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($sub['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($sub['email']); ?></td>
                                <td><?php echo htmlspecialchars($sub['phone'] ?? '-'); ?></td>
                                <td><span class="badge badge-blue"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $sub['role']))); ?></span></td>
                                <td style="max-width: 300px; font-size: 0.9rem; color: var(--text-secondary);">
                                    <?php echo htmlspecialchars($sub['interest'] ?? '-'); ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo !empty($sub['newsletter_opt_in']) ? '✅' : ''; ?>
                                </td>
                                <td>
                                    <button type="submit" name="delete_id" value="<?php echo $sub['id']; ?>" class="btn btn-outline btn-sm" style="color: #dc3545; border-color: #dc3545;" onclick="return confirm('Are you sure you want to delete this subscriber?');">Delete</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php $qs = $search ? '&search=' . urlencode($search) : ''; ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $qs; ?>" class="btn btn-outline btn-sm">← Previous</a>
                    <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">← Previous</button>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $qs; ?>" class="btn btn-outline btn-sm">Next →</a>
                    <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">Next →</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        </form>
    </div>

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

        // Select All Logic
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>