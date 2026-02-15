<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

// Get category from URL
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';

if (empty($category)) {
    header('Location: index.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Get posts in category
try {
    $db = Database::getInstance()->getConnection();
    
    // Get total count
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE category = ? AND status = 'published'");
    $count_stmt->execute([$category]);
    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
    // Get posts
    $stmt = $db->prepare("SELECT * FROM blog_posts 
                          WHERE category = ? AND status = 'published' 
                          ORDER BY published_date DESC 
                          LIMIT ? OFFSET ?");
    $stmt->execute([$category, $per_page, $offset]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = $category . ' Articles';
    $page_description = 'Browse all articles about ' . $category;
    
} catch (PDOException $e) {
    $error = "Unable to load category posts";
    $posts = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMdx Blog</title>
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="main-nav">
        <div class="nav-container">
            <a href="../index.php" class="logo">
                <span class="dna-icon">🧬</span>
                <span>LuckyGeneMdx</span>
            </a>
            <ul class="nav-menu">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../about-genetic-screening.php">About Screening</a></li>
                <li><a href="../how-it-works.php">How It Works</a></li>
                <li><a href="../resources/">Resources</a></li>
                <li><a href="index.php" class="active">Blog</a></li>
                <li><a href="../request-kit.php" class="btn-primary">Request Kit</a></li>
            </ul>
        </div>
    </nav>

    <!-- Category Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <div class="breadcrumb">
                    <a href="index.php">Blog</a> / <span><?php echo htmlspecialchars($category); ?></span>
                </div>
                <h1 class="fade-in"><?php echo htmlspecialchars($category); ?></h1>
                <p class="lead fade-in-delay"><?php echo $total_posts; ?> article<?php echo $total_posts !== 1 ? 's' : ''; ?> in this category</p>
            </div>
        </div>
        <div class="dna-background"></div>
    </section>

    <!-- Main Content -->
    <main>
        <section class="content-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php elseif (empty($posts)): ?>
                    <div class="no-results">
                        <div class="icon">📝</div>
                        <h3>No articles in this category yet</h3>
                        <p>Check back soon for new content about <?php echo htmlspecialchars($category); ?>.</p>
                        <a href="index.php" class="btn-secondary">Browse All Articles</a>
                    </div>
                <?php else: ?>
                    <div class="blog-grid">
                        <?php foreach ($posts as $post): ?>
                            <article class="blog-card">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <div class="blog-card-image">
                                        <img src="../uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-card-content">
                                    <div class="blog-card-meta">
                                        <span class="category"><?php echo htmlspecialchars($post['category']); ?></span>
                                        <span class="date"><?php echo date('M j, Y', strtotime($post['published_date'])); ?></span>
                                    </div>
                                    
                                    <h3 class="blog-card-title">
                                        <a href="post.php?id=<?php echo $post['id']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="blog-card-excerpt">
                                        <?php echo htmlspecialchars($post['excerpt']); ?>
                                    </p>
                                    
                                    <div class="blog-card-footer">
                                        <span class="author">By <?php echo htmlspecialchars($post['author']); ?></span>
                                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">
                                            Read More →
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?cat=<?php echo urlencode($category); ?>&page=<?php echo $page - 1; ?>" 
                                   class="btn-secondary">← Previous</a>
                            <?php endif; ?>
                            
                            <div class="page-numbers">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="page-number active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?cat=<?php echo urlencode($category); ?>&page=<?php echo $i; ?>" 
                                           class="page-number"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?cat=<?php echo urlencode($category); ?>&page=<?php echo $page + 1; ?>" 
                                   class="btn-secondary">Next →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

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
