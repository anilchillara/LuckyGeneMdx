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
    <style>
        .progress-tracker {
            max-width: 800px;
            margin: 3rem auto;
            position: relative;
        }
        .progress-line {
            position: absolute;
            top: 50px;
            left: 40px;
            right: 40px;
            height: 4px;
            background: var(--color-medium-gray);
            z-index: 0;
        }
        .progress-line-fill {
            height: 100%;
            background: var(--gradient-primary);
            transition: width 0.5s ease;
        }
        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }
        .progress-step {
            text-align: center;
            flex: 1;
        }
        .progress-step-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: white;
            border: 4px solid var(--color-medium-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        .progress-step.active .progress-step-circle {
            border-color: var(--color-medical-teal);
            background: rgba(0, 179, 164, 0.1);
            box-shadow: 0 0 20px rgba(0, 179, 164, 0.3);
        }
        .progress-step.completed .progress-step-circle {
            border-color: var(--color-medical-teal);
            background: var(--color-medical-teal);
            color: white;
        }
        .progress-step-title {
            font-weight: 600;
            color: var(--color-primary-deep-blue);
            margin-bottom: 0.5rem;
        }
        .progress-step-desc {
            font-size: 0.85rem;
            color: var(--color-dark-gray);
        }
        @media (max-width: 768px) {
            .progress-steps { flex-direction: column; }
            .progress-line { display: none; }
            .progress-step { margin-bottom: 2rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>


    <main id="main-content">
        <!-- Page Header - UNCHANGED -->
        <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
            <div class="container">
                <h1 style="color: var(--color-white); margin-bottom: 1rem;">Track Your Order</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
                Enter your order number to view the current status of your screening kit.
                </p>
            </div>
        </section>
    
            
            <!-- Search Form -->
            <div class="glass-card" style="max-width: 600px; margin: 3rem auto 3rem;">
                <form method="POST" action="">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="order_number" class="form-label">Order Number</label>
                        <div style="display: flex; gap: 1rem;">
                            <input 
                                type="text" 
                                id="order_number" 
                                name="order_number" 
                                class="form-input" 
                                placeholder="LGM240214ABC123"
                                required
                                style="flex: 1;"
                                value="<?php echo htmlspecialchars($orderNumber); ?>"
                            >
                            <button type="submit" class="btn btn-primary" style="padding: 0.875rem 2rem;">
                                Track Order
                            </button>
                        </div>
                        <small style="color: var(--color-dark-gray); display: block; margin-top: 0.5rem;">
                            Your order number was sent to your email and begins with "LGM"
                        </small>
                    </div>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="glass-card" style="max-width: 600px; margin: 0 auto; background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3);">
                    <p style="margin: 0; color: #c33;"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($order): ?>
                <!-- Order Details -->
                <div class="glass-card" style="max-width: 800px; margin: 0 auto 3rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                        <div>
                            <h2 style="margin-bottom: 0.5rem;">Order Details</h2>
                            <p style="color: var(--color-dark-gray); margin: 0;">
                                Order placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.9rem; color: var(--color-dark-gray); margin-bottom: 0.25rem;">Order Number</div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--color-primary-deep-blue);">
                                <?php echo htmlspecialchars($order['order_number']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-2">
                            <div style="padding: 1rem; background: var(--color-light-gray); border-radius: var(--radius-sm);">
                                <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.25rem;">Current Status</div>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--color-medical-teal);">
                                    <?php echo htmlspecialchars($order['status_name']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($order['tracking_number']): ?>
                        <div class="col col-2">
                            <div style="padding: 1rem; background: var(--color-light-gray); border-radius: var(--radius-sm);">
                                <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.25rem;">Tracking Number</div>
                                <div style="font-size: 1.125rem; font-weight: 600; color: var(--color-primary-deep-blue);">
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
                <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
                    <h3 style="margin-bottom: 1rem;">What's Next?</h3>
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
                        <a href="user-portal/" class="btn btn-primary" style="margin-top: 1rem;">View Results</a>
                    <?php endif; ?>
                </div>
                
            <?php endif; ?>
            
            <!-- Help Section -->
            <div class="glass-card" style="max-width: 800px; margin: 3rem auto 3rem; text-align: center;">
                <h3 style="margin-bottom: 1rem;">Need Help?</h3>
                <p style="color: var(--color-dark-gray); margin-bottom: 1.5rem;">
                    Have questions about your order or the screening process?
                </p>
                <div>
                    <a href="mailto:support@luckygenemdx.com" class="btn btn-outline">Email Support</a>
                    <a href="tel:1-800-GENE-TEST" class="btn btn-outline" style="margin-left: 1rem;">Call Us</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
