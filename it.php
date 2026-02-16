<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
session_start();
setSecurityHeaders();

/**
 * BACKGROUND VIDEO CONFIGURATION
 * Edit these values to adjust the appearance of your DNA animation.
 */
$video_path = "assets/video/DNA_BG.mp4"; // Ensure the path matches your server structure
$video_opacity = 0.4; // Adjust between 0.0 (hidden) and 1.0 (fully visible)
$video_speed = 0.4;   // 1.0 is normal, 0.5 is half speed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure Your Family's Genetic Future with LuckyGeneMDx comprehensive carrier screening. $99 genetic testing kit with results in 14-21 days.">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>LuckyGeneMDx - Comprehensive Genetic Carrier Screening | $99</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <style>
        /* --- Video Background Styling --- */
        .hero {
            position: relative;
            overflow: hidden;
            background-color: #0A1F44; /* Fallback base color */
            min-height: 80vh;
        }

        .hero-video-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;  /* Forces wrapper to full width of the hero section */
            height: 100%; /* Forces wrapper to full height of the hero section */
            z-index: 1;
            opacity: <?php echo $video_opacity; ?>;
            pointer-events: none;
            overflow: hidden; /* Ensures no video overflow spills out of the hero */
        }

        #bg-dna-video {
            display: block;
            /* "object-fit: none" ensures the video is NOT resized/scaled */
            object-fit: none;
            min-width: 100vw;
            min-height: 100vh;
        }

        .hero-content {
            position: relative;
            z-index: 10; /* Ensures content stays above video layers */
        }

        /* --- Testimonials Carousel Styles --- */
        .testimonials-simple-wrapper {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .testimonials-track {
            display: flex;
            transition: transform 0.5s ease;
            gap: 30px;
        }
        
        .testimonial-slide {
            min-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .testimonial-card-centered {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 700px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .testimonial-quote-centered {
            font-size: 1.3rem;
            font-style: italic;
            line-height: 1.8;
            color: #1a237e;
            margin-bottom: 30px;
            position: relative;
        }
        
        .testimonial-quote-centered::before {
            content: '"';
            font-size: 4rem;
            color: #00B3A4;
            opacity: 0.2;
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .testimonial-author-centered {
            font-size: 1.1rem;
            font-weight: 600;
            color: #00B3A4;
            margin-bottom: 5px;
        }
        
        .testimonial-location-centered {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .carousel-nav-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 40px;
        }
        
        .carousel-nav-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 2px solid #00B3A4;
            color: #00B3A4;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            font-weight: bold;
        }
        
        .carousel-nav-btn:hover:not(:disabled) {
            background: #00B3A4;
            color: white;
            transform: scale(1.1);
        }
        
        .carousel-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        .carousel-indicator-dots {
            display: flex;
            gap: 10px;
        }
        
        .carousel-indicator-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #d4d4d4;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .carousel-indicator-dot.active {
            background: #00B3A4;
            width: 35px;
            border-radius: 6px;
        }
        
        @media (max-width: 768px) {
            .testimonial-card-centered {
                padding: 30px 20px;
            }
            .testimonial-quote-centered {
                font-size: 1.1rem;
            }
            #bg-dna-video {
                object-fit: cover; /* On mobile, we cover to ensure no blank spaces */
            }
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <?php include 'includes/header.php'; ?>
    
    <main id="main-content">
        <section class="hero" aria-labelledby="hero-heading">
            <div class="hero-video-wrapper">
                <video autoplay muted loop playsinline id="bg-dna-video">
                    <source src="<?php echo $video_path; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
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
                                <circle cx="100" cy="50" r="30" fill="#00B3A4" opacity="0.3"/>
                                <text x="100" y="55" text-anchor="middle" fill="#0A1F44" font-size="14" font-weight="600">
                                    Parent 1
                                </text>
                                <text x="100" y="70" text-anchor="middle" fill="#0A1F44" font-size="12">
                                    Carrier
                                </text>
                                
                                <circle cx="300" cy="50" r="30" fill="#00B3A4" opacity="0.3"/>
                                <text x="300" y="55" text-anchor="middle" fill="#0A1F44" font-size="14" font-weight="600">
                                    Parent 2
                                </text>
                                <text x="300" y="70" text-anchor="middle" fill="#0A1F44" font-size="12">
                                    Carrier
                                </text>
                                
                                <line x1="100" y1="80" x2="75" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="100" y1="80" x2="150" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="300" y1="80" x2="250" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                <line x1="300" y1="80" x2="325" y2="200" stroke="#0A1F44" stroke-width="2"/>
                                
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
                        <div class="timeline-icon">💑</div>
                        <h4>Before Trying to Conceive</h4>
                        <p>
                            Get screened before pregnancy begins. This gives you maximum time to consult 
                            with genetic counselors and understand your options.
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
        
        <section class="section marquee-3d-section" style="background: #f8f9fa; padding: 80px 0; overflow: hidden;">
            <div class="container">
                <h2 id="testimonials-heading" class="text-center mb-5">
                    Trusted by Families Nationwide
                </h2>
                
                <?php
                try {
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("
                        SELECT name, age, location, quote 
                        FROM testimonials 
                        WHERE is_active = 1 
                        ORDER BY display_order ASC, created_at DESC
                    ");
                    $stmt->execute();
                    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($testimonials)):
                ?>
                    <div class="testimonials-simple-wrapper">
                        <div class="testimonials-track" id="testimonialsTrack">
                            <?php foreach ($testimonials as $testimonial): ?>
                                <div class="testimonial-slide">
                                    <div class="testimonial-card-centered">
                                        <div class="testimonial-quote-centered">
                                            <?php echo htmlspecialchars($testimonial['quote']); ?>
                                        </div>
                                        <div class="testimonial-author-centered">
                                            <?php echo htmlspecialchars($testimonial['name']); ?><?php echo !empty($testimonial['age']) ? ', ' . (int)$testimonial['age'] : ''; ?>
                                        </div>
                                        <?php if (!empty($testimonial['location'])): ?>
                                            <div class="testimonial-location-centered">
                                                <?php echo htmlspecialchars($testimonial['location']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($testimonials) > 1): ?>
                        <div class="carousel-nav-controls">
                            <button class="carousel-nav-btn" id="prevBtn" onclick="changeTestimonial(-1)">‹</button>
                            <div class="carousel-indicator-dots" id="indicatorDots">
                                <?php for ($i = 0; $i < count($testimonials); $i++): ?>
                                    <div class="carousel-indicator-dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToTestimonial(<?php echo $i; ?>)"></div>
                                <?php endfor; ?>
                            </div>
                            <button class="carousel-nav-btn" id="nextBtn" onclick="changeTestimonial(1)">›</button>
                        </div>
                        <div style="text-align: center; margin-top: 20px; color: #6c757d; font-size: 0.9rem;">
                            <span id="testimonialCounter">1 / <?php echo count($testimonials); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php 
                    else:
                        echo '<p class="text-center">Join hundreds of families securing their future today.</p>';
                    endif;
                } catch (Exception $e) {
                    error_log("Database error: " . $e->getMessage());
                }
                ?>
            </div>
        </section>
        
        <section class="section conversion-area" style="background: var(--color-light-gray); padding: 5rem 0;">
            <div class="container">
                <div class="sale-card" style="background: var(--color-white); border-radius: 24px; box-shadow: 0 20px 50px rgba(10, 31, 68, 0.12); padding: 4.5rem 2rem; text-align: center; border: 1px solid var(--color-medium-gray); max-width: 900px; margin: 0 auto;">
                    
                    <div style="display: inline-block; background: rgba(0, 179, 164, 0.1); color: var(--color-medical-teal); padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1.5px;">
                        Limited Time Pricing
                    </div>

                    <h2 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                        Invest $99 Today for a Healthier Tomorrow
                    </h2>
                    
                    <p style="font-size: 1.2rem; color: var(--color-dark-gray); margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                        Take the first step toward informed family planning with comprehensive genetic carrier screening. Clear insights, delivered privately.
                    </p>

                    <div style="margin-bottom: 2.5rem;">
                        <span style="font-size: 4.5rem; font-weight: 800; color: var(--color-primary-deep-blue);">$99</span>
                        <span style="font-size: 1.8rem; color: var(--color-dark-gray); text-decoration: line-through; opacity: 0.5; margin-left: 12px;">$249</span>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <a href="request-kit.php" class="btn btn-primary btn-large btn-pulse ">
                            Get Your Screening Kit
                        </a>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 2rem; opacity: 0.9; flex-wrap: wrap;">
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Secure checkout
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Private results
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Expert support
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DNA Video Playback Controls
            const dnaVideo = document.getElementById('bg-dna-video');
            if (dnaVideo) {
                // Set playback speed as defined in PHP config
                dnaVideo.playbackRate = <?php echo $video_speed; ?>;
            }
            
            // Initialize other UI components
            initTestimonialsCarousel();
        });
        
        // Simple Testimonials Carousel
        let currentTestimonial = 0;
        let totalTestimonials = 0;
        let autoPlayTimer = null;
        
        function initTestimonialsCarousel() {
            const track = document.getElementById('testimonialsTrack');
            if (!track) return;
            
            const slides = track.querySelectorAll('.testimonial-slide');
            totalTestimonials = slides.length;
            
            if (totalTestimonials <= 1) return;
            
            updateTestimonialDisplay();
            startAutoPlay();
            
            track.addEventListener('mouseenter', stopAutoPlay);
            track.addEventListener('mouseleave', startAutoPlay);
        }
        
        function changeTestimonial(direction) {
            currentTestimonial += direction;
            
            if (currentTestimonial < 0) {
                currentTestimonial = totalTestimonials - 1;
            } else if (currentTestimonial >= totalTestimonials) {
                currentTestimonial = 0;
            }
            
            updateTestimonialDisplay();
        }
        
        function goToTestimonial(index) {
            currentTestimonial = index;
            updateTestimonialDisplay();
        }
        
        function updateTestimonialDisplay() {
            const track = document.getElementById('testimonialsTrack');
            const dots = document.querySelectorAll('.carousel-indicator-dot');
            const counter = document.getElementById('testimonialCounter');
            
            if (!track) return;
            
            track.style.transform = `translateX(-${currentTestimonial * 100}%)`;
            
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentTestimonial);
            });
            
            if (counter) {
                counter.textContent = `${currentTestimonial + 1} / ${totalTestimonials}`;
            }
        }
        
        function startAutoPlay() {
            stopAutoPlay();
            if (totalTestimonials > 1) {
                autoPlayTimer = setInterval(() => {
                    changeTestimonial(1);
                }, 2200);
            }
        }
        
        function stopAutoPlay() {
            if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
            }
        }
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') changeTestimonial(-1);
            if (e.key === 'ArrowRight') changeTestimonial(1);
        });
    </script>
</body>
</html>