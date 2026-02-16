<!-- BULLETPROOF RESPONSIVE NAVIGATION -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="navbar-logo">
            🧬 <span>LuckyGeneMDx</span>
        </a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <div class="nav-menu" id="navMenu">
            <a href="index.php">Home</a>
            <a href="about-genetic-screening.php">About Screening</a>
            <a href="how-it-works.php">How It Works</a>
            <a href="resources.php">Resources</a>
            <a href="track-order.php">Track Order</a>
            <a href="request-kit.php" class="nav-btn">Request Kit</a>
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

.nav-menu a {
    text-decoration: none;
    color: #0A1F44;
    font-weight: 500;
    font-size: 1rem;
    padding: 8px 0;
    transition: color 0.3s;
}

.nav-menu a:hover {
    color: #00B3A4;
}

.nav-menu .nav-btn {
    background: #00B3A4;
    color: white !important;
    padding: 12px 28px;
    border-radius: 50px;
}

.nav-menu .nav-btn:hover {
    background: #008c7a;
    transform: translateY(-2px);
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
}

.mobile-menu-btn span {
    width: 25px;
    height: 3px;
    background: #0A1F44;
    border-radius: 3px;
    transition: 0.3s;
}

/* Hamburger Active State */
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
@media screen and (max-width: 768px) {
    /* Show hamburger */
    .mobile-menu-btn {
        display: flex;
    }
    
    /* Mobile menu */
    .nav-menu {
        position: fixed;
        top: 80px;
        left: 0;
        width: 100%;
        background: white;
        flex-direction: column;
        gap: 0;
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    /* Open state */
    .nav-menu.open {
        max-height: 500px;
        padding: 20px 0;
    }
    
    .nav-menu a {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        width: 100%;
    }
    
    .nav-menu .nav-btn {
        margin: 10px 20px;
        text-align: center;
        border-bottom: none;
    }
}
</style>

<script>
// SIMPLE TOGGLE SCRIPT
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.getElementById('navMenu');
    
    if (btn && menu) {
        btn.addEventListener('click', function() {
            btn.classList.toggle('active');
            menu.classList.toggle('open');
        });
        
        // Close when clicking a link
        menu.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                btn.classList.remove('active');
                menu.classList.remove('open');
            });
        });
    }
});
</script>