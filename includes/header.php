<?php
// Get user info if logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
$userInitials = '';

if ($isLoggedIn) {
    try {
        // Only try to get user data if Database class is available
        if (class_exists('Database')) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT full_name, email FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Get initials from name
                $nameParts = explode(' ', $user['full_name']);
                $userInitials = strtoupper(substr($nameParts[0], 0, 1));
                if (isset($nameParts[1])) {
                    $userInitials .= strtoupper(substr($nameParts[1], 0, 1));
                }
            }
        } else {
            // Fallback: use session data
            if (isset($_SESSION['user_name'])) {
                $nameParts = explode(' ', $_SESSION['user_name']);
                $userInitials = strtoupper(substr($nameParts[0], 0, 1));
                if (isset($nameParts[1])) {
                    $userInitials .= strtoupper(substr($nameParts[1], 0, 1));
                }
                $user = [
                    'full_name' => $_SESSION['user_name'],
                    'email' => $_SESSION['user_email'] ?? ''
                ];
            }
        }
    } catch (Exception $e) {
        // Silently handle any database errors
        error_log("Header user data error: " . $e->getMessage());
        
        // Fallback to session data
        if (isset($_SESSION['user_name'])) {
            $nameParts = explode(' ', $_SESSION['user_name']);
            $userInitials = strtoupper(substr($nameParts[0], 0, 1));
            if (isset($nameParts[1])) {
                $userInitials .= strtoupper(substr($nameParts[1], 0, 1));
            }
            $user = [
                'full_name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'] ?? ''
            ];
        }
    }
}

// Determine if we're in patient portal
$inPortal = strpos($_SERVER['PHP_SELF'], '/user-portal/') !== false;
$baseUrl = $inPortal ? '../' : '';
$portalUrl = $inPortal ? '' : 'user-portal/';
?>

<!-- MODERN RESPONSIVE NAVIGATION WITH USER DROPDOWN -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?php echo $baseUrl; ?>index.php" class="navbar-logo">
            🧬 <span>LuckyGeneMDx</span>
        </a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <div class="nav-menu" id="navMenu">
            <a href="<?php echo $baseUrl; ?>index.php">Home</a>
            <a href="<?php echo $baseUrl; ?>about-genetic-screening.php">About Screening</a>
            <a href="<?php echo $baseUrl; ?>how-it-works.php">How It Works</a>
            <a href="<?php echo $baseUrl; ?>resources.php">Resources</a>
            <a href="<?php echo $baseUrl; ?>track-order.php">Track Order</a>
            
            <?php if ($isLoggedIn && $user): ?>
                <!-- User Account Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-dropdown-btn" id="userDropdownBtn" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo htmlspecialchars($userInitials); ?>
                        </div>
                        <span class="user-name-desktop"><?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?></span>
                        <svg class="dropdown-arrow" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                            <path d="M6 8L2 4h8l-4 4z"/>
                        </svg>
                    </button>
                    
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <div class="dropdown-user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                <div class="dropdown-user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="<?php echo $portalUrl; ?>index.php" class="dropdown-item">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 2a1 1 0 011 1v5h5a1 1 0 110 2H9v5a1 1 0 11-2 0V10H2a1 1 0 110-2h5V3a1 1 0 011-1z"/>
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="<?php echo $portalUrl; ?>orders.php" class="dropdown-item">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M3 3h10a1 1 0 011 1v8a1 1 0 01-1 1H3a1 1 0 01-1-1V4a1 1 0 011-1zm1 2v6h8V5H4z"/>
                            </svg>
                            My Orders
                        </a>
                        
                        <a href="<?php echo $portalUrl; ?>results.php" class="dropdown-item">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M4 2h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V4a2 2 0 012-2zm1 3v1h6V5H5zm0 3v1h6V8H5zm0 3v1h4v-1H5z"/>
                            </svg>
                            My Results
                        </a>
                        
                        <a href="<?php echo $portalUrl; ?>settings.php" class="dropdown-item">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 1a1 1 0 011 1v.5a5 5 0 012.5 1.5l.5-.5a1 1 0 111.5 1.5l-.5.5a5 5 0 011.5 2.5H15a1 1 0 110 2h-.5a5 5 0 01-1.5 2.5l.5.5a1 1 0 11-1.5 1.5l-.5-.5a5 5 0 01-2.5 1.5V15a1 1 0 11-2 0v-.5a5 5 0 01-2.5-1.5l-.5.5a1 1 0 11-1.5-1.5l.5-.5A5 5 0 012.5 9H2a1 1 0 110-2h.5a5 5 0 011.5-2.5L3.5 4A1 1 0 115 2.5l.5.5A5 5 0 018 2.5V2a1 1 0 011-1zm0 5a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                            Settings
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="<?php echo $portalUrl; ?>logout.php" class="dropdown-item dropdown-item-danger">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M6 2a1 1 0 00-1 1v10a1 1 0 001 1h1a1 1 0 100-2H6V4h1a1 1 0 100-2H6zm5 3a1 1 0 00-.707 1.707L11.586 8l-1.293 1.293a1 1 0 101.414 1.414l2-2a1 1 0 000-1.414l-2-2A1 1 0 0011 5z"/>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest User - Show Login & Request Kit -->
                <a href="<?php echo $baseUrl; ?>user-portal/login.php" class="nav-link-login">Sign In</a>
                <a href="<?php echo $baseUrl; ?>request-kit.php" class="nav-btn">Request Kit</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* ===== RESET ===== */
* { margin: 0; padding: 0; box-sizing: border-box; }

/* ===== NAVBAR BASE ===== */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
}

.navbar-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 80px;
}

/* ===== LOGO ===== */
.navbar-logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0A1F44;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.navbar-logo span {
    background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ===== DESKTOP MENU ===== */
.nav-menu {
    display: flex;
    gap: 30px;
    align-items: center;
}

.nav-menu a,
.nav-link-login {
    text-decoration: none;
    color: #0A1F44;
    font-weight: 500;
    font-size: 1rem;
    padding: 8px 0;
    transition: color 0.3s;
}

.nav-menu a:hover,
.nav-link-login:hover {
    color: #00B3A4;
}

.nav-btn {
    background: #00B3A4;
    color: white !important;
    padding: 12px 28px;
    border-radius: 50px;
}

.nav-btn:hover {
    background: #008c7a;
    transform: translateY(-2px);
}

/* ===== USER DROPDOWN ===== */
.user-dropdown {
    position: relative;
}

.user-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    background: none;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    padding: 6px 16px 6px 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-dropdown-btn:hover {
    border-color: #00B3A4;
    background: rgba(0, 179, 164, 0.05);
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.user-name-desktop {
    font-weight: 600;
    color: #0A1F44;
    font-size: 0.95rem;
}

.dropdown-arrow {
    transition: transform 0.3s ease;
    color: #666;
}

.user-dropdown-btn[aria-expanded="true"] .dropdown-arrow {
    transform: rotate(180deg);
}

/* ===== DROPDOWN MENU ===== */
.user-dropdown-menu {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 280px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1001;
}

.user-dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    padding: 16px;
    border-bottom: 1px solid #f0f0f0;
}

.dropdown-user-name {
    font-weight: 600;
    color: #0A1F44;
    margin-bottom: 4px;
}

.dropdown-user-email {
    font-size: 0.85rem;
    color: #666;
}

.dropdown-divider {
    height: 1px;
    background: #f0f0f0;
    margin: 8px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #0A1F44;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: rgba(0, 179, 164, 0.08);
    color: #00B3A4;
}

.dropdown-item-danger:hover {
    background: rgba(220, 53, 69, 0.08);
    color: #dc3545;
}

.dropdown-icon {
    opacity: 0.7;
}

/* ===== HAMBURGER (Hidden on Desktop) ===== */
.mobile-menu-btn {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    z-index: 1001;
}

.mobile-menu-btn span {
    width: 25px;
    height: 3px;
    background: #0A1F44;
    border-radius: 3px;
    transition: 0.3s;
}

.mobile-menu-btn.active span:nth-child(1) {
    transform: rotate(45deg) translate(7px, 7px);
}

.mobile-menu-btn.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-btn.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -7px);
}

/* ===== BODY OFFSET ===== */
body { padding-top: 80px; }

/* ===== MOBILE STYLES ===== */
@media screen and (max-width: 1024px) {
    .user-name-desktop {
        display: none;
    }
    
    .user-dropdown-btn {
        padding: 6px;
        border-radius: 50%;
    }
}

@media screen and (max-width: 768px) {
    .mobile-menu-btn {
        display: flex;
    }
    
    .nav-menu {
        position: fixed;
        top: 80px;
        left: 0;
        width: 100%;
        background: white;
        flex-direction: column;
        gap: 0;
        align-items: stretch;
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .nav-menu.open {
        max-height: 100vh;
        overflow-y: auto;
    }
    
    .nav-menu > a {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        width: 100%;
    }
    
    .nav-btn {
        margin: 12px 20px;
        text-align: center;
        border-bottom: none;
    }
    
    .nav-link-login {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    /* Mobile User Dropdown */
    .user-dropdown {
        width: 100%;
        position: static;
    }
    
    .user-dropdown-btn {
        width: calc(100% - 40px);
        margin: 12px 20px;
        justify-content: flex-start;
        padding: 12px 16px;
        border-radius: 12px;
    }
    
    .user-name-desktop {
        display: block;
    }
    
    .user-dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        border-radius: 0;
        margin-top: 0;
        opacity: 1;
        visibility: visible;
        transform: none;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .user-dropdown-menu.show {
        max-height: 400px;
    }
    
    .dropdown-header {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function() {
            mobileBtn.classList.toggle('active');
            navMenu.classList.toggle('open');
        });
        
        // Close mobile menu when clicking nav links (except dropdown)
        navMenu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!link.closest('.user-dropdown-menu')) {
                    mobileBtn.classList.remove('active');
                    navMenu.classList.remove('open');
                }
            });
        });
    }
    
    // User dropdown toggle
    const dropdownBtn = document.getElementById('userDropdownBtn');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.contains('show');
            
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-dropdown')) {
                closeDropdown();
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    }
    
    function openDropdown() {
        dropdownMenu.classList.add('show');
        dropdownBtn.setAttribute('aria-expanded', 'true');
    }
    
    function closeDropdown() {
        dropdownMenu.classList.remove('show');
        dropdownBtn.setAttribute('aria-expanded', 'false');
    }
});
</script>