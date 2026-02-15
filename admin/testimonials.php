<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $db->prepare("INSERT INTO testimonials (name, age, location, quote, is_active, display_order, created_at) VALUES (?, ?, ?, ?, 1, ?, NOW())");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['age'] ?: null,
                        $_POST['location'] ?: null,
                        $_POST['quote'],
                        $_POST['display_order'] ?? 0
                    ]);
                    $success = "Testimonial added successfully!";
                    break;
                    
                case 'update':
                    $stmt = $db->prepare("UPDATE testimonials SET name = ?, age = ?, location = ?, quote = ?, is_active = ?, display_order = ? WHERE testimonial_id = ?");
                    $stmt->execute([
                        $_POST['name'],
                        $_POST['age'] ?: null,
                        $_POST['location'] ?: null,
                        $_POST['quote'],
                        $_POST['is_active'],
                        $_POST['display_order'] ?? 0,
                        $_POST['testimonial_id']
                    ]);
                    $success = "Testimonial updated successfully!";
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM testimonials WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $success = "Testimonial deleted successfully!";
                    break;
                    
                case 'toggle_status':
                    $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $success = "Testimonial status updated successfully!";
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 25;
$offset = ($page - 1) * $perPage;

// Build query
$where_clauses = [];
$params = [];

if ($status_filter === 'active') {
    $where_clauses[] = "is_active = 1";
} elseif ($status_filter === 'inactive') {
    $where_clauses[] = "is_active = 0";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get total count
$countSql = "SELECT COUNT(*) as total FROM testimonials $where_sql";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalTestimonials = $stmt->fetch()['total'];
$totalPages = ceil($totalTestimonials / $perPage);

// Get testimonials ordered by display_order
$sql = "SELECT * FROM testimonials $where_sql ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn(),
    'active' => $db->query("SELECT COUNT(*) FROM testimonials WHERE is_active = 1")->fetchColumn(),
    'inactive' => $db->query("SELECT COUNT(*) FROM testimonials WHERE is_active = 0")->fetchColumn(),
];

$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonial Management - LuckyGeneMDx Admin</title>
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
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--color-medical-teal);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-medical-teal);
            margin-bottom: 0.25rem;
        }
        .stat-label {
            color: var(--color-dark-gray);
            font-size: 0.9rem;
        }
        .filters-bar {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: end;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .table-container {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-medium-gray); }
        .table th { font-weight: 600; color: var(--color-primary-deep-blue); background: var(--color-light-gray); white-space: nowrap; }
        .table tbody tr:hover { background: var(--color-light-gray); }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 500;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-secondary { background: #e2e3e5; color: #383d41; }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-fast);
            text-decoration: none;
            display: inline-block;
        }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.85rem; }
        .btn-primary { background: var(--color-medical-teal); color: white; }
        .btn-primary:hover { background: #009688; }
        .btn-secondary { background: var(--color-dark-gray); color: white; }
        .btn-secondary:hover { background: #555; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-info:hover { background: #138496; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-outline { background: white; color: var(--color-primary-deep-blue); border: 1px solid var(--color-medium-gray); }
        .btn-outline:hover { background: var(--color-light-gray); }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        .modal-content {
            background: white;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: var(--radius-md);
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-close {
            float: right;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: #999;
            cursor: pointer;
        }
        .modal-close:hover { color: #000; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--color-medium-gray);
            border-radius: var(--radius-sm);
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--color-dark-gray);
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1.5rem;
        }
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--color-medium-gray);
            border-radius: var(--radius-sm);
            color: var(--color-primary-deep-blue);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        .pagination a:hover {
            background: var(--color-medical-teal);
            color: white;
            border-color: var(--color-medical-teal);
        }
        .pagination .active {
            background: var(--color-medical-teal);
            color: white;
            border-color: var(--color-medical-teal);
        }
        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h2>🧬 LuckyGeneMDx</h2>
                <div class="admin-sidebar-user">
                    <?php echo htmlspecialchars($adminName); ?><br>
                    <small><?php echo htmlspecialchars($adminRole); ?></small>
                </div>
            </div>
            
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item">📊 Dashboard</a>
                <a href="orders.php" class="admin-nav-item">📦 Orders</a>
                <a href="upload-results.php" class="admin-nav-item">📄 Upload Results</a>
                <a href="users.php" class="admin-nav-item">👥 Users</a>
                <a href="testimonials.php" class="admin-nav-item active">💬 Testimonials</a>
                <a href="blog.php" class="admin-nav-item">📰 Blog</a>
                <a href="settings.php" class="admin-nav-item">⚙️ Settings</a>
                <a href="logout.php" class="admin-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1 style="margin-bottom: 0.25rem;">Testimonial Management</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">
                    <?php echo number_format($totalTestimonials); ?> total testimonials
                </p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                    <div class="stat-label">Total Testimonials</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['active']); ?></div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['inactive']); ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="" class="filters-bar">
                <div class="filter-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
                    </select>
                </div>
                
                <div class="filter-group" style="flex: 0;">
                    <button type="submit" class="btn btn-primary">
                        🔍 Filter
                    </button>
                </div>
                
                <div class="filter-group" style="flex: 0;">
                    <button type="button" onclick="showAddModal()" class="btn btn-primary">
                        + Add Testimonial
                    </button>
                </div>
                
                <?php if ($status_filter !== 'all'): ?>
                <div class="filter-group" style="flex: 0;">
                    <a href="testimonials.php" class="btn btn-outline">
                        ✕ Clear
                    </a>
                </div>
                <?php endif; ?>
            </form>

            <!-- Testimonials Table -->
            <div class="table-container">
                <?php if (empty($testimonials)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">💬</div>
                        <h3>No testimonials found</h3>
                        <p>
                            <?php if ($status_filter !== 'all'): ?>
                                Try adjusting your filters.
                            <?php else: ?>
                                Add your first testimonial using the button above.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Location</th>
                                    <th>Quote</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testimonials as $testimonial): ?>
                                    <tr>
                                        <td><?php echo $testimonial['testimonial_id']; ?></td>
                                        <td><strong><?php echo $testimonial['display_order']; ?></strong></td>
                                        <td><strong><?php echo htmlspecialchars($testimonial['name']); ?></strong></td>
                                        <td><?php echo $testimonial['age'] ?? '-'; ?></td>
                                        <td><?php echo htmlspecialchars($testimonial['location'] ?? '-'); ?></td>
                                        <td style="max-width: 300px;">
                                            <?php echo htmlspecialchars(substr($testimonial['quote'], 0, 100)); ?>...
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $testimonial['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($testimonial['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <button onclick='editTestimonial(<?php echo json_encode($testimonial, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' 
                                                    class="btn btn-sm btn-secondary">Edit</button>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['testimonial_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-info">
                                                    <?php echo $testimonial['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this testimonial? This cannot be undone.');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['testimonial_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        $queryParams = [];
                        if ($status_filter !== 'all') $queryParams['status'] = $status_filter;
                        
                        if ($page > 1):
                            $queryParams['page'] = $page - 1;
                        ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>">← Previous</a>
                        <?php else: ?>
                            <span class="disabled">← Previous</span>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                            $queryParams['page'] = $i;
                            if ($i == $page):
                        ?>
                                <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                                <a href="?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
                        <?php
                            endif;
                        endfor;
                        ?>
                        
                        <?php if ($page < $totalPages):
                            $queryParams['page'] = $page + 1;
                        ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>">Next →</a>
                        <?php else: ?>
                            <span class="disabled">Next →</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="testimonialModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Testimonial</h2>
            <form method="POST">
                <input type="hidden" name="action" id="form_action" value="add">
                <input type="hidden" name="testimonial_id" id="form_testimonial_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="name" id="form_name" required placeholder="Sarah M.">
                    </div>
                    
                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" id="form_age" placeholder="29">
                    </div>
                    
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="form_location" placeholder="Boston, MA">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Testimonial Quote *</label>
                    <textarea name="quote" id="form_quote" rows="4" required placeholder="Getting screened before starting our family gave us peace of mind..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Display Order</label>
                        <input type="number" name="display_order" id="form_display_order" value="0" min="0">
                        <small style="color: #666;">Lower numbers appear first</small>
                    </div>
                    
                    <div class="form-group" id="status_group" style="display: none;">
                        <label>Status</label>
                        <select name="is_active" id="form_is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">Save Testimonial</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Testimonial';
            document.getElementById('form_action').value = 'add';
            document.getElementById('testimonialModal').querySelectorAll('form')[0].reset();
            document.getElementById('form_display_order').value = '0';
            document.getElementById('status_group').style.display = 'none';
            document.getElementById('testimonialModal').style.display = 'block';
        }

        function editTestimonial(testimonial) {
            document.getElementById('modalTitle').textContent = 'Edit Testimonial';
            document.getElementById('form_action').value = 'update';
            document.getElementById('form_testimonial_id').value = testimonial.testimonial_id;
            document.getElementById('form_name').value = testimonial.name;
            document.getElementById('form_age').value = testimonial.age || '';
            document.getElementById('form_location').value = testimonial.location || '';
            document.getElementById('form_quote').value = testimonial.quote;
            document.getElementById('form_display_order').value = testimonial.display_order || 0;
            document.getElementById('form_is_active').value = testimonial.is_active ? '1' : '0';
            document.getElementById('status_group').style.display = 'block';
            document.getElementById('testimonialModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('testimonialModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('testimonialModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>