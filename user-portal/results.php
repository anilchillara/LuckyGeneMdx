<?php
// define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
session_start();
setSecurityHeaders();

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
    <title>My Results - LuckyGeneMDx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .result-item {
            display: flex; flex-direction: column;
            border-left: 4px solid var(--color-medical-teal);
            margin-bottom: 2rem;
        }
        .result-meta-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem; margin: 1.5rem 0;
            background: var(--color-light-gray);
            padding: 1.5rem; border-radius: var(--radius-sm);
        }
        .meta-lbl { font-size: 0.8rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem; }
        .meta-val { font-weight: 600; color: var(--color-primary-deep-blue); font-size: 1.05rem; }
        
        .disclaimer-box {
            background: rgba(10, 31, 68, 0.03);
            border: 1px solid rgba(10, 31, 68, 0.08);
            border-radius: var(--radius-sm);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--color-dark-gray);
        }
        .disclaimer-box ul { padding-left: 1.2rem; margin: 0.5rem 0 0; }
        .disclaimer-box li { margin-bottom: 0.25rem; }

        .empty-icon {
            width: 80px; height: 80px;
            background: var(--color-light-gray);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--color-dark-gray);
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="portal-page">
        <div class="portal-hero">
            <div class="container">
                <h1>Genetic Reports</h1>
                <p>View, download, and share your comprehensive screening results.</p>
            </div>
        </div>

        <div class="container">
            <?php if (empty($results)): ?>
                <div class="content-card text-center" style="padding: 4rem 2rem;">
                    <div class="empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <h3 style="margin-bottom: 1rem;">No Results Available</h3>
                    <p style="margin-bottom: 2rem; color: var(--color-dark-gray);">
                        Your results will appear here once your sample has been processed.<br>
                        Standard processing time is 14–21 days from sample receipt.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="orders.php" class="btn btn-primary">View Order Status</a>
                        <a href="../track-order.php" class="btn btn-outline">Track Shipment</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <div class="content-card result-item">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h3>Results: Order #<?php echo htmlspecialchars($result['order_number']); ?></h3>
                                <p style="color: var(--color-medical-teal); font-weight: 500;">
                                    <span style="font-size:1.2rem; vertical-align:middle;">✨</span> Comprehensive Carrier Screen Ready
                                </p>
                            </div>
                        </div>

                        <div class="result-meta-grid">
                            <div>
                                <div class="meta-lbl">Result Date</div>
                                <div class="meta-val"><?php echo date('M j, Y', strtotime($result['upload_date'])); ?></div>
                            </div>
                            <div>
                                <div class="meta-lbl">Type</div>
                                <div class="meta-val">Full Panel (300+)</div>
                            </div>
                            <div>
                                <div class="meta-lbl">File Size</div>
                                <div class="meta-val"><?php echo number_format($result['file_size'] / 1024, 1); ?> KB</div>
                            </div>
                        </div>

                        <div class="disclaimer-box">
                            <strong>Important Medical Context:</strong>
                            <ul>
                                <li>These are screening results, not a medical diagnosis.</li>
                                <li>We recommend reviewing this report with a genetic counselor or your healthcare provider.</li>
                            </ul>
                        </div>

                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="view-result.php?id=<?php echo $result['result_id']; ?>" target="_blank" class="btn btn-primary">
                                View PDF Report
                            </a>
                            <a href="download-result.php?id=<?php echo $result['result_id']; ?>" class="btn btn-outline" download>
                                Download
                            </a>
                            <a href="mailto:counseling@luckygenemdx.com" class="btn btn-outline">
                                Request Counselor Call
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="glass-card text-center" style="margin-top: 3rem;">
                    <h3>Need help interpreting your results?</h3>
                    <p>Our team of board-certified genetic counselors is here to walk you through your report.</p>
                    <a href="../support.php" class="btn btn-outline" style="margin-top: 1rem;">Visit Support Center</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>