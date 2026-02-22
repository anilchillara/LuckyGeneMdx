<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
session_start();
setSecurityHeaders();

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
$orderModel = new Order();

// Get filters from query string
$statusFilter = isset($_GET['status']) ? intval($_GET['status']) : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build WHERE clause
$where = [];
$params = [];

if ($statusFilter) {
    $where[] = "o.status_id = :status_id";
    $params[':status_id'] = $statusFilter;
}

if ($searchQuery) {
    $where[] = "(o.order_number LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = '%' . $searchQuery . '%';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count for pagination
try {
    $countSql = "SELECT COUNT(*) as total 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.user_id 
                 $whereClause";
    $stmt = $db->prepare($countSql);
    $stmt->execute($params);
    $totalOrders = $stmt->fetch()['total'];
    $totalPages = ceil($totalOrders / $perPage);
    
    // Get orders
    $sql = "SELECT o.order_id, o.order_number, o.order_date, o.status_id, o.tracking_number,
                   o.shipping_city, o.shipping_state, o.price,
                   os.status_name, os.display_order,
                   u.full_name, u.email, u.phone
            FROM orders o
            JOIN order_status os ON o.status_id = os.status_id
            JOIN users u ON o.user_id = u.user_id
            $whereClause
            ORDER BY o.order_date DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $orders = $stmt->fetchAll();
    
    // Get all statuses for filter
    $statuses = $orderModel->getOrderStatuses();
    
} catch(PDOException $e) {
    error_log("Orders Page Error: " . $e->getMessage());
    $orders = [];
    $statuses = [];
}

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_',' ',$_SESSION['admin_role'] ?? 'Admin'));
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - LuckyGeneMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="brand">
    <span>🧬</span> <?php echo htmlspecialchars(SITE_NAME); ?> <span class="admin-badge">Admin</span>
  </a>
  <div class="nav-items">
    <a href="index.php" class="nav-link">Dashboard</a>
    <a href="orders.php" class="nav-link active">Orders</a>
    <a href="users.php" class="nav-link">Users</a>
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
            <h1>Order Management</h1>
            <p><?php echo number_format($totalOrders); ?> total orders</p>
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
                        placeholder="Order #, Name, Email..."
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                    >
            </div>
            
            <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
                <label>Status</label>
                <select name="status">
                        <option value="">All Statuses</option>
                        <?php foreach($statuses as $status): ?>
                            <option value="<?php echo $status['status_id']; ?>" <?php echo $statusFilter == $status['status_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
            </div>
            
            <button type="submit" class="btn">🔍 Filter</button>
            
            <?php if ($searchQuery || $statusFilter): ?>
                <a href="orders.php" class="btn btn-outline">✕ Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="card" style="padding:0; overflow:hidden;">
                <?php if (empty($orders)): ?>
            <div style="text-align:center; padding:4rem 2rem;">
                <div style="font-size:4rem; margin-bottom:1rem; opacity:0.3;">📦</div>
                        <h3>No orders found</h3>
                <p style="color:var(--text-secondary);">
                            <?php if ($searchQuery || $statusFilter): ?>
                                Try adjusting your filters or search terms.
                            <?php else: ?>
                                Orders will appear here once customers start placing them.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): 
                            $badgeClass = 'orange';
                            if ($order['display_order'] == 2) $badgeClass = 'blue';
                            elseif ($order['display_order'] >= 3 && $order['display_order'] <= 4) $badgeClass = 'orange';
                            elseif ($order['display_order'] == 5) $badgeClass = 'green';
                                ?>
                        <tr onclick="window.location.href='order-detail.php?id=<?php echo $order['order_id']; ?>'" style="cursor:pointer;">
                                        <td>
                                <span style="font-family:monospace; font-weight:600; color:var(--ms-blue);"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><span style="color:var(--text-secondary); font-size:0.85rem;"><?php echo htmlspecialchars($order['email']); ?></span></td>
                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                    <span class="badge badge-<?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($order['status_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($order['tracking_number']): ?>
                                        <span style="font-family: monospace; font-size: 0.85rem; color:var(--text-primary);">
                                                    <?php echo htmlspecialchars($order['tracking_number']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--color-dark-gray); font-size: 0.85rem;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                    <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline btn-sm" onclick="event.stopPropagation();">
                                                View →
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
            <div style="padding:1rem; display:flex; justify-content:center; gap:0.5rem; border-top:1px solid var(--glass-border);">
                        <?php
                        $queryParams = [];
                        if ($searchQuery) $queryParams['search'] = $searchQuery;
                        if ($statusFilter) $queryParams['status'] = $statusFilter;
                        
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
                        $end = min($totalPages, $page + 2);
                        
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