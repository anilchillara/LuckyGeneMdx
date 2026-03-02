<?php
define('LuckyGenesMDx', true);
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
                    // Log Activity
                    $id = $db->lastInsertId();
                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'add_testimonial', 'testimonial', ?, ?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], $id, "Added testimonial from " . $_POST['name'], $_SERVER['REMOTE_ADDR']]);
                    break;
                    
                case 'update':
                    $stmt = $db->prepare("SELECT name, quote, is_active FROM testimonials WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $oldTestimonial = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    // Log Activity
                    $changes = [];
                    if ($oldTestimonial['name'] != $_POST['name']) $changes[] = "Name changed";
                    if ($oldTestimonial['quote'] != $_POST['quote']) $changes[] = "Quote updated";
                    if ($oldTestimonial['is_active'] != $_POST['is_active']) $changes[] = "Status changed";

                    $details = "Updated testimonial ID " . $_POST['testimonial_id'] . ". " . implode(', ', $changes);

                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'update_testimonial', 'testimonial', ?, ?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], $_POST['testimonial_id'], $details, $_SERVER['REMOTE_ADDR']]);
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("SELECT name FROM testimonials WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $name = $stmt->fetchColumn();

                    $stmt = $db->prepare("DELETE FROM testimonials WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $success = "Testimonial deleted successfully!";
                    // Log Activity
                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'delete_testimonial', 'testimonial', ?, ?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], $_POST['testimonial_id'], "Deleted testimonial from: " . ($name ?: 'Unknown'), $_SERVER['REMOTE_ADDR']]);
                    break;
                    
                case 'toggle_status':
                    $stmt = $db->prepare("SELECT is_active, name FROM testimonials WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $current = $stmt->fetch(PDO::FETCH_ASSOC);

                    $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE testimonial_id = ?");
                    $stmt->execute([$_POST['testimonial_id']]);
                    $success = "Testimonial status updated successfully!";
                    // Log Activity
                    $newStatus = $current['is_active'] ? 'Inactive' : 'Active';
                    $stmt = $db->prepare("INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address) VALUES (?, 'toggle_testimonial', 'testimonial', ?, ?, ?)");
                    $stmt->execute([$_SESSION['admin_id'], $_POST['testimonial_id'], "Toggled status to $newStatus for " . $current['name'], $_SERVER['REMOTE_ADDR']]);
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
    <title>Testimonial Management - LuckyGenesMDx Admin</title>
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
        .filters-form {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .filters-form { flex-direction: column; align-items: stretch; }
            .filters-form .btn { width: 100%; margin-top: 0.5rem; }
            .filters-form .form-group { min-width: 100%; margin-bottom: 0.5rem !important; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1>Testimonial Management</h1>
                <p><?php echo number_format($totalTestimonials); ?> total testimonials</p>
            </div>
        </div>

            <?php if (isset($success)): ?>
            <div class="glass-card-teal-left p-3 mb-3 text-teal"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="glass-card-error p-3 mb-3 text-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Statistics -->
        <div class="admin-grid" style="margin-bottom: 2rem;">
            <div class="admin-card admin-stat-card col-span-4 blue">
                <div class="stat-lbl">Total Testimonials</div>
                <div class="stat-val"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="admin-card admin-stat-card col-span-4 green">
                <div class="stat-lbl">Active</div>
                <div class="stat-val"><?php echo number_format($stats['active']); ?></div>
            </div>
            <div class="admin-card admin-stat-card col-span-4 orange">
                <div class="stat-lbl">Inactive</div>
                <div class="stat-val"><?php echo number_format($stats['inactive']); ?></div>
                </div>
        </div>

            <!-- Filters -->
        <div class="admin-card" style="margin-bottom: 2rem;">
            <form method="GET" action="" class="filters-form">
                <div class="form-group" style="flex:1; min-width:200px; margin-bottom:0;">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive Only</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    🔍 Filter
                </button>
                
                <button type="button" onclick="showAddModal()" class="btn btn-success">
                    + Add Testimonial
                </button>
                
                <?php if ($status_filter !== 'all'): ?>
                    <a href="testimonials.php" class="btn btn-outline">✕ Clear</a>
                <?php endif; ?>
            </form>
        </div>

            <!-- Testimonials Table -->
        <div class="admin-card" style="padding:0; overflow:hidden;">
                <?php if (empty($testimonials)): ?>
                <div style="text-align:center; padding:4rem 2rem;">
                    <div style="font-size:4rem; margin-bottom:1rem; opacity:0.3;">💬</div>
                        <h3>No testimonials found</h3>
                    <p style="color:var(--color-text-gray);">
                            <?php if ($status_filter !== 'all'): ?>
                                Try adjusting your filters.
                            <?php else: ?>
                                Add your first testimonial using the button above.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table">
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
                                        <span class="badge badge-<?php echo $testimonial['is_active'] ? 'green' : 'orange'; ?>">
                                                <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($testimonial['created_at'])); ?></td>
                                        <td style="white-space: nowrap;">
                                            <button onclick='editTestimonial(<?php echo json_encode($testimonial, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' 
                                                class="btn btn-outline btn-sm">Edit</button>
                                            
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="testimonial_id" value="<?php echo $testimonial['testimonial_id']; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $testimonial['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
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
                        <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm">← Previous</a>
                        <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">← Previous</button>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                            $queryParams['page'] = $i;
                            if ($i == $page):
                        ?>
                            <button class="btn btn-sm" style="cursor:default;"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm"><?php echo $i; ?></a>
                        <?php
                            endif;
                        endfor;
                        ?>
                        
                        <?php if ($page < $totalPages):
                            $queryParams['page'] = $page + 1;
                        ?>
                        <a href="?<?php echo http_build_query($queryParams); ?>" class="btn btn-outline btn-sm">Next →</a>
                        <?php else: ?>
                        <button class="btn btn-outline btn-sm" disabled style="opacity:0.5; cursor:not-allowed;">Next →</button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="testimonialModal" class="admin-modal">
        <div class="admin-modal-dialog">
            <div class="admin-modal-content">
                <form method="POST">
                    <div class="admin-modal-header">
                        <h3 id="modalTitle" class="admin-modal-title">Add New Testimonial</h3>
                        <button type="button" class="admin-modal-close" onclick="closeModal()">&times;</button>
                    </div>
                    <div class="admin-modal-body">
                        <input type="hidden" name="action" id="form_action" value="add">
                        <input type="hidden" name="testimonial_id" id="form_testimonial_id">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Name *</label>
                                <input type="text" name="name" id="form_name" required placeholder="Sarah M." class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" name="age" id="form_age" placeholder="29" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" id="form_location" placeholder="Boston, MA" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Testimonial Quote *</label>
                            <textarea name="quote" id="form_quote" rows="4" required placeholder="Getting screened before starting our family gave us peace of mind..." class="form-control"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="display_order" id="form_display_order" value="0" min="0" class="form-control">
                                <small style="color: var(--color-text-gray);">Lower numbers appear first</small>
                            </div>
                            
                            <div class="form-group" id="status_group" style="display: none;">
                                <label>Status</label>
                                <select name="is_active" id="form_is_active" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="admin-modal-footer">
                        <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Testimonial</button>
                    </div>
                </form>
            </div>
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