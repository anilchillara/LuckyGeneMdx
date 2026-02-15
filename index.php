<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure Your Family's Genetic Future with LuckyGeneMDx comprehensive carrier screening. $99 genetic testing kit with results in 14-21 days.">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>LuckyGeneMDx - Comprehensive Genetic Carrier Screening | $99</title>
    
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
        <!-- Hero Section -->
        <section class="hero" aria-labelledby="hero-heading">
            <div class="hero-background">
                <div class="particles" aria-hidden="true"></div>
                <div id="dna-container" aria-hidden="true"></div>
            </div>
            
            <div class="hero-content">
                <h1 id="hero-heading">Secure Your Family's Genetic Future</h1>
                <p class="hero-subtitle">
                    Comprehensive carrier screening for over 300 genetic conditions. 
                    Know your genetic health status before family planning begins.
                </p>
                
                <div class="hero-cta">
                    <a href="request-kit.php" class="btn btn-primary btn-large btn-pulse ">
                        Request Screening Kit - $99
                    </a>
                    <a href="about-genetic-screening.php" class="btn btn-outline btn-large">
                        Learn More
                    </a>
                </div>
                
                <div class="trust-badges" aria-label="Trust indicators">
                    <div class="trust-badge">
                        <span class="trust-badge-icon" aria-hidden="true">✓</span>
                        <span>ACMG Aligned</span>
                    </div>
                    <div class="trust-badge">
                        <span class="trust-badge-icon" aria-hidden="true">🔒</span>
                        <span>Secure & Private</span>
                    </div>
                    <div class="trust-badge">
                        <span class="trust-badge-icon" aria-hidden="true">⚕️</span>
                        <span>CDC Reference Standards</span>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Awareness Section -->
        <section class="section" aria-labelledby="awareness-heading">
            <div class="container">
                <div class="row">
                    <div class="col col-2">
                        <h2 id="awareness-heading">What is Genetic Carrier Screening?</h2>
                        <p>
                            Carrier screening is a type of genetic test that identifies whether you carry 
                            a gene mutation for certain inherited disorders. Even if you don't have the 
                            condition yourself, you could pass it to your children.
                        </p>
                        <p>
                            Understanding your carrier status empowers you to make informed decisions about 
                            family planning and gives you the opportunity to explore options with your 
                            healthcare provider.
                        </p>
                        <h3>Why It Matters Before Family Planning</h3>
                        <ul style="margin-bottom: 2rem;">
                            <li><strong>Early Knowledge:</strong> Identify risks before pregnancy</li>
                            <li><strong>Informed Decisions:</strong> Work with your doctor on family planning options</li>
                            <li><strong>Peace of Mind:</strong> Understand your genetic health status</li>
                            <li><strong>Family Awareness:</strong> Information that benefits your entire family</li>
                        </ul>
                        <a href="about-genetic-screening.php" class="btn btn-primary">
                            Learn About Carrier Screening
                        </a>
                    </div>
                    
                    <div class="col col-2 fade-in">
                        <div class="glass-card" style="padding: 3rem;">
                            <h4 style="text-align: center; margin-bottom: 2rem;">
                                Recessive Inheritance Pattern
                            </h4>
                            <svg viewBox="0 0 400 300" style="width: 100%; max-width: 400px; margin: 0 auto;">
                                <!-- Parent 1 (Carrier) -->
                                <circle cx="100" cy="50" r="30" fill="#00B3A4" opacity="0.3"/>
                                <text x="100" y="55" text-anchor="middle" fill="#0A1F44" font-size="14" font-weight="600">
                                    Parent 1
                                </text>
                                <text x="100" y="70" text-anchor="middle" fill="#0A1F44" font-size="12">
                                    Carrier
                                </text>
                                
                                <!-- Parent 2 (Carrier) -->
                                <circle cx="300" cy="50" r="30" fill="#00B3A4" opacity="0.3"/>
                                <text x="300" y="55" text-anchor="middle" fill="#0A1F44" font-size="14" font-weight="600">
                                    Parent 2
                                </text>
                                <text x="300" y="70" text-anchor="middle" fill="#0A1F44" font-size="12">
                                    Carrier
                                </text>
                                
                                <!-- Lines to children -->
                                <line x1="100" y1="80" x2="75" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="100" y1="80" x2="150" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="300" y1="80" x2="250" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="300" y1="80" x2="325" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                
                                <!-- Child outcomes -->
                                <circle cx="75" cy="230" r="25" fill="#28a745"/>
                                <text x="75" y="235" text-anchor="middle" fill="white" font-size="11" font-weight="600">
                                    25%
                                </text>
                                <text x="75" y="270" text-anchor="middle" fill="#0A1F44" font-size="10">
                                    Not Affected
                                </text>
                                
                                <circle cx="150" cy="230" r="25" fill="#00B3A4" opacity="0.5"/>
                                <text x="150" y="235" text-anchor="middle" fill="#0A1F44" font-size="11" font-weight="600">
                                    50%
                                </text>
                                <text x="150" y="270" text-anchor="middle" fill="#0A1F44" font-size="10">
                                    Carrier
                                </text>
                                
                                <circle cx="250" cy="230" r="25" fill="#00B3A4" opacity="0.5"/>
                                <text x="250" y="235" text-anchor="middle" fill="#0A1F44" font-size="11" font-weight="600">
                                    50%
                                </text>
                                <text x="250" y="270" text-anchor="middle" fill="#0A1F44" font-size="10">
                                    Carrier
                                </text>
                                
                                <circle cx="325" cy="230" r="25" fill="#dc3545" opacity="0.7"/>
                                <text x="325" y="235" text-anchor="middle" fill="white" font-size="11" font-weight="600">
                                    25%
                                </text>
                                <text x="325" y="270" text-anchor="middle" fill="#0A1F44" font-size="10">
                                    Affected
                                </text>
                            </svg>
                            <p style="text-align: center; margin-top: 2rem; font-size: 0.9rem; color: var(--color-dark-gray);">
                                When both parents are carriers, each pregnancy has a 25% chance of the child being affected
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Scientific Credibility Section -->
        <section class="section" style="background: var(--gradient-hero); color: var(--color-white); text-align: center;">
            <div class="container">
                <h2 style="color: var(--color-white);">Aligned with Medical Genetics Standards</h2>
                <p style="max-width: 800px; margin: 0 auto 3rem; opacity: 0.95;">
                    Our comprehensive carrier screening follows guidelines from leading medical organizations, 
                    ensuring you receive accurate, reliable genetic information.
                </p>
                
                <div class="row stagger-animation" style="justify-content: center;">
                    <div class="col col-3">
                        <div class="glass-card-dark">
                            <h3 style="color: var(--color-medical-teal); font-size: 2rem;">300+</h3>
                            <p>Genetic Conditions Screened</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card-dark">
                            <h3 style="color: var(--color-medical-teal); font-size: 2rem;">ACMG</h3>
                            <p>Standards Alignment</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card-dark">
                            <h3 style="color: var(--color-medical-teal); font-size: 2rem;">$99</h3>
                            <p>Affordable Pricing</p>
                        </div>
                    </div>
                </div>
                
                <p style="margin-top: 3rem; font-size: 0.9rem; opacity: 0.8;">
                    <em>Disclaimer: LuckyGeneMDx is not affiliated with ACMG or CDC. 
                    Our testing aligns with standards established by these organizations.</em>
                </p>
            </div>
        </section>
        
        <!-- Generational Timeline Section -->
        <section class="section timeline" aria-labelledby="timeline-heading">
            <div class="container">
                <h2 id="timeline-heading" class="text-center mb-5">
                    Plan Today. Protect Tomorrow.
                </h2>
                <p class="text-center" style="max-width: 700px; margin: 0 auto 4rem;">
                    Understanding your genetic carrier status is a powerful step in your family planning journey. 
                    Here's how knowledge empowers each life stage.
                </p>
                
                <div class="timeline-container">
                    <div class="timeline-line" aria-hidden="true"></div>
                    
                    <div class="timeline-item fade-in">
                        <div class="timeline-icon">👫</div>
                        <h4>Pre-Marriage</h4>
                        <p>
                            Learn your genetic status early in your relationship to make informed 
                            decisions together about your future family.
                        </p>
                    </div>
                    
                    <div class="timeline-item fade-in">
                        <div class="timeline-icon">📅</div>
                        <h4>Family Planning</h4>
                        <p>
                            Work with your healthcare provider to understand options and plan 
                            for a healthy pregnancy based on your results.
                        </p>
                    </div>
                    
                    <div class="timeline-item fade-in">
                        <div class="timeline-icon">🤰</div>
                        <h4>Pregnancy</h4>
                        <p>
                            If you're already pregnant, carrier screening can still provide 
                            valuable information for your healthcare team.
                        </p>
                    </div>
                    
                    <div class="timeline-item fade-in">
                        <div class="timeline-icon">👶</div>
                        <h4>Child's Future</h4>
                        <p>
                            Your genetic knowledge can benefit your child's health throughout 
                            their lifetime and inform their own family planning.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Testimonials Section -->
        <section class="section" style="background: var(--color-light-gray);" aria-labelledby="testimonials-heading">
            <div class="container">
                <h2 id="testimonials-heading" class="text-center mb-5">
                    Trusted by Families Nationwide
                </h2>
                
                <div class="testimonials-carousel" style="max-width: 800px; margin: 0 auto; position: relative;">
                    <?php
                    $testimonials = [
                        [
                            'name' => 'Sarah M.',
                            'age' => 29,
                            'location' => 'Boston, MA',
                            'quote' => 'Getting screened before starting our family gave us peace of mind. The process was simple and the results were clear and easy to understand.'
                        ],
                        [
                            'name' => 'Michael & Jennifer T.',
                            'age' => 32,
                            'location' => 'Austin, TX',
                            'quote' => 'We discovered we were both carriers for the same condition. Thanks to early knowledge, we were able to work with our doctor on family planning options.'
                        ],
                        [
                            'name' => 'Dr. Lisa Chen',
                            'age' => 35,
                            'location' => 'San Francisco, CA',
                            'quote' => 'As a physician myself, I appreciate the scientific rigor and ACMG-aligned approach. This is how carrier screening should be done.'
                        ],
                        [
                            'name' => 'Robert K.',
                            'age' => 28,
                            'location' => 'Seattle, WA',
                            'quote' => 'The $99 price point made this accessible when other genetic tests were too expensive. Results came back in exactly 3 weeks as promised.'
                        ]
                    ];
                    
                    foreach ($testimonials as $testimonial):
                    ?>
                    <div class="testimonial-item glass-card" style="text-align: center; padding: 3rem;">
                        <p style="font-size: 1.25rem; font-style: italic; margin-bottom: 2rem; line-height: 1.8;">
                            "<?php echo htmlspecialchars($testimonial['quote']); ?>"
                        </p>
                        <p style="font-weight: 600; color: var(--color-medical-teal); margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($testimonial['name']); ?>, <?php echo $testimonial['age']; ?>
                        </p>
                        <p style="font-size: 0.9rem; color: var(--color-dark-gray);">
                            <?php echo htmlspecialchars($testimonial['location']); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                    
                    <button class="carousel-prev" style="position: absolute; left: -50px; top: 50%; transform: translateY(-50%); background: var(--color-white); border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; box-shadow: var(--shadow-md);" aria-label="Previous testimonial">‹</button>
                    <button class="carousel-next" style="position: absolute; right: -50px; top: 50%; transform: translateY(-50%); background: var(--color-white); border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; box-shadow: var(--shadow-md);" aria-label="Next testimonial">›</button>
                </div>
            </div>
        </section>
        
        <!-- Final CTA Section -->
        <section class="section" style="background: var(--gradient-primary); color: var(--color-white); text-align: center;">
            <div class="container">
                <h2 style="color: var(--color-white); margin-bottom: 1.5rem;">
                    Invest $99 Today for a Healthier Tomorrow
                </h2>
                <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.95;">
                    Take the first step toward informed family planning with comprehensive genetic carrier screening.
                </p>
                <a href="request-kit.php" class="btn btn-large" style="background: var(--color-white); color: var(--color-primary-deep-blue); margin-bottom: 1.5rem;">
                    Get Your Screening Kit
                </a>
                <p style="font-size: 0.9rem; opacity: 0.8;">
                    Secure checkout • Private results • Expert support
                </p>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        // Generate DNA helix on page load
        document.addEventListener('DOMContentLoaded', function() {
            LuckyGeneMDx.generateDNAHelix('dna-container');
        });
    </script>
</body>
</html>
