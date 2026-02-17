<?php
/**
 * Global Footer Template - LuckyGeneMDx
 * Professional Standard: Medical-tech aesthetic with Teal dominance
 */
if (!defined('luckygenemdx')) exit;
?>

<style>
    /* Global 90% Scaling */
    html {
        zoom: 90%; 
    }

    /* Standardized Footer Container */
    .site-footer {
        background: linear-gradient(
            -45deg, 
            #00B3A4, /* Medical Teal - Dominant */
            #0A1F44, /* Deep Blue */
            #00B3A4, /* Medical Teal - Middle */
            /* #6C63FF, Soft Purple */
            #00B3A4  /* Medical Teal - End */
        );
        background-size: 400% 400%;
        animation: footerFlow 30s ease infinite;
        color: #FFFFFF;
        padding: 5rem 0 2rem;
        font-family: 'Inter', sans-serif;
    }

    @keyframes footerFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.5fr;
        gap: 3rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Typography & Hierarchy */
    .footer-brand h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
        letter-spacing: -0.5px;
        color: #FFFFFF;
    }

    .footer-brand p {
        font-size: 0.95rem;
        line-height: 1.7;
        opacity: 0.9;
    }

    .footer-column h4 {
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-links a {
        color: #FFFFFF;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        opacity: 0.8;
    }

    .footer-links a:hover {
        opacity: 1;
        padding-left: 5px;
        color: #00B3A4;
    }

    /* Bottom Bar */
    .footer-bottom {
        max-width: 1200px;
        margin: 4rem auto 0;
        padding: 2rem 20px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        text-align: center;
    }

    .disclaimer-text {
        font-size: 0.8rem;
        line-height: 1.6;
        opacity: 0.7;
        margin-bottom: 1.5rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .footer-legal a {
        color: #FFFFFF;
        font-size: 0.8rem;
        margin: 0 1rem;
        text-decoration: none;
        opacity: 0.7;
    }

    @media (max-width: 768px) {
        .footer-grid { grid-template-columns: 1fr 1fr; }
        .footer-brand { grid-column: span 2; }
    }
</style>

<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <h3>LuckyGeneMDx</h3>
            <p>Advancing family health through clinical-grade genetic carrier screening. We provide the clarity needed to make informed decisions about your genetic future.</p>
        </div>

        <div class="footer-column">
            <h4>Platform</h4>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about-genetic-screening.php">About Screening</a></li>
                <li><a href="how-it-works.php">Process</a></li>
                <li><a href="resources.php">Resources</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Support</h4>
            <ul class="footer-links">
                <li><a href="track-order.php">Track Order</a></li>
                <li><a href="user-portal/login.php">Patient Login</a></li>
                <li><a href="faq.php">Help Center</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Contact Us</h4>
            <ul class="footer-links">
                <li><span style="font-size: 0.9rem; opacity: 0.9;">support@luckygenemdx.com</span></li>
                <li><span style="font-size: 0.9rem; opacity: 0.9;">1-800-GENE-TEST</span></li>
                <li style="margin-top: 1rem;"><a href="request-kit.php" style="background: #FFFFFF; color: #0A1F44; padding: 8px 15px; border-radius: 4px; font-weight: bold; opacity: 1;">Order Kit - $99</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="disclaimer-text">
            <strong>Clinical Disclaimer:</strong> LuckyGeneMDx screening results are for informational purposes and are not diagnostic. This test does not replace genetic counseling or professional medical consultation. We operate in alignment with ACMG and CDC standards for genetic testing.
        </div>
        <div class="footer-legal">
            <a href="privacy-policy.php">Privacy Policy</a>
            <a href="terms-of-service.php">Terms of Service</a>
            <span style="font-size: 0.8rem; opacity: 0.5; margin-left: 1rem;">© <?php echo date('Y'); ?> LuckyGeneMDx. All rights reserved.</span>
        </div>
    </div>
</footer>