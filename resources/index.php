<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

$page_title = 'Knowledge Hub - Genetic Screening Resources';
$page_description = 'Comprehensive educational resources about genetic carrier screening, inherited conditions, and family planning.';

// Get featured resources
try {
    $db = Database::getInstance()->getConnection();
    
    // Get featured articles
    $featured_stmt = $db->prepare("SELECT * FROM educational_resources 
                                    WHERE featured = 1 AND status = 'published' 
                                    ORDER BY created_at DESC 
                                    LIMIT 3");
    $featured_stmt->execute();
    $featured = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories
    $cat_stmt = $db->query("SELECT DISTINCT category FROM educational_resources 
                             WHERE status = 'published' 
                             ORDER BY category");
    $categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get recent resources
    $recent_stmt = $db->prepare("SELECT * FROM educational_resources 
                                  WHERE status = 'published' 
                                  ORDER BY created_at DESC 
                                  LIMIT 9");
    $recent_stmt->execute();
    $recent = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $featured = [];
    $categories = [];
    $recent = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMDx</title>
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="main-nav">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <span class="dna-icon">🧬</span>
                <span>LuckyGeneMDx</span>
            </a>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../about-genetic-screening.php">About Screening</a></li>
                <li><a href="../how-it-works.php">How It Works</a></li>
                <li><a href="index.php" class="active">Resources</a></li>
                <li><a href="../blog/">Blog</a></li>
                <li><a href="../request-kit.php" class="btn-primary">Request Kit</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="fade-in">Knowledge Hub</h1>
                <p class="lead fade-in-delay">Your comprehensive resource for understanding genetic carrier screening</p>
                
                <!-- Search Bar -->
                <form action="search.php" method="GET" class="hero-search">
                    <input type="text" 
                           name="q" 
                           placeholder="Search resources, conditions, FAQs..." 
                           class="search-input">
                    <button type="submit" class="btn-primary">Search</button>
                </form>
            </div>
        </div>
        <div class="dna-background"></div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Featured Resources -->
        <?php if (!empty($featured)): ?>
            <section class="content-section">
                <div class="container">
                    <h2 class="text-center">Featured Resources</h2>
                    <div class="featured-resources">
                        <?php foreach ($featured as $resource): ?>
                            <article class="featured-card">
                                <div class="featured-content">
                                    <span class="category-badge"><?php echo htmlspecialchars($resource['category']); ?></span>
                                    <h3>
                                        <a href="article.php?id=<?php echo $resource['id']; ?>">
                                            <?php echo htmlspecialchars($resource['title']); ?>
                                        </a>
                                    </h3>
                                    <p><?php echo htmlspecialchars($resource['excerpt']); ?></p>
                                    <a href="article.php?id=<?php echo $resource['id']; ?>" class="read-more">
                                        Learn More →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Browse by Category -->
        <section class="content-section gray-bg">
            <div class="container">
                <h2 class="text-center">Browse by Topic</h2>
                <p class="text-center subtitle">Explore our comprehensive library of genetic health resources</p>
                
                <div class="resource-categories">
                    <a href="category.php?cat=Understanding%20Carrier%20Status" class="resource-category-card">
                        <div class="icon">🧬</div>
                        <h3>Understanding Carrier Status</h3>
                        <p>Learn what it means to be a genetic carrier and how it affects you and your family.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                    
                    <a href="category.php?cat=Genetic%20Conditions" class="resource-category-card">
                        <div class="icon">📊</div>
                        <h3>Genetic Conditions</h3>
                        <p>Detailed information about inherited disorders and their inheritance patterns.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                    
                    <a href="category.php?cat=Family%20Planning" class="resource-category-card">
                        <div class="icon">👨‍👩‍👧‍👦</div>
                        <h3>Family Planning</h3>
                        <p>Making informed decisions about pregnancy and reproductive options.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                    
                    <a href="category.php?cat=Testing%20%26%20Results" class="resource-category-card">
                        <div class="icon">🔬</div>
                        <h3>Testing & Results</h3>
                        <p>Understanding the screening process and how to interpret your results.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                    
                    <a href="category.php?cat=Genetic%20Counseling" class="resource-category-card">
                        <div class="icon">💬</div>
                        <h3>Genetic Counseling</h3>
                        <p>Learn about genetic counseling services and when to seek professional guidance.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                    
                    <a href="category.php?cat=Pregnancy%20%26%20Genetics" class="resource-category-card">
                        <div class="icon">🤰</div>
                        <h3>Pregnancy & Genetics</h3>
                        <p>Genetic considerations during pregnancy and prenatal testing options.</p>
                        <span class="explore-link">Explore →</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Recent Resources -->
        <?php if (!empty($recent)): ?>
            <section class="content-section">
                <div class="container">
                    <h2 class="text-center">Recent Resources</h2>
                    <div class="resources-grid">
                        <?php foreach ($recent as $resource): ?>
                            <article class="resource-card">
                                <div class="resource-content">
                                    <span class="category"><?php echo htmlspecialchars($resource['category']); ?></span>
                                    <h3>
                                        <a href="article.php?id=<?php echo $resource['id']; ?>">
                                            <?php echo htmlspecialchars($resource['title']); ?>
                                        </a>
                                    </h3>
                                    <p><?php echo htmlspecialchars(substr($resource['excerpt'], 0, 120)) . '...'; ?></p>
                                    <a href="article.php?id=<?php echo $resource['id']; ?>" class="read-more-small">
                                        Read More →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Quick Links Section -->
        <section class="content-section gray-bg">
            <div class="container">
                <h2 class="text-center">Quick Access</h2>
                <div class="quick-links-grid">
                    <a href="../about-genetic-screening.php" class="quick-link-card">
                        <h4>📖 About Genetic Screening</h4>
                        <p>Introduction to carrier screening and why it matters</p>
                    </a>
                    <a href="../how-it-works.php" class="quick-link-card">
                        <h4>⚙️ How It Works</h4>
                        <p>Step-by-step guide to the screening process</p>
                    </a>
                    <a href="faq.php" class="quick-link-card">
                        <h4>❓ Frequently Asked Questions</h4>
                        <p>Answers to common questions about carrier screening</p>
                    </a>
                    <a href="glossary.php" class="quick-link-card">
                        <h4>📚 Genetics Glossary</h4>
                        <p>Definitions of key genetics and medical terms</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- Downloadable Resources -->
        <section class="content-section">
            <div class="container">
                <h2 class="text-center">Downloadable Guides</h2>
                <p class="text-center subtitle">Take these resources with you</p>
                
                <div class="download-resources">
                    <div class="download-card">
                        <div class="download-icon">📄</div>
                        <h3>Carrier Screening Guide</h3>
                        <p>Comprehensive overview of genetic carrier screening for couples planning a family.</p>
                        <a href="../assets/downloads/carrier-screening-guide.pdf" class="btn-secondary" download>
                            Download PDF
                        </a>
                    </div>
                    
                    <div class="download-card">
                        <div class="download-icon">📋</div>
                        <h3>Genetic Health Checklist</h3>
                        <p>Pre-conception checklist for understanding your genetic health status.</p>
                        <a href="../assets/downloads/genetic-health-checklist.pdf" class="btn-secondary" download>
                            Download PDF
                        </a>
                    </div>
                    
                    <div class="download-card">
                        <div class="download-icon">📊</div>
                        <h3>Understanding Results</h3>
                        <p>Guide to interpreting your carrier screening results and next steps.</p>
                        <a href="../assets/downloads/understanding-results.pdf" class="btn-secondary" download>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section conversion-area" style="background: var(--color-light-gray); padding: 5rem 0;">
            <div class="container">
                <div class="sale-card" style="background: var(--color-white); border-radius: 24px; box-shadow: 0 20px 50px rgba(10, 31, 68, 0.12); padding: 4.5rem 2rem; text-align: center; border: 1px solid var(--color-medium-gray); max-width: 900px; margin: 0 auto;">
                    
                    <div style="display: inline-block; background: rgba(0, 179, 164, 0.1); color: var(--color-medical-teal); padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1.5px;">
                        Secure Your Genetic Future
                    </div>

                    <h2 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                        Ready to Take the Next Step?
                    </h2>
                    
                    <p style="font-size: 1.2rem; color: var(--color-dark-gray); margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                        Order your comprehensive carrier screening kit today for just $99. Clinical-grade insights, 100% private, and physician-reviewed.
                    </p>

                    <div style="margin-bottom: 2.5rem;">
                        <span style="font-size: 4.5rem; font-weight: 800; color: var(--color-primary-deep-blue);">$99</span>
                        <span style="font-size: 1.8rem; color: var(--color-dark-gray); text-decoration: line-through; opacity: 0.5; margin-left: 12px;">$249</span>
                    </div>

                    <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2rem;">
                        <a href="request-kit.php" class="btn btn-large" style="background: var(--color-medical-teal); color: var(--color-white); padding: 1.25rem 3rem; border-radius: 100px; font-size: 1.1rem; text-decoration: none; font-weight: 700; display: inline-block; transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(0, 179, 164, 0.2);">
                            Request Your Kit
                        </a>
                        <a href="about-genetic-screening.php" class="btn btn-outline btn-large" style="border: 2px solid var(--color-primary-deep-blue); color: var(--color-primary-deep-blue); padding: 1.25rem 3rem; border-radius: 100px; font-size: 1.1rem; text-decoration: none; font-weight: 700; display: inline-block;">
                            Learn More
                        </a>
                    </div>

                    <div style="display: flex; justify-content: center; gap: 2rem; opacity: 0.9; flex-wrap: wrap; border-top: 1px solid var(--color-medium-gray); padding-top: 2rem;">
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Free Shipping
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Secure Lab Processing
                        </span>
                        <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                            <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Expert Support
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    
</body>
</html>
