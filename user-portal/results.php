<?php
define('LuckyGenes', true);
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
    SELECT r.*, o.order_number, o.order_date, 
           COALESCE(os.status_name, 'Unknown') as status_name,
           k.kit_barcode, k.assigned_to, k.is_gift
    FROM results r
    LEFT JOIN kits k ON r.kit_id = k.kit_id
    LEFT JOIN orders o ON COALESCE(r.order_id, k.order_id) = o.order_id
    LEFT JOIN order_status os ON k.kit_status_id = os.status_id
    WHERE o.user_id = :user_id OR k.gift_redeemed_by = :user_id
    ORDER BY r.upload_date DESC
");
$stmt->execute([':user_id' => $userId]);
$results = $stmt->fetchAll();

$stmt = $db->prepare("SELECT full_name FROM users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$user = $stmt->fetch();
$initials  = strtoupper(substr($user['full_name'],0,1));
if (strpos($user['full_name'],' ')!==false) $initials .= strtoupper(substr(explode(' ',$user['full_name'])[1],0,1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results - LuckyGenes</title>
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
            <h1>Genetic Reports</h1>
            <p>View, download, and share your comprehensive screening results.</p>
        </div>

            <?php if (empty($results)): ?>
                <div class="card text-center p-4">
                    <div class="fs-3 mb-1">📄</div>
                    <h3 class="mb-1">No Results Available</h3>
                    <p class="mb-2">
                        Your results will appear here once your sample has been processed.<br>
                        Standard processing time is 14–21 days from sample receipt.
                    </p>
                    <div class="flex-center-wrap">
                        <a href="orders.php" class="btn btn-primary">View Order Status</a>
                        <a href="../track-order.php" class="btn btn-outline">Track Shipment</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): 
                    $label = !empty($result['assigned_to']) ? $result['assigned_to'] : ($result['is_gift'] ? 'Gift Kit' : 'My Kit');
                ?>
                    <div class="card result-card">
                        <div class="flex-between-start">
                            <div>
                                <h3>🧬 <?php echo htmlspecialchars($label); ?> Results</h3>
                                <p style="font-family:monospace; color:var(--color-navy); font-size:0.9rem; margin-top:0.25rem;">
                                    Barcode: <?php echo htmlspecialchars($result['kit_barcode'] ?? 'N/A'); ?> (Order #<?php echo htmlspecialchars($result['order_number']); ?>)
                                </p>
                                <p class="text-primary font-semibold" style="margin-top:0.5rem;">
                                    <span class="fs-1-2 align-middle">✨</span> Comprehensive Carrier Screen Ready
                                </p>
                            </div>
                        </div>

                        <div class="info-panel">
                            <div>
                                <div class="stat-lbl">Result Date</div>
                                <div class="font-semibold"><?php echo date('M j, Y', strtotime($result['upload_date'])); ?></div>
                            </div>
                            <div>
                                <div class="stat-lbl">Type</div>
                                <div class="font-semibold">Full Panel (300+)</div>
                            </div>
                            <div>
                                <div class="stat-lbl">File Size</div>
                                <div class="font-semibold"><?php echo number_format($result['file_size'] / 1024, 1); ?> KB</div>
                            </div>
                        </div>

                        <div class="alert-warning">
                            <strong>Important Medical Context:</strong>
                            <ul class="ml-1-5 mt-0-5">
                                <li>These are screening results, not a medical diagnosis.</li>
                                <li>We recommend reviewing this report with a genetic counselor or your healthcare provider.</li>
                            </ul>
                        </div>

                        <div class="flex-gap-1 flex-wrap">
                            <a href="../api/download-result.php?order_id=<?php echo $result['order_id']; ?>&kit_id=<?php echo $result['kit_id']; ?>" target="_blank" class="btn btn-primary">
                                View PDF Report
                            </a>
                            <a href="../api/download-result.php?order_id=<?php echo $result['order_id']; ?>&kit_id=<?php echo $result['kit_id']; ?>&download=1" class="btn btn-outline" download>
                                Download
                            </a>
                            <a href="mailto:counseling@LuckyGenes.com" class="btn btn-outline">
                                Request Counselor Call
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="card mt-3 text-center">
                    <h3>Need help interpreting your results?</h3>
                    <p>Our team of board-certified genetic counselors is here to walk you through your report.</p>
                    <a href="../support.php" class="btn btn-outline mt-1">Visit Support Center</a>
                </div>
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