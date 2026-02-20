<?php
define('luckygenemdx', true);
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
$adminRole = ucwords(str_replace('_',' ',$_SESSION['admin_role'] ?? 'Admin'));
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Order #<?php echo htmlspecialchars($order['order_number']); ?> - LuckyGeneMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="brand">
        <span>🧬</span> LuckyGeneMDx <span class="admin-badge">Admin</span>
      </a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link active">Orders</a>
        <a href="users.php" class="nav-link">Users</a>
        <a href="upload-results.php" class="nav-link">Upload Results</a>
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
            <div style="font-size: 0.9rem;">
                <a href="index.php">Dashboard</a>
                <span>/</span>
                <a href="orders.php">Orders</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($order['order_number']); ?></span>
            </div>
        </div>
            
            <?php if ($success): ?>
                <div class="msg msg-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="msg msg-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Order Header -->
            <div class="card" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                    <div>
                        <h1 style="margin-bottom: 0.5rem;">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <p style="color: var(--text-secondary); margin: 0;">
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
                
                <div class="grid">
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
                        <div style="font-family: monospace; font-size: 1.1rem; font-weight:600;">
                            <?php echo htmlspecialchars($order['tracking_number']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Customer Information</h2>
                
                <div class="grid">
                    <div class="col-span-6">
                        <div class="stat-lbl">Full Name</div>
                        <div style="font-size:1.2rem; font-weight:600;"><?php echo htmlspecialchars($order['full_name']); ?></div>
                    </div>
                    <div class="col-span-6">
                        <div class="stat-lbl">Email Address</div>
                        <div style="font-size: 1.1rem;">
                            <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>">
                                <?php echo htmlspecialchars($order['email']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 2rem 0 1rem;">Shipping Address</h3>
                <div style="background:var(--glass-hover); padding:1rem; border-radius:var(--radius);">
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
            <div class="card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Update Order</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="update_status" value="1">
                    
                    <div class="grid">
                        <div class="col-span-6">
                            <div class="form-group">
                                <label for="status_id">Order Status</label>
                                <select id="status_id" name="status_id" required>
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
                        ><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">
                        💾 Update Order
                    </button>
                </form>
            </div>
            
            <!-- Results Section -->
            <div class="card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Test Results</h2>
                
                <?php if ($result): ?>
                    <div style="padding: 1.5rem; background: var(--glass-hover); border-radius: var(--radius); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.5rem;">
                                    ✅ Results Available
                                </div>
                                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                    Uploaded on <?php echo date('F j, Y', strtotime($result['upload_date'])); ?>
                                </div>
                                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                    Accessed <?php echo $result['accessed_count']; ?> time(s)
                                </div>
                            </div>
                            <div>
                                <a href="../api/download-result.php?order_id=<?php echo $orderId; ?>" 
                                   class="btn" 
                                   target="_blank">
                                    📄 View PDF
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="padding: 2rem; background: var(--glass-hover); border-radius: var(--radius); text-align: center;">
                        <div style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;">📄</div>
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                            No results uploaded yet
                        </p>
                        <a href="upload-results.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn">
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
                <a href="upload-results.php?order=<?php echo urlencode($order['order_number']); ?>" class="btn">
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