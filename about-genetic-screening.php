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
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMDx</title>
    
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
    <?php include 'includes/header.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content">
        <!-- Page Header -->
        <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
            <div class="container">
                <h1 style="color: var(--color-white); margin-bottom: 1rem;">Understanding Genetic Carrier Screening</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
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

                        <div class="glass-card" style="background: rgba(0, 179, 164, 0.1); border-left: 4px solid var(--color-medical-teal); padding: 1.5rem; margin: 2rem 0;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Key Point</h4>
                            <p style="margin: 0;">
                                Carrier screening is <strong>not diagnostic</strong>. It identifies risk before 
                                pregnancy, allowing couples to make informed decisions about family planning.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 1.5rem; text-align: center;">
                            <img src="assets/images/CarrierStatus.png" alt="Inheritance Pattern" style="width: 100%; border-radius: 8px;">
                            <!-- Legend -->
                            <text x="200" y="310" text-anchor="middle" fill="#0A1F44" font-size="12">Inheritance Pattern</text>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Screen -->
        <section class="section" style="background: var(--color-light-gray);">
            <div class="container">
                <h2 class="text-center" style="margin-bottom: 1rem;">Why Consider Carrier Screening?</h2>
                <p class="text-center" style="max-width: 700px; margin: 0 auto 3rem; color: var(--color-dark-gray);">
                    Understanding your carrier status provides valuable information for family planning
                </p>
                
                <div class="row">
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem; height: 100%;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                            <h3>Know Your Risk</h3>
                            <p>Understand if you and your partner are carriers for the same genetic conditions.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem; height: 100%;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                            <h3>Plan Ahead</h3>
                            <p>Make informed decisions about family planning with comprehensive genetic information.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem; height: 100%;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🏥</div>
                            <h3>Early Awareness</h3>
                            <p>Prepare for potential medical needs and connect with specialists early if needed.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem; height: 100%;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">💙</div>
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
                <h2 class="text-center" style="margin-bottom: 1rem;">Common Conditions Screened</h2>
                <p class="text-center" style="max-width: 700px; margin: 0 auto 3rem; color: var(--color-dark-gray);">
                    Our comprehensive panel tests for over 300 genetic conditions, including:
                </p>
                
                <div class="row">
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Cystic Fibrosis</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Affects the lungs and digestive system. Carrier frequency: 1 in 25 (Caucasian)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Sickle Cell Disease</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Affects red blood cells and oxygen transport. Carrier frequency: 1 in 13 (African American)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Tay-Sachs Disease</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Progressive nervous system disorder. Carrier frequency: 1 in 30 (Ashkenazi Jewish)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Spinal Muscular Atrophy</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Affects muscle movement and strength. Carrier frequency: 1 in 50 (general population)
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Fragile X Syndrome</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Leading inherited cause of intellectual disability. Carrier frequency: 1 in 250 females
                            </p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--color-medical-teal); margin-bottom: 0.5rem;">Thalassemia</h4>
                            <p style="font-size: 0.9rem; margin: 0;">
                                Blood disorder affecting hemoglobin. Carrier frequency: varies by ethnicity
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center" style="margin-top: 2rem;">
                    <p style="color: var(--color-dark-gray); margin-bottom: 1rem;">
                        And 294+ additional genetic conditions
                    </p>
                </div>
            </div>
        </section>

        <!-- Who Should Screen -->
        <section class="section" style="background: var(--color-light-gray);">
            <div class="container">
                <h2 class="text-center" style="margin-bottom: 3rem;">Who Should Consider Screening?</h2>
                
                <div class="row">
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; margin-bottom: 1.5rem;">
                            <h3 style="color: var(--color-medical-teal);">✓ Pre-Conception Planning</h3>
                            <p>Couples planning to have children who want to understand their genetic risks before pregnancy.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; margin-bottom: 1.5rem;">
                            <h3 style="color: var(--color-medical-teal);">✓ Family History</h3>
                            <p>Individuals with a family history of genetic disorders or known carriers in the family.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; margin-bottom: 1.5rem;">
                            <h3 style="color: var(--color-medical-teal);">✓ Ethnic Background</h3>
                            <p>Individuals from populations with higher carrier frequencies for certain conditions.</p>
                        </div>
                    </div>
                    <div class="col col-2">
                        <div class="glass-card" style="padding: 2rem; margin-bottom: 1.5rem;">
                            <h3 style="color: var(--color-medical-teal);">✓ General Awareness</h3>
                            <p>Anyone interested in understanding their genetic health and potential risks.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card" style="background: rgba(108, 99, 255, 0.1); border-left: 4px solid var(--color-purple-accent); padding: 2rem; margin-top: 2rem;">
                    <h4 style="color: var(--color-purple-accent); margin-bottom: 1rem;">Professional Guidelines</h4>
                    <p>
                        The American College of Obstetricians and Gynecologists (ACOG) and the American College 
                        of Medical Genetics and Genomics (ACMG) recommend offering carrier screening to all 
                        individuals considering pregnancy or currently pregnant.
                    </p>
                    <p style="font-size: 0.9rem; margin: 0; opacity: 0.8;">
                        <em>LuckyGeneMDx follows these professional guidelines but is not directly affiliated with these organizations.</em>
                    </p>
                </div>
            </div>
        </section>

        <!-- Scientific Credibility -->
        <section class="section">
            <div class="container">
                <h2 class="text-center" style="margin-bottom: 1rem;">Scientific Standards & Credibility</h2>
                <p class="text-center" style="max-width: 700px; margin: 0 auto 3rem; color: var(--color-dark-gray);">
                    Our screening aligns with established medical genetics standards
                </p>
                
                <div class="row">
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🔬</div>
                            <h4>ACMG Guidelines</h4>
                            <p>Our panel follows American College of Medical Genetics and Genomics standards.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">✓</div>
                            <h4>CLIA-Certified Lab</h4>
                            <p>All testing performed in Clinical Laboratory Improvement Amendments certified laboratories.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
                            <h4>CAP-Accredited</h4>
                            <p>Laboratory partners maintain College of American Pathologists accreditation.</p>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">👨‍⚕️</div>
                            <h4>Board-Certified Review</h4>
                            <p>Results reviewed by board-certified geneticists and genetic counselors.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-card" style="background: rgba(255, 152, 0, 0.1); border-left: 4px solid #FF9800; padding: 2rem; margin-top: 3rem;">
                    <p style="margin: 0; font-size: 0.95rem;">
                        <strong>Important Note:</strong> LuckyGeneMDx provides genetic carrier screening services 
                        aligned with professional standards. We are not affiliated with, endorsed by, or officially 
                        connected to ACMG, ACOG, CDC, or other medical organizations mentioned. Carrier screening 
                        is not a diagnostic test and should not replace consultation with qualified healthcare providers.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section" style="background: var(--gradient-primary); color: var(--color-white); text-align: center;">
            <div class="container">
                <h2 style="color: var(--color-white); margin-bottom: 1.5rem;">
                    Ready to Learn About Your Carrier Status?
                </h2>
                <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.95;">
                    Take the first step toward informed family planning with comprehensive genetic carrier screening.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="request-kit.php" class="btn btn-large" style="background: var(--color-white); color: var(--color-primary-deep-blue);">
                        Request Your Kit - $99
                    </a>
                    <a href="how-it-works.php" class="btn btn-outline btn-large" style="border-color: var(--color-white); color: var(--color-white);">
                        See How It Works
                    </a>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>