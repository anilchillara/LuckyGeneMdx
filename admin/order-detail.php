<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
session_start();
setSecurityHeaders();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
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

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = '';
$error = '';

if (!$orderId) {
    header('Location: orders.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$orderModel = new Order();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security validation failed.';
    } else {
        $newStatusId = intval($_POST['status_id']);
        $trackingNumber = trim($_POST['tracking_number'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        
        // Get old status name
        $stmt = $db->prepare("SELECT os.status_name FROM orders o JOIN order_status os ON o.status_id = os.status_id WHERE o.order_id = ?");
        $stmt->execute([$orderId]);
        $oldStatus = $stmt->fetchColumn();

        $result = $orderModel->updateOrderStatus($orderId, $newStatusId, $trackingNumber ?: null);
        
        if ($result['success']) {
            // Add notes if provided
            if ($notes) {
                try {
                    $sql = "UPDATE orders SET notes = :notes WHERE order_id = :order_id";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([':notes' => $notes, ':order_id' => $orderId]);
                } catch(PDOException $e) {
                    error_log("Notes Update Error: " . $e->getMessage());
                }
            }
            
            // Log activity
            try {
                $stmt = $db->prepare("SELECT status_name FROM order_status WHERE status_id = ?");
                $stmt->execute([$newStatusId]);
                $newStatusName = $stmt->fetchColumn();

                $sql = "INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) 
                        VALUES (:admin_id, 'update_order_status', 'order', :order_id, :details, :ip)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':admin_id' => $_SESSION['admin_id'],
                    ':order_id' => $orderId,
                    ':details' => "Status changed from '$oldStatus' to '$newStatusName'",
                    ':ip' => $_SERVER['REMOTE_ADDR']
                ]);
            } catch(PDOException $e) {
                error_log("Activity Log Error: " . $e->getMessage());
            }
            
            $success = 'Order updated successfully!';
        } else {
            $error = $result['message'];
        }
    }
}

// Get order details
try {
    $order = $orderModel->getOrderById($orderId);
    
    if (!$order) {
        header('Location: orders.php');
        exit;
    }
    
    // Get all statuses
    $statuses = $orderModel->getOrderStatuses();
    
    // Check if results exist
    $stmt = $db->prepare("SELECT * FROM results WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $result = $stmt->fetch();
    
} catch(PDOException $e) {
    error_log("Order Detail Error: " . $e->getMessage());
    header('Location: orders.php');
    exit;
}

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_',' ',$_SESSION['admin_role'] ?? 'Admin'));
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Order #<?php echo htmlspecialchars($order['order_number']); ?> - LuckyGenes Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .order-header-flex { display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem; }
        @media (max-width: 768px) {
            .order-header-flex { flex-direction: column; gap: 1rem; }
            .result-flex { flex-direction: column; align-items: flex-start !important; gap: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <div style="font-size: 0.9rem; color: var(--color-text-gray);">
                <a href="index.php" class="text-dark-gray">Dashboard</a>
                <span>/</span>
                <a href="orders.php" class="text-dark-gray">Orders</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
        </div>
            
            <?php if ($success): ?>
                <div class="glass-card-teal-left p-3 mb-3 text-teal"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="glass-card-error p-3 mb-3 text-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Order Header -->
            <div class="admin-card" style="margin-bottom: 2rem;">
                <div class="order-header-flex">
                    <div>
                        <h1 style="margin-bottom: 0.5rem;">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <p style="color: var(--color-text-gray); margin: 0;">
                            Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['order_date'])); ?>
                        </p>
                    </div>
                    <div>
                        <?php
                        $badgeClass = 'orange';
                        if ($order['display_order'] == 2) $badgeClass = 'blue';
                        elseif ($order['display_order'] >= 3 && $order['display_order'] <= 4) $badgeClass = 'orange';
                        elseif ($order['display_order'] == 5) $badgeClass = 'green';
                        ?>
                        <span class="badge badge-<?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($order['status_name']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="admin-grid">
                    <div class="col-span-4">
                        <div class="stat-lbl">Order Total</div>
                        <div style="font-size:1.2rem; font-weight:600;">$<?php echo number_format($order['price'], 2); ?></div>
                    </div>
                    <div class="col-span-4">
                        <div class="stat-lbl">Payment Status</div>
                        <div style="font-size:1.2rem; font-weight:600;"><?php echo ucfirst($order['payment_status']); ?></div>
                    </div>
                    <?php if ($order['tracking_number']): ?>
                    <div class="col-span-4">
                        <div class="stat-lbl">Tracking Number</div>
                        <div style="font-family: monospace; font-size: 1.1rem; font-weight:600; color: var(--color-navy);">
                            <?php echo htmlspecialchars($order['tracking_number']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="admin-card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Customer Information</h2>
                
                <div class="admin-grid">
                    <div class="col-span-6">
                        <div class="stat-lbl">Full Name</div>
                        <div style="font-size:1.2rem; font-weight:600;"><?php echo htmlspecialchars($order['full_name']); ?></div>
                    </div>
                    <div class="col-span-6">
                        <div class="stat-lbl">Email Address</div>
                        <div style="font-size: 1.1rem; word-break: break-all; color: var(--color-medical-teal);">
                            <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>" class="text-teal">
                                <?php echo htmlspecialchars($order['email']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 2rem 0 1rem;">Shipping Address</h3>
                <div style="background:var(--color-off-white); padding:1rem; border-radius:12px; border: 1px solid var(--color-border);">
                    <p style="margin:0; line-height:1.7;">
                        <?php echo htmlspecialchars($order['shipping_address_line1']); ?><br>
                        <?php if ($order['shipping_address_line2']): ?>
                            <?php echo htmlspecialchars($order['shipping_address_line2']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                        <?php echo htmlspecialchars($order['shipping_state']); ?> 
                        <?php echo htmlspecialchars($order['shipping_zip']); ?>
                    </p>
                </div>
            </div>
            
            <!-- Update Order Status -->
            <div class="admin-card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Update Order</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="update_status" value="1">
                    
                    <div class="admin-grid">
                        <div class="col-span-6">
                            <div class="form-group">
                                <label for="status_id">Order Status</label>
                                <select id="status_id" name="status_id" required class="form-select">
                                    <?php foreach($statuses as $status): ?>
                                        <option value="<?php echo $status['status_id']; ?>" 
                                                <?php echo $order['status_id'] == $status['status_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['status_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-span-6">
                            <div class="form-group">
                                <label for="tracking_number">Tracking Number</label>
                                <input 
                                    type="text" 
                                    id="tracking_number" 
                                    name="tracking_number" 
                                    value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>"
                                    placeholder="e.g., 1Z999AA10123456784"
                                    class="form-control"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Internal Notes</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="4"
                            placeholder="Add any internal notes about this order..."
                            class="form-control"
                        ><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        💾 Update Order
                    </button>
                </form>
            </div>
            
            <!-- Results Section -->
            <div class="admin-card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Test Results</h2>
                
                <?php if ($result): ?>
                    <div style="padding: 1.5rem; background: var(--color-off-white); border-radius: 12px; border: 1px solid var(--color-border); margin-bottom: 1rem;">
                        <div class="result-flex" style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.5rem;">
                                    ✅ Results Available
                                </div>
                                <div style="font-size: 0.9rem; color: var(--color-text-gray);">
                                    Uploaded on <?php echo date('F j, Y', strtotime($result['upload_date'])); ?>
                                </div>
                                <div style="font-size: 0.9rem; color: var(--color-text-gray);">
                                    Accessed <?php echo $result['accessed_count']; ?> time(s)
                                </div>
                            </div>
                            <div>
                                <a href="../api/download-result.php?order_id=<?php echo $orderId; ?>" 
                                   class="btn btn-primary" 
                                   target="_blank">
                                    📄 View PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="padding: 2rem; background: var(--color-off-white); border-radius: 12px; border: 1px solid var(--color-border); text-align: center;">
                        <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">📄</div>
                        <p style="color: var(--color-text-gray); margin-bottom: 1rem;">
                            No results uploaded yet
                        </p>
                        <a href="upload-results.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-primary">
                            📤 Upload Results
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="orders.php" class="btn btn-outline">
                    ← Back to Orders
                </a>
                <a href="upload-results.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn btn-primary">
                    📤 Upload Results
                </a>
                <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>" class="btn btn-outline">
                    ✉️ Email Customer
                </a>
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
</body>
</html>