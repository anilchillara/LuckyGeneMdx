<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();

$page_title = 'Clinical Resources & Genetic Library';
$page_description = 'Explore our knowledge base on carrier status, rare diseases, and global clinical advocacy groups.';

$resources = [
    ["name" => "Orphanet", "url" => "https://www.orpha.net", "domain" => "orpha.net", "longDesc" => "The world's leading portal for rare diseases and orphan drugs providing a unique inventory of rare diseases."],
    ["name" => "ClinicalTrials.gov", "url" => "https://clinicaltrials.gov", "domain" => "clinicaltrials.gov", "longDesc" => "A global database of clinical studies provided by the U.S. National Library of Medicine."],
    ["name" => "GARD (NIH)", "url" => "https://rarediseases.info.nih.gov", "domain" => "nih.gov", "longDesc" => "The Genetic and Rare Diseases Information Center providing reliable information about rare conditions."],
    ["name" => "NORD", "url" => "https://rarediseases.org", "domain" => "rarediseases.org", "longDesc" => "The National Organization for Rare Disorders dedicated to individuals with rare diseases."],
    ["name" => "Global Genes", "url" => "https://globalgenes.org", "domain" => "globalgenes.org", "longDesc" => "An international non-profit connecting and empowering the rare disease community."],
    ["name" => "RDCRN", "url" => "https://www.rarediseasesnetwork.org", "domain" => "nih.gov", "longDesc" => "Facilitates collaboration to accelerate medical research and new treatment discoveries."]
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
        .marquee-wrapper { position: relative; overflow: hidden; padding: 2rem 0; }
        .marquee-container {
            display: flex;
            gap: 2rem;
            width: max-content;
            animation: scroll-left 50s linear infinite;
        }
        .marquee-wrapper:hover .marquee-container { animation-play-state: paused; }
        @keyframes scroll-left {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .resource-card {
            width: 350px;
            flex-shrink: 0;
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main id="main-content">
        <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
            <div class="container">
                <h1 style="color: var(--color-white); margin-bottom: 1rem;">Clinical Resources</h1>
                <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
                    Access global knowledge bases and advocacy networks for genetic health.
                </p>
            </div>
        </section>

        

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

        <section class="section">
            <div class="container">
                <h2 class="text-center" style="margin-bottom: 2rem;">Clinical Knowledge Base</h2>
                <div class="marquee-wrapper">
                    <div class="marquee-container">
                        <?php 
                        $loop_resources = array_merge($resources, $resources);
                        foreach ($loop_resources as $res): 
                        ?>
                        <a href="<?php echo $res['url']; ?>" target="_blank" class="resource-card">
                            <div class="glass-card" style="padding: 2rem; height: 100%;">
                                <div style="font-size: 1.5rem; margin-bottom: 1rem;">🌐</div>
                                <h4 style="margin-bottom: 0.2rem;"><?php echo $res['name']; ?></h4>
                                <p style="font-size: 0.7rem; font-weight: bold; color: var(--color-medical-teal); text-transform: uppercase; margin-bottom: 1rem;"><?php echo $res['domain']; ?></p>
                                <p style="font-size: 0.85rem; color: var(--color-dark-gray); line-height: 1.4;">
                                    <?php echo $res['longDesc']; ?>
                                </p>
                                <div style="margin-top: 1.5rem; font-size: 0.75rem; font-weight: bold; color: var(--color-primary-deep-blue);">EXPLORE PORTAL →</div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
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
                        Unlock the data hidden in your DNA. Understanding your carrier status is the most powerful step you can take for your family’s future health.
                    </p>

                    <div style="margin-bottom: 2.5rem;">
                        <span style="font-size: 4.5rem; font-weight: 800; color: var(--color-primary-deep-blue);">$99</span>
                        <span style="font-size: 1.8rem; color: var(--color-dark-gray); text-decoration: line-through; opacity: 0.5; margin-left: 12px;">$249</span>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <a href="request-kit.php" class="btn btn-primary btn-large btn-pulse ">
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