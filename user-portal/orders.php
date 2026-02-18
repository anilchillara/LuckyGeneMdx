<?php
// define('luckygenemdx', true);
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

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    $orders = $orderModel->getUserOrders($userId);
} catch(PDOException $e) {
    error_log("Patient Orders Error: " . $e->getMessage());
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - LuckyGeneMDx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        /* Page Specific Styles */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: -3rem; /* Overlap hero */
            margin-bottom: 3rem;
            position: relative;
            z-index: 2;
        }
        .stat-card {
            background: var(--color-white);
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            text-align: center;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .stat-val { font-family: var(--font-primary); font-size: 2.5rem; font-weight: 700; color: var(--color-medical-teal); line-height: 1; margin-bottom: 0.5rem; }
        .stat-lbl { color: var(--color-dark-gray); font-size: 0.9rem; font-weight: 500; }

        .order-card {
            background: var(--color-white);
            border: 1px solid var(--color-medium-gray);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            transition: all var(--transition-normal);
        }
        .order-card:hover {
            border-color: var(--color-medical-teal);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .order-header { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-light-gray); padding-bottom: 1rem; }
        
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 50px;
            font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-received { background: #e3f2fd; color: #1565c0; }
        .badge-shipped { background: rgba(0, 179, 164, 0.12); color: var(--color-medical-teal); }
        .badge-processing { background: #fff3e0; color: #e65100; }
        .badge-ready { background: #e8f5e9; color: #2e7d32; }

        .order-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .meta-label { font-size: 0.8rem; color: var(--color-dark-gray); margin-bottom: 0.25rem; }
        .meta-value { font-weight: 600; color: var(--color-primary-deep-blue); }
        
        .order-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="portal-page">
        <div class="portal-hero">
            <div class="container">
                <h1>My Orders</h1>
                <p>Track your screening kits and view order history.</p>
            </div>
        </div>

        <div class="container">
            
            <?php if (!empty($orders)): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-val"><?php echo count($orders); ?></div>
                    <div class="stat-lbl">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-val"><?php echo count(array_filter($orders, fn($o) => in_array($o['status_id'], [2, 3, 4]))); ?></div>
                    <div class="stat-lbl">In Progress</div>
                </div>
                <div class="stat-card">
                    <div class="stat-val"><?php echo count(array_filter($orders, fn($o) => $o['status_id'] == 5)); ?></div>
                    <div class="stat-lbl">Results Ready</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="content-card text-center mb-5">
                    <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.2;">📦</div>
                    <h2 style="margin-bottom: 1rem;">No Orders Yet</h2>
                    <p style="max-width: 500px; margin: 0 auto 2rem auto;">
                        Start your journey to genetic clarity. Order your first screening kit today.
                    </p>
                    <a href="../request-kit.php" class="btn btn-primary btn-large">
                        Order Screening Kit &mdash; $99
                    </a>
                </div>
            <?php else: ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Order History</h3>
                    <a href="../request-kit.php" class="btn btn-primary">+ New Order</a>
                </div>

                <?php foreach($orders as $order): 
                    $badgeClass = 'badge-received';
                    $badgeIcon = '📋';
                    $statusText = 'Received';
                    
                    switch($order['status_id']) {
                        case 2: $badgeClass = 'badge-shipped'; $badgeIcon = '🚚'; $statusText = 'Shipped'; break;
                        case 3: $badgeClass = 'badge-processing'; $badgeIcon = '🧪'; $statusText = 'Sample Received'; break;
                        case 4: $badgeClass = 'badge-processing'; $badgeIcon = '🔬'; $statusText = 'Processing'; break;
                        case 5: $badgeClass = 'badge-ready'; $badgeIcon = '✅'; $statusText = 'Results Ready'; break;
                    }
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h4 style="margin: 0; color: var(--color-primary-deep-blue);">
                                Order #<?php echo htmlspecialchars($order['order_number']); ?>
                            </h4>
                            <span style="font-size: 0.9rem; color: var(--color-dark-gray);">
                                <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <span><?php echo $badgeIcon; ?></span> <?php echo $statusText; ?>
                        </span>
                    </div>

                    <div class="order-meta">
                        <div>
                            <div class="meta-label">Total Amount</div>
                            <div class="meta-value">$<?php echo number_format($order['price'], 2); ?></div>
                        </div>
                        <div>
                            <div class="meta-label">Shipping To</div>
                            <div class="meta-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                        <div>
                            <div class="meta-label">Last Update</div>
                            <div class="meta-value"><?php echo date('M j', strtotime($order['order_date'])); // Simplified ?></div>
                        </div>
                    </div>

                    <div class="order-actions">
                        <a href="../track-order.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline">
                            Track Status
                        </a>
                        <?php if ($order['status_id'] == 5): ?>
                            <a href="results.php" class="btn btn-primary">View Results</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>