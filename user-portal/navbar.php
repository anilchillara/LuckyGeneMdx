<?php
if (!defined('LuckyGenesMDx')) {
    exit('Direct access denied');
}

$currentPage = basename($_SERVER['PHP_SELF']);

// Ensure initials are available if not set by the parent page
if (!isset($initials)) {
    $nameForNav = $_SESSION['user_name'] ?? 'Patient';
    $initials = strtoupper(substr($nameForNav, 0, 1));
    if (strpos($nameForNav, ' ') !== false) {
        $initials .= strtoupper(substr(explode(' ', $nameForNav)[1], 0, 1));
    }
}
?>
<nav class="navbar">
  <a href="../index.php" class="brand">
    <img src="../assets/images/logo_small.png" alt="Logo" style="height: 32px; width: auto;"> <?php echo htmlspecialchars(SITE_NAME); ?>
  </a>
  <button class="mobile-toggle" id="mobile-menu-btn" aria-label="Toggle navigation">☰</button>
  <div class="nav-items" id="nav-items">
    <a href="index.php" class="nav-link <?php echo ($currentPage == 'index.php' || $currentPage == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
    <a href="orders.php" class="nav-link <?php echo $currentPage == 'orders.php' ? 'active' : ''; ?>">My Orders</a>
    <a href="results.php" class="nav-link <?php echo $currentPage == 'results.php' ? 'active' : ''; ?>">Results</a>
    <a href="notifications.php" class="nav-link <?php echo $currentPage == 'notifications.php' ? 'active' : ''; ?>">Notifications</a>
    <a href="settings.php" class="nav-link <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">Settings</a>
  </div>
  <div class="user-menu" id="user-menu">
    <button id="theme-toggle" class="btn btn-outline btn-sm" style="border:none; font-size:1.2rem; padding:4px 8px; margin-right:5px; background:transparent;">🌙</button>
    <a href="profile.php" class="avatar" title="My Profile" style="text-decoration:none;"><?php echo htmlspecialchars($initials); ?></a>
    <a href="logout.php" class="btn btn-outline btn-sm">Sign Out</a>
  </div>
</nav>
<script>
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const navItems = document.getElementById('nav-items');
    const userMenu = document.getElementById('user-menu');
    if(mobileBtn) mobileBtn.addEventListener('click', () => {
        navItems.classList.toggle('active');
        userMenu.classList.toggle('active');
        mobileBtn.textContent = navItems.classList.contains('active') ? '✕' : '☰';
    });
</script>