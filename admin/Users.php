<?php
    define('luckygenemdx', true);
    require_once '../includes/config.php';
    require_once '../includes/Database.php';

    session_start();

    // Check admin authentication
    if (! isset($_SESSION['admin_id'])) {
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
                    if (! $email) {
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
                        $_POST['zip_code'] ?? null,
                    ]);

                    $success = "User added successfully!";
                    break;

                case 'update':
                    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                    if (! $email) {
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
                        $_POST['user_id'],
                    ]);

                    $success = "User updated successfully!";
                    break;

                case 'reset_password':
                    $new_password  = bin2hex(random_bytes(8));
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
    $search        = $_GET['search'] ?? '';
    $page          = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage       = 25;
    $offset        = ($page - 1) * $perPage;

    // Build query
    $where_clauses = [];
    $params        = [];

    if ($status_filter === 'active') {
    $where_clauses[] = "is_active = 1";
    } elseif ($status_filter === 'inactive') {
    $where_clauses[] = "is_active = 0";
    }

    if (! empty($search)) {
    $where_clauses[]   = "(full_name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
    }

    $where_sql = ! empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM users $where_sql";
    $stmt     = $db->prepare($countSql);
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
    'total'       => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'active'      => $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
    'inactive'    => $db->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn(),
    'with_orders' => $db->query("SELECT COUNT(DISTINCT user_id) FROM orders")->fetchColumn(),
    ];

    $adminName = $_SESSION['admin_username'];
    $adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role']));
    $initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - LuckyGeneMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Page specific styles */
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
            background: var(--glass-panel);
            margin: 2rem auto;
            padding: 2rem;
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            color: var(--text-primary);
        }
        .modal-close {
            float: right;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: var(--text-secondary);
            cursor: pointer;
        }
        .modal-close:hover { color: var(--text-primary); }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
            border-top: 1px solid var(--glass-border);
        }
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
        <a href="users.php" class="nav-link active">Users</a>
        <a href="upload-results.php" class="nav-link">Upload Results</a>
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
                <h1>User Management</h1>
                <p><?php echo number_format($totalUsers); ?> total users</p>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="msg msg-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="msg msg-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="grid" style="margin-bottom: 2rem;">
            <div class="card stat-card col-span-3 blue">
                <div class="stat-lbl">Total Users</div>
                <div class="stat-val"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="card stat-card col-span-3 green">
                <div class="stat-lbl">Active</div>
                <div class="stat-val"><?php echo number_format($stats['active']); ?></div>
            </div>
            <div class="card stat-card col-span-3 orange">
                <div class="stat-lbl">Inactive</div>
                <div class="stat-val"><?php echo number_format($stats['inactive']); ?></div>
            </div>
            <div class="card stat-card col-span-3 red">
                <div class="stat-lbl">With Orders</div>
                <div class="stat-val"><?php echo number_format($stats['with_orders']); ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card" style="margin-bottom: 2rem;">
            <form method="GET" action="" style="display:flex; gap:1rem; align-items:end; flex-wrap:wrap;">
                <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
                    <label>Search</label>
                    <input
                        type="text"
                        name="search"
                        placeholder="Name, Email, Phone..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                </div>

                <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
                    <label>Status</label>
                    <select name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Users</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
                    </select>
                </div>

                <button type="submit" class="btn">🔍 Filter</button>
                <button type="button" onclick="showAddModal()" class="btn btn-outline">+ Add User</button>

                <?php if ($search || $status_filter !== 'all'): ?>
                    <a href="users.php" class="btn btn-outline">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Users Table -->
        <div class="card" style="padding:0; overflow:hidden;">
                <?php if (empty($users)): ?>
                <div style="text-align:center; padding:4rem 2rem;">
                    <div style="font-size:4rem; margin-bottom:1rem; opacity:0.3;">👥</div>
                        <h3>No users found</h3>
                    <p style="color:var(--text-secondary);">
                            <?php if ($search || $status_filter !== 'all'): ?>
                                Try adjusting your filters or search terms.
                            <?php else: ?>
                                Users will appear here once they register.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                    <table class="data-table">
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
                                    <td><span style="color:var(--text-secondary);"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></span></td>
                                        <td>
                                            <?php if ($user['order_count'] > 0): ?>
                                            <a href="orders.php?user_id=<?php echo $user['user_id']; ?>" style="font-weight: 500;">
                                                    <?php echo $user['order_count']; ?> order<?php echo $user['order_count'] > 1 ? 's' : ''; ?>
                                                </a>
                                            <?php else: ?>
                                            <span style="color: var(--text-secondary);">No orders</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                        <span class="badge badge-<?php echo $user['is_active'] ? 'green' : 'orange'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <button onclick='editUser(<?php echo json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                class="btn btn-outline btn-sm">Edit</button>

                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline">
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
                            if ($search) {
                                $queryParams['search'] = $search;
                            }

                            if ($status_filter !== 'all') {
                                $queryParams['status'] = $status_filter;
                            }

                            // Previous button
                            if ($page > 1):
                                $queryParams['page'] = $page - 1;
                        ?>
                        <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm">← Previous</a>
                        <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">← Previous</button>
                        <?php endif; ?>

                        <!-- Page numbers -->
                        <?php
                            $start = max(1, $page - 2);
                            $end   = min($totalPages, $page + 2);

                            for ($i = $start; $i <= $end; $i++):
                                $queryParams['page'] = $i;
                                if ($i == $page):
                        ?>
                            <button class="btn btn-sm" style="cursor:default;"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm"><?php echo $i; ?></a>
                        <?php
                            endif;
                            endfor;
                        ?>

                        <!-- Next button -->
                        <?php if ($page < $totalPages):
                                $queryParams['page'] = $page + 1;
                        ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm">Next →</a>
                        <?php else: ?>
                            <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">Next →</button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
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
                    <small style="color: var(--text-secondary);">Minimum 8 characters</small>
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
                    <button type="submit" class="btn">Save User</button>
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