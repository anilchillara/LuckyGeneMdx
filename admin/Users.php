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

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                    if (!$email) {
                        throw new Exception('Invalid email address');
                    }
                    
                    $check = $db->prepare("SELECT user_id FROM users WHERE email = ?");
                    $check->execute([$email]);
                    if ($check->fetch()) {
                        throw new Exception('Email already exists');
                    }
                    
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    
                    $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password_hash, date_of_birth, address, city, state, zip_code, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");
                    $stmt->execute([
                        $_POST['full_name'],
                        $email,
                        $_POST['phone'],
                        $password_hash,
                        $_POST['date_of_birth'],
                        $_POST['address'] ?? null,
                        $_POST['city'] ?? null,
                        $_POST['state'] ?? null,
                        $_POST['zip_code'] ?? null
                    ]);
                    
                    $success = "User added successfully!";
                    break;
                    
                case 'update':
                    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                    if (!$email) {
                        throw new Exception('Invalid email address');
                    }
                    
                    $check = $db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
                    $check->execute([$email, $_POST['user_id']]);
                    if ($check->fetch()) {
                        throw new Exception('Email already exists for another user');
                    }
                    
                    $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, date_of_birth = ?, address = ?, city = ?, state = ?, zip_code = ?, is_active = ?, updated_at = NOW() WHERE user_id = ?");
                    $stmt->execute([
                        $_POST['full_name'],
                        $email,
                        $_POST['phone'],
                        $_POST['date_of_birth'],
                        $_POST['address'] ?? null,
                        $_POST['city'] ?? null,
                        $_POST['state'] ?? null,
                        $_POST['zip_code'] ?? null,
                        $_POST['is_active'],
                        $_POST['user_id']
                    ]);
                    
                    $success = "User updated successfully!";
                    break;
                    
                case 'reset_password':
                    $new_password = bin2hex(random_bytes(8));
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $stmt = $db->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE user_id = ?");
                    $stmt->execute([$password_hash, $_POST['user_id']]);
                    
                    $success = "Password reset! New password: <strong>" . $new_password . "</strong> (Save this - user will need it to login)";
                    break;
                    
                case 'toggle_status':
                    $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active, updated_at = NOW() WHERE user_id = ?");
                    $stmt->execute([$_POST['user_id']]);
                    
                    $success = "User status updated successfully!";
                    break;
                    
                case 'delete':
                    $check = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                    $check->execute([$_POST['user_id']]);
                    $orderCount = $check->fetchColumn();
                    
                    if ($orderCount > 0) {
                        throw new Exception("Cannot delete user with existing orders. Deactivate instead.");
                    }
                    
                    $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
                    $stmt->execute([$_POST['user_id']]);
                    
                    $success = "User deleted successfully!";
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Build query
$where_clauses = [];
$params = [];

if ($status_filter === 'active') {
    $where_clauses[] = "is_active = 1";
} elseif ($status_filter === 'inactive') {
    $where_clauses[] = "is_active = 0";
}

if (!empty($search)) {
    $where_clauses[] = "(full_name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
$countSql = "SELECT COUNT(*) as total FROM users $where_sql";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalUsers = $stmt->fetch()['total'];
$totalPages = ceil($totalUsers / $perPage);

// Get users with order count
$sql = "SELECT u.*, 
        COUNT(DISTINCT o.order_id) as order_count,
        MAX(o.order_date) as last_order_date
        FROM users u
        LEFT JOIN orders o ON u.user_id = o.user_id
        $where_sql
        GROUP BY u.user_id
        ORDER BY u.created_at DESC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'active' => $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
    'inactive' => $db->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn(),
    'with_orders' => $db->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetchColumn(),
];

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - LuckyGeneMDx Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
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
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--color-medical-teal);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-medical-teal);
            margin-bottom: 0.25rem;
        }
        .stat-label {
            color: var(--color-dark-gray);
            font-size: 0.9rem;
        }
        .filters-bar {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .table-container {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-medium-gray); }
        .table th { font-weight: 600; color: var(--color-primary-deep-blue); background: var(--color-light-gray); white-space: nowrap; }
        .table tbody tr:hover { background: var(--color-light-gray); }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
            text-decoration: none;
            display: inline-block;
        }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.85rem; }
        .btn-primary { background: var(--color-medical-teal); color: white; }
        .btn-primary:hover { background: #009688; }
        .btn-secondary { background: var(--color-dark-gray); color: white; }
        .btn-secondary:hover { background: #555; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-outline { background: white; color: var(--color-primary-deep-blue); border: 1px solid var(--color-medium-gray); }
        .btn-outline:hover { background: var(--color-light-gray); }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: var(--radius-md);
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-close {
            float: right;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: #999;
            cursor: pointer;
        }
        .modal-close:hover { color: #000; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--color-medium-gray);
            border-radius: var(--radius-sm);
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-dark-gray);
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--color-medium-gray);
            border-radius: var(--radius-sm);
            color: var(--color-primary-deep-blue);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        .pagination a:hover {
            background: var(--color-medical-teal);
            color: white;
            border-color: var(--color-medical-teal);
        }
        .pagination .active {
            background: var(--color-medical-teal);
            color: white;
            border-color: var(--color-medical-teal);
        }
        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include 'sidenav.php'; ?>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 style="margin-bottom: 0.25rem;">User Management</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">
                    <?php echo number_format($totalUsers); ?> total users
                </p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['active']); ?></div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['inactive']); ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['with_orders']); ?></div>
                    <div class="stat-label">With Orders</div>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="" class="filters-bar">
                <div class="filter-group">
                    <label class="form-label">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-input" 
                        placeholder="Name, Email, Phone..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                </div>
                
                <div class="filter-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Users</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
                    </select>
                </div>
                
                <div class="filter-group" style="flex: 0;">
                    <button type="submit" class="btn btn-primary">
                        🔍 Filter
                    </button>
                </div>
                
                <div class="filter-group" style="flex: 0;">
                    <button type="button" onclick="showAddModal()" class="btn btn-primary">
                        + Add User
                    </button>
                </div>
                
                <?php if ($search || $status_filter !== 'all'): ?>
                <div class="filter-group" style="flex: 0;">
                    <a href="users.php" class="btn btn-outline">
                        ✕ Clear
                    </a>
                </div>
                <?php endif; ?>
            </form>

            <!-- Users Table -->
            <div class="table-container">
                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <h3>No users found</h3>
                        <p>
                            <?php if ($search || $status_filter !== 'all'): ?>
                                Try adjusting your filters or search terms.
                            <?php else: ?>
                                Users will appear here once they register.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($user['order_count'] > 0): ?>
                                                <a href="orders.php?user_id=<?php echo $user['user_id']; ?>" style="color: var(--color-medical-teal); font-weight: 500;">
                                                    <?php echo $user['order_count']; ?> order<?php echo $user['order_count'] > 1 ? 's' : ''; ?>
                                                </a>
                                            <?php else: ?>
                                                <span style="color: #999;">No orders</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <button onclick='editUser(<?php echo json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' 
                                                    class="btn btn-sm btn-secondary">Edit</button>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                            
                                            <?php if ($user['order_count'] == 0): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        $queryParams = [];
                        if ($search) $queryParams['search'] = $search;
                        if ($status_filter !== 'all') $queryParams['status'] = $status_filter;
                        
                        // Previous button
                        if ($page > 1):
                            $queryParams['page'] = $page - 1;
                        ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>">← Previous</a>
                        <?php else: ?>
                            <span class="disabled">← Previous</span>
                        <?php endif; ?>
                        
                        <!-- Page numbers -->
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                            $queryParams['page'] = $i;
                            if ($i == $page):
                        ?>
                                <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                                <a href="?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
                        <?php
                            endif;
                        endfor;
                        ?>
                        
                        <!-- Next button -->
                        <?php if ($page < $totalPages):
                            $queryParams['page'] = $page + 1;
                        ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>">Next →</a>
                        <?php else: ?>
                            <span class="disabled">Next →</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <span class="modal-close" onclick="closeUserModal()">&times;</span>
            <h2 id="modalTitle">Add New User</h2>
            <form method="POST" id="userForm">
                <input type="hidden" name="action" id="form_action" value="add">
                <input type="hidden" name="user_id" id="form_user_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" id="form_full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="form_email" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="form_phone">
                    </div>
                    
                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" id="form_dob" required>
                    </div>
                </div>
                
                <div class="form-group" id="password_group">
                    <label>Password *</label>
                    <input type="password" name="password" id="form_password" minlength="8">
                    <small style="color: #666;">Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" id="form_address">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" id="form_city">
                    </div>
                    
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" id="form_state" maxlength="2">
                    </div>
                    
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" name="zip_code" id="form_zip" maxlength="10">
                    </div>
                </div>
                
                <div class="form-group" id="status_group" style="display: none;">
                    <label>Status</label>
                    <select name="is_active" id="form_is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <button type="button" class="btn btn-outline" onclick="closeUserModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('form_action').value = 'add';
            document.getElementById('userForm').reset();
            document.getElementById('password_group').style.display = 'block';
            document.getElementById('form_password').required = true;
            document.getElementById('status_group').style.display = 'none';
            document.getElementById('userModal').style.display = 'block';
        }

        function editUser(user) {
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('form_action').value = 'update';
            document.getElementById('form_user_id').value = user.user_id;
            document.getElementById('form_full_name').value = user.full_name;
            document.getElementById('form_email').value = user.email;
            document.getElementById('form_phone').value = user.phone || '';
            document.getElementById('form_dob').value = user.date_of_birth;
            document.getElementById('form_address').value = user.address || '';
            document.getElementById('form_city').value = user.city || '';
            document.getElementById('form_state').value = user.state || '';
            document.getElementById('form_zip').value = user.zip_code || '';
            document.getElementById('form_is_active').value = user.is_active;
            
            document.getElementById('password_group').style.display = 'none';
            document.getElementById('form_password').required = false;
            document.getElementById('status_group').style.display = 'block';
            document.getElementById('userModal').style.display = 'block';
        }

        function closeUserModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>