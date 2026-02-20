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
