<!-- Modern Responsive Navigation -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="navbar-container">
        <a href="index.php" class="navbar-logo" aria-label="LuckyGeneMDx Home">
            🧬 <span class="logo-text">LuckyGeneMDx</span>
        </a>
        
        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <ul class="navbar-menu" id="navbarMenu" role="menubar">
            <li role="none"><a href="index.php" role="menuitem">Home</a></li>
            <li role="none"><a href="about-genetic-screening.php" role="menuitem">About Screening</a></li>
            <li role="none"><a href="how-it-works.php" role="menuitem">How It Works</a></li>
            <li role="none"><a href="resources.php" role="menuitem">Resources</a></li>
            <li role="none"><a href="track-order.php" role="menuitem">Track Order</a></li>
            <li role="none" class="nav-cta"><a href="request-kit.php" class="btn btn-primary" role="menuitem">Request Kit</a></li>
        </ul>
    </div>
</nav>

<style>
/* ============================================
   MODERN RESPONSIVE NAVBAR
   ============================================ */

.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: white;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    transition: box-shadow 0.3s ease;
}

.navbar.scrolled {
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
}

.navbar-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 80px;
}

/* Logo */
.navbar-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #0A1F44;
    text-decoration: none;
    transition: transform 0.2s ease;
}

.navbar-logo:hover {
    transform: scale(1.05);
}

.logo-text {
    background: linear-gradient(135deg, #0A1F44 0%, #00B3A4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Desktop Menu */
.navbar-menu {
    display: flex;
    align-items: center;
    gap: 35px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.navbar-menu li {
    position: relative;
}

.navbar-menu a {
    text-decoration: none;
    color: #0A1F44;
    font-weight: 500;
    font-size: 1rem;
    transition: color 0.3s ease;
    position: relative;
    padding: 8px 0;
}

.navbar-menu a:not(.btn):hover {
    color: #00B3A4;
}

/* Active state underline */
.navbar-menu a:not(.btn)::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: #00B3A4;
    transition: width 0.3s ease;
}

.navbar-menu a:not(.btn):hover::after,
.navbar-menu a:not(.btn).active::after {
    width: 100%;
}

/* CTA Button */
.navbar-menu .btn-primary {
    background: #00B3A4;
    color: white;
    padding: 12px 28px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.navbar-menu .btn-primary:hover {
    background: #008c7a;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 179, 164, 0.3);
}

/* Hamburger Toggle (Hidden on Desktop) */
.navbar-toggle {
    display: none;
    flex-direction: column;
    justify-content: space-around;
    width: 30px;
    height: 24px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    z-index: 1001;
}

.hamburger-line {
    width: 100%;
    height: 3px;
    background: #0A1F44;
    border-radius: 10px;
    transition: all 0.3s ease;
}

/* Hamburger Animation */
.navbar-toggle.active .hamburger-line:nth-child(1) {
    transform: rotate(45deg) translate(6px, 6px);
}

.navbar-toggle.active .hamburger-line:nth-child(2) {
    opacity: 0;
}

.navbar-toggle.active .hamburger-line:nth-child(3) {
    transform: rotate(-45deg) translate(6px, -6px);
}

/* Body offset for fixed navbar */
body {
    padding-top: 80px;
}

/* ============================================
   TABLET & MOBILE RESPONSIVE
   ============================================ */

@media (max-width: 1024px) {
    .navbar-menu {
        gap: 25px;
    }
    
    .navbar-menu a {
        font-size: 0.95rem;
    }
}

@media (max-width: 768px) {
    .navbar-container {
        height: 70px;
        padding: 0 15px;
    }
    
    body {
        padding-top: 70px;
    }
    
    /* Show hamburger on mobile */
    .navbar-toggle {
        display: flex;
    }
    
    /* Mobile Menu Styles */
    .navbar-menu {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        background: white;
        flex-direction: column;
        gap: 0;
        padding: 0;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-menu.active {
        max-height: calc(100vh - 70px);
        opacity: 1;
        overflow-y: auto;
        padding: 20px 0;
    }
    
    .navbar-menu li {
        width: 100%;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .navbar-menu li:last-child {
        border-bottom: none;
    }
    
    .navbar-menu a {
        display: block;
        padding: 18px 20px;
        font-size: 1.05rem;
    }
    
    .navbar-menu a:not(.btn)::after {
        display: none;
    }
    
    /* Mobile CTA Button */
    .nav-cta {
        margin: 20px 20px 10px;
        border-bottom: none !important;
    }
    
    .navbar-menu .btn-primary {
        display: block;
        text-align: center;
        width: 100%;
        padding: 15px 28px;
    }
    
    /* Prevent body scroll when menu is open */
    body.menu-open {
        overflow: hidden;
    }
}

/* Small Mobile */
@media (max-width: 480px) {
    .navbar-container {
        height: 65px;
    }
    
    body {
        padding-top: 65px;
    }
    
    .navbar-menu {
        top: 65px;
    }
    
    .navbar-logo {
        font-size: 1.3rem;
    }
}
</style>

<script>
// ============================================
// NAVBAR FUNCTIONALITY
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    const body = document.body;
    
    // ============================================
    // Mobile Menu Toggle
    // ============================================
    if (navbarToggle) {
        navbarToggle.addEventListener('click', function() {
            const isActive = navbarMenu.classList.contains('active');
            
            if (isActive) {
                // Close menu
                navbarMenu.classList.remove('active');
                navbarToggle.classList.remove('active');
                body.classList.remove('menu-open');
                navbarToggle.setAttribute('aria-expanded', 'false');
            } else {
                // Open menu
                navbarMenu.classList.add('active');
                navbarToggle.classList.add('active');
                body.classList.add('menu-open');
                navbarToggle.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Close menu when clicking on a link
        const menuLinks = navbarMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                navbarMenu.classList.remove('active');
                navbarToggle.classList.remove('active');
                body.classList.remove('menu-open');
                navbarToggle.setAttribute('aria-expanded', 'false');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideNav = navbar.contains(event.target);
            const isMenuActive = navbarMenu.classList.contains('active');
            
            if (!isClickInsideNav && isMenuActive) {
                navbarMenu.classList.remove('active');
                navbarToggle.classList.remove('active');
                body.classList.remove('menu-open');
                navbarToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // ============================================
    // Scroll Effect
    // ============================================
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Add shadow when scrolled
        if (currentScroll > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // ============================================
    // Set Active Link Based on Current Page
    // ============================================
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navbar-menu a:not(.btn)');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
    
    // ============================================
    // Smooth Scroll for Anchor Links
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const navbarHeight = navbar.offsetHeight;
                    const targetPosition = target.offsetTop - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});

// ============================================
// Handle Window Resize
// ============================================
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        const navbarToggle = document.getElementById('navbarToggle');
        const navbarMenu = document.getElementById('navbarMenu');
        
        // Close mobile menu on resize to desktop
        if (window.innerWidth > 768 && navbarMenu.classList.contains('active')) {
            navbarMenu.classList.remove('active');
            navbarToggle.classList.remove('active');
            document.body.classList.remove('menu-open');
            navbarToggle.setAttribute('aria-expanded', 'false');
        }
    }, 250);
});
</script>