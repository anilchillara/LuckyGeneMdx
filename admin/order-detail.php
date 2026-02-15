<?php
define('LUCKYGENEMXD', true);
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
                $sql = "INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) 
                        VALUES (:admin_id, 'update_order_status', 'order', :order_id, :details, :ip)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    ':admin_id' => $_SESSION['admin_id'],
                    ':order_id' => $orderId,
                    ':details' => "Status updated to status_id: $newStatusId",
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Order #<?php echo htmlspecialchars($order['order_number']); ?> - LuckyGeneMdx Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px;
            background: var(--color-primary-deep-blue);
            color: white;
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .admin-sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .admin-sidebar-header h2 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .admin-sidebar-user { font-size: 0.85rem; opacity: 0.8; }
        .admin-nav { margin-top: 2rem; }
        .admin-nav-item {
            display: block;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            transition: all var(--transition-fast);
            border-left: 3px solid transparent;
        }
        .admin-nav-item:hover, .admin-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--color-medical-teal);
        }
        .admin-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            background: var(--color-light-gray);
        }
        .content-card {
            background: white;
            padding: 2rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }
        .info-item {
            padding: 1rem;
            background: var(--color-light-gray);
            border-radius: var(--radius-sm);
        }
        .info-label {
            font-size: 0.85rem;
            color: var(--color-dark-gray);
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--color-primary-deep-blue);
        }
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-full);
            font-weight: 500;
        }
        .status-badge.received { background: #cce5ff; color: #004085; }
        .status-badge.shipped { background: #d1ecf1; color: #0c5460; }
        .status-badge.processing { background: #fff3cd; color: #856404; }
        .status-badge.ready { background: #d4edda; color: #155724; }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
        }
        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: var(--color-dark-gray);
        }
        .breadcrumb a:hover {
            color: var(--color-medical-teal);
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar (same as orders.php) -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>🧬 LuckyGeneMdx</h2>
                <div class="admin-sidebar-user">
                    <?php echo htmlspecialchars($adminName); ?><br>
                    <small><?php echo ucwords(str_replace('_', ' ', $_SESSION['admin_role'])); ?></small>
                </div>
            </div>
            
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">📊 Dashboard</a>
                <a href="orders.php" class="admin-nav-item active">📦 Orders</a>
                <a href="upload-results.php" class="admin-nav-item">📄 Upload Results</a>
                <a href="users.php" class="admin-nav-item">👥 Users</a>
                <a href="testimonials.php" class="admin-nav-item">💬 Testimonials</a>
                <a href="blog.php" class="admin-nav-item">📰 Blog</a>
                <a href="resources.php" class="admin-nav-item">📖 Resources</a>
                <a href="settings.php" class="admin-nav-item">⚙️ Settings</a>
                <a href="logout.php" class="admin-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="breadcrumb">
                <a href="index.php">Dashboard</a>
                <span>/</span>
                <a href="orders.php">Orders</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
            
            <?php if ($success): ?>
                <div class="alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Order Header -->
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                    <div>
                        <h1 style="margin-bottom: 0.5rem;">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <p style="color: var(--color-dark-gray); margin: 0;">
                            Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($order['order_date'])); ?>
                        </p>
                    </div>
                    <div>
                        <?php
                        $badgeClass = 'received';
                        if ($order['display_order'] == 2) $badgeClass = 'shipped';
                        elseif ($order['display_order'] >= 3 && $order['display_order'] <= 4) $badgeClass = 'processing';
                        elseif ($order['display_order'] == 5) $badgeClass = 'ready';
                        ?>
                        <span class="status-badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($order['status_name']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Order Total</div>
                        <div class="info-value">$<?php echo number_format($order['price'], 2); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Status</div>
                        <div class="info-value"><?php echo ucfirst($order['payment_status']); ?></div>
                    </div>
                    <?php if ($order['tracking_number']): ?>
                    <div class="info-item">
                        <div class="info-label">Tracking Number</div>
                        <div class="info-value" style="font-family: monospace; font-size: 1rem;">
                            <?php echo htmlspecialchars($order['tracking_number']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="content-card">
                <h2 style="margin-bottom: 1.5rem;">Customer Information</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['full_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value" style="font-size: 1rem;">
                            <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>">
                                <?php echo htmlspecialchars($order['email']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 2rem 0 1rem;">Shipping Address</h3>
                <div class="info-item">
                    <div class="info-value" style="font-weight: 400; line-height: 1.6;">
                        <?php echo htmlspecialchars($order['shipping_address_line1']); ?><br>
                        <?php if ($order['shipping_address_line2']): ?>
                            <?php echo htmlspecialchars($order['shipping_address_line2']); ?><br>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                        <?php echo htmlspecialchars($order['shipping_state']); ?> 
                        <?php echo htmlspecialchars($order['shipping_zip']); ?>
                    </div>
                </div>
            </div>
            
            <!-- Update Order Status -->
            <div class="content-card">
                <h2 style="margin-bottom: 1.5rem;">Update Order</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="update_status" value="1">
                    
                    <div class="row">
                        <div class="col col-2">
                            <div class="form-group">
                                <label for="status_id" class="form-label required">Order Status</label>
                                <select id="status_id" name="status_id" class="form-select" required>
                                    <?php foreach($statuses as $status): ?>
                                        <option value="<?php echo $status['status_id']; ?>" 
                                                <?php echo $order['status_id'] == $status['status_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['status_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col col-2">
                            <div class="form-group">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input 
                                    type="text" 
                                    id="tracking_number" 
                                    name="tracking_number" 
                                    class="form-input"
                                    value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>"
                                    placeholder="e.g., 1Z999AA10123456784"
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Internal Notes</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-input" 
                            rows="4"
                            placeholder="Add any internal notes about this order..."
                        ><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">
                        💾 Update Order
                    </button>
                </form>
            </div>
            
            <!-- Results Section -->
            <div class="content-card">
                <h2 style="margin-bottom: 1.5rem;">Test Results</h2>
                
                <?php if ($result): ?>
                    <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: var(--radius-sm); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; color: var(--color-primary-deep-blue); margin-bottom: 0.5rem;">
                                    ✅ Results Available
                                </div>
                                <div style="font-size: 0.9rem; color: var(--color-dark-gray);">
                                    Uploaded on <?php echo date('F j, Y', strtotime($result['upload_date'])); ?>
                                </div>
                                <div style="font-size: 0.9rem; color: var(--color-dark-gray);">
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
                    <div style="padding: 2rem; background: var(--color-light-gray); border-radius: var(--radius-sm); text-align: center;">
                        <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">📄</div>
                        <p style="color: var(--color-dark-gray); margin-bottom: 1rem;">
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
        </main>
    </div>
</body>
</html>
