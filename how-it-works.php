<?php
define('LuckyGenesMDx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
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
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">
        <section class="page-header">
                <p>
                    Simple, secure, and scientifically rigorous — from order to results in 2-3 weeks.
                </p>
        </section>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <section class="section">
            <div class="container">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h2>Your Journey to Genetic Awareness</h2>
                    <p style="color: var(--color-dark-gray);">Our streamlined 5-step process makes genetic carrier screening accessible and convenient.</p>
                </div>

                <div class="row">
                    <div class="col col-2">
                        <div class="glass-card process-card" style="border-top-color: var(--color-medical-teal);">
                            <div class="process-header">
                                <span class="process-number" style="background: var(--color-medical-teal);">1</span>
                                <h3 class="process-title"><i class="fas fa-shopping-cart" style="color: var(--color-medical-teal); margin-right: 8px;"></i> Order Your Kit</h3>
                            </div>
                            <p>Complete our secure online form. Your kit ships within 1-2 business days.</p>
                            <ul style="font-size: 0.9rem; padding-left: 1.2rem;">
                                <li>Payment information ($<?php echo number_format(KIT_PRICE, 0); ?>)</li>
                                <li>Informed consent included</li>
                            </ul>
                            <div class="process-meta" style="color: var(--color-medical-teal);">⏱️ 5 minutes</div>
                        </div>
                    </div>
                    
                    <div class="col col-2">
                        <div class="glass-card process-card" style="border-top-color: #2979ff;">
                            <div class="process-header">
                                <span class="process-number" style="background: #2979ff;">2</span>
                                <h3 class="process-title"><i class="fas fa-box-open" style="color: #2979ff; margin-right: 8px;"></i> Receive Your Kit</h3>
                            </div>
                            <p>Everything needed for saliva collection—no needles or blood draws required.</p>
                            <ul style="font-size: 0.9rem; padding-left: 1.2rem;">
                                <li>Saliva collection tube</li>
                                <li>Prepaid return label</li>
                            </ul>
                            <div class="process-meta" style="color: #2979ff;">⏱️ 2-3 days shipping</div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col col-3">
                        <div class="glass-card process-card" style="border-top-color: #9177C7;">
                            <div class="process-header">
                                <span class="process-number" style="background: #9177C7;">3</span>
                                <h3 class="process-title" style="color: #9177C7;"><i class="fas fa-vial" style="margin-right: 8px;"></i> Collect Sample</h3>
                            </div>
                            <p style="font-size: 0.9rem;">Simple saliva-based collection. No fasting, just 10 minutes of your time.</p>
                        </div>
                    </div>
                    
                    <div class="col col-3">
                        <div class="glass-card process-card" style="border-top-color: var(--color-medical-teal);">
                            <div class="process-header">
                                <span class="process-number" style="background: var(--color-medical-teal);">4</span>
                                <h3 class="process-title" style="color: var(--color-medical-teal);"><i class="fas fa-microscope" style="margin-right: 8px;"></i> Lab Analysis</h3>
                            </div>
                            <p style="font-size: 0.9rem;">Samples processed in CLIA-certified, CAP-accredited laboratories.</p>
                        </div>
                    </div>

                    <div class="col col-3">
                        <div class="glass-card process-card" style="border-top-color: #2979ff;">
                            <div class="process-header">
                                <span class="process-number" style="background: #2979ff;">5</span>
                                <h3 class="process-title" style="color: #2979ff;"><i class="fas fa-file-medical-alt" style="margin-right: 8px;"></i> Access Results</h3>
                            </div>
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

        <!-- CTA Section -->

        <?php if (defined('SHOW_CTA') && SHOW_CTA): ?>
        <section class="sec-cta">
            <div class="cta-box reveal">
                <div class="cta-pill">Begin Your Path to Clarity</div>
                <h2>Ready to Start Your Journey?</h2>
                <p>Join thousands of families who have chosen proactive screening. Order your clinical-grade kit today.</p>
                
                <div class="cta-pricing">
                    <span class="cta-price">$<?php echo number_format(KIT_PRICE, 0); ?></span>
                    <span class="cta-strike">$249</span>
                </div>
                
                <a href="request-kit.php" class="btn-cta-main">
                    Request Your Kit Now
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 9h12M9 3l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                
                <div class="cta-meta">
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                        Results in 3 Weeks
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        100% Secure & Private
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                        Physician Support
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>