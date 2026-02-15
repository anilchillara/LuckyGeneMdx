<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// Get post ID
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get post details
try {
    $db = Database::getInstance()->getConnection();
    
    // Get the post
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ? AND status = 'published'");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header('Location: index.php');
        exit;
    }
    
    // Increment view count
    $update_stmt = $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
    $update_stmt->execute([$post_id]);
    
    // Get related posts (same category, excluding current)
    $related_stmt = $db->prepare("SELECT * FROM blog_posts 
                                   WHERE category = ? AND id != ? AND status = 'published' 
                                   ORDER BY published_date DESC 
                                   LIMIT 3");
    $related_stmt->execute([$post['category'], $post_id]);
    $related_posts = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = $post['title'];
    $page_description = $post['excerpt'];
    
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
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="<?php echo $post['published_date']; ?>">
    <meta property="article:author" content="<?php echo htmlspecialchars($post['author']); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMdx Blog</title>
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Article Header -->
    <article class="blog-post">
        <header class="post-header">
            <div class="container">
                <div class="post-meta">
                    <a href="index.php?category=<?php echo urlencode($post['category']); ?>" class="category-badge">
                        <?php echo htmlspecialchars($post['category']); ?>
                    </a>
                    <span class="post-date"><?php echo date('F j, Y', strtotime($post['published_date'])); ?></span>
                </div>
                
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="post-author-info">
                    <div class="author-details">
                        <span class="author-name">By <?php echo htmlspecialchars($post['author']); ?></span>
                        <span class="reading-time">• <?php echo ceil(str_word_count($post['content']) / 200); ?> min read</span>
                    </div>
                </div>
            </div>
        </header>

        <?php if (!empty($post['featured_image'])): ?>
            <div class="post-featured-image">
                <img src="../uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="post-content">
            <div class="container">
                <div class="post-body">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Post Footer -->
                <footer class="post-footer">
                    <div class="post-tags">
                        <?php if (!empty($post['tags'])): ?>
                            <strong>Tags:</strong>
                            <?php 
                            $tags = explode(',', $post['tags']);
                            foreach ($tags as $tag): 
                                $tag = trim($tag);
                            ?>
                                <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-share">
                        <strong>Share:</strong>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($post['title']); ?>&url=<?php echo urlencode(SITE_URL . '/blog/post.php?id=' . $post_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn twitter">Twitter</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog/post.php?id=' . $post_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn facebook">Facebook</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(SITE_URL . '/blog/post.php?id=' . $post_id); ?>" 
                           target="_blank" rel="noopener" class="share-btn linkedin">LinkedIn</a>
                    </div>
                </footer>

                <!-- Disclaimer -->
                <div class="post-disclaimer">
                    <h4>Important Disclaimer</h4>
                    <p>This article is for educational purposes only and does not constitute medical advice. Genetic carrier screening is not diagnostic. Always consult with qualified healthcare professionals, including genetic counselors and physicians, for personalized medical guidance and recommendations.</p>
                </div>
            </div>
        </div>
    </article>

    <!-- Related Posts -->
    <?php if (!empty($related_posts)): ?>
        <section class="content-section gray-bg">
            <div class="container">
                <h2 class="text-center">Related Articles</h2>
                <div class="blog-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                    <?php foreach ($related_posts as $related): ?>
                        <article class="blog-card">
                            <?php if (!empty($related['featured_image'])): ?>
                                <div class="blog-card-image">
                                    <img src="../uploads/blog/<?php echo htmlspecialchars($related['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($related['title']); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <span class="category"><?php echo htmlspecialchars($related['category']); ?></span>
                                    <span class="date"><?php echo date('M j, Y', strtotime($related['published_date'])); ?></span>
                                </div>
                                
                                <h3 class="blog-card-title">
                                    <a href="post.php?id=<?php echo $related['id']; ?>">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h3>
                                
                                <p class="blog-card-excerpt">
                                    <?php echo htmlspecialchars($related['excerpt']); ?>
                                </p>
                                
                                <a href="post.php?id=<?php echo $related['id']; ?>" class="read-more">
                                    Read More →
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Take Control of Your Genetic Future</h2>
                <p>Learn about your carrier status with comprehensive genetic screening.</p>
                <div class="cta-buttons">
                    <a href="../request-kit.php" class="btn-primary btn-lg">Request Screening Kit - $99</a>
                    <a href="../how-it-works.php" class="btn-secondary btn-lg">Learn How It Works</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>LuckyGeneMdx</h4>
                    <p>Empowering families with genetic awareness for a healthier future.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="../about-genetic-screening.php">About Screening</a></li>
                        <li><a href="../how-it-works.php">How It Works</a></li>
                        <li><a href="../request-kit.php">Request Kit</a></li>
                        <li><a href="../track-order.php">Track Order</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="../resources/">Knowledge Hub</a></li>
                        <li><a href="index.php">Blog</a></li>
                        <li><a href="../privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="../terms-of-service.php">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <p>Email: support@luckygenemmdx.com</p>
                    <p>Phone: 1-800-LUCKYGENE</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> LuckyGeneMdx. All rights reserved.</p>
                <p class="disclaimer">Carrier screening is not diagnostic. Consult with healthcare professionals for medical advice.</p>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html>
