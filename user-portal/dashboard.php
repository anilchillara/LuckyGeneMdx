<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
session_start();
setSecurityHeaders();

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset(); session_destroy(); header('Location: login.php?timeout=1'); exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();
$orderModel = new Order();
$userId = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    if (!$user) { session_unset(); session_destroy(); header('Location: login.php'); exit; }
    $orders       = $orderModel->getUserOrders($userId);
    $totalOrders  = count($orders);
    $pendingOrders = 0; $resultsReady = 0;
    foreach ($orders as $o) { if ($o['status_id'] < 5) $pendingOrders++; if ($o['status_id'] == 5) $resultsReady++; }
    $recentOrder = !empty($orders) ? $orders[0] : null;
    $stmt = $db->prepare("SELECT r.*, o.order_number, o.order_date FROM results r JOIN orders o ON r.order_id = o.order_id WHERE o.user_id = :user_id ORDER BY r.upload_date DESC");
    $stmt->execute([':user_id' => $userId]);
    $results = $stmt->fetchAll();
} catch(PDOException $e) { error_log($e->getMessage()); $orders = []; $results = []; }

$firstName = explode(' ', $user['full_name'])[0];
$initials  = strtoupper(substr($user['full_name'],0,1));
if (strpos($user['full_name'],' ')!==false) $initials .= strtoupper(substr(explode(' ',$user['full_name'])[1],0,1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard | LuckyGenes</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/main.css">
<link rel="stylesheet" href="../css/portal.css">
</head>
<body>

<!-- TOP NAV -->
<nav class="navbar">
  <a href="../index.php" class="brand">
    <img src="../assets/images/logo_small.png" alt="Logo" style="height: 32px; width: auto;"> <?php echo htmlspecialchars(SITE_NAME); ?>
  </a>
  <div class="nav-items">
    <a href="index.php" class="nav-link active">Dashboard</a>
    <a href="orders.php" class="nav-link">My Orders</a>
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

    <!-- Header -->
    <div class="header-section">
        <h1>Welcome back, <?php echo htmlspecialchars($firstName); ?></h1>
        <p><?php echo date('l, F jS'); ?> • Patient Portal</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid">
        <div class="card stat-card col-span-4 blue">
            <div class="stat-lbl">Total Orders</div>
            <div class="stat-val"><?php echo $totalOrders; ?></div>
            <div class="stat-desc">Lifetime history</div>
        </div>
        <div class="card stat-card col-span-4 orange">
            <div class="stat-lbl">In Progress</div>
            <div class="stat-val"><?php echo $pendingOrders; ?></div>
            <div class="stat-desc">Processing at lab</div>
        </div>
        <div class="card stat-card col-span-4 green">
            <div class="stat-lbl">Results Ready</div>
            <div class="stat-val"><?php echo $resultsReady; ?></div>
            <div class="stat-desc">Available for download</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid mt-2">
        
        <!-- Left Column: Recent Activity -->
        <div class="col-span-8">
            <div class="card">
                <div class="card-header-flex">
                    <h3>Recent Activity</h3>
                    <a href="orders.php" class="text-teal">View All Orders →</a>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="text-center p-2">
                        <div class="fs-2 mb-1">📦</div>
                        <p>No orders yet. Start your journey today.</p>
                        <a href="../request-kit.php" class="btn btn-primary">Order Kit</a>
                    </div>
                <?php else: ?>
                    <?php foreach(array_slice($orders, 0, 3) as $o): 
                        $statusClass = 'orange';
                        $statusText = 'Processing';
                        if ($o['status_id'] == 2) { $statusClass = 'blue'; $statusText = 'Shipped'; }
                        if ($o['status_id'] == 5) { $statusClass = 'green'; $statusText = 'Complete'; }
                    ?>
                    <div class="list-item">
                        <div>
                            <h4>Order #<?php echo htmlspecialchars($o['order_number']); ?></h4>
                            <p><?php echo date('M j, Y', strtotime($o['order_date'])); ?></p>
                        </div>
                        <span class="badge badge-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Quick Actions -->
        <div class="col-span-4">
            <div class="card">
                <h3 class="mb-1">Quick Actions</h3>
                <div class="action-grid">
                    <a href="../request-kit.php" class="action-btn">
                        <span class="action-icon">🛒</span>
                        <span class="action-label">New Order</span>
                    </a>
                    <a href="results.php" class="action-btn">
                        <span class="action-icon">📄</span>
                        <span class="action-label">My Results</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>