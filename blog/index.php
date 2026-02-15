<?php
require_once '../includes/config.php';
require_once '../includes/Database.php';

$page_title = 'Blog - Genetic Health Insights';
$page_description = 'Expert insights on genetic carrier screening, family planning, and inherited conditions.';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Category filter
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Get blog posts
try {
    $db = Database::getInstance()->getConnection();
    
    // Build query
    $where_clauses = ["status = 'published'"];
    $params = [];
    
    if (!empty($search)) {
        $where_clauses[] = "(title LIKE ? OR content LIKE ? OR excerpt LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    if (!empty($category)) {
        $where_clauses[] = "category = ?";
        $params[] = $category;
    }
    
    $where_sql = implode(' AND ', $where_clauses);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM blog_posts WHERE $where_sql";
    $count_stmt = $db->prepare($count_sql);
    $count_stmt->execute($params);
    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
    // Get posts
    $sql = "SELECT * FROM blog_posts 
            WHERE $where_sql 
            ORDER BY published_date DESC 
            LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for filter
    $cat_sql = "SELECT DISTINCT category FROM blog_posts WHERE status = 'published' ORDER BY category";
    $categories = $db->query($cat_sql)->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    $error = "Unable to load blog posts";
    $posts = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <title><?php echo htmlspecialchars($page_title); ?> | LuckyGeneMdx</title>
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

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="fade-in">Genetic Health Insights</h1>
                <p class="lead fade-in-delay">Expert perspectives on carrier screening, genetics, and family planning</p>
            </div>
        </div>
        <div class="dna-background"></div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Search & Filter Section -->
        <section class="content-section" style="padding-top: 2rem;">
            <div class="container">
                <div class="blog-controls">
                    <form method="GET" action="" class="blog-search-form">
                        <div class="search-bar">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search articles..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   class="search-input">
                            <button type="submit" class="btn-primary">Search</button>
                        </div>
                    </form>
                    
                    <div class="category-filters">
                        <a href="index.php" class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">All Topics</a>
                        <?php foreach ($categories as $cat): ?>
                            <a href="?category=<?php echo urlencode($cat); ?>" 
                               class="filter-btn <?php echo $category === $cat ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Blog Posts Grid -->
        <section class="content-section">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <p><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php elseif (empty($posts)): ?>
                    <div class="no-results">
                        <div class="icon">📝</div>
                        <h3>No articles found</h3>
                        <p>Try adjusting your search or filter criteria.</p>
                        <a href="index.php" class="btn-secondary">View All Articles</a>
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
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                                   class="btn-secondary">← Previous</a>
                            <?php endif; ?>
                            
                            <div class="page-numbers">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="page-number active"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                                           class="page-number"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
                                   class="btn-secondary">Next →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Newsletter Signup -->
        <section class="content-section gray-bg">
            <div class="container">
                <div class="newsletter-section">
                    <h2>Stay Informed</h2>
                    <p>Get the latest insights on genetic health, carrier screening, and family planning delivered to your inbox.</p>
                    <form method="POST" action="../api/newsletter-signup.php" class="newsletter-form">
                        <input type="email" name="email" placeholder="Your email address" required>
                        <button type="submit" class="btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="content-section">
            <div class="container">
                <h2 class="text-center">Explore Topics</h2>
                <div class="topic-categories">
                    <a href="?category=Carrier%20Screening" class="topic-card">
                        <div class="icon">🧬</div>
                        <h3>Carrier Screening</h3>
                        <p>Understanding genetic carrier status and testing</p>
                    </a>
                    <a href="?category=Family%20Planning" class="topic-card">
                        <div class="icon">👨‍👩‍👧‍👦</div>
                        <h3>Family Planning</h3>
                        <p>Making informed reproductive decisions</p>
                    </a>
                    <a href="?category=Genetic%20Conditions" class="topic-card">
                        <div class="icon">📊</div>
                        <h3>Genetic Conditions</h3>
                        <p>Learn about inherited disorders</p>
                    </a>
                    <a href="?category=Pregnancy%20%26%20Genetics" class="topic-card">
                        <div class="icon">🤰</div>
                        <h3>Pregnancy & Genetics</h3>
                        <p>Genetic considerations during pregnancy</p>
                    </a>
                </div>
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
