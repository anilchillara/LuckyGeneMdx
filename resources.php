<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();

$page_title = 'Clinical Resources & Genetic Library';
$page_description = 'Explore our knowledge base on carrier status, rare diseases, and global clinical advocacy groups.';

$resources = [
    [
        "name" => "Orphanet",
        "url" => "https://www.orpha.net",
        "domain" => "orpha.net",
        "longDesc" => "The definitive global resource for rare diseases and orphan drugs, offering a comprehensive nomenclature, an encyclopedia of conditions, and a directory of specialized care centers and diagnostic laboratories across 40 countries.",
        "color" => "#00B3A4" // Medical Teal
    ],
    [
        "name" => "ClinicalTrials.gov",
        "url" => "https://clinicaltrials.gov",
        "domain" => "clinicaltrials.gov",
        "longDesc" => "A centralized registry and results database of publicly and privately funded clinical studies conducted around the world, managed by the U.S. National Library of Medicine to provide transparency in medical research.",
        "color" => "#07327b" // Deep Blue
    ],
    [
        "name" => "GARD (NIH)",
        "url" => "https://rarediseases.info.nih.gov",
        "domain" => "nih.gov",
        "longDesc" => "The Genetic and Rare Diseases Information Center (GARD) provides the public with free, easy-to-understand information on rare and genetic conditions, translating complex scientific data into actionable resources for patients and families.",
        "color" => "#6C63FF" // Soft Purple
    ],
    [
        "name" => "NORD",
        "url" => "https://rarediseases.org",
        "domain" => "rarediseases.org",
        "longDesc" => "The National Organization for Rare Disorders (NORD) is a primary advocacy organization providing patient assistance programs, education, and research grants while lobbying for legislation that benefits the 30 million Americans with rare diseases.",
        "color" => "#00B3A4" // Medical Teal
    ],
    [
        "name" => "Global Genes",
        "url" => "https://globalgenes.org",
        "domain" => "globalgenes.org",
        "longDesc" => "A leading international non-profit that builds and unites the rare disease community by equipping patient advocates with tools, training, and resources to accelerate research and widen the drug development pipeline.",
        "color" => "#07327b" // Deep Blue
    ],
    [
        "name" => "RDCRN",
        "url" => "https://www.rarediseasesnetwork.org",
        "domain" => "nih.gov",
        "longDesc" => "The Rare Diseases Clinical Research Network (RDCRN) facilitates collaborative research through a network of 20+ consortia, focusing on natural history studies, clinical trial readiness, and the training of new investigators in the field.",
        "color" => "#6C63FF" // Soft Purple
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | LuckyGeneMDx</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    
    <style>
        /* PREMIUM INFINITE MARQUEE CAROUSEL */
        .marquee-section {
            background: linear-gradient(135deg, #f5f7fa 0%,rgb(152, 214, 219) 100%);
            padding: 80px 0;
            overflow: hidden;
            position: relative;
        }
        
        /* ===== FIXED MARQUEE SECTION ===== */
        .marquee-section {
            background: #f8fafc;
            padding: 80px 0;
            overflow: hidden;
            position: relative;
        }
        
        /* Gradient Fades for a "stunning" look */
        .marquee-section::before,
        .marquee-section::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 200px;
            z-index: 10;
            pointer-events: none;
        }
        
        .marquee-section::before {
            left: 0;
            background: linear-gradient(to right, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
        }
        
        .marquee-section::after {
            right: 0;
            background: linear-gradient(to left, #f8fafc 0%, rgba(248, 250, 252, 0) 100%);
        }
        
        .marquee-container {
            display: flex;
            width: max-content; /* Critical: Allows the container to expand to its full content width */
            animation: scroll-infinite 40s linear infinite; /* Slowed down to 40s for smooth movement */
            will-change: transform;
        }
        
        .marquee-track {
            display: flex;
            gap: 30px; /* Space between cards */
            padding-right: 30px; /* Must match the gap to ensure the loop reset is invisible */
        }
        
        @keyframes scroll-infinite {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); } /* Resets perfectly onto the second track */
        }
        
        .marquee-container:hover {
            animation-play-state: paused; /* User can pause to read */
        }

        /* Card Styling */
        .resource-card {
            width: 380px;
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid #edf2f7;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .resource-card h3 {
            color: #0A1F44;
            margin: 0 0 15px;
            font-size: 1.4rem;
        }

        .resource-card p {
            color: #4A5568;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .resource-link {
            color: #00B3A4;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* PREMIUM RESOURCE CARD */
        .resource-card {
            width: 420px;
            flex-shrink: 0;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
        }
        
        .resource-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        /* COLOR ACCENT BAR */
        .card-accent {
            height: 6px;
            background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .card-accent::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 1s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        /* CARD HEADER */
        .card-header {
            padding: 30px 30px 20px;
            position: relative;
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            /* background: var(--card-color); */
            background: var(white);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .card-domain {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 179, 164, 0.08);
            color: #00B3A4;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        
        .card-domain::before {
            content: '🌐';
            font-size: 14px;
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0A1F44;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        
        /* CARD BODY */
        .card-body {
            padding: 0 30px 25px;
        }
        
        .card-description {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 25px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* CARD FOOTER */
        .card-footer {
            padding: 20px 30px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #e2e8f0;
        }
        
        .visit-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--card-color);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .resource-card:hover .visit-btn {
            gap: 12px;
            padding-right: 24px;
        }
        
        .visit-btn::after {
            content: '→';
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .resource-card:hover .visit-btn::after {
            transform: translateX(4px);
        }
        
        .resource-type {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .resource-type::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 6s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* STATS BADGE */
        .stats-badge {
            position: absolute;
            top: 30px;
            right: 30px;
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--card-color);
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .resource-card {
                width: 340px;
            }
            
            .marquee-section::before,
            .marquee-section::after {
                width: 50px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">
        <!-- Page Header - UNCHANGED -->
        <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
            <div class="container">
                <h1 style="color: var(--color-white); margin-bottom: 1rem;">Clinical Resources</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
                    Access global knowledge bases and advocacy networks for genetic health.
                </p>
            </div>
        </section>

        <!-- Statistics Section - UNCHANGED -->
        <section class="section" style="background: var(--color-light-gray);">
            <div class="container">
                <div style="text-align: center; margin-bottom: 3rem;">
                    <h2>Rare is not Scarce</h2>
                    <p style="color: var(--color-dark-gray);">A condition is "rare" when it affects fewer than 1 in 2,000 people.</p>
                </div>
                
                <div class="row">
                    <div class="col col-3">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;">300M+</h3>
                            <h4 style="color: var(--color-medical-teal); font-size: 0.8rem; letter-spacing: 1px;">GLOBAL PATIENTS</h4>
                            <p style="font-size: 0.85rem;">Equivalent to the 3rd largest country in the world.</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;">72%</h3>
                            <h4 style="color: var(--color-medical-teal); font-size: 0.8rem; letter-spacing: 1px;">GENETIC ROOT</h4>
                            <p style="font-size: 0.85rem;">The vast majority of rare diseases originate in the DNA.</p>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="glass-card" style="text-align: center; padding: 2rem;">
                            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;">5 Yrs</h3>
                            <h4 style="color: var(--color-medical-teal); font-size: 0.8rem; letter-spacing: 1px;">DIAGNOSTIC ODYSSEY</h4>
                            <p style="font-size: 0.85rem;">Average time to receive an accurate diagnosis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PREMIUM INFINITE MARQUEE - Clinical Knowledge Base -->
        <section class="marquee-section">
            <div class="container">
                <div style="text-align: center; margin-bottom: 50px;">
                    <h2 style="font-size: 2.5rem; color: #0A1F44; margin-bottom: 12px; font-weight: 700;">
                        Clinical Knowledge Base
                    </h2>
                    <p style="color: #64748b; font-size: 1.1rem; max-width: 700px; margin: 0 auto;">
                        Trusted global genomic databases and clinical reference standards
                    </p>
                </div>
            </div>
            
            <div class="marquee-container">
                <!-- First set of cards -->
                <div class="marquee-track">
                    <?php foreach ($resources as $resource): ?>
                        <a href="<?php echo htmlspecialchars($resource['url']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="resource-card"
                           style="--card-color: <?php echo $resource['color']; ?>; --card-color-light: <?php echo $resource['color'] . '80'; ?>;">
                            
                            <!-- Color Accent -->
                            <div class="card-accent"></div>
                            
                            <!-- Card Header -->
                            <div class="card-header">
                                <div class="stats-badge">Verified</div>
                                
                                <div class="card-icon">
                                    🧬
                                </div>
                                
                                <div class="card-domain">
                                    <?php echo htmlspecialchars($resource['domain']); ?>
                                </div>
                                
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($resource['name']); ?>
                                </h3>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body">
                                <p class="card-description">
                                    <?php echo htmlspecialchars($resource['longDesc']); ?>
                                </p>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer">
                                <div class="resource-type">
                                    Active Portal
                                </div>
                                <div class="visit-btn">
                                    Visit Resource
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Duplicate set for infinite loop -->
                <div class="marquee-track" aria-hidden="true">
                    <?php foreach ($resources as $resource): ?>
                        <a href="<?php echo htmlspecialchars($resource['url']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="resource-card"
                           style="--card-color: <?php echo $resource['color']; ?>; --card-color-light: <?php echo $resource['color'] . '80'; ?>;">
                            
                            <div class="card-accent"></div>
                            
                            <div class="card-header">
                                <div class="stats-badge">Verified</div>
                                
                                <div class="card-icon">
                                    🧬
                                </div>
                                
                                <div class="card-domain">
                                    <?php echo htmlspecialchars($resource['domain']); ?>
                                </div>
                                
                                <h3 class="card-title">
                                    <?php echo htmlspecialchars($resource['name']); ?>
                                </h3>
                            </div>
                            
                            <div class="card-body">
                                <p class="card-description">
                                    <?php echo htmlspecialchars($resource['longDesc']); ?>
                                </p>
                            </div>
                            
                            <div class="card-footer">
                                <div class="resource-type">
                                    Active Portal
                                </div>
                                <div class="visit-btn">
                                    Visit Resource
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <p style="color: #64748b; font-size: 0.95rem; font-weight: 500;">
                    ← Hover to pause • Click any card to visit portal →
                </p>
            </div>
        </section>

        <!-- CTA Section - UNCHANGED -->
        <section class="section conversion-area" style="background: var(--color-light-gray); padding: 5rem 0;">
            <div class="container">
                <div class="sale-card" style="background: var(--color-white); border-radius: 24px; box-shadow: 0 20px 50px rgba(10, 31, 68, 0.12); padding: 4.5rem 2rem; text-align: center; border: 1px solid var(--color-medium-gray); max-width: 850px; margin: 0 auto;">
                    
                    <div style="display: inline-block; background: rgba(0, 179, 164, 0.1); color: var(--color-medical-teal); padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1.5px;">
                        Empower Your Decisions
                    </div>

                    <h2 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                        Knowledge is Power
                    </h2>
                    
                    <p style="font-size: 1.2rem; color: var(--color-dark-gray); margin-bottom: 2.5rem; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                        Unlock the data hidden in your DNA. Understanding your carrier status is the most powerful step you can take for your family's future health.
                    </p>

                    <div style="margin-bottom: 2.5rem;">
                        <span style="font-size: 4.5rem; font-weight: 800; color: var(--color-primary-deep-blue);">$99</span>
                        <span style="font-size: 1.8rem; color: var(--color-dark-gray); text-decoration: line-through; opacity: 0.5; margin-left: 12px;">$249</span>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <a href="request-kit.php" class="btn btn-primary btn-large btn-pulse">
                            Request Your Kit Now - $99
                        </a>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 2rem; opacity: 0.9; flex-wrap: wrap; border-top: 1px solid var(--color-medium-gray); padding-top: 2rem;">
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> CLIA Certified
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Privacy First
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Expert Guidance
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
