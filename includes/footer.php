<?php
/**
 * Global Footer Template - LuckyGeneMDx
 * Refactored: No zoom, correct paths, unified styling
 */
if (!defined('luckygenemdx')) exit;
?>

<!-- #2F377D 0%, #2F538B 40%, #4CB7AA 100% -->

<style>
/* Footer-specific styles (rest comes from main.css) */
.site-footer {
    background: linear-gradient(-45deg, #2F377D ,rgb(2, 150, 138), #2F538B, rgb(45, 150, 137));
    background-size: 400% 400%;
    animation: footerFlow 30s ease infinite;
    color: #FFFFFF;
    padding: 5rem 0 2rem;
    font-family: var(--font-body);
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
    padding: 0 1.5rem;
}

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
    color: #4ef0e6;
}

.footer-bottom {
    max-width: 1200px;
    margin: 4rem auto 0;
    padding: 2rem 1.5rem 0;
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

.footer-legal {
    font-size: 0.8rem;
}

.footer-legal a {
    color: #FFFFFF;
    margin: 0 1rem;
    text-decoration: none;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.footer-legal a:hover {
    opacity: 1;
}

.footer-cta-btn {
    background: #FFFFFF;
    color: #0A1F44 !important;
    padding: 8px 15px;
    border-radius: 4px;
    font-weight: bold;
    opacity: 1;
    transition: all 0.3s;
}

.footer-cta-btn:hover {
    background: #4ef0e6;
    color: #0A1F44;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        padding: 0 1rem;
    }
    
    .footer-brand {
        grid-column: span 2;
    }
    
    .site-footer {
        padding: 3rem 0 1.5rem;
    }
    
    .footer-bottom {
        padding: 1.5rem 1rem 0;
    }
    
    .footer-legal a {
        display: inline-block;
        margin: 0.5rem;
    }
}

@media (max-width: 480px) {
    .footer-grid {
        grid-template-columns: 1fr;
    }
    
    .footer-brand {
        grid-column: span 1;
    }
    
    .footer-column {
        text-align: center;
    }
    
    .footer-links {
        text-align: center;
    }
}
</style>

<footer class="site-footer">
    <div class="footer-grid">
        <!-- Brand Column -->
        <div class="footer-brand">
            <h3>🧬 LuckyGeneMDx</h3>
            <p>
                Advancing family health through clinical-grade genetic carrier screening. 
                We provide the clarity needed to make informed decisions about your genetic future.
            </p>
        </div>

        <!-- Platform Links -->
        <div class="footer-column">
            <h4>Platform</h4>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about-genetic-screening.php">About Screening</a></li>
                <li><a href="how-it-works.php">How It Works</a></li>
                <li><a href="resources.php">Resources</a></li>
            </ul>
        </div>

        <!-- Support Links -->
        <div class="footer-column">
            <h4>Support</h4>
            <ul class="footer-links">
                <li><a href="track-order.php">Track Order</a></li>
                <li><a href="user-portal/login.php">Patient Login</a></li>
                <li><a href="faq.php">Help Center</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>

        <!-- Contact Column -->
        <div class="footer-column">
            <h4>Contact Us</h4>
            <ul class="footer-links">
                <li style="opacity: 0.9; font-size: 0.9rem;">
                    📧 support@luckygenemdx.com
                </li>
                <li style="opacity: 0.9; font-size: 0.9rem;">
                    📞 1-800-GENE-TEST
                </li>
                <li style="margin-top: 1.5rem;">
                    <a href="request-kit.php" class="footer-cta-btn">
                        Order Kit - $99
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="footer-bottom">
        <div class="disclaimer-text">
            <strong>Clinical Disclaimer:</strong> 
            LuckyGeneMDx screening results are for informational purposes and are not diagnostic. 
            This test does not replace genetic counseling or professional medical consultation. 
            We operate in alignment with ACMG and CDC standards for genetic testing.
        </div>
        
        <div class="footer-legal">
            <a href="privacy-policy.php">Privacy Policy</a>
            <a href="terms-of-service.php">Terms of Service</a>
            <span style="opacity: 0.5; margin-left: 1rem;">
                © <?php echo date('Y'); ?> LuckyGeneMDx. All rights reserved.
            </span>
        </div>
    </div>
</footer>