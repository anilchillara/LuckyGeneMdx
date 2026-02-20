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

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    $orders = $orderModel->getUserOrders($userId);
} catch(PDOException $e) {
    error_log("Patient Orders Error: " . $e->getMessage());
    $orders = [];
}

$firstName = explode(' ', $user['full_name'])[0];
$initials  = strtoupper(substr($user['full_name'],0,1));
if (strpos($user['full_name'],' ')!==false) $initials .= strtoupper(substr(explode(' ',$user['full_name'])[1],0,1));
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
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <!-- Portal Navbar -->
    <nav class="navbar">
      <a href="../index.php" class="brand"><span>🧬</span> LuckyGeneMDx</a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link active">My Orders</a>
        <a href="results.php" class="nav-link">Results</a>
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
            <h1>My Orders</h1>
            <p>Track your screening kits and view order history.</p>
        </div>

            <?php if (!empty($orders)): ?>
            <div class="grid" style="margin-bottom: 2rem;">
                <div class="card stat-card col-span-4">
                    <div class="stat-val"><?php echo count($orders); ?></div>
                    <div class="stat-lbl">Total Orders</div>
                </div>
                <div class="card stat-card col-span-4">
                    <div class="stat-val"><?php echo count(array_filter($orders, fn($o) => in_array($o['status_id'], [2, 3, 4]))); ?></div>
                    <div class="stat-lbl">In Progress</div>
                </div>
                <div class="card stat-card col-span-4">
                    <div class="stat-val"><?php echo count(array_filter($orders, fn($o) => $o['status_id'] == 5)); ?></div>
                    <div class="stat-lbl">Results Ready</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="card" style="text-align:center; padding: 4rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.2;">📦</div>
                    <h2 style="margin-bottom: 1rem;">No Orders Yet</h2>
                    <p style="max-width: 500px; margin: 0 auto 2rem auto;">
                        Start your journey to genetic clarity. Order your first screening kit today.
                    </p>
                    <a href="../request-kit.php" class="btn">
                        Order Screening Kit &mdash; $99
                    </a>
                </div>
            <?php else: ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3>Order History</h3>
                    <a href="../request-kit.php" class="btn">+ New Order</a>
                </div>

                <?php foreach($orders as $order): 
                    $badgeClass = 'orange';
                    $statusText = 'Received';
                    
                    switch($order['status_id']) {
                        case 2: $badgeClass = 'blue'; $statusText = 'Shipped'; break;
                        case 3: $badgeClass = 'orange'; $statusText = 'Sample Received'; break;
                        case 4: $badgeClass = 'orange'; $statusText = 'Processing'; break;
                        case 5: $badgeClass = 'green'; $statusText = 'Results Ready'; break;
                    }
                ?>
                <div class="card">
                    <div style="display:flex; justify-content:space-between; margin-bottom:1rem; border-bottom:1px solid var(--glass-border); padding-bottom:1rem;">
                        <div>
                            <h4 style="margin: 0;">
                                Order #<?php echo htmlspecialchars($order['order_number']); ?>
                            </h4>
                            <span style="font-size: 0.9rem; color: var(--text-secondary);">
                                <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; margin-bottom:1.5rem;">
                        <div>
                            <div class="stat-lbl">Total Amount</div>
                            <div style="font-weight:600;">$<?php echo number_format($order['price'], 2); ?></div>
                        </div>
                        <div>
                            <div class="stat-lbl">Shipping To</div>
                            <div style="font-weight:600;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                        </div>
                        <div>
                            <div class="stat-lbl">Last Update</div>
                            <div style="font-weight:600;"><?php echo date('M j', strtotime($order['order_date'])); ?></div>
                        </div>
                    </div>

                    <div style="display:flex; gap:1rem;">
                        <a href="../track-order.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline">
                            Track Status
                        </a>
                        <?php if ($order['status_id'] == 5): ?>
                            <a href="results.php" class="btn">View Results</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
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