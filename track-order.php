<?php
define('LuckyGenes', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Order.php';
session_start();
setSecurityHeaders();

$order = null;
$error = '';
$orderNumber = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['order'])) {
    $orderNumber = trim($_POST['order_number'] ?? $_GET['order'] ?? '');
    
    if ($orderNumber) {
        $orderModel = new Order();
        $order = $orderModel->getOrderByNumber($orderNumber); // now includes $order['kits']
        
        if (!$order) {
            $error = 'Order not found. Please check your order number and try again.';
        }
    } else {
        $error = 'Please enter an order number.';
    }
}

// Get all statuses for progress display
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM order_status ORDER BY display_order ASC");
$allStatuses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>


    <main id="main-content">
        <!-- Page Header - UNCHANGED -->
        <section class="page-header">
                <p>
                Enter your order number to view the current status of your screening kit.
                </p>
        </section>
    
            
            <!-- Search Form -->
            <div class="glass-card track-order-form-card">
                <form method="POST" action="">
                    <div class="form-group track-order-form-group">
                        <label for="order_number" class="form-label">Order Number</label>
                        <div class="track-order-input-group">
                            <input 
                                type="text" 
                                id="order_number" 
                                name="order_number" 
                                class="form-input track-order-input" 
                                placeholder="LGM240214ABC123"
                                required
                                value="<?php echo htmlspecialchars($orderNumber); ?>"
                            >
                            <button type="submit" class="btn btn-primary track-order-btn">
                                Track Order
                            </button>
                        </div>
                        <small class="track-order-help">
                            Your order number was sent to your email and begins with "LGM"
                        </small>
                    </div>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="glass-card track-order-error">
                    <p class="track-order-error-text"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($order): ?>
                <!-- Order Details -->
                <div class="glass-card track-order-details">
                    <div class="track-order-header">
                        <div>
                            <h2 class="track-order-header-text">Order Details</h2>
                            <p class="track-order-header-subtext">
                                Order placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="track-order-number-label">Order Number</div>
                            <div class="track-order-number-value">
                                <?php echo htmlspecialchars($order['order_number']); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $kits = $order['kits'] ?? [];
                $kitCount = count($kits);
                foreach ($kits as $kitIndex => $kit):
                    $label = !empty($kit['assigned_to'])
                        ? htmlspecialchars($kit['assigned_to'])
                        : ($kitCount > 1 ? 'Kit ' . ($kitIndex + 1) : 'Your Kit');
                    $isGiftPending = $kit['is_gift'] && empty($kit['gift_redeemed_at']);
                ?>

                <!-- Per-Kit Progress Block -->
                <div class="glass-card track-order-details" style="margin-top:1.5rem;">
                    <div class="track-order-header">
                        <div>
                            <h3 class="track-order-header-text" style="font-size:1.1rem;">
                                <?php echo $kitCount > 1 ? '🧬 ' . $label : '🧬 Screening Kit'; ?>
                            </h3>
                            <span style="font-size:0.8rem; font-family:monospace; color:var(--color-text-gray); letter-spacing:1px;">
                                Barcode: <?php echo htmlspecialchars($kit['kit_barcode']); ?>
                            </span>
                        </div>
                        <div class="text-right">
                            <?php if ($isGiftPending): ?>
                                <span class="badge badge-blue">🎁 Awaiting Claim</span>
                            <?php else: ?>
                                <div class="track-order-number-label">Current Status</div>
                                <div class="font-semibold text-medical-teal"><?php echo htmlspecialchars($kit['status_name']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($kit['kit_barcode'] && !empty($order['tracking_number'])): ?>
                    <div class="row" style="margin-top:0.5rem;">
                        <div class="col col-2">
                            <div class="track-order-status-box">
                                <div class="track-order-number-label">Courier Tracking</div>
                                <div class="font-lg font-semibold text-primary-deep-blue">
                                    <?php echo htmlspecialchars($order['tracking_number']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($isGiftPending): ?>
                        <div style="text-align:center; padding: 2rem 1rem; color:var(--color-text-gray);">
                            <p>⏳ This kit is a gift that hasn't been claimed yet. Once the recipient clicks the link in their email and creates an account, tracking will begin here.</p>
                        </div>
                    <?php else: ?>
                        <!-- Progress Tracker per kit -->
                        <div class="progress-tracker" style="margin-top:1.5rem;">
                            <div class="progress-line">
                                <div class="progress-line-fill" style="width: <?php echo (($kit['display_order'] - 1) / (count($allStatuses) - 1)) * 100; ?>%;"></div>
                            </div>
                            <div class="progress-steps">
                                <?php
                                $statusIcons = ['📦', '🚚', '🧪', '🔬', '✅'];
                                foreach ($allStatuses as $index => $status):
                                    $isCompleted = $kit['display_order'] > $status['display_order'];
                                    $isActive    = $kit['display_order'] == $status['display_order'];
                                    $class       = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
                                ?>
                                <div class="progress-step <?php echo $class; ?>">
                                    <div class="progress-step-circle">
                                        <?php echo $isCompleted ? '✓' : $statusIcons[$index]; ?>
                                    </div>
                                    <div class="progress-step-title"><?php echo htmlspecialchars($status['status_name']); ?></div>
                                    <div class="progress-step-desc"><?php echo htmlspecialchars($status['description'] ?? ''); ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Next Steps per kit -->
                        <div class="glass-card track-order-next-steps" style="margin-top:1.5rem;">
                            <h3 class="mb-2">What's Next?</h3>
                            <?php if ($kit['display_order'] == 1): ?>
                                <p>Your kit has been ordered and is being prepared for shipment. Expect it within 3-5 business days.</p>
                            <?php elseif ($kit['display_order'] == 2): ?>
                                <p>Your screening kit has been shipped! Follow the included instructions to collect and return your sample.</p>
                            <?php elseif ($kit['display_order'] == 3): ?>
                                <p>We've received your sample at our lab! Processing typically takes 14-21 days.</p>
                            <?php elseif ($kit['display_order'] == 4): ?>
                                <p>Your sample is currently being processed. Results expected within <?php echo RESULTS_PROCESSING_DAYS; ?> business days.</p>
                            <?php else: ?>
                                <p>Your results are ready!</p>
                                <a href="user-portal/" class="btn btn-primary mt-2">View Results in Portal</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; // gift pending ?>
                </div>

                <?php endforeach; // kits ?>

                <?php if (empty($kits)): ?>
                <div class="glass-card" style="text-align:center; padding:2rem; color:var(--color-text-gray);">
                    <p>No kits found for this order. If you placed this order recently, please check back in a few minutes.</p>
                </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="glass-card track-order-help-section">
                <h3 class="mb-2">Need Help?</h3>
                <p class="text-dark-gray mb-3">
                    Have questions about your order or the screening process?
                </p>
                <div>
                    <a href="mailto:<?php echo htmlspecialchars(SUPPORT_EMAIL); ?>" class="btn btn-outline">Email Support</a>
                    <a href="tel:1-800-GENE-TEST" class="btn btn-outline ml-2">Call Us</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
