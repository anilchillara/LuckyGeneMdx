<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
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
    <style>
        :root {
            --primary-blue: #0A1F44;
            --medical-teal: #00B3A4;
            --text-gray: #4A5568;
            --light-bg: #F7FAFC;
            --white: #FFFFFF;
            --warning-amber: #D97706;
        }

        /* ===== SHARED LEGAL HERO ===== */
        .legal-hero {
            position: relative;
            background: linear-gradient(135deg, var(--color-primary-deep-blue) 0%, var(--color-medical-teal) 75%, var(--color-soft-purple) 100%);
            color: var(--white);
            padding: 100px 20px 140px;
            text-align: center;
            overflow: hidden;
        }

        .hero-dna-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.05;
            background-image: radial-gradient(var(--medical-teal) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .legal-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            margin-bottom: 20px;
        }

        .hero-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 179, 164, 0.3);
            background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);"
        }

        /* ===== CONTENT CARD ===== */
        .legal-content {
            max-width: 1000px;
            margin: -80px auto 80px;
            background: var(--white);
            padding: 60px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(10, 31, 68, 0.1);
            position: relative;
            z-index: 20;
        }

        /* ===== GRAPHICAL ELEMENTS ===== */
        .disclaimer-box {
            background: rgba(217, 119, 6, 0.05);
            border: 1px dashed var(--warning-amber);
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .warning-icon {
            font-size: 2.5rem;
            color: var(--warning-amber);
        }

        .service-steps {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin: 40px 0;
            padding: 20px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .step {
            flex: 1;
            text-align: center;
        }

        .step-num {
            width: 35px; height: 35px;
            background: var(--medical-teal);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 700;
        }

        .compliance-badges {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 50px;
            opacity: 0.6;
            filter: grayscale(1);
        }

        .badge-item {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary-blue);
        }

        @media (max-width: 768px) {
            .legal-content { padding: 40px 20px; margin-top: -40px; }
            .service-steps { flex-direction: column; gap: 30px; }
            .disclaimer-box { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

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