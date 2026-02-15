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
    
    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    // Get user's orders
    $orders = $orderModel->getUserOrders($userId);
    
    // Get order statistics
    $totalOrders = count($orders);
    $pendingOrders = 0;
    $resultsReady = 0;
    
    foreach ($orders as $order) {
        if ($order['status_id'] < 5) {
            $pendingOrders++;
        }
        if ($order['status_id'] == 5) {
            $resultsReady++;
        }
    }
    
    // Get most recent order
    $recentOrder = !empty($orders) ? $orders[0] : null;
    
    // Check if any results are available
    $stmt = $db->prepare("
        SELECT r.*, o.order_number, o.order_date 
        FROM results r
        JOIN orders o ON r.order_id = o.order_id
        WHERE o.user_id = :user_id
        ORDER BY r.upload_date DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    $results = $stmt->fetchAll();
    
} catch(PDOException $e) {
    error_log("Patient Dashboard Error: " . $e->getMessage());
    $orders = [];
    $results = [];
}

$userName = $user['full_name'];
$firstName = explode(' ', $userName)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - LuckyGeneMDx Patient Portal</title>
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
        .welcome-banner {
            background: var(--gradient-hero);
            color: white;
            padding: 3rem 2rem;
            border-radius: var(--radius-lg);
            margin-bottom: 2rem;
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
            transition: all var(--transition-normal);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        .stat-value { 
            font-size: 2.5rem; 
            font-weight: 700; 
            color: var(--color-medical-teal); 
            margin: 0.5rem 0; 
        }
        .stat-label { 
            color: var(--color-dark-gray); 
            font-size: 0.9rem; 
        }
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
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
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .quick-action-card {
            background: var(--color-light-gray);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            text-align: center;
            transition: all var(--transition-normal);
            text-decoration: none;
            color: var(--color-primary-deep-blue);
        }
        .quick-action-card:hover {
            background: var(--color-medical-teal);
            color: white;
            transform: translateY(-2px);
        }
        .quick-action-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--color-dark-gray);
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        @media (max-width: 768px) {
            .portal-sidebar {
                transform: translateX(-100%);
                transition: transform var(--transition-normal);
            }
            .portal-sidebar.active {
                transform: translateX(0);
            }
            .portal-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="portal-wrapper">
        <!-- Sidebar -->
        <aside class="portal-sidebar">
            <div class="portal-sidebar-header">
                <h2>🧬 LuckyGeneMDx</h2>
                <div class="portal-sidebar-user">
                    <?php echo htmlspecialchars($firstName); ?><br>
                    <small><?php echo htmlspecialchars($user['email']); ?></small>
                </div>
            </div>
            
            <nav class="portal-nav">
                <a href="index.php" class="portal-nav-item active">🏠 Dashboard</a>
                <a href="orders.php" class="portal-nav-item">📦 My Orders</a>
                <a href="results.php" class="portal-nav-item">📄 My Results</a>
                <a href="settings.php" class="portal-nav-item">⚙️ Settings</a>
                <a href="../resources" class="portal-nav-item">📖 Resources</a>
                <a href="logout.php" class="portal-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="portal-main">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <h1 style="color: white; margin-bottom: 0.5rem;">Welcome back, <?php echo htmlspecialchars($firstName); ?>!</h1>
                <p style="opacity: 0.9; margin: 0;">
                    <?php if ($resultsReady > 0): ?>
                        You have <?php echo $resultsReady; ?> result<?php echo $resultsReady > 1 ? 's' : ''; ?> ready to view.
                    <?php elseif ($pendingOrders > 0): ?>
                        Your order<?php echo $pendingOrders > 1 ? 's are' : ' is'; ?> being processed.
                    <?php else: ?>
                        Your genetic health journey starts here.
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo $totalOrders; ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        All time
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value" style="color: #f39c12;"><?php echo $pendingOrders; ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        Being processed
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Results Ready</div>
                    <div class="stat-value" style="color: #27ae60;"><?php echo $resultsReady; ?></div>
                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                        Available to view
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="content-card">
                <h2 style="margin-bottom: 1.5rem;">Quick Actions</h2>
                <div class="quick-actions">
                    <a href="orders.php" class="quick-action-card">
                        <div class="quick-action-icon">📦</div>
                        <div style="font-weight: 600;">View Orders</div>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem; opacity: 0.7;">Track your kits</div>
                    </a>
                    
                    <a href="results.php" class="quick-action-card">
                        <div class="quick-action-icon">📄</div>
                        <div style="font-weight: 600;">View Results</div>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem; opacity: 0.7;">Access your reports</div>
                    </a>
                    
                    <a href="../request-kit.php" class="quick-action-card">
                        <div class="quick-action-icon">🛒</div>
                        <div style="font-weight: 600;">Order Kit</div>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem; opacity: 0.7;">New screening</div>
                    </a>
                    
                    <a href="settings.php" class="quick-action-card">
                        <div class="quick-action-icon">⚙️</div>
                        <div style="font-weight: 600;">Settings</div>
                        <div style="font-size: 0.85rem; margin-top: 0.25rem; opacity: 0.7;">Update profile</div>
                    </a>
                </div>
            </div>
            
            <!-- Recent Order -->
            <?php if ($recentOrder): ?>
            <div class="content-card">
                <h2 style="margin-bottom: 1.5rem;">Most Recent Order</h2>
                
                <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <div style="font-weight: 600; font-size: 1.125rem; margin-bottom: 0.5rem;">
                                Order #<?php echo htmlspecialchars($recentOrder['order_number']); ?>
                            </div>
                            <div style="font-size: 0.9rem; color: var(--color-dark-gray); margin-bottom: 1rem;">
                                Placed on <?php echo date('F j, Y', strtotime($recentOrder['order_date'])); ?>
                            </div>
                            <?php
                            $badgeClass = 'badge-received';
                            if ($recentOrder['status_id'] == 2) $badgeClass = 'badge-shipped';
                            elseif ($recentOrder['status_id'] >= 3 && $recentOrder['status_id'] <= 4) $badgeClass = 'badge-processing';
                            elseif ($recentOrder['status_id'] == 5) $badgeClass = 'badge-ready';
                            ?>
                            <span class="order-status-badge <?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($recentOrder['status_name']); ?>
                            </span>
                        </div>
                        <div>
                            <a href="../track-order.php?order=<?php echo urlencode($recentOrder['order_number']); ?>" class="btn btn-primary">
                                Track Order →
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($recentOrder['status_id'] == 5): ?>
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--color-medium-gray);">
                            <div style="color: #27ae60; font-weight: 600; margin-bottom: 0.5rem;">
                                ✅ Your results are ready!
                            </div>
                            <a href="results.php" class="btn btn-primary">
                                View Results →
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="content-card">
                <div class="empty-state">
                    <div class="empty-state-icon">🧬</div>
                    <h3>No Orders Yet</h3>
                    <p style="margin-bottom: 1.5rem;">
                        Ready to start your genetic health journey? Order your first screening kit today.
                    </p>
                    <a href="../request-kit.php" class="btn btn-primary btn-large">
                        Order Screening Kit - $99
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Available Results -->
            <?php if (!empty($results)): ?>
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="margin: 0;">Recent Results</h2>
                    <a href="results.php" style="color: var(--color-medical-teal); font-weight: 500;">View All →</a>
                </div>
                
                <?php foreach(array_slice($results, 0, 3) as $result): ?>
                    <div style="padding: 1.25rem; background: var(--color-light-gray); border-radius: var(--radius-sm); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                    Order #<?php echo htmlspecialchars($result['order_number']); ?>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--color-dark-gray);">
                                    Results uploaded <?php echo date('F j, Y', strtotime($result['upload_date'])); ?>
                                </div>
                            </div>
                            <a href="results.php" class="btn btn-primary">
                                View PDF →
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="content-card" style="background: #f8f9fa;">
                <h3 style="margin-bottom: 1rem;">Need Help?</h3>
                <p style="color: var(--color-dark-gray); margin-bottom: 1rem;">
                    Have questions about your screening or results? Our support team is here to help.
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="mailto:support@luckygenemxd.com" class="btn btn-outline">
                        ✉️ Email Support
                    </a>
                    <a href="tel:1-800-GENE-TEST" class="btn btn-outline">
                        📞 Call Us
                    </a>
                    <a href="../resources" class="btn btn-outline">
                        📖 FAQs & Resources
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Mobile menu toggle (if needed in future)
        // Add hamburger menu for mobile
    </script>
</body>
</html>
