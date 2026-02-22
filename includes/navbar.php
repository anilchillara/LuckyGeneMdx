<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['user_id']);

// Ensure Database class is loaded
require_once __DIR__ . '/Database.php';

// Fetch Navbar Items from Database
$navItems = [];
$mainItems = [];
$actionItems = [];
$useDbNav = false;
try {
    $db = Database::getInstance()->getConnection();
    // Check if table exists to prevent errors before migration
    $stmt = $db->query("SHOW TABLES LIKE 'navbar_items'");
    if ($stmt->rowCount() > 0) {
        $useDbNav = true;
        $stmt = $db->query("SELECT * FROM navbar_items WHERE is_active = 1 ORDER BY display_order ASC");
        $navItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($navItems as $item) {
            if (!isset($item['section']) || $item['section'] === 'main') {
                $mainItems[] = $item;
            } else {
                $actionItems[] = $item;
            }
        }
    }
} catch (Exception $e) {
    // Fallback to defaults if DB error
    $useDbNav = false;
}

// Default items if DB is empty or table missing
if (!$useDbNav) {
    $mainItems = [
        ['label' => 'Home', 'url' => 'index.php'],
        ['label' => 'About Screening', 'url' => 'about-genetic-screening.php'],
        ['label' => 'How It Works', 'url' => 'how-it-works.php'],
        ['label' => 'Resources', 'url' => 'resources.php'],
        ['label' => 'Contact', 'url' => 'contact.php'],
        ['label' => 'Track Order', 'url' => 'track-order.php'],
        ['label' => 'Interest List', 'url' => 'intrest-list.php']
    ];
}
?>
<style>
    :root {
        /* Brand Palette */
        --nav-deep-blue: #0A1F44;
        --nav-teal: #00B3A4;
        --nav-teal-dark: #008c7a;
        --nav-white: #FFFFFF;
        --nav-gray: #6C757D;
        --nav-light-gray: #F8F9FA;
        --nav-border: #E9ECEF;
        --nav-brand-gradient: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
    }

    /* Navigation Container */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.8rem 2rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--nav-border);
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: system-ui, "Segoe UI", Roboto, sans-serif;
    }

    /* Brand Logo */
    .brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1.2rem;
        text-decoration: none;
        background: var(--nav-brand-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .brand span {
        font-size: 1.4rem;
        -webkit-text-fill-color: initial;
    }

    /* Nav Items */
    .nav-items {
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .nav-link {
        color: var(--nav-gray);
        font-weight: 500;
        font-size: 0.95rem;
        text-decoration: none;
        padding: 0.5rem 0;
        transition: color 0.2s;
        position: relative;
    }

    .nav-link:hover, .nav-link.active {
        color: var(--nav-teal);
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--nav-teal);
        border-radius: 2px;
    }

    /* Action Buttons */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .btn-nav {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-nav-outline {
        color: var(--nav-deep-blue);
        border: 1px solid var(--nav-border);
        background: transparent;
    }
    .btn-nav-outline:hover {
        border-color: var(--nav-teal);
        color: var(--nav-teal);
    }

    .btn-nav-primary {
        background: var(--nav-teal);
        color: white;
        border: 1px solid var(--nav-teal);
    }
    .btn-nav-primary:hover {
        background: var(--nav-teal-dark);
        border-color: var(--nav-teal-dark);
    }

    /* Mobile Toggle */
    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--nav-deep-blue);
        padding: 0.5rem;
    }

    @media (max-width: 960px) {
        .navbar {
            flex-wrap: wrap;
            padding: 1rem;
        }
        
        .mobile-toggle {
            display: block;
        }

        .nav-items, .nav-actions {
            display: none;
            width: 100%;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            margin-top: 1rem;
        }
        
        .nav-items.active, .nav-actions.active {
            display: flex;
        }

        .nav-link {
            width: 100%;
            padding: 1rem 0;
            border-bottom: 1px solid var(--nav-border);
        }
        
        .nav-actions {
            border-top: 1px solid var(--nav-border);
            padding-top: 1rem;
            gap: 1rem;
        }
    }

    /* Dark Mode Overrides for Navbar */
    body.dark-theme .navbar {
        background: rgba(59, 59, 59, 0.95);
        border-bottom-color: #606060;
    }
    body.dark-theme .nav-link {
        color: #909090;
    }
    body.dark-theme .nav-link:hover, body.dark-theme .nav-link.active {
        color: #B2B2B2;
    }
    body.dark-theme .btn-nav-outline {
        color: #B2B2B2;
        border-color: #606060;
    }
    body.dark-theme .mobile-toggle {
        color: #B2B2B2;
    }
    body.dark-theme .brand {
        background: linear-gradient(135deg, #909090 0%, #B2B2B2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<nav class="navbar">
    <a href="index.php" class="brand">
        <span>🧬</span> <?php echo htmlspecialchars(SITE_NAME); ?>
    </a>
    <button class="mobile-toggle" id="mobile-menu-btn" aria-label="Toggle navigation">☰</button>
    <div class="nav-items" id="nav-items">
        <?php foreach ($mainItems as $item): ?>
            <a href="<?php echo htmlspecialchars($item['url']); ?>" class="nav-link <?php echo $currentPage == basename($item['url']) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($item['label']); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="nav-actions" id="nav-actions">
        <button id="theme-toggle" class="btn-nav btn-nav-outline" style="border:none; font-size:1.2rem; padding:4px 8px; margin-right:5px; background:transparent;">🌙</button>
        
        <?php if ($useDbNav): ?>
            <?php foreach ($actionItems as $item): 
                // Auth Status: 0=All, 1=LoggedIn, 2=LoggedOut
                if ($item['auth_status'] == 1 && !$isLoggedIn) continue;
                if ($item['auth_status'] == 2 && $isLoggedIn) continue;
            ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>" class="<?php echo htmlspecialchars($item['css_class'] ?? 'btn-nav btn-nav-outline'); ?>">
                    <?php echo htmlspecialchars($item['label']); ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if ($isLoggedIn): ?>
            <!-- Fallback if DB empty -->
            <a href="user-portal/index.php" class="btn-nav btn-nav-outline">Dashboard</a>
            <a href="user-portal/logout.php" class="btn-nav btn-nav-primary">Sign Out</a>
            <?php else: ?>
            <a href="user-portal/login.php" class="btn-nav btn-nav-outline">Patient Login</a>
            <a href="request-kit.php" class="btn-nav btn-nav-primary">Order Kit</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>

<script>
    (function() {
        const toggle = document.getElementById('theme-toggle');
        const body = document.body;
        
        if (localStorage.getItem('portal_theme') === 'dark') {
            body.classList.add('dark-theme');
            if(toggle) toggle.textContent = '☀️';
        }

        if(toggle) toggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('portal_theme', isDark ? 'dark' : 'light');
            toggle.textContent = isDark ? '☀️' : '🌙';
        });

        // Mobile Menu Toggle
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const navItems = document.getElementById('nav-items');
        const navActions = document.getElementById('nav-actions');
        if(mobileBtn) mobileBtn.addEventListener('click', () => {
            navItems.classList.toggle('active');
            navActions.classList.toggle('active');
            mobileBtn.textContent = navItems.classList.contains('active') ? '✕' : '☰';
        });
    })();
</script>