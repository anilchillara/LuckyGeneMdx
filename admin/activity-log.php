<?php
define('LuckyGenes', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

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

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Get total count
$countSql = "SELECT COUNT(*) as total FROM activity_log";
$stmt = $db->query($countSql);
$totalLogs = $stmt->fetch()['total'];
$totalPages = ceil($totalLogs / $perPage);

// Get logs
$sql = "SELECT l.*, a.username 
        FROM activity_log l 
        LEFT JOIN admins a ON l.admin_id = a.admin_id 
        ORDER BY l.created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$adminName = $_SESSION['admin_username'];
$initials  = strtoupper(substr($adminName,0,2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log - LuckyGenes Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
            border-top: 1px solid var(--color-border);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1>Activity Log</h1>
                <p>System events and administrative actions</p>
            </div>
            <a href="export-activity-log.php" class="btn btn-primary">Export CSV</a>
        </div>

        <div class="admin-card" style="padding:0; overflow:hidden;">
            <?php if (empty($logs)): ?>
                <div style="text-align:center; padding:4rem 2rem;">
                    <div style="font-size:4rem; margin-bottom:1rem; opacity:0.3;">📋</div>
                    <h3>No activity recorded</h3>
                    <p style="color:var(--color-text-gray);">System events will appear here.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td style="white-space:nowrap; color:var(--color-text-gray); font-size:0.85rem;">
                                        <?php echo date('M j, Y H:i', strtotime($log['created_at'])); ?>
                                    </td>
                                    <td>
                                        <?php if ($log['username']): ?>
                                            <strong><?php echo htmlspecialchars($log['username']); ?></strong>
                                        <?php else: ?>
                                            <span style="color:var(--color-text-gray);">System/Unknown</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-blue"><?php echo htmlspecialchars($log['action']); ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            echo htmlspecialchars($log['details'] ?? ''); 
                                            if ($log['entity_type'] && $log['entity_id']) {
                                                echo ' <span style="color:var(--color-text-gray); font-size:0.85rem;">(' . htmlspecialchars($log['entity_type']) . ' #' . htmlspecialchars($log['entity_id']) . ')</span>';
                                            }
                                        ?>
                                    </td>
                                    <td style="font-family:monospace; font-size:0.85rem;">
                                        <?php echo htmlspecialchars($log['ip_address']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline btn-sm">← Previous</a>
                    <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">← Previous</button>
                    <?php endif; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline btn-sm">Next →</a>
                    <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">Next →</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
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