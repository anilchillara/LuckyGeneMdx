<?php
define('luckygenemdx', true);
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
    <title>My Results - LuckyGeneMDx</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body>
    <nav class="navbar">
      <a href="../index.php" class="brand"><span>🧬</span> LuckyGeneMDx</a>
      <div class="nav-items">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="orders.php" class="nav-link">My Orders</a>
        <a href="results.php" class="nav-link active">Results</a>
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
            <h1>Genetic Reports</h1>
            <p>View, download, and share your comprehensive screening results.</p>
        </div>

            <?php if (empty($results)): ?>
                <div class="card" style="text-align:center; padding: 4rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
                    <h3 style="margin-bottom: 1rem;">No Results Available</h3>
                    <p style="margin-bottom: 2rem;">
                        Your results will appear here once your sample has been processed.<br>
                        Standard processing time is 14–21 days from sample receipt.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="orders.php" class="btn">View Order Status</a>
                        <a href="../track-order.php" class="btn btn-outline">Track Shipment</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <div class="card" style="margin-bottom: 2rem; border-left: 4px solid var(--ms-blue);">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h3>Results: Order #<?php echo htmlspecialchars($result['order_number']); ?></h3>
                                <p style="color: var(--ms-blue); font-weight: 600;">
                                    <span style="font-size:1.2rem; vertical-align:middle;">✨</span> Comprehensive Carrier Screen Ready
                                </p>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:1rem; background: #f8f9fa; padding: 1rem; border-radius: 4px; margin: 1rem 0;">
                            <div>
                                <div class="stat-lbl">Result Date</div>
                                <div style="font-weight:600;"><?php echo date('M j, Y', strtotime($result['upload_date'])); ?></div>
                            </div>
                            <div>
                                <div class="stat-lbl">Type</div>
                                <div style="font-weight:600;">Full Panel (300+)</div>
                            </div>
                            <div>
                                <div class="stat-lbl">File Size</div>
                                <div style="font-weight:600;"><?php echo number_format($result['file_size'] / 1024, 1); ?> KB</div>
                            </div>
                        </div>

                        <div style="background: #fff8f0; border: 1px solid #ffeeba; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                            <strong>Important Medical Context:</strong>
                            <ul style="margin: 0.5rem 0 0 1.5rem;">
                                <li>These are screening results, not a medical diagnosis.</li>
                                <li>We recommend reviewing this report with a genetic counselor or your healthcare provider.</li>
                            </ul>
                        </div>

                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="view-result.php?id=<?php echo $result['result_id']; ?>" target="_blank" class="btn">
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

                <div class="card" style="margin-top: 3rem; text-align: center;">
                    <h3>Need help interpreting your results?</h3>
                    <p>Our team of board-certified genetic counselors is here to walk you through your report.</p>
                    <a href="../support.php" class="btn btn-outline" style="margin-top: 1rem;">Visit Support Center</a>
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