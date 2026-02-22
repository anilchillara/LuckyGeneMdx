<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
session_start();
setSecurityHeaders();

if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset(); session_destroy(); header('Location: login.php?timeout=1'); exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();

try {
    $totalOrders   = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status_id IN (1,2)")->fetchColumn();
    $resultsReady  = $db->query("SELECT COUNT(*) FROM orders WHERE status_id = 5")->fetchColumn();
    $totalUsers    = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $recentOrders  = $db->query("
        SELECT o.order_id, o.order_number, o.order_date, o.price, os.status_name, os.status_id, u.full_name, u.email
        FROM orders o
        JOIN order_status os ON o.status_id = os.status_id
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.order_date DESC LIMIT 10
    ")->fetchAll();
    $ordersByStatus = $db->query("
        SELECT os.status_name, COUNT(*) as cnt
        FROM orders o JOIN order_status os ON o.status_id = os.status_id
        GROUP BY os.status_name, os.display_order ORDER BY os.display_order
    ")->fetchAll();

    // Get order trends (last 30 days)
    $orderTrends = $db->query("
        SELECT DATE(order_date) as date, COUNT(*) as count 
        FROM orders 
        WHERE order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
        GROUP BY DATE(order_date) 
        ORDER BY date ASC
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch(PDOException $e) { error_log($e->getMessage()); $totalOrders=$pendingOrders=$resultsReady=$totalUsers=0; $recentOrders=$ordersByStatus=[]; }

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_',' ',$_SESSION['admin_role']));
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | LuckyGeneMDx</title>
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="brand">
    <span>🧬</span> <?php echo htmlspecialchars(SITE_NAME); ?> <span class="admin-badge">Admin</span>
  </a>
  <div class="nav-items">
    <a href="index.php" class="nav-link active">Dashboard</a>
    <a href="orders.php" class="nav-link">Orders</a>
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
            <h1>Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($adminName); ?> • <?php echo htmlspecialchars($adminRole); ?></p>
        </div>
        <a href="orders.php" class="btn">View All Orders</a>
    </div>

    <!-- Stats Grid -->
    <div class="grid">
        <div class="card stat-card col-span-3 blue">
            <div class="stat-lbl">Total Orders</div>
            <div class="stat-val"><?php echo number_format($totalOrders); ?></div>
            <div class="stat-desc">All time</div>
        </div>
        <div class="card stat-card col-span-3 orange">
            <div class="stat-lbl">Pending</div>
            <div class="stat-val"><?php echo number_format($pendingOrders); ?></div>
            <div class="stat-desc">Awaiting processing</div>
        </div>
        <div class="card stat-card col-span-3 green">
            <div class="stat-lbl">Results Ready</div>
            <div class="stat-val"><?php echo number_format($resultsReady); ?></div>
            <div class="stat-desc">Published</div>
        </div>
        <div class="card stat-card col-span-3 red">
            <div class="stat-lbl">Total Users</div>
            <div class="stat-val"><?php echo number_format($totalUsers); ?></div>
            <div class="stat-desc">Registered accounts</div>
        </div>
    </div>

    <div class="grid" style="margin-top: 2rem;">
        <!-- Order Trends Chart -->
        <div class="col-span-12">
            <div class="card">
                <div class="header-section">
                    <h3>Order Trends (Last 30 Days)</h3>
                </div>
                <div style="position: relative; height: 250px; width: 100%;">
                    <canvas id="orderTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-span-12">
            <div class="card">
                <div class="header-section">
                    <h3>Recent Orders</h3>
                </div>
                <div style="overflow-x:auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recentOrders)): ?>
                            <tr><td colspan="7" style="text-align:center;padding:2rem;">No orders yet</td></tr>
                            <?php else: ?>
                            <?php foreach($recentOrders as $o):
                                $badgeClass = 'orange';
                                $statusText = 'Received';
                                switch($o['status_id']){
                                    case 2: $badgeClass='blue'; $statusText='Shipped'; break;
                                    case 3: $badgeClass='orange'; $statusText='Sample Rcvd'; break;
                                    case 4: $badgeClass='orange'; $statusText='Processing'; break;
                                    case 5: $badgeClass='green'; $statusText='Ready'; break;
                                }
                            ?>
                            <tr>
                                <td style="font-family:monospace; font-weight:600;"><?php echo htmlspecialchars($o['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($o['full_name']); ?></td>
                                <td style="color:var(--text-secondary);"><?php echo htmlspecialchars($o['email']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($o['order_date'])); ?></td>
                                <td style="font-weight:600;">$<?php echo number_format($o['price'],2); ?></td>
                                <td><span class="badge badge-<?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                                <td><a href="order-detail.php?id=<?php echo $o['order_id']; ?>" class="btn btn-outline btn-sm">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Chart.js Implementation
    const ctx = document.getElementById('orderTrendsChart').getContext('2d');
    
    // Prepare data
    const dates = <?php echo json_encode(array_keys($orderTrends)); ?>;
    const counts = <?php echo json_encode(array_values($orderTrends)); ?>;
    
    // Fill in missing dates with 0
    const today = new Date();
    const last30Days = [];
    const dataPoints = [];
    
    for (let i = 29; i >= 0; i--) {
        const d = new Date(today);
        d.setDate(d.getDate() - i);
        const dateString = d.toISOString().split('T')[0];
        last30Days.push(dateString);
        
        const index = dates.indexOf(dateString);
        dataPoints.push(index !== -1 ? counts[index] : 0);
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: last30Days,
            datasets: [{
                label: 'Orders',
                data: dataPoints,
                borderColor: '#0078D4',
                backgroundColor: 'rgba(0, 120, 212, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
</body>
</html>
