<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['user_id']);
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

    /* Dark Mode Overrides for Navbar */
    body.dark-theme .navbar {
        background: rgba(26, 29, 33, 0.95);
        border-bottom-color: #343a40;
    }
    body.dark-theme .nav-link {
        color: #adb5bd;
    }
    body.dark-theme .nav-link:hover, body.dark-theme .nav-link.active {
        color: var(--nav-teal);
    }
    body.dark-theme .btn-nav-outline {
        color: #ffffff;
        border-color: #495057;
    }
</style>

<nav class="navbar">
    <a href="index.php" class="brand">
        <span>🧬</span> LuckyGeneMDx
    </a>
    <div class="nav-items">
        <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">Home</a>
        <a href="about-genetic-screening.php" class="nav-link <?php echo $currentPage == 'about-genetic-screening.php' ? 'active' : ''; ?>">About Screening</a>
        <a href="how-it-works.php" class="nav-link <?php echo $currentPage == 'how-it-works.php' ? 'active' : ''; ?>">How It Works</a>
        <a href="resources.php" class="nav-link <?php echo $currentPage == 'resources.php' ? 'active' : ''; ?>">Resources</a>
        <a href="contact.php" class="nav-link <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>">Contact</a>
        <a href="track-order.php" class="nav-link <?php echo $currentPage == 'track-order.php' ? 'active' : ''; ?>">Track Order</a>
    </div>
    <div class="nav-actions">
        <button id="theme-toggle" class="btn-nav btn-nav-outline" style="border:none; font-size:1.2rem; padding:4px 8px; margin-right:5px; background:transparent;">🌙</button>
        <?php if ($isLoggedIn): ?>
            <a href="user-portal/index.php" class="btn-nav btn-nav-outline">Dashboard</a>
            <a href="user-portal/logout.php" class="btn-nav btn-nav-primary">Sign Out</a>
        <?php else: ?>
            <a href="user-portal/login.php" class="btn-nav btn-nav-outline">Patient Login</a>
            <a href="request-kit.php" class="btn-nav btn-nav-primary">Order Kit</a>
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
    })();
</script>