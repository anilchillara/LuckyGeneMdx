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
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMDx Blog</title>
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
    <section class="section conversion-area" style="background: var(--color-light-gray); padding: 5rem 0;">
        <div class="container">
            <div class="sale-card" style="background: var(--color-white); border-radius: 24px; box-shadow: 0 20px 50px rgba(10, 31, 68, 0.12); padding: 4.5rem 2rem; text-align: center; border: 1px solid var(--color-medium-gray); max-width: 900px; margin: 0 auto;">
                
                <div style="display: inline-block; background: rgba(0, 179, 164, 0.1); color: var(--color-medical-teal); padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 1.5px;">
                    Secure Your Legacy
                </div>

                <h2 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem; font-size: 2.5rem; font-weight: 700; line-height: 1.2;">
                    Take Control of Your Genetic Future
                </h2>
                
                <p style="font-size: 1.2rem; color: var(--color-dark-gray); margin-bottom: 2.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    Learn about your carrier status with comprehensive genetic screening. Empower your family planning with clinical-grade data.
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
                        Learn How It Works
                    </a>
                </div>

                <div style="display: flex; justify-content: center; gap: 2rem; opacity: 0.9; flex-wrap: wrap; border-top: 1px solid var(--color-medium-gray); padding-top: 2rem;">
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> 100+ Conditions
                    </span>
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> HIPAA Protected
                    </span>
                    <span style="font-size: 0.95rem; color: var(--color-primary-deep-blue); display: flex; align-items: center;">
                        <span style="color: var(--color-medical-teal); margin-right: 8px;">✓</span> Results in 21 Days
                    </span>
                </div>
            </div>
        </div>
    </section>


    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
