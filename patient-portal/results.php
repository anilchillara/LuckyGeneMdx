<?php
define('LUCKYGENEMXD', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
session_start();
setSecurityHeaders();

// Check user authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get all results for this user
$stmt = $db->prepare("
    SELECT r.*, o.order_number, o.order_date, os.status_name
    FROM results r
    JOIN orders o ON r.order_id = o.order_id
    JOIN order_status os ON o.status_id = os.status_id
    WHERE o.user_id = :user_id
    ORDER BY r.upload_date DESC
");
$stmt->execute([':user_id' => $userId]);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results - LuckyGeneMdx Patient Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .portal-wrapper { display: flex; min-height: 100vh; }
        .portal-sidebar {
            width: 260px; background: var(--color-primary-deep-blue); color: white; padding: 2rem 0;
            position: fixed; height: 100vh; overflow-y: auto;
        }
        .portal-sidebar-header { padding: 0 1.5rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .portal-sidebar-header h2 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .portal-sidebar-user { font-size: 0.85rem; opacity: 0.8; }
        .portal-nav { margin-top: 2rem; }
        .portal-nav-item {
            display: block; padding: 0.875rem 1.5rem; color: rgba(255,255,255,0.8);
            transition: all var(--transition-fast); border-left: 3px solid transparent;
        }
        .portal-nav-item:hover, .portal-nav-item.active {
            background: rgba(255,255,255,0.1); color: white; border-left-color: var(--color-medical-teal);
        }
        .portal-main { flex: 1; margin-left: 260px; padding: 2rem; background: var(--color-light-gray); }
        .page-header {
            background: white; padding: 1.5rem 2rem; border-radius: var(--radius-md);
            margin-bottom: 2rem; box-shadow: var(--shadow-sm);
        }
        .result-card {
            background: white; padding: 2rem; border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;
            transition: all var(--transition-normal);
        }
        .result-card:hover { box-shadow: var(--shadow-lg); }
        .result-header {
            display: flex; justify-content: space-between; align-items: start;
            margin-bottom: 1.5rem; padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--color-medium-gray);
        }
        .info-label { font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.25rem; }
        .info-value { font-weight: 500; color: var(--color-primary-deep-blue); }
        @media (max-width: 768px) {
            .portal-sidebar { display: none; }
            .portal-main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="portal-wrapper">
        <!-- Sidebar -->
        <aside class="portal-sidebar">
            <div class="portal-sidebar-header">
                <h2>🧬 LuckyGeneMdx</h2>
                <div class="portal-sidebar-user">
                    <?php echo htmlspecialchars($userName); ?>
                </div>
            </div>
            <nav class="portal-nav">
                <a href="index.php" class="portal-nav-item">🏠 Dashboard</a>
                <a href="orders.php" class="portal-nav-item">📦 My Orders</a>
                <a href="results.php" class="portal-nav-item active">📄 My Results</a>
                <a href="settings.php" class="portal-nav-item">⚙️ Settings</a>
                <a href="../index.php" class="portal-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">← Back to Website</a>
                <a href="logout.php" class="portal-nav-item">🚪 Logout</a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="portal-main">
            <div class="page-header">
                <h1 style="margin-bottom: 0.25rem;">My Results</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">
                    View and download your genetic screening results
                </p>
            </div>
            
            <?php if (empty($results)): ?>
                <div class="glass-card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.5;">📄</div>
                    <h3 style="margin-bottom: 1rem;">No Results Available Yet</h3>
                    <p style="color: var(--color-dark-gray); margin-bottom: 2rem;">
                        Your results will appear here once your sample has been processed.<br>
                        Processing typically takes 14-21 days after we receive your sample.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="orders.php" class="btn btn-primary">
                            View My Orders
                        </a>
                        <a href="../track-order.php" class="btn btn-outline">
                            Track Order Status
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Results List -->
                <?php foreach ($results as $result): ?>
                    <div class="result-card">
                        <div class="result-header">
                            <div>
                                <h3 style="margin-bottom: 0.5rem;">
                                    Screening Results - Order <?php echo htmlspecialchars($result['order_number']); ?>
                                </h3>
                                <p style="color: var(--color-dark-gray); margin: 0; font-size: 0.9rem;">
                                    Uploaded on <?php echo date('F j, Y', strtotime($result['upload_date'])); ?>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 2rem; color: var(--color-medical-teal);">✓</div>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                            <div>
                                <div class="info-label">Order Date</div>
                                <div class="info-value"><?php echo date('M j, Y', strtotime($result['order_date'])); ?></div>
                            </div>
                            <div>
                                <div class="info-label">Results Date</div>
                                <div class="info-value"><?php echo date('M j, Y', strtotime($result['upload_date'])); ?></div>
                            </div>
                            <div>
                                <div class="info-label">File Size</div>
                                <div class="info-value"><?php echo number_format($result['file_size'] / 1024, 2); ?> KB</div>
                            </div>
                            <div>
                                <div class="info-label">Times Viewed</div>
                                <div class="info-value"><?php echo $result['accessed_count']; ?></div>
                            </div>
                        </div>
                        
                        <div style="padding: 1.5rem; background: var(--color-light-gray); border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                            <h4 style="margin-bottom: 1rem;">Important Information</h4>
                            <ul style="line-height: 1.8; padding-left: 1.5rem;">
                                <li>These results are screening results, not diagnostic tests</li>
                                <li>Please review your results with a healthcare provider or genetic counselor</li>
                                <li>Results do not replace medical advice or genetic counseling</li>
                                <li>Keep a copy of your results for your medical records</li>
                            </ul>
                        </div>
                        
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="view-result.php?id=<?php echo $result['result_id']; ?>" 
                               class="btn btn-primary" target="_blank">
                                📄 View Results PDF
                            </a>
                            <a href="download-result.php?id=<?php echo $result['result_id']; ?>" 
                               class="btn btn-outline" download>
                                ⬇️ Download Results
                            </a>
                            <a href="mailto:support@luckygenemxd.com?subject=Results Question - Order <?php echo urlencode($result['order_number']); ?>" 
                               class="btn btn-outline">
                                ✉️ Contact Support
                            </a>
                        </div>
                        
                        <?php if ($result['last_accessed']): ?>
                            <p style="margin-top: 1rem; font-size: 0.85rem; color: var(--color-dark-gray);">
                                Last accessed: <?php echo date('F j, Y \a\t g:i A', strtotime($result['last_accessed'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                
                <!-- Additional Resources -->
                <div class="glass-card" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Understanding Your Results</h3>
                    <p style="margin-bottom: 1.5rem;">
                        Need help interpreting your genetic screening results? We're here to help.
                    </p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <a href="../resources" class="btn btn-outline">
                            📖 Educational Resources
                        </a>
                        <a href="mailto:support@luckygenemxd.com" class="btn btn-outline">
                            💬 Schedule Genetic Counseling
                        </a>
                        <a href="tel:1-800-GENE-TEST" class="btn btn-outline">
                            📞 Call Support
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
