<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
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

// Get statistics
try {
    // Total orders
    $stmt = $db->query("SELECT COUNT(*) as total FROM orders");
    $totalOrders = $stmt->fetch()['total'];
    
    // Pending orders
    $stmt = $db->query("SELECT COUNT(*) as total FROM orders WHERE status_id IN (1,2)");
    $pendingOrders = $stmt->fetch()['total'];
    
    // Results ready
    $stmt = $db->query("SELECT COUNT(*) as total FROM orders WHERE status_id = 5");
    $resultsReady = $stmt->fetch()['total'];
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // Recent orders
    $stmt = $db->query("
        SELECT o.order_id, o.order_number, o.order_date, os.status_name, u.full_name, u.email
        FROM orders o
        JOIN order_status os ON o.status_id = os.status_id
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC
        LIMIT 10
    ");
    $recentOrders = $stmt->fetchAll();
    
    // Orders by status
    $stmt = $db->query("
        SELECT os.status_name, COUNT(*) as count
        FROM orders o
        JOIN order_status os ON o.status_id = os.status_id
        GROUP BY os.status_name, os.display_order
        ORDER BY os.display_order
    ");
    $ordersByStatus = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $error = "Error loading dashboard data.";
}

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LuckyGeneMDx</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }
        .stat-value { font-size: 2.5rem; font-weight: 700; color: var(--color-medical-teal); margin: 0.5rem 0; }
        .stat-label { color: var(--color-dark-gray); font-size: 0.9rem; }
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-medium-gray); }
        .table th { font-weight: 600; color: var(--color-primary-deep-blue); background: var(--color-light-gray); }
        .table tr:hover { background: var(--color-light-gray); }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include 'sidenav.php'; ?>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <h1 style="margin-bottom: 0.25rem;">Dashboard</h1>
                    <p style="color: var(--color-dark-gray); margin: 0;">Welcome back, <?php echo htmlspecialchars($adminName); ?>!</p>
                </div>
                <div>
                    <span style="color: var(--color-dark-gray); font-size: 0.9rem;">
                        <?php echo date('l, F j, Y'); ?>
                    </span>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo number_format($totalOrders); ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        All time orders
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Pending Orders</div>
                    <div class="stat-value" style="color: #f39c12;"><?php echo number_format($pendingOrders); ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        Awaiting processing
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Results Ready</div>
                    <div class="stat-value" style="color: #27ae60;"><?php echo number_format($resultsReady); ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        Available to view
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value" style="color: var(--color-soft-purple);"><?php echo number_format($totalUsers); ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        Registered accounts
                    </div>
                </div>
            </div>
            
            <!-- Orders by Status -->
            <div class="content-card">
                <h3 style="margin-bottom: 1.5rem;">Orders by Status</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <?php foreach($ordersByStatus as $status): ?>
                        <div style="padding: 1rem; background: var(--color-light-gray); border-radius: var(--radius-sm);">
                            <div style="font-size: 1.75rem; font-weight: 600; color: var(--color-medical-teal);">
                                <?php echo $status['count']; ?>
                            </div>
                            <div style="font-size: 0.9rem; color: var(--color-dark-gray);">
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0;">Recent Orders</h3>
                    <a href="orders.php" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">View All</a>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 2rem; color: var(--color-dark-gray);">
                                        No orders yet
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($recentOrders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo htmlspecialchars($order['status_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" style="color: var(--color-medical-teal);">
                                                View →
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
