<?php
define('luckygenemdx', true);
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
        $order = $orderModel->getOrderByNumber($orderNumber);
        
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
    <title>Track Your Order - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>


    <main id="main-content">
        <!-- Page Header - UNCHANGED -->
        <section class="page-header">
            <div class="container">
                <h1>Track Your Order</h1>
                <p>
                Enter your order number to view the current status of your screening kit.
                </p>
            </div>
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
                                class="form-input" 
                                placeholder="LGM240214ABC123"
                                required
                                class="track-order-input"
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
                            <h2 class="mb-1">Order Details</h2>
                            <p class="text-dark-gray mb-0">
                                Order placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="font-sm text-dark-gray mb-1">Order Number</div>
                            <div class="font-lg font-bold text-deep-blue">
                                <?php echo htmlspecialchars($order['order_number']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-2">
                            <div class="track-order-status-box">
                                <div class="font-sm text-dark-gray mb-1">Current Status</div>
                                <div class="font-lg font-semibold text-teal">
                                    <?php echo htmlspecialchars($order['status_name']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($order['tracking_number']): ?>
                        <div class="col col-2">
                            <div class="track-order-status-box">
                                <div class="font-sm text-dark-gray mb-1">Tracking Number</div>
                                <div class="font-lg font-semibold text-deep-blue">
                                    <?php echo htmlspecialchars($order['tracking_number']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Progress Tracker -->
                <div class="progress-tracker">
                    <div class="progress-line">
                        <div class="progress-line-fill" style="width: <?php echo (($order['display_order'] - 1) / (count($allStatuses) - 1)) * 100; ?>%;"></div>
                    </div>
                    
                    <div class="progress-steps">
                        <?php 
                        $statusIcons = ['📦', '🚚', '🧪', '🔬', '✅'];
                        foreach($allStatuses as $index => $status): 
                            $isCompleted = $order['display_order'] > $status['display_order'];
                            $isActive = $order['display_order'] == $status['display_order'];
                            $class = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
                        ?>
                        <div class="progress-step <?php echo $class; ?>">
                            <div class="progress-step-circle">
                                <?php echo $isCompleted ? '✓' : $statusIcons[$index]; ?>
                            </div>
                            <div class="progress-step-title">
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </div>
                            <div class="progress-step-desc">
                                <?php echo htmlspecialchars($status['description'] ?? ''); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="glass-card track-order-next-steps">
                    <h3 class="mb-2">What's Next?</h3>
                    <?php if ($order['display_order'] == 1): ?>
                        <p>Your order has been received and is being prepared for shipment. You should receive your kit within 3-5 business days.</p>
                    <?php elseif ($order['display_order'] == 2): ?>
                        <p>Your screening kit has been shipped! Once you receive it, follow the included instructions to collect your sample and return it to our lab using the prepaid shipping label.</p>
                    <?php elseif ($order['display_order'] == 3): ?>
                        <p>We've received your sample at our lab! Our team is beginning the screening process, which typically takes 14-21 days. We'll notify you when your results are ready.</p>
                    <?php elseif ($order['display_order'] == 4): ?>
                        <p>Your sample is currently being processed. Results are expected within <?php echo RESULTS_PROCESSING_DAYS; ?> business days. We'll email you as soon as they're ready.</p>
                    <?php else: ?>
                        <p>Your results are ready! You can now view them in your user portal.</p>
                        <a href="user-portal/" class="btn btn-primary mt-2">View Results</a>
                    <?php endif; ?>
                </div>
                
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="glass-card track-order-help-section">
                <h3 class="mb-2">Need Help?</h3>
                <p class="text-dark-gray mb-3">
                    Have questions about your order or the screening process?
                </p>
                <div>
                    <a href="mailto:support@luckygenemdx.com" class="btn btn-outline">Email Support</a>
                    <a href="tel:1-800-GENE-TEST" class="btn btn-outline ml-2">Call Us</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
