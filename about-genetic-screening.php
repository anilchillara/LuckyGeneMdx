<?php
define('LuckyGenesMDx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
session_start();
setSecurityHeaders();

$page_title = 'About Genetic Carrier Screening';
$page_description = 'Learn about genetic carrier screening, why it matters, and how it can help secure your family\'s genetic future.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    
    <!-- Preload critical assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/main.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content">
        <!-- Page Header -->
        <section class="page-header">
            <p>Knowledge that empowers your family planning decisions</p>
        </section>

        <!-- What is Carrier Screening -->
        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col col-2">
                        <h2>What is Genetic Carrier Screening?</h2>
                        <p>
                            Genetic carrier screening is a type of genetic test that can tell you whether you 
                            carry a gene for certain genetic disorders. Carriers are generally healthy individuals 
                            who have one copy of a gene mutation that, when present in two copies, causes a 
                            genetic disorder.
                        </p>
                        
                        <p>
                            When both parents are carriers of the same genetic condition, there's a 25% chance 
                            with each pregnancy that their child could inherit both copies of the mutated gene 
                            and be affected by the disorder.
                        </p>

                        <div class="glass-card glass-card-teal-left p-3 mt-4 mb-4">
                            <h4 class="text-teal mb-1">Key Point</h4>
                            <p class="mb-0">
                                Carrier screening is <strong>not diagnostic</strong>. It identifies risk before 
                                pregnancy, allowing couples to make informed decisions about family planning.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col col-2">
                        <div class="glass-card p-3 text-center">
                            <img src="assets/images/CarrierStatus.png" alt="Inheritance Pattern" class="w-100 img-rounded">
                            <!-- Legend -->
                            <p class="font-xs mt-2 text-dark-gray">Inheritance Pattern</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Carrier status is a fundamental concept in genetics, and understanding it is crucial for anyone considering family planning. By identifying carrier status, individuals can gain insights into their genetic risks and make informed decisions about their reproductive health. In the next sections, we will explore why carrier screening is important, what conditions are commonly screened for, and who should consider getting screened. -->
        <section class="section">
            <div class="container">
                <div class="row" >
                    <div class="col col-2">
                        <div class="pill-badge pill-badge-teal mb-2">GENETIC BLUEPRINT</div>
                        <h2>Hidden in our Genetic Code</h2>
                        <p>Carrier status refers to individuals who carry one mutated gene for a recessive condition. While typically asymptomatic, if both parents are carriers, there is a 25% probability of passing it to their children.</p>
                        
                        <div class="row mt-4">
                            <div class="col col-2">
                                <div class="glass-card p-3 glass-card-teal-top">
                                    <h3 class="text-teal mb-0">25%</h3>
                                    <p class="font-sm mt-1">Inheritance risk if both parents are carriers.</p>
                                </div>
                            </div>
                            <div class="col col-2">
                                <div class="glass-card p-3 glass-card-blue-top">
                                    <h3 class="text-deep-blue mb-0">1 in 4</h3>
                                    <p class="font-sm mt-1">Probability of a child being affected.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col col-2">
                        <div class="glass-card p-3 text-center">
                            <img src="assets/images/autoRes.png" alt="Inheritance Pattern" class="w-100 img-rounded">
                            <p class="font-xs mt-2 font-italic text-dark-gray">Visualizing the autosomal recessive probability distribution.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Screen -->
        <section class="section section-light">
            <div class="container">
                <h2 class="text-center mb-2">Why Consider Carrier Screening?</h2>
                <p class="section-intro text-dark-gray">
                    Understanding your carrier status provides valuable information for family planning
                </p>
                
                <div class="row">
                    <div class="col col-4">
                        <div class="glass-card text-center p-4 h-100">
                            <div class="icon-box">📊</div>
                            <h3>Know Your Risk</h3>
                            <p>Understand if you and your partner are carriers for the same genetic conditions.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4 h-100">
                            <div class="icon-box">🎯</div>
                            <h3>Plan Ahead</h3>
                            <p>Make informed decisions about family planning with comprehensive genetic information.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4 h-100">
                            <div class="icon-box">🏥</div>
                            <h3>Early Awareness</h3>
                            <p>Prepare for potential medical needs and connect with specialists early if needed.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4 h-100">
                            <div class="icon-box">💙</div>
                            <h3>Peace of Mind</h3>
                            <p>Gain confidence and reduce uncertainty about your genetic health.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What Conditions -->
        <section class="section">
            <div class="container">
                <h2 class="text-center mb-2">Common Conditions Screened</h2>
                <p class="section-intro text-dark-gray">
                    Our comprehensive panel tests for over 300 genetic conditions, including:
                </p>
                
                <div class="row">
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Cystic Fibrosis</h4>
                            <p class="font-sm mb-0">
                                Affects the lungs and digestive system. Carrier frequency: 1 in 25 (Caucasian)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Sickle Cell Disease</h4>
                            <p class="font-sm mb-0">
                                Affects red blood cells and oxygen transport. Carrier frequency: 1 in 13 (African American)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Tay-Sachs Disease</h4>
                            <p class="font-sm mb-0">
                                Progressive nervous system disorder. Carrier frequency: 1 in 30 (Ashkenazi Jewish)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Spinal Muscular Atrophy</h4>
                            <p class="font-sm mb-0">
                                Affects muscle movement and strength. Carrier frequency: 1 in 50 (general population)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Fragile X Syndrome</h4>
                            <p class="font-sm mb-0">
                                Leading inherited cause of intellectual disability. Carrier frequency: 1 in 250 females
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card p-3 mb-3">
                            <h4 class="text-teal mb-1">Thalassemia</h4>
                            <p class="font-sm mb-0">
                                Blood disorder affecting hemoglobin. Carrier frequency: varies by ethnicity
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-dark-gray mb-2">
                        And 294+ additional genetic conditions
                    </p>
                </div>
            </div>
        </section>

        <!-- Who Should Screen -->
        <section class="section section-light">
            <div class="container">
                <h2 class="text-center mb-5">Who Should Consider Screening?</h2>
                
                <div class="row">
                    <div class="col col-2">
                        <div class="glass-card p-4 mb-3">
                            <h3 class="text-teal">✓ Pre-Conception Planning</h3>
                            <p>Couples planning to have children who want to understand their genetic risks before pregnancy.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card p-4 mb-3">
                            <h3 class="text-teal">✓ Family History</h3>
                            <p>Individuals with a family history of genetic disorders or known carriers in the family.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card p-4 mb-3">
                            <h3 class="text-teal">✓ Ethnic Background</h3>
                            <p>Individuals from populations with higher carrier frequencies for certain conditions.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card p-4 mb-3">
                            <h3 class="text-teal">✓ General Awareness</h3>
                            <p>Anyone interested in understanding their genetic health and potential risks.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card glass-card-purple-left p-4 mt-4">
                    <h4 class="text-purple mb-2">Professional Guidelines</h4>
                    <p>
                        The American College of Obstetricians and Gynecologists (ACOG) and the American College 
                        of Medical Genetics and Genomics (ACMG) recommend offering carrier screening to all 
                        individuals considering pregnancy or currently pregnant.
                    </p>
                    <p class="font-sm mb-0 opacity-80">
                        <em>LuckyGenesMDx follows these professional guidelines but is not directly affiliated with these organizations.</em>
                    </p>
                </div>
            </div>
        </section>

        <!-- Scientific Credibility -->
        <section class="section">
            <div class="container">
                <h2 class="text-center mb-2">Scientific Standards & Credibility</h2>
                <p class="section-intro text-dark-gray">
                    Our screening aligns with established medical genetics standards
                </p>
                
                <div class="row">
                    <div class="col col-4">
                        <div class="glass-card text-center p-4">
                            <div class="icon-box">🔬</div>
                            <h4>ACMG Guidelines</h4>
                            <p>Our panel follows American College of Medical Genetics and Genomics standards.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4">
                            <div class="icon-box">✓</div>
                            <h4>CLIA-Certified Lab</h4>
                            <p>All testing performed in Clinical Laboratory Improvement Amendments certified laboratories.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4">
                            <div class="icon-box">🏆</div>
                            <h4>CAP-Accredited</h4>
                            <p>Laboratory partners maintain College of American Pathologists accreditation.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card text-center p-4">
                            <div class="icon-box">👨‍⚕️</div>
                            <h4>Board-Certified Review</h4>
                            <p>Results reviewed by board-certified geneticists and genetic counselors.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card glass-card-teal-left p-3 mt-4 mb-4">
                    
                <h4 class="text-teal mb-1">Important Note</h4>
                    <p class="mb-0">
                        <strong></strong> <?php echo htmlspecialchars(SITE_NAME); ?> provides genetic carrier screening services 
                        aligned with professional standards. We are not affiliated with, endorsed by, or officially 
                        connected to ACMG, ACOG, CDC, or other medical organizations mentioned. Carrier screening 
                        is not a diagnostic test and should not replace consultation with qualified healthcare providers.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        
        <?php if (defined('SHOW_CTA') && SHOW_CTA): ?>
        <section class="sec-cta">
            <div class="cta-box reveal">
                <div class="cta-pill">Limited Time Offer</div>
                <h2>Secure Your Family's Future</h2>
                <p>Join thousands of proactive families. Get clinical-grade insights delivered privately to your door in just 14-21 days.</p>
                
                <div class="cta-pricing">
                    <span class="cta-price">$<?php echo number_format(KIT_PRICE, 0); ?></span>
                    <span class="cta-strike">$249</span>
                </div>
                
                <a href="request-kit.php" class="btn-cta-main">
                    Order Your Screening Kit
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 9h12M9 3l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                
                <div class="cta-meta">
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                        HIPAA Compliant
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        CLIA Certified Lab
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                        Free 2-Day Shipping
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