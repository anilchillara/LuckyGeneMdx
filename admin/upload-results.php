<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/Order.php';
require_once '../includes/User.php';
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

$db = Database::getInstance()->getConnection();
$orderModel = new Order();

$success = '';
$error = '';
$order = null;
$searchQuery = isset($_GET['order']) ? trim($_GET['order']) : '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['result_file'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security validation failed.';
    } else {
        $orderNumber = trim($_POST['order_number']);
        $order = $orderModel->getOrderByNumber($orderNumber);
        
        if (!$order) {
            $error = 'Order not found.';
        } else {
            $file = $_FILES['result_file'];
            
            // Validate file
            $allowedTypes = ['application/pdf'];
            $maxSize = UPLOAD_MAX_SIZE; // 5MB
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload error. Please try again.';
            } elseif ($file['size'] > $maxSize) {
                $error = 'File is too large. Maximum size is 5MB.';
            } elseif (!in_array($file['type'], $allowedTypes) && !in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
                $error = 'Invalid file type. Only PDF files are allowed.';
            } else {
                // Create uploads directory if it doesn't exist
                $uploadsDir = dirname(__DIR__) . '/uploads/results';
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0700, true);
                }
                
                // Generate encrypted filename
                $encryptedName = 'result_' . $order['order_id'] . '_' . bin2hex(random_bytes(16)) . '.pdf';
                $destination = $uploadsDir . '/' . $encryptedName;
                
                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    // Calculate file hash for integrity
                    $fileHash = hash_file('sha256', $destination);
                    
                    try {
                        // Check if result already exists
                        $stmt = $db->prepare("SELECT result_id FROM results WHERE order_id = :order_id");
                        $stmt->execute([':order_id' => $order['order_id']]);
                        $existingResult = $stmt->fetch();
                        
                        if ($existingResult) {
                            // Update existing result
                            $sql = "UPDATE results 
                                    SET file_path = :file_path, 
                                        encrypted_filename = :encrypted_filename,
                                        upload_date = NOW(),
                                        uploaded_by = :admin_id,
                                        file_size = :file_size,
                                        file_hash = :file_hash
                                    WHERE order_id = :order_id";
                        } else {
                            // Insert new result
                            $sql = "INSERT INTO results 
                                    (order_id, file_path, encrypted_filename, uploaded_by, file_size, file_hash, upload_date) 
                                    VALUES 
                                    (:order_id, :file_path, :encrypted_filename, :admin_id, :file_size, :file_hash, NOW())";
                        }
                        
                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            ':order_id' => $order['order_id'],
                            ':file_path' => '/uploads/results/' . $encryptedName,
                            ':encrypted_filename' => $encryptedName,
                            ':admin_id' => $_SESSION['admin_id'],
                            ':file_size' => $file['size'],
                            ':file_hash' => $fileHash
                        ]);
                        
                        // Update order status to "Results Ready" (status_id = 5)
                        $orderModel->updateOrderStatus($order['order_id'], 5);
                        
                        // Log activity
                        $sql = "INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) 
                                VALUES (:admin_id, 'upload_result', 'order', :order_id, :details, :ip)";
                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            ':admin_id' => $_SESSION['admin_id'],
                            ':order_id' => $order['order_id'],
                            ':details' => "Uploaded result file: $encryptedName",
                            ':ip' => $_SERVER['REMOTE_ADDR']
                        ]);
                        
                        $success = "Results uploaded successfully for order #{$orderNumber}!";
                        
                        // Send email notification
                        $userModel = new User();
                        $userModel->sendResultsNotification($order['email'], $order['full_name'], $order['order_number']);
                        
                    } catch(PDOException $e) {
                        error_log("Results Upload Error: " . $e->getMessage());
                        $error = 'Database error. Please try again.';
                        // Delete uploaded file on error
                        unlink($destination);
                    }
                } else {
                    $error = 'Failed to save file. Please check permissions.';
                }
            }
        }
    }
}

// Search for order if query provided
if ($searchQuery && !$order) {
    $order = $orderModel->getOrderByNumber($searchQuery);
}

$adminName = $_SESSION['admin_username'];
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Upload Results - LuckyGeneMDx Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Page specific styles */
        .file-upload-area {
            border: 2px dashed var(--glass-border);
            border-radius: var(--radius);
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
            background: var(--glass-hover);
        }
        .file-upload-area:hover {
            border-color: var(--ms-blue);
            background: var(--glass-panel);
        }
        .file-upload-area.dragover {
            border-color: var(--ms-blue);
            background: var(--ms-blue-light);
        }
        .file-info {
            margin-top: 1rem;
            padding: 1rem;
            background: var(--glass-hover);
            border-radius: var(--radius);
            display: none;
        }
        .order-info {
            padding: 1.5rem;
            background: var(--glass-hover);
            border-radius: var(--radius);
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
      <a href="index.php" class="brand">
        <span>🧬</span> LuckyGeneMDx <span class="admin-badge">Admin</span>
      </a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link">Orders</a>
        <a href="users.php" class="nav-link">Users</a>
        <a href="upload-results.php" class="nav-link active">Upload Results</a>
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
                <h1>Upload Test Results</h1>
                <p>Upload PDF result files for customer orders. Files are encrypted and stored securely.</p>
            </div>
        </div>
            
            <?php if ($success): ?>
                <div class="msg msg-success">
                    <strong>✅ Success!</strong> <?php echo htmlspecialchars($success); ?>
                    <div style="margin-top: 1rem;">
                        <a href="upload-results.php" class="btn">Upload Another</a>
                        <a href="orders.php" class="btn btn-outline" style="margin-left: 1rem;">View Orders</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="msg msg-error">
                    <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- Search for Order -->
            <div class="card" style="margin-bottom: 2rem;">
                <h2 style="margin-bottom: 1.5rem;">Find Order</h2>
                
                <form method="GET" action="">
                    <div style="display: flex; gap: 1rem; align-items: end;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label for="order">Order Number</label>
                            <input 
                                type="text" 
                                id="order" 
                                name="order" 
                                placeholder="LGM240214ABC123"
                                value="<?php echo htmlspecialchars($searchQuery); ?>"
                                required
                            >
                        </div>
                        <button type="submit" class="btn">
                            🔍 Search
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if ($order): ?>
                <!-- Order Information -->
                <div class="order-info">
                    <h3 style="margin-bottom: 1rem;">Order Information</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary);">Order Number</div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($order['order_number']); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary);">Customer</div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($order['full_name']); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary);">Email</div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($order['email']); ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; color: var(--text-secondary);">Current Status</div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($order['status_name']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Check if results already uploaded -->
                <?php
                $stmt = $db->prepare("SELECT * FROM results WHERE order_id = :order_id");
                $stmt->execute([':order_id' => $order['order_id']]);
                $existingResult = $stmt->fetch();
                
                if ($existingResult):
                ?>
                    <div class="msg" style="background:#fff3cd; color:#856404; border:1px solid #ffeaa7;">
                        <strong>⚠️ Notice:</strong> Results have already been uploaded for this order on 
                        <?php echo date('F j, Y', strtotime($existingResult['upload_date'])); ?>.
                        Uploading a new file will replace the existing one.
                    </div>
                <?php endif; ?>
                
                <!-- Upload Form -->
                <div class="card" style="margin-bottom: 2rem;">
                    <h2 style="margin-bottom: 1.5rem;">Upload PDF Results</h2>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($order['order_number']); ?>">
                        
                        <div class="file-upload-area" id="fileUploadArea">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                                Drop PDF file here or click to browse
                            </div>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                Maximum file size: 5MB | PDF files only
                            </div>
                            <input 
                                type="file" 
                                id="result_file" 
                                name="result_file" 
                                accept=".pdf,application/pdf"
                                required
                                style="display: none;"
                            >
                        </div>
                        
                        <div class="file-info" id="fileInfo">
                            <strong>Selected file:</strong> <span id="fileName"></span><br>
                            <strong>Size:</strong> <span id="fileSize"></span>
                        </div>
                        
                        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                            <button type="submit" class="btn" id="uploadButton" disabled>
                                📤 Upload Results
                            </button>
                            <a href="upload-results.php" class="btn btn-outline">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
            <!-- Instructions -->
            <div class="card" style="background: var(--glass-hover);">
                <h3 style="margin-bottom: 1rem;">📋 Upload Instructions</h3>
                <ol style="line-height: 1.8;">
                    <li>Search for the order using the order number</li>
                    <li>Verify the customer information is correct</li>
                    <li>Upload the PDF file containing the test results</li>
                    <li>The system will automatically:
                        <ul style="margin-top: 0.5rem;">
                            <li>Encrypt the filename for security</li>
                            <li>Store the file in a secure location</li>
                            <li>Update the order status to "Results Ready"</li>
                            <li>Send an email notification to the customer (when configured)</li>
                        </ul>
                    </li>
                </ol>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--glass-panel); border-radius: var(--radius); border-left: 4px solid var(--ms-blue);">
                    <strong>Security Note:</strong> All uploaded files are encrypted and stored outside the web root for maximum security.
                </div>
            </div>
    </div>
    
    <script>
        const fileInput = document.getElementById('result_file');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadButton = document.getElementById('uploadButton');
        
        // Click to select file
        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // File selected
        fileInput.addEventListener('change', (e) => {
            handleFile(e.target.files[0]);
        });
        
        // Drag and drop
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        
        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });
        
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const file = e.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') {
                fileInput.files = e.dataTransfer.files;
                handleFile(file);
            } else {
                alert('Please upload a PDF file only.');
            }
        });
        
        function handleFile(file) {
            if (!file) return;
            
            if (file.type !== 'application/pdf') {
                alert('Please upload a PDF file only.');
                return;
            }
            
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File is too large. Maximum size is 5MB.');
                return;
            }
            
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            fileInfo.style.display = 'block';
            uploadButton.disabled = false;
        }
        
        // Form submission
        document.getElementById('uploadForm')?.addEventListener('submit', (e) => {
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<span class="spinner"></span> Uploading...';
        });
    </script>
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
