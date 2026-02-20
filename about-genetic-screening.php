<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
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
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMdx</title>
    
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
            <div class="container">
                <h1>Genetic Carrier Screening</h1>
                <p>
                    Knowledge that empowers your family planning decisions
                </p>
            </div>
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
                            <text x="200" y="310" text-anchor="middle" fill="#0A1F44" font-size="12">Inheritance Pattern</text>
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
                        <em>LuckyGeneMdx follows these professional guidelines but is not directly affiliated with these organizations.</em>
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
                        <strong></strong> LuckyGeneMdx provides genetic carrier screening services 
                        aligned with professional standards. We are not affiliated with, endorsed by, or officially 
                        connected to ACMG, ACOG, CDC, or other medical organizations mentioned. Carrier screening 
                        is not a diagnostic test and should not replace consultation with qualified healthcare providers.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        
        <section class="section conversion-block section-light section-padded">
            <div class="container">
                <div class="cta-card-large">
                    
                    <div class="pill-badge-outline-teal mb-3">
                        Limited Time Offer
                    </div>

                    <h2 class="text-deep-blue mb-3 font-xxl font-bold">
                        Secure Your Family's Future
                    </h2>
                    
                    <p class="text-dark-gray mb-5 mx-auto lh-1-6" style="font-size: 1.15rem; max-width: 650px;">
                        Join thousands of proactive families. Get clinical-grade insights delivered privately to your door in just 14-21 days.
                    </p>

                    <div class="mb-5">
                        <span class="price-display">$99</span>
                        <span class="price-strike">$249</span>
                    </div>

                    <div class="flex-center-wrap" style="gap: 1.5rem;">
                        <a href="request-kit.php" class="btn btn-primary btn-large btn-pulse ">
                            Order Your Screening Kit
                        </a>
                        <a href="how-it-works.php" class="btn btn-outline btn-large">
                            See How It Works
                        </a>
                    </div>

                    <div class="check-list-horizontal mt-5">
                        <span class="check-item"><span>✓</span> HIPAA Compliant</span>
                        <span class="check-item"><span>✓</span> CLIA Certified Lab</span>
                        <span class="check-item"><span>✓</span> Free 2-Day Shipping</span>
                    </div>
                </div>
            </div>
        </section>

    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>