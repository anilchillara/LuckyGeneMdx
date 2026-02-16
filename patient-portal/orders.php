<?php
define('LUCKYGENEMXD', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
session_start();
setSecurityHeaders();

// Check patient authentication
if (!isset($_SESSION['user_id'])) {
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
$userId = $_SESSION['user_id'];

// Get user information
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    
    // Get all user's orders
    $orders = $orderModel->getUserOrders($userId);
    
} catch(PDOException $e) {
    error_log("Patient Orders Error: " . $e->getMessage());
    $orders = [];
}

$userName = $user['full_name'];
$firstName = explode(' ', $userName)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - LuckyGeneMDx Patient Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .portal-wrapper { display: flex; min-height: 100vh; }
        .portal-sidebar {
            width: 260px;
            background: var(--color-primary-deep-blue);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .portal-sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .portal-sidebar-header h2 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .portal-sidebar-user { font-size: 0.85rem; opacity: 0.8; }
        .portal-nav { margin-top: 2rem; }
        .portal-nav-item {
            display: block;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
        }
        .portal-nav-item:hover, .portal-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--color-medical-teal);
        }
        .portal-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            background: var(--color-light-gray);
        }
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .order-card {
            padding: 1.5rem;
            background: var(--color-light-gray);
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            transition: all var(--transition-normal);
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .order-status-badge {
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
    <div class="portal-wrapper">
        <!-- INCLUDE RESPONSIVE SIDEBAR -->
        <?php include 'includes/portal-sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="portal-main">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
            <h1 style="color: white; margin-bottom: 0.5rem;">My Orders</h1>
                <p style="opacity: 0.9; margin: 0">
                    View and track all your screening kit orders
                </p>
            </div>
            
            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="content-card">
                    <div class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <h3>No Orders Yet</h3>
                        <p style="margin-bottom: 1.5rem;">
                            You haven't placed any orders yet. Order your first screening kit to get started.
                        </p>
                        <a href="../request-kit.php" class="btn btn-primary btn-large">
                            Order Screening Kit - $99
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="content-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2 style="margin: 0;"><?php echo count($orders); ?> Order<?php echo count($orders) > 1 ? 's' : ''; ?></h2>
                        <a href="../request-kit.php" class="btn btn-primary">
                            + New Order
                        </a>
                    </div>
                    
                    <?php foreach($orders as $order): 
                        $badgeClass = 'badge-received';
                        $badgeText = 'Order Received';
                        
                        switch($order['status_id']) {
                            case 2:
                                $badgeClass = 'badge-shipped';
                                $badgeText = 'Kit Shipped';
                                break;
                            case 3:
                                $badgeClass = 'badge-processing';
                                $badgeText = 'Sample Received';
                                break;
                            case 4:
                                $badgeClass = 'badge-processing';
                                $badgeText = 'Processing';
                                break;
                            case 5:
                                $badgeClass = 'badge-ready';
                                $badgeText = 'Results Ready';
                                break;
                        }
                    ?>
                        <div class="order-card">
                            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-weight: 600; font-size: 1.125rem; margin-bottom: 0.5rem;">
                                        Order #<?php echo htmlspecialchars($order['order_number']); ?>
                                    </div>
                                    <div style="font-size: 0.9rem; color: var(--color-dark-gray);">
                                        Placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                                    </div>
                                </div>
                                <span class="order-status-badge <?php echo $badgeClass; ?>">
                                    <?php echo $badgeText; ?>
                                </span>
                            </div>
                            
                            <div style="padding-top: 1rem; border-top: 1px solid var(--color-medium-gray); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <div style="font-size: 0.85rem; color: var(--color-dark-gray);">
                                        Total: <strong>${<?php echo number_format($order['price'], 2); ?></strong>
                                    </div>
                                    <?php if ($order['status_id'] == 5): ?>
                                        <div style="color: #27ae60; font-weight: 600; font-size: 0.9rem; margin-top: 0.25rem;">
                                            ✅ Results available
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="../track-order.php?order=<?php echo urlencode($order['order_number']); ?>" 
                                       class="btn btn-outline">
                                        Track Order
                                    </a>
                                    <?php if ($order['status_id'] == 5): ?>
                                        <a href="results.php" class="btn btn-primary">
                                            View Results →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Order Again CTA -->
                <div class="content-card" style="background: var(--gradient-hero); color: white; text-align: center;">
                    <h3 style="color: white; margin-bottom: 1rem;">Need Another Screening?</h3>
                    <p style="opacity: 0.9; margin-bottom: 1.5rem;">
                        Order additional screening kits for family members or updated testing.
                    </p>
                    <a href="../request-kit.php" class="btn btn-large" style="background: white; color: var(--color-primary-deep-blue);">
                        Order New Kit - $99
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
