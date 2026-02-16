<?php
require_once 'includes/config.php';
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
    <style>
        :root {
            --primary-blue: #0A1F44;
            --medical-teal: #00B3A4;
            --text-gray: #4A5568;
            --light-bg: #F7FAFC;
            --white: #FFFFFF;
        }

        /* ===== LEGAL HERO SECTION ===== */
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
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            background-image: radial-gradient(var(--medical-teal) 1px, transparent 1px);
            background-size: 30px 30px;
            pointer-events: none;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(0, 179, 164, 0.5);
            color: var(--primary-blue);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 179, 164, 0.3);
        }

        .legal-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        /* ===== CONTENT STRUCTURE ===== */
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

        /* ===== GRAPHICAL GRID (Information We Collect) ===== */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .info-card {
            background: var(--light-bg);
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            border-color: var(--medical-teal);
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .info-card h4 {
            color: var(--primary-blue);
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .info-card p {
            font-size: 0.85rem;
            color: var(--text-gray);
            line-height: 1.4;
        }

        /* ===== SECURITY FEATURE LIST ===== */
        .security-banner {
            background: var(--primary-blue);
            color: white;
            border-radius: 16px;
            padding: 40px;
            margin: 40px 0;
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .security-icon-large {
            font-size: 4rem;
            opacity: 0.9;
        }

        .security-text h3 {
            color: var(--medical-teal);
            margin-bottom: 10px;
        }

        /* ===== RIGHTS SECTION ===== */
        .rights-list {
            list-style: none;
            padding: 0;
        }

        .right-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 20px;
            border-bottom: 1px solid #edf2f7;
        }

        .right-check {
            color: var(--medical-teal);
            font-weight: bold;
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .legal-content { padding: 40px 20px; margin-top: -40px; }
            .security-banner { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

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