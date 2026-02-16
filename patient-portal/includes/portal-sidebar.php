<!-- RESPONSIVE PATIENT PORTAL SIDEBAR -->
<aside class="portal-sidebar" id="portalSidebar">
    <div class="portal-sidebar-header">
        <a href="../index.php" class="portal-logo">
            🧬 <span class="logo-text">LuckyGeneMDx</span>
        </a>
        <div class="portal-user-info">
            <div class="portal-user-name"><?php echo htmlspecialchars($userName ?? $firstName ?? 'User'); ?></div>
        
        </div>
    </div>
    
    <nav class="portal-nav">
        <a href="index.php" class="portal-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="orders.php" class="portal-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📦</span>
            <span class="nav-text">My Orders</span>
        </a>
        <a href="results.php" class="portal-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'results.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📄</span>
            <span class="nav-text">My Results</span>
        </a>
        <a href="settings.php" class="portal-nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <span class="nav-icon">⚙️</span>
            <span class="nav-text">Settings</span>
        </a>
        
        <div class="nav-divider"></div>
        
        <a href="../resources.php" class="portal-nav-item">
            <span class="nav-icon">📖</span>
            <span class="nav-text">Resources</span>
        </a>
        <a href="../index.php" class="portal-nav-item">
            <span class="nav-icon">←</span>
            <span class="nav-text">Back to Website</span>
        </a>
        <a href="logout.php" class="portal-nav-item nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </nav>
</aside>

<!-- Mobile Menu Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle" aria-label="Toggle sidebar menu">
    <span></span>
    <span></span>
    <span></span>
</button>

<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
/* ============================================
   RESPONSIVE PORTAL SIDEBAR
   ============================================ */

/* Sidebar Base */
.portal-sidebar {
    width: 280px;
    background: linear-gradient(180deg, #0A1F44 0%, #051129 100%);
    color: white;
    padding: 0;
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 1100;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Sidebar Header */
.portal-sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

/* Logo Styling (Matches Navbar) */
.portal-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
    margin-bottom: 1.5rem;
    transition: transform 0.2s ease;
}

.portal-logo:hover {
    transform: scale(1.05);
}

.portal-logo .logo-text {
    background: linear-gradient(135deg, #ffffff 0%, #00B3A4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* User Info */
.portal-user-info {
    padding: 1rem;
    background: rgba(0, 179, 164, 0.1);
    border-radius: 12px;
    border-left: 3px solid #00B3A4;
}

.portal-user-name {
    font-weight: 600;
    font-size: 1.05rem;
    color: white;
    margin-bottom: 0.25rem;
}

.portal-user-email {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Navigation */
.portal-nav {
    padding: 1.5rem 0;
}

.portal-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 24px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.portal-nav-item:hover {
    background: rgba(255, 255, 255, 0.08);
    color: white;
    border-left-color: rgba(0, 179, 164, 0.5);
}

.portal-nav-item.active {
    background: rgba(0, 179, 164, 0.15);
    color: white;
    border-left-color: #00B3A4;
    font-weight: 600;
}

.portal-nav-item.active::before {
    content: '';
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: #00B3A4;
    border-radius: 50%;
}

.nav-icon {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.nav-text {
    font-size: 0.95rem;
    flex: 1;
}

/* Nav Divider */
.nav-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
    margin: 1.5rem 1rem;
}

/* Logout Button Styling */
.nav-logout {
    margin-top: 1rem;
    color: rgba(255, 100, 100, 0.9);
}

.nav-logout:hover {
    background: rgba(255, 100, 100, 0.1);
    border-left-color: #ff6464;
    color: #ff8888;
}

/* Mobile Toggle Button (Hidden on Desktop) */
.mobile-sidebar-toggle {
    display: none;
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1200;
    width: 45px;
    height: 45px;
    background: #0A1F44;
    border: none;
    border-radius: 10px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.mobile-sidebar-toggle:hover {
    background: #00B3A4;
    transform: scale(1.05);
}

.mobile-sidebar-toggle span {
    width: 22px;
    height: 3px;
    background: white;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.mobile-sidebar-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(6px, 6px);
}

.mobile-sidebar-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-sidebar-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* Sidebar Overlay (Mobile Only) */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sidebar-overlay.active {
    opacity: 1;
}

/* Main Content Offset */
.portal-main {
    margin-left: 280px;
    padding: 2rem;
    background: #f8f9fa;
    min-height: 100vh;
}

/* ============================================
   TABLET & MOBILE RESPONSIVE
   ============================================ */

@media (max-width: 1024px) {
    .portal-sidebar {
        width: 260px;
    }
    
    .portal-main {
        margin-left: 260px;
    }
}

@media (max-width: 768px) {
    /* Show mobile toggle */
    .mobile-sidebar-toggle {
        display: flex;
    }
    
    /* Hide sidebar by default on mobile */
    .portal-sidebar {
        transform: translateX(-100%);
    }
    
    /* Show sidebar when active */
    .portal-sidebar.active {
        transform: translateX(0);
    }
    
    /* Show overlay when sidebar is active */
    .sidebar-overlay {
        display: block;
    }
    
    .sidebar-overlay.active {
        opacity: 1;
    }
    
    /* Remove main content offset on mobile */
    .portal-main {
        margin-left: 0;
        padding: 80px 15px 2rem;
    }
    
    /* Adjust sidebar header for mobile */
    .portal-sidebar-header {
        padding: 1.5rem 1.25rem;
    }
    
    .portal-logo {
        font-size: 1.3rem;
    }
    
    /* Prevent body scroll when sidebar is open */
    body.sidebar-open {
        overflow: hidden;
    }
}

@media (max-width: 480px) {
    .portal-sidebar {
        width: 85vw;
        max-width: 300px;
    }
    
    .mobile-sidebar-toggle {
        width: 40px;
        height: 40px;
    }
    
    .mobile-sidebar-toggle span {
        width: 20px;
    }
    
    .portal-main {
        padding: 70px 12px 1.5rem;
    }
}

/* Scrollbar Styling for Sidebar */
.portal-sidebar::-webkit-scrollbar {
    width: 6px;
}

.portal-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.portal-sidebar::-webkit-scrollbar-thumb {
    background: rgba(0, 179, 164, 0.5);
    border-radius: 3px;
}

.portal-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 179, 164, 0.7);
}
</style>

<script>
// ============================================
// RESPONSIVE SIDEBAR FUNCTIONALITY
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('portalSidebar');
    const toggle = document.getElementById('mobileSidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    if (!sidebar || !toggle || !overlay) return;
    
    // Toggle sidebar on mobile
    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
    });
    
    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        closeSidebar();
    });
    
    // Close sidebar when clicking nav links on mobile
    const navLinks = sidebar.querySelectorAll('.portal-nav-item');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        }, 250);
    });
    
    function toggleSidebar() {
        const isActive = sidebar.classList.contains('active');
        
        if (isActive) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    function openSidebar() {
        sidebar.classList.add('active');
        toggle.classList.add('active');
        overlay.classList.add('active');
        body.classList.add('sidebar-open');
    }
    
    function closeSidebar() {
        sidebar.classList.remove('active');
        toggle.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('sidebar-open');
    }
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
});
</script>