<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();
$page_title = 'Privacy Policy';
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
            <div class="hero-badge">Trust & Transparency</div>
            <h1>Privacy Policy</h1>
            <p class="hero-subtitle">Your genetic privacy is our highest priority. We employ clinical-grade security to protect your Molecular Diagnostic (MDx) data.</p>
            <div class="last-updated">
                📅 Effective Date: <?php echo date('F j, Y'); ?>
            </div>
        </div>
    </section>

    <main class="legal-content">
        <section class="legal-section">
            <h2>1. Information We Collect</h2>
            <p>To provide high-fidelity carrier screening, LuckyGeneMDx collects data essential for clinical accuracy and HIPAA compliance:</p>
            
            <div class="info-grid">
                <div class="info-card">
                    <div class="icon-circle">👤</div>
                    <h4>Identity Data</h4>
                    <p>Name and contact info for secure patient verification.</p>
                </div>
                <div class="info-card">
                    <div class="icon-circle">🧪</div>
                    <h4>Biological Data</h4>
                    <p>Samples used exclusively for DNA extraction.</p>
                </div>
                <div class="info-card">
                    <div class="icon-circle">🧬</div>
                    <h4>Genomic Data</h4>
                    <p>Identified genetic variants and carrier status.</p>
                </div>
                <div class="info-card">
                    <div class="icon-circle">📋</div>
                    <h4>Clinical History</h4>
                    <p>Family history used for residual risk calculation.</p>
                </div>
            </div>
        </section>

        <section class="legal-section">
            <h2>2. How We Use Your Information</h2>
            <p>Your genetic data is processed within a secure clinical environment for these exclusive purposes:</p>
            <div class="highlight-box">
                <p><strong>Clinical Reporting:</strong> We translate molecular markers into actionable health reports for you and your provider.</p>
                <p><strong>Screening Accuracy:</strong> Using your background data to refine the statistical precision of your carrier results.</p>
            </div>
        </section>

        <div class="security-banner">
            <div class="security-icon-large">🛡️</div>
            <div class="security-text">
                <h3>3. Clinical-Grade Security</h3>
                <p>We use <strong>AES-256 bank-level encryption</strong> and de-identified lab processing (barcoding) to ensure your identity and DNA results never meet in an unencrypted environment.</p>
            </div>
        </div>

        <section class="legal-section">
            <h2>4. Your Patient Rights</h2>
            <p>As a patient, you maintain full sovereignty over your molecular data:</p>
            
            <div class="rights-list">
                <div class="right-item">
                    <span class="right-check">✓</span>
                    <div>
                        <strong>Right to Access:</strong> Download your clinical reports via the Patient Portal at any time.
                    </div>
                </div>
                <div class="right-item">
                    <span class="right-check">✓</span>
                    <div>
                        <strong>Right to Correction:</strong> Update your clinical history or contact information instantly.
                    </div>
                </div>
                <div class="right-item">
                    <span class="right-check">✓</span>
                    <div>
                        <strong>Right to Deletion:</strong> Request the destruction of your biological sample after clinical retention periods.
                    </div>
                </div>
            </div>
        </section>

        <section class="legal-section" style="margin-top: 50px; text-align: center;">
            <p>Questions about your data? Contact our Compliance Officer.</p>
            <a href="mailto:privacy@luckygenemdx.com" style="color: var(--medical-teal); font-weight: 600; text-decoration: none;">
                privacy@luckygenemdx.com
            </a>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>