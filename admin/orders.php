<?php
define('LUCKYGENEMXD', true);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - LuckyGeneMdx Admin</title>
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
        .table tbody tr { cursor: pointer; }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-received { background: #cce5ff; color: #004085; }
        .badge-shipped { background: #d1ecf1; color: #0c5460; }
        .badge-processing { background: #fff3cd; color: #856404; }
        .badge-ready { background: #d4edda; color: #155724; }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
            background: white;
            border-radius: 0 0 var(--radius-md) var(--radius-md);
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
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>🧬 LuckyGeneMdx</h2>
                <div class="admin-sidebar-user">
                    <?php echo htmlspecialchars($adminName); ?><br>
                    <small><?php echo ucwords(str_replace('_', ' ', $_SESSION['admin_role'])); ?></small>
                </div>
            </div>
            
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">📊 Dashboard</a>
                <a href="orders.php" class="admin-nav-item active">📦 Orders</a>
                <a href="upload-results.php" class="admin-nav-item">📄 Upload Results</a>
                <a href="users.php" class="admin-nav-item">👥 Users</a>
                <a href="testimonials.php" class="admin-nav-item">💬 Testimonials</a>
                <a href="blog.php" class="admin-nav-item">📰 Blog</a>
                <a href="resources.php" class="admin-nav-item">📖 Resources</a>
                <a href="settings.php" class="admin-nav-item">⚙️ Settings</a>
                <a href="logout.php" class="admin-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 style="margin-bottom: 0.25rem;">Order Management</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">
                    <?php echo number_format($totalOrders); ?> total orders
                </p>
            </div>
            
            <!-- Filters -->
            <form method="GET" action="" class="filters-bar">
                <div class="filter-group">
                    <label class="form-label">Search</label>
                    <input 
                        type="text" 
                        name="search" 
                        class="form-input" 
                        placeholder="Order #, Name, Email..."
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                    >
                </div>
                
                <div class="filter-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <?php foreach($statuses as $status): ?>
                            <option value="<?php echo $status['status_id']; ?>" <?php echo $statusFilter == $status['status_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group" style="flex: 0;">
                    <button type="submit" class="btn btn-primary">
                        🔍 Filter
                    </button>
                </div>
                
                <?php if ($searchQuery || $statusFilter): ?>
                <div class="filter-group" style="flex: 0;">
                    <a href="orders.php" class="btn btn-outline">
                        ✕ Clear
                    </a>
                </div>
                <?php endif; ?>
            </form>
            
            <!-- Orders Table -->
            <div class="table-container">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <h3>No orders found</h3>
                        <p>
                            <?php if ($searchQuery || $statusFilter): ?>
                                Try adjusting your filters or search terms.
                            <?php else: ?>
                                Orders will appear here once customers start placing them.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Tracking</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): 
                                    $badgeClass = 'badge-received';
                                    if ($order['display_order'] == 2) $badgeClass = 'badge-shipped';
                                    elseif ($order['display_order'] >= 3 && $order['display_order'] <= 4) $badgeClass = 'badge-processing';
                                    elseif ($order['display_order'] == 5) $badgeClass = 'badge-ready';
                                ?>
                                    <tr onclick="window.location.href='order-detail.php?id=<?php echo $order['order_id']; ?>'">
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                                        <td><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($order['status_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($order['tracking_number']): ?>
                                                <span style="font-family: monospace; font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($order['tracking_number']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color: var(--color-dark-gray); font-size: 0.85rem;">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" 
                                               style="color: var(--color-medical-teal); font-weight: 500;"
                                               onclick="event.stopPropagation();">
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
                    <div class="pagination">
                        <?php
                        $queryParams = [];
                        if ($searchQuery) $queryParams['search'] = $searchQuery;
                        if ($statusFilter) $queryParams['status'] = $statusFilter;
                        
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
</body>
</html>
