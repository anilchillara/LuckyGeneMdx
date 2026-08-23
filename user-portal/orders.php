<?php
define('LuckyGenes', true);
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
    $receivedGifts = $orderModel->getReceivedGiftKits($userId);
} catch(PDOException $e) {
    error_log("Patient Orders Error: " . $e->getMessage());
    $orders = [];
    $receivedGifts = [];
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
    <title>My Orders - LuckyGenes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

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

        <?php
        // Gift claimed banner
        if (isset($_GET['gift_claimed']) && $_GET['gift_claimed'] === '1'):
            $claimedBarcode = htmlspecialchars(trim($_GET['barcode'] ?? ''));
        ?>
        <div class="card" style="background:rgba(0,179,164,0.1); border:1px solid var(--color-medical-teal); margin-bottom:1.5rem;">
            <p style="margin:0; font-weight:600; color:var(--color-medical-teal);">
                🎁 Gift kit claimed! Your kit (<code><?php echo $claimedBarcode; ?></code>) is now active and appears below.
            </p>
        </div>
        <?php endif; ?>

        <?php if (!empty($receivedGifts)): ?>
        <!-- Received Gift Kits Section -->
        <div style="margin-bottom:2rem;">
            <h3 style="margin-bottom:1rem;">🎁 Received Gift Kits</h3>
            <?php foreach ($receivedGifts as $giftKit):
                $gBadgeClass = 'orange';
                switch ($giftKit['status_id'] ?? $giftKit['kit_status_id']) {
                    case 2: $gBadgeClass = 'blue';   break;
                    case 5: $gBadgeClass = 'green';  break;
                }
                $gifterFirst = explode(' ', $giftKit['gifted_by_name'] ?? 'Someone')[0];
            ?>
            <div class="card">
                <div class="card-header-flex">
                    <div>
                        <h4 style="margin:0;">🎁 Gift from <?php echo htmlspecialchars($gifterFirst); ?></h4>
                        <span style="font-size:0.85rem; font-family:monospace; color:var(--color-text-gray); letter-spacing:1px;">
                            Barcode: <?php echo htmlspecialchars($giftKit['kit_barcode']); ?>
                        </span>
                    </div>
                    <span class="badge badge-<?php echo $gBadgeClass; ?>"><?php echo htmlspecialchars($giftKit['status_name']); ?></span>
                </div>
                <div style="display:flex; gap:1rem; margin-top:1rem;" class="flex-wrap">
                    <?php if (($giftKit['kit_status_id'] ?? 0) == 5): ?>
                        <a href="results.php" class="btn">View Results</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($orders) && empty($receivedGifts)): ?>
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
                    $kitStatusMap = [1 => 'orange', 2 => 'blue', 3 => 'orange', 4 => 'orange', 5 => 'green'];
                    $kits = $order['kits'] ?? [];

                    // Overall order badge based on most advanced kit status
                    $maxKitStatus = 1;
                    foreach ($kits as $k) { $maxKitStatus = max($maxKitStatus, (int)($k['kit_status_id'] ?? 1)); }
                    $badgeClass = $kitStatusMap[$maxKitStatus] ?? 'orange';
                    $statusText = 'Received';
                    switch($maxKitStatus) {
                        case 2: $statusText = 'Shipped';         break;
                        case 3: $statusText = 'Sample Received'; break;
                        case 4: $statusText = 'Processing';      break;
                        case 5: $statusText = 'Results Ready';   break;
                    }
                ?>
                <div class="card">
                    <div class="card-header-flex">
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

                    <!-- Per-Kit rows -->
                    <?php if (!empty($kits)): ?>
                    <div style="margin-top:1rem; border-top:1px solid var(--color-border); padding-top:1rem;">
                        <?php foreach ($kits as $ki => $kit):
                            $kLabel = !empty($kit['assigned_to']) ? $kit['assigned_to'] : ('Kit ' . ($ki + 1));
                            $kBadge = $kitStatusMap[$kit['kit_status_id'] ?? 1] ?? 'orange';
                            $kStatus = 'Received';
                            switch ($kit['kit_status_id'] ?? 1) {
                                case 2: $kStatus = 'Shipped';         break;
                                case 3: $kStatus = 'Sample Received'; break;
                                case 4: $kStatus = 'Processing';      break;
                                case 5: $kStatus = 'Results Ready';   break;
                            }
                            $isGiftPending = $kit['is_gift'] && empty($kit['gift_redeemed_at']);
                        ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding: 0.6rem 0; <?php echo $ki > 0 ? 'border-top:1px solid var(--color-border);' : ''; ?>">
                            <div>
                                <strong style="font-size:0.9rem;">🧬 <?php echo htmlspecialchars($kLabel); ?></strong><br>
                                <span style="font-size:0.78rem; font-family:monospace; color:var(--color-text-gray); letter-spacing:1px;"><?php echo htmlspecialchars($kit['kit_barcode']); ?></span>
                            </div>
                            <div style="text-align:right;">
                                <?php if ($isGiftPending): ?>
                                    <span class="badge badge-blue">🎁 Awaiting Claim</span>
                                <?php else: ?>
                                    <span class="badge badge-<?php echo $kBadge; ?>"><?php echo $kStatus; ?></span>
                                <?php endif; ?>
                                <?php if (($kit['kit_status_id'] ?? 0) == 5): ?>
                                    <a href="results.php" class="btn btn-outline btn-sm" style="margin-left:0.5rem;">Results</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Order-level actions -->
                    <div style="display:flex; gap:1rem; margin-top:1rem;" class="flex-wrap">
                        <a href="../track-order.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-outline">
                            Track Status
                        </a>
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