<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// Get article ID
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($article_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get article details
try {
    $db = Database::getInstance()->getConnection();
    
    // Get the article
    $stmt = $db->prepare("SELECT * FROM educational_resources WHERE id = ? AND status = 'published'");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        header('Location: index.php');
        exit;
    }
    
    // Increment view count
    $update_stmt = $db->prepare("UPDATE educational_resources SET views = views + 1 WHERE id = ?");
    $update_stmt->execute([$article_id]);
    
    // Get related articles (same category)
    $related_stmt = $db->prepare("SELECT * FROM educational_resources 
                                   WHERE category = ? AND id != ? AND status = 'published' 
                                   ORDER BY created_at DESC 
                                   LIMIT 3");
    $related_stmt->execute([$article['category'], $article_id]);
    $related_articles = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = $article['title'];
    $page_description = $article['excerpt'];
    
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMDx Resources</title>
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

    <!-- Article Header -->
    <article class="resource-article">
        <header class="article-header">
            <div class="container">
                <div class="breadcrumb">
                    <a href="index.php">Resources</a> / 
                    <a href="category.php?cat=<?php echo urlencode($article['category']); ?>">
                        <?php echo htmlspecialchars($article['category']); ?>
                    </a> / 
                    <span><?php echo htmlspecialchars($article['title']); ?></span>
                </div>
                
                <div class="article-meta">
                    <span class="category-badge"><?php echo htmlspecialchars($article['category']); ?></span>
                    <span class="updated">Last updated: <?php echo date('F j, Y', strtotime($article['updated_at'])); ?></span>
                </div>
                
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="article-info">
                    <span class="reading-time">⏱️ <?php echo ceil(str_word_count($article['content']) / 200); ?> min read</span>
                    <span class="views">👁️ <?php echo number_format($article['views']); ?> views</span>
                </div>
            </div>
        </header>

        <!-- Table of Contents (if article is long) -->
        <?php if (str_word_count($article['content']) > 500): ?>
            <aside class="article-toc">
                <div class="container">
                    <div class="toc-content">
                        <h3>Table of Contents</h3>
                        <nav class="toc-nav">
                            <!-- This would be dynamically generated from article headings -->
                            <a href="#overview">Overview</a>
                            <a href="#key-points">Key Points</a>
                            <a href="#details">Detailed Information</a>
                            <a href="#next-steps">Next Steps</a>
                        </nav>
                    </div>
                </div>
            </aside>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="article-content">
            <div class="container">
                <div class="article-body">
                    <?php echo $article['content']; ?>
                </div>

                <!-- Article Footer -->
                <footer class="article-footer">
                    <div class="helpful-section">
                        <p><strong>Was this article helpful?</strong></p>
                        <div class="helpful-buttons">
                            <button class="btn-helpful" data-article-id="<?php echo $article_id; ?>" data-helpful="yes">
                                👍 Yes
                            </button>
                            <button class="btn-helpful" data-article-id="<?php echo $article_id; ?>" data-helpful="no">
                                👎 No
                            </button>
                        </div>
                    </div>
                    
                    <div class="article-share">
                        <strong>Share this resource:</strong>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($article['title']); ?>&url=<?php echo urlencode(SITE_URL . '/resources/article.php?id=' . $article_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn twitter">Twitter</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/resources/article.php?id=' . $article_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn facebook">Facebook</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/resources/article.php?id=' . $article_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn linkedin">LinkedIn</a>
                        <button class="share-btn copy" onclick="copyToClipboard('<?php echo SITE_URL . '/resources/article.php?id=' . $article_id; ?>')">
                            Copy Link
                        </button>
                    </div>
                </footer>

                <!-- Medical Disclaimer -->
                <div class="article-disclaimer">
                    <h4>📋 Medical Disclaimer</h4>
                    <p>This educational resource is provided for informational purposes only and does not constitute medical advice. Genetic carrier screening is not diagnostic. Always consult with qualified healthcare professionals, including board-certified genetic counselors and physicians, for personalized medical guidance, diagnostic services, and treatment recommendations. The information provided here should not be used as a substitute for professional medical care.</p>
                </div>

                <!-- References Section -->
                <?php if (!empty($article['references'])): ?>
                    <div class="article-references">
                        <h4>📚 References & Further Reading</h4>
                        <div class="references-list">
                            <?php echo $article['references']; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </article>

    <!-- Related Resources -->
    <?php if (!empty($related_articles)): ?>
        <section class="content-section gray-bg">
            <div class="container">
                <h2 class="text-center">Related Resources</h2>
                <div class="resources-grid">
                    <?php foreach ($related_articles as $related): ?>
                        <article class="resource-card">
                            <div class="resource-content">
                                <span class="category"><?php echo htmlspecialchars($related['category']); ?></span>
                                <h3>
                                    <a href="article.php?id=<?php echo $related['id']; ?>">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h3>
                                <p><?php echo htmlspecialchars(substr($related['excerpt'], 0, 120)) . '...'; ?></p>
                                <a href="article.php?id=<?php echo $related['id']; ?>" class="read-more-small">
                                    Read More →
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center" style="margin-top: 2rem;">
                    <a href="category.php?cat=<?php echo urlencode($article['category']); ?>" class="btn-secondary">
                        View All <?php echo htmlspecialchars($article['category']); ?> Resources
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="section conversion-area" style="background: var(--color-light-gray); padding: 5rem 0;">
        <div class="container">
            <div class="sale-card" style="background: var(--color-white); border-radius: 24px; box-shadow: 0 20px 50px rgba(10, 31, 68, 0.12); padding: 4.5rem 2rem; text-align: center; border: 1px solid var(--color-medium-gray); max-width: 900px; margin: 0 auto;">
                
                <div style="display: inline-block; background: rgba(0, 179, 164, 0.1); color: var(--color-medical-teal); padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1.5px;">
                    Secure Your Genetic Insights
                </div>

                <h2 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                    Ready to Learn Your Carrier Status?
                </h2>
                
                <p style="font-size: 1.2rem; color: var(--color-dark-gray); margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Take proactive steps toward informed family planning with comprehensive genetic screening. Simple testing, profound clarity.
                </p>

                <div style="margin-bottom: 2.5rem;">
                    <span style="font-size: 4.5rem; font-weight: 800; color: var(--color-primary-deep-blue);">$99</span>
                    <span style="font-size: 1.8rem; color: var(--color-dark-gray); text-decoration: line-through; opacity: 0.5; margin-left: 12px;">$249</span>
                </div>

                <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2rem;">
                    <a href="request-kit.php" class="btn btn-large" style="background: var(--color-medical-teal); color: var(--color-white); padding: 1.25rem 3rem; border-radius: 100px; font-size: 1.1rem; text-decoration: none; font-weight: 700; display: inline-block; transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(0, 179, 164, 0.2);">
                        Request Screening Kit - $99
                    </a>
                    <a href="how-it-works.php" class="btn btn-outline btn-large" style="border: 2px solid var(--color-primary-deep-blue); color: var(--color-primary-deep-blue); padding: 1.25rem 3rem; border-radius: 100px; font-size: 1.1rem; text-decoration: none; font-weight: 700; display: inline-block;">
                        See How It Works
                    </a>
                </div>

                <div style="display: flex; justify-content: center; gap: 2rem; opacity: 0.9; flex-wrap: wrap; border-top: 1px solid var(--color-medium-gray); padding-top: 2rem;">
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> At-Home Swab
                    </span>
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Physician Reviewed
                    </span>
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> 100% Confidential
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link copied to clipboard!');
            });
        }
    </script>
</body>
</html>
