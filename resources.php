<?php
define('LuckyGenesMDx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
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
        "color" => "#00e5ff" // Medical Teal
    ],
    [
        "name" => "ClinicalTrials.gov",
        "url" => "https://clinicaltrials.gov",
        "domain" => "clinicaltrials.gov",
        "longDesc" => "A centralized registry and results database of publicly and privately funded clinical studies conducted around the world, managed by the U.S. National Library of Medicine to provide transparency in medical research.",
        "color" => "#2979ff" // Deep Blue
    ],
    [
        "name" => "GARD (NIH)",
        "url" => "https://rarediseases.info.nih.gov",
        "domain" => "nih.gov",
        "longDesc" => "The Genetic and Rare Diseases Information Center (GARD) provides the public with free, easy-to-understand information on rare and genetic conditions, translating complex scientific data into actionable resources for patients and families.",
        "color" => "#9177C7" // Soft Purple
    ],
    [
        "name" => "NORD",
        "url" => "https://rarediseases.org",
        "domain" => "rarediseases.org",
        "longDesc" => "The National Organization for Rare Disorders (NORD) is a primary advocacy organization providing patient assistance programs, education, and research grants while lobbying for legislation that benefits the 30 million Americans with rare diseases.",
        "color" => "#00e5ff" // Medical Teal
    ],
    [
        "name" => "Global Genes",
        "url" => "https://globalgenes.org",
        "domain" => "globalgenes.org",
        "longDesc" => "A leading international non-profit that builds and unites the rare disease community by equipping patient advocates with tools, training, and resources to accelerate research and widen the drug development pipeline.",
        "color" => "#2979ff" // Deep Blue
    ],
    [
        "name" => "RDCRN",
        "url" => "https://www.rarediseasesnetwork.org",
        "domain" => "nih.gov",
        "longDesc" => "The Rare Diseases Clinical Research Network (RDCRN) facilitates collaborative research through a network of 20+ consortia, focusing on natural history studies, clinical trial readiness, and the training of new investigators in the field.",
        "color" => "#9177C7" // Soft Purple
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">
        <!-- Page Header - UNCHANGED -->
        <section class="page-header">
                <p>
                    Access global knowledge bases and advocacy networks for genetic health.
                </p>
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
                <div class="resources-intro">
                    <h2>
                        Clinical Knowledge Base
                    </h2>
                    <p>
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
            
            <div class="resources-footer">
                <p>
                    ← Hover to pause • Click any card to visit portal →
                </p>
            </div>
        </section>

        <!-- CTA Section - UNCHANGED -->
        <?php if (defined('SHOW_CTA') && SHOW_CTA): ?>
        <section class="sec-cta">
            <div class="cta-box reveal">
                <div class="cta-pill">Empower Your Decisions</div>
                <h2>Knowledge is Power</h2>
                <p>Unlock the data hidden in your DNA. Understanding your carrier status is the most powerful step you can take for your family's future health.</p>
                
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
                        CLIA Certified
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Privacy First
                    </div>
                    <div class="cta-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                        Expert Guidance
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
