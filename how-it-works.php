<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();

$page_title = 'How It Works - 5 Step Process';
$page_description = 'Simple 5-step process for genetic carrier screening. From ordering your kit to receiving your results.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMdx</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <main id="main-content">
        <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
            <div class="container">
                <h1 style="color: var(--color-white); margin-bottom: 1rem;">How It Works</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
                    Simple, secure, and scientifically rigorous — from order to results in 2-3 weeks.
                </p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h2>Your Journey to Genetic Awareness</h2>
                    <p style="color: var(--color-dark-gray);">Our streamlined 5-step process makes genetic carrier screening accessible and convenient.</p>
                </div>

                <div class="row">
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; height: 100%; border-top: 4px solid var(--color-medical-teal);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <span style="background: var(--color-medical-teal); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">1</span>
                                <h3 style="margin: 0;">Order Your Kit</h3>
                            </div>
                            <p>Complete our secure online form. Your kit ships within 1-2 business days.</p>
                            <ul style="font-size: 0.9rem; padding-left: 1.2rem;">
                                <li>Payment information ($99)</li>
                                <li>Informed consent included</li>
                            </ul>
                            <div style="margin-top: 1rem; font-weight: 600; color: var(--color-medical-teal);">⏱️ 5 minutes</div>
                        </div>
                    </div>
                    
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; height: 100%; border-top: 4px solid var(--color-medical-teal);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <span style="background: var(--color-medical-teal); color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">2</span>
                                <h3 style="margin: 0;">Receive Your Kit</h3>
                            </div>
                            <p>Everything needed for saliva collection—no needles or blood draws required.</p>
                            <ul style="font-size: 0.9rem; padding-left: 1.2rem;">
                                <li>Saliva collection tube</li>
                                <li>Prepaid return label</li>
                            </ul>
                            <div style="margin-top: 1rem; font-weight: 600; color: var(--color-medical-teal);">⏱️ 2-3 days shipping</div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 2rem;">
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; height: 100%;">
                            <h4 style="color: var(--color-medical-teal);">3. Collect Sample</h4>
                            <p style="font-size: 0.9rem;">Simple saliva-based collection. No fasting, just 10 minutes of your time.</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; height: 100%;">
                            <h4 style="color: var(--color-medical-teal);">4. Lab Analysis</h4>
                            <p style="font-size: 0.9rem;">Samples processed in CLIA-certified, CAP-accredited laboratories.</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; height: 100%;">
                            <h4 style="color: var(--color-medical-teal);">5. Access Results</h4>
                            <p style="font-size: 0.9rem;">View your comprehensive report through our secure, encrypted portal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" style="background: var(--color-light-gray);">
            <div class="container">
                <h2 class="text-center" style="margin-bottom: 3rem;">Security & Privacy</h2>
                <div class="row">
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔒</div>
                            <h4>HIPAA Compliant</h4>
                            <p style="font-size: 0.9rem;">Full compliance with healthcare data standards.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🛡️</div>
                            <h4>Encrypted</h4>
                            <p style="font-size: 0.9rem;">Results stored with AES-256 encryption.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" style="background: var(--gradient-primary); color: var(--color-white); text-align: center;">
            <div class="container">
                <h2 style="color: var(--color-white); margin-bottom: 1.5rem;">Ready to Start Your Journey?</h2>
                <a href="request-kit.php" class="btn btn-large" style="background: var(--color-white); color: var(--color-primary-deep-blue);">
                    Request Your Kit Now - $99
                </a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>