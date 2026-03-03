<?php
if (!defined('LuckyGenesMDx')) {
    exit('Direct access denied');
}
$currentPage = basename($_SERVER['PHP_SELF']);
$adminName = $_SESSION['admin_username'] ?? 'Admin';
$initials = strtoupper(substr($adminName, 0, 2));
?>
<nav class="admin-navbar">
  <a href="index.php" class="admin-brand">
    <img src="../assets/images/logo_small.png" alt="Logo" style="height: 32px; width: auto;"> <?php echo htmlspecialchars(SITE_NAME); ?> <span class="pill-badge-teal">Admin</span>
  </a>
  <button class="mobile-toggle" id="mobile-menu-btn" aria-label="Toggle navigation">☰</button>
  <div class="admin-nav-items" id="admin-nav-items">
    <a href="index.php" class="admin-nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">Dashboard</a>
    <a href="orders.php" class="admin-nav-link <?php echo ($currentPage == 'orders.php' || $currentPage == 'order-detail.php') ? 'active' : ''; ?>">Orders</a>
    <a href="Users.php" class="admin-nav-link <?php echo $currentPage == 'Users.php' ? 'active' : ''; ?>">Users</a>
    <a href="interest-list.php" class="admin-nav-link <?php echo $currentPage == 'interest-list.php' ? 'active' : ''; ?>">Interest List</a>
    <a href="testimonials.php" class="admin-nav-link <?php echo $currentPage == 'testimonials.php' ? 'active' : ''; ?>">Testimonials</a>
    <a href="blog.php" class="admin-nav-link <?php echo $currentPage == 'blog.php' ? 'active' : ''; ?>">Blog</a>
    <a href="upload-results.php" class="admin-nav-link <?php echo $currentPage == 'upload-results.php' ? 'active' : ''; ?>">Upload Results</a>
    <a href="activity-log.php" class="admin-nav-link <?php echo $currentPage == 'activity-log.php' ? 'active' : ''; ?>">Activity Log</a>
    <a href="settings.php" class="admin-nav-link <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">Settings</a>
  </div>
  <div class="admin-user-menu" id="admin-user-menu">
    <button id="theme-toggle" class="btn btn-outline btn-sm" style="border:none; font-size:1.2rem; padding:4px 8px; margin-right:5px; background:transparent;">🌙</button>
    <div class="admin-avatar"><?php echo htmlspecialchars($initials); ?></div>
    <a href="logout.php" class="btn btn-outline btn-sm">Sign Out</a>
  </div>
</nav>
<script>
    const mobileBtnAdmin = document.getElementById('mobile-menu-btn');
    const navItemsAdmin = document.getElementById('admin-nav-items');
    const userMenuAdmin = document.getElementById('admin-user-menu');
    if(mobileBtnAdmin) mobileBtnAdmin.addEventListener('click', () => {
        navItemsAdmin.classList.toggle('active');
        userMenuAdmin.classList.toggle('active');
        mobileBtnAdmin.textContent = navItemsAdmin.classList.contains('active') ? '✕' : '☰';
    });
</script>