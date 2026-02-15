<?php
$adminName = $_SESSION['admin_username'];
$adminRole = ucwords(str_replace('_', ' ', $_SESSION['admin_role']));
?>

<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h2> 🧬 LuckyGeneMDx</h2>
        <div class="admin-sidebar-user" style="text-transform: uppercase;">
            <?php echo htmlspecialchars($adminName); ?><br>
            <small><?php echo htmlspecialchars($adminRole); ?></small>
        </div>
    </div>
    
    <nav class="admin-nav">
        <?php
        // Get current page to set active class
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>
        <a href="index.php" class="admin-nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">📊 Dashboard</a>
        <a href="orders.php" class="admin-nav-item <?php echo $currentPage == 'orders.php' ? 'active' : ''; ?>">📦 Orders</a>
        <a href="upload-results.php" class="admin-nav-item <?php echo $currentPage == 'upload-results.php' ? 'active' : ''; ?>">📄 Upload Results</a>
        <a href="users.php" class="admin-nav-item <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">👥 Users</a>
        <a href="testimonials.php" class="admin-nav-item <?php echo $currentPage == 'testimonials.php' ? 'active' : ''; ?>">💬 Testimonials</a>
        <a href="blog.php" class="admin-nav-item <?php echo $currentPage == 'blog.php' ? 'active' : ''; ?>">📰 Blog</a>
        <a href="settings.php" class="admin-nav-item <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">⚙️ Settings</a>
        
        <a href="logout.php" class="admin-nav-item" style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">🚪 Logout</a>
    </nav>
</aside>