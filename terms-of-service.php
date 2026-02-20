<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();
$page_title = 'Terms of Service';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section class="legal-hero">
        <div class="hero-dna-overlay"></div>
        <div class="container">
            <div class="hero-badge">Legal Agreement</div>
            <h1>Terms of Service</h1>
            <p class="hero-subtitle">Governing the use of LuckyGeneMDx clinical platforms and genetic screening protocols.</p>
            <div class="last-updated">📅 Effective Date: February 16, 2026</div>
        </div>
    </section>

    <main class="legal-content">
        <section class="legal-section">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing the <strong>LuckyGeneMDx</strong> portal, requesting a collection kit, or submitting a biological sample, you enter into a legally binding agreement. Our services are strictly for individuals aged 18 or older, or those with explicit parental consent for pediatric screening.</p>
        </section>

        <section class="legal-section">
            <h2>2. Service Description</h2>
            <p>LuckyGeneMDx provides comprehensive Molecular Diagnostic (MDx) services specializing in carrier screening for inherited conditions. Our process involves:</p>
            
            <div class="service-steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <p><strong>Sample Collection</strong><br><small>Saliva or Blood</small></p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <p><strong>Lab Processing</strong><br><small>CLIA-Certified</small></p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <p><strong>Result Delivery</strong><br><small>Secure Portal</small></p>
                </div>
            </div>
            
            <p>We provide genomic analysis of specific markers to determine if an individual "carries" a genetic mutation that could be passed to their offspring.</p>
        </section>

        <section class="legal-section">
            <h2>3. Medical Disclaimer</h2>
            <div class="disclaimer-box">
                <div class="warning-icon">⚠️</div>
                <div class="disclaimer-text">
                    <strong>Critical Information:</strong> Carrier screening is a <u>risk-assessment tool</u>, not a definitive diagnostic test. A negative result significantly reduces, but does not entirely eliminate, the risk of being a carrier (Residual Risk). These results should <strong>never</strong> replace professional medical advice or clinical diagnosis from a board-certified genetic counselor.
                </div>
            </div>
        </section>

        <section class="legal-section">
            <h2>4. Laboratory Standards</h2>
            <p>All biological samples are processed in laboratories that maintain <strong>CLIA (Clinical Laboratory Improvement Amendments)</strong> certification and <strong>CAP (College of American Pathologists)</strong> accreditation. We adhere to the highest clinical standards to ensure the reproducibility and accuracy of your genomic data.</p>
        </section>

        <div class="compliance-badges">
            <div class="badge-item">
                <div style="font-size: 2rem;">🛡️</div>
                HIPAA COMPLIANT
            </div>
            <div class="badge-item">
                <div style="font-size: 2rem;">🔬</div>
                CLIA CERTIFIED
            </div>
            <div class="badge-item">
                <div style="font-size: 2rem;">✅</div>
                CAP ACCREDITED
            </div>
        </div>

        <section style="margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 30px;">
            <p style="font-size: 0.9rem; color: var(--text-gray);">
                Have questions regarding these terms? <br>
                <a href="contact.php" style="color: var(--medical-teal); font-weight: 600; text-decoration: none;">Contact Legal Department</a>
            </p>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>