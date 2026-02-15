<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';

session_start();

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Session timeout logic from testimonials.php
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

$db = Database::getInstance()->getConnection();

// Helper to generate slug from title
function createSlug($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            $is_published = ($_POST['status'] === 'published') ? 1 : 0;
            $slug = createSlug($_POST['title']);
            $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;

            switch ($_POST['action']) {
                case 'add':
                    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, category, author_id, published_at, is_published, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['excerpt'],
                        $_POST['content'],
                        $_POST['category'],
                        $_SESSION['admin_id'],
                        $published_at,
                        $is_published
                    ]);
                    $success = "Blog post added successfully!";
                    break;
                    
                case 'update':
                    $stmt = $db->prepare("UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, category = ?, published_at = ?, is_published = ?, updated_at = NOW() WHERE post_id = ?");
                    $stmt->execute([
                        $_POST['title'],
                        $slug,
                        $_POST['excerpt'],
                        $_POST['content'],
                        $_POST['category'],
                        $published_at,
                        $is_published,
                        $_POST['post_id']
                    ]);
                    $success = "Blog post updated successfully!";
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM blog_posts WHERE post_id = ?");
                    $stmt->execute([$_POST['post_id']]);
                    $success = "Blog post deleted successfully!";
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$category_filter = $_GET['category'] ?? 'all';

// Build query
$where_clauses = [];
$params = [];

if ($status_filter !== 'all') {
    $where_clauses[] = "is_published = ?";
    $params[] = ($status_filter === 'published') ? 1 : 0;
}

if ($category_filter !== 'all') {
    $where_clauses[] = "category = ?";
    $params[] = $category_filter;
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get blog posts
$sql = "SELECT b.*, a.username as author_name 
        FROM blog_posts b 
        LEFT JOIN admins a ON b.author_id = a.admin_id 
        $where_sql 
        ORDER BY b.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $db->query("SELECT DISTINCT category FROM blog_posts WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Statistics
$stats = [
    'total' => $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
    'published' => $db->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = 1")->fetchColumn(),
    'drafts' => $db->query("SELECT COUNT(*) FROM blog_posts WHERE is_published = 0")->fetchColumn(),
];

$adminName = $_SESSION['admin_username'] ?? 'Admin';
$adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role'] ?? 'staff'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management | LuckyGeneMDx Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        /* Shared Styles from Testimonials */
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
            text-decoration: none;
        }
        .admin-nav-item:hover, .admin-nav-item.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--color-medical-teal);
        }
        .admin-main { flex: 1; margin-left: 260px; padding: 2rem; background: var(--color-light-gray); }
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--color-medical-teal);
        }
        .stat-value { font-size: 2rem; font-weight: 700; color: var(--color-medical-teal); margin-bottom: 0.25rem; }
        .stat-label { color: var(--color-dark-gray); font-size: 0.9rem; }
        .filters-bar {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
            display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;
        }
        .filter-group { flex: 1; min-width: 200px; }
        .table-container { background: white; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); overflow: hidden; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-medium-gray); }
        .table th { font-weight: 600; color: var(--color-primary-deep-blue); background: var(--color-light-gray); }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 500; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: var(--radius-sm); font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--color-medical-teal); color: white; }
        .btn-secondary { background: var(--color-dark-gray); color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 2rem auto; padding: 2rem; border-radius: var(--radius-md); max-width: 850px; max-height: 90vh; overflow-y: auto; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.6rem; border: 1px solid var(--color-medium-gray); border-radius: var(--radius-sm); font-family: inherit; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .alert { padding: 1rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
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
                <a href="testimonials.php" class="admin-nav-item">💬 Testimonials</a>
                <a href="blog.php" class="admin-nav-item active">📝 Blog Posts</a>
                <a href="settings.php" class="admin-nav-item">⚙️ Settings</a>
                <a href="logout.php" class="admin-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1 style="margin-bottom: 0.25rem;">Blog Management</h1>
                <p style="color: var(--color-dark-gray); margin: 0;">Manage your articles and announcements</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
                    <div class="stat-label">Total Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['published']); ?></div>
                    <div class="stat-label">Published</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['drafts']); ?></div>
                    <div class="stat-label">Drafts</div>
                </div>
            </div>

            <form method="GET" class="filters-bar">
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Category</label>
                    <select name="category" onchange="this.form.submit()">
                        <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="flex: 0;">
                    <button type="button" onclick="showAddModal()" class="btn btn-primary">+ New Post</button>
                </div>
            </form>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?php echo $post['post_id']; ?></td>
                                <td style="max-width: 250px;"><strong><?php echo htmlspecialchars(substr($post['title'], 0, 50)); ?>...</strong></td>
                                <td><span class="badge badge-secondary"><?php echo htmlspecialchars($post['category'] ?? 'General'); ?></span></td>
                                <td><?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $post['is_published'] ? 'success' : 'warning'; ?>">
                                        <?php echo $post['is_published'] ? 'Published' : 'Draft'; ?>
                                    </span>
                                </td>
                                <td><?php echo $post['published_at'] ? date('M d, Y', strtotime($post['published_at'])) : '-'; ?></td>
                                <td style="white-space: nowrap;">
                                    <button onclick='editPost(<?php echo json_encode($post, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="btn btn-sm btn-secondary">Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this post?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="postModal" class="modal">
        <div class="modal-content">
            <span style="float:right; cursor:pointer; font-size:1.5rem;" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Blog Post</h2>
            <form method="POST" id="postForm" style="margin-top: 1.5rem;">
                <input type="hidden" name="action" id="form_action" value="add">
                <input type="hidden" name="post_id" id="form_post_id">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="form_title" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <input type="text" name="category" id="form_category" required list="categoryList">
                        <datalist id="categoryList">
                            <option value="Carrier Screening">
                            <option value="Family Planning">
                            <option value="Genetic Conditions">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" id="form_status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Publish Date</label>
                        <input type="datetime-local" name="published_at" id="form_published_at">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Excerpt *</label>
                    <textarea name="excerpt" id="form_excerpt" rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Content (HTML allowed) *</label>
                    <textarea name="content" id="form_content" rows="12" required></textarea>
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Save Post</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Blog Post';
            document.getElementById('form_action').value = 'add';
            document.getElementById('postForm').reset();
            document.getElementById('postModal').style.display = 'block';
        }

        function editPost(post) {
            document.getElementById('modalTitle').textContent = 'Edit Blog Post';
            document.getElementById('form_action').value = 'update';
            document.getElementById('form_post_id').value = post.post_id;
            document.getElementById('form_title').value = post.title;
            document.getElementById('form_category').value = post.category;
            document.getElementById('form_excerpt').value = post.excerpt;
            document.getElementById('form_content').value = post.content;
            document.getElementById('form_status').value = post.is_published == 1 ? 'published' : 'draft';
            
            if (post.published_at) {
                const date = new Date(post.published_at);
                document.getElementById('form_published_at').value = date.toISOString().slice(0, 16);
            }
            document.getElementById('postModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('postModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('postModal')) closeModal();
        }
    </script>
</body>
</html>