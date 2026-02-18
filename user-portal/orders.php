<?php
define('luckygenemdx', true);
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
        .portal-page {
            min-height: 100vh;
            background: var(--color-light-gray);
            padding-bottom: 4rem;
        }
        
        .portal-hero {
            background: var(--gradient-hero);
            color: white;
            padding: 4rem 0 3rem;
            margin-bottom: 3rem;
        }
        
        .portal-hero h1 {
            color: white;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
        }
        
        .portal-hero p {
            opacity: 0.9;
            font-size: 1.125rem;
        }
        
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .order-card {
            padding: 1.5rem;
            background: var(--color-light-gray);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--color-medical-teal);
        }
        
        .order-status-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .badge-received { background: #e3f2fd; color: #1565c0; }
        .badge-shipped { background: #e0f2f1; color: #00695c; }
        .badge-processing { background: #fff3e0; color: #e65100; }
        .badge-ready { background: #e8f5e9; color: #2e7d32; }
        
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: var(--color-dark-gray);
        }
        
        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            opacity: 0.3;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--color-medical-teal);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--color-dark-gray);
            font-size: 0.9rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header with user dropdown -->
    <?php include '../includes/header.php'; ?>
    
    <div class="portal-page">
        <!-- Hero Section -->
        <div class="portal-hero">
            <div class="container">
                <h1>My Orders</h1>
                <p>View and track all your screening kit orders</p>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="container" style="max-width: 1200px;">
            
            <?php if (!empty($orders)): ?>
                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($orders); ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($orders, fn($o) => $o['status_id'] == 5)); ?></div>
                        <div class="stat-label">Results Ready</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($orders, fn($o) => in_array($o['status_id'], [2, 3, 4]))); ?></div>
                        <div class="stat-label">In Progress</div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="content-card">
                    <div class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <h2 style="margin-bottom: 1rem;">No Orders Yet</h2>
                        <p style="margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                            You haven't placed any orders yet. Order your first screening kit to get started on your genetic health journey.
                        </p>
                        <a href="../request-kit.php" class="btn btn-primary btn-large">
                            Order Screening Kit - $99
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="content-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid var(--color-light-gray);">
                        <div>
                            <h2 style="margin: 0 0 0.5rem 0;">Order History</h2>
                            <p style="color: var(--color-dark-gray); margin: 0;">
                                <?php echo count($orders); ?> order<?php echo count($orders) > 1 ? 's' : ''; ?> placed
                            </p>
                        </div>
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
                                    <div style="font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-primary-deep-blue);">
                                        Order #<?php echo htmlspecialchars($order['order_number']); ?>
                                    </div>
                                    <div style="font-size: 0.95rem; color: var(--color-dark-gray);">
                                        📅 Placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                                    </div>
                                </div>
                                <span class="order-status-badge <?php echo $badgeClass; ?>">
                                    <?php echo $badgeText; ?>
                                </span>
                            </div>
                            
                            <div style="padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                <div>
                                    <div style="font-size: 0.9rem; color: var(--color-dark-gray);">
                                        Total: <strong style="color: var(--color-primary-deep-blue); font-size: 1.1rem;">$<?php echo number_format($order['price'], 2); ?></strong>
                                    </div>
                                    <?php if ($order['status_id'] == 5): ?>
                                        <div style="color: #2e7d32; font-weight: 600; font-size: 0.95rem; margin-top: 0.5rem;">
                                            ✅ Results available
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    <a href="../track-order.php?order=<?php echo urlencode($order['order_number']); ?>" 
                                       class="btn btn-outline">
                                        📍 Track Order
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
                <div class="content-card" style="background: linear-gradient(135deg, var(--color-primary-deep-blue) 0%, var(--color-medical-teal) 100%); color: white; text-align: center;">
                    <h3 style="color: white; margin-bottom: 1rem; font-size: 1.75rem;">Need Another Screening?</h3>
                    <p style="opacity: 0.95; margin-bottom: 2rem; font-size: 1.05rem;">
                        Order additional screening kits for family members or updated testing.
                    </p>
                    <a href="../request-kit.php" class="btn btn-large" style="background: white; color: var(--color-primary-deep-blue); font-weight: 600;">
                        Order New Kit - $99
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>