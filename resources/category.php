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
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Get resources in category
try {
    $db = Database::getInstance()->getConnection();
    
    // Get total count
    $count_stmt = $db->prepare("SELECT COUNT(*) FROM educational_resources WHERE category = ? AND status = 'published'");
    $count_stmt->execute([$category]);
    $total_resources = $count_stmt->fetchColumn();
    $total_pages = ceil($total_resources / $per_page);
    
    // Get resources
    $stmt = $db->prepare("SELECT * FROM educational_resources 
                          WHERE category = ? AND status = 'published' 
                          ORDER BY created_at DESC 
                          LIMIT ? OFFSET ?");
    $stmt->execute([$category, $per_page, $offset]);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $page_title = $category . ' Resources';
    $page_description = 'Educational resources about ' . $category;
    
} catch (PDOException $e) {
    $error = "Unable to load category resources";
    $resources = [];
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
                <li><a href="index.php" class="active">Resources</a></li>
                <li><a href="../blog/">Blog</a></li>
                <li><a href="../request-kit.php" class="btn-primary">Request Kit</a></li>
            </ul>
        </div>
    </nav>

    <!-- Category Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <div class="breadcrumb">
                    <a href="index.php">Resources</a> / <span><?php echo htmlspecialchars($category); ?></span>
                </div>
                <h1 class="fade-in"><?php echo htmlspecialchars($category); ?></h1>
                <p class="lead fade-in-delay"><?php echo $total_resources; ?> educational resource<?php echo $total_resources !== 1 ? 's' : ''; ?></p>
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
                <?php elseif (empty($resources)): ?>
                    <div class="no-results">
                        <div class="icon">📚</div>
                        <h3>No resources in this category yet</h3>
                        <p>Check back soon for new educational content about <?php echo htmlspecialchars($category); ?>.</p>
                        <a href="index.php" class="btn-secondary">Browse All Resources</a>
                    </div>
                <?php else: ?>
                    <div class="resources-grid">
                        <?php foreach ($resources as $resource): ?>
                            <article class="resource-card">
                                <div class="resource-content">
                                    <span class="category"><?php echo htmlspecialchars($resource['category']); ?></span>
                                    <h3>
                                        <a href="article.php?id=<?php echo $resource['id']; ?>">
                                            <?php echo htmlspecialchars($resource['title']); ?>
                                        </a>
                                    </h3>
                                    <p><?php echo htmlspecialchars($resource['excerpt']); ?></p>
                                    <div class="resource-meta">
                                        <span class="reading-time">⏱️ <?php echo ceil(str_word_count($resource['content']) / 200); ?> min</span>
                                        <span class="views">👁️ <?php echo number_format($resource['views']); ?></span>
                                    </div>
                                    <a href="article.php?id=<?php echo $resource['id']; ?>" class="read-more-small">
                                        Read More →
                                    </a>
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

        <!-- Related Categories -->
        <section class="content-section gray-bg">
            <div class="container">
                <h2 class="text-center">Explore Other Topics</h2>
                <div class="category-links">
                    <a href="category.php?cat=Understanding%20Carrier%20Status" class="category-link-btn">
                        Understanding Carrier Status
                    </a>
                    <a href="category.php?cat=Genetic%20Conditions" class="category-link-btn">
                        Genetic Conditions
                    </a>
                    <a href="category.php?cat=Family%20Planning" class="category-link-btn">
                        Family Planning
                    </a>
                    <a href="category.php?cat=Testing%20%26%20Results" class="category-link-btn">
                        Testing & Results
                    </a>
                    <a href="category.php?cat=Genetic%20Counseling" class="category-link-btn">
                        Genetic Counseling
                    </a>
                    <a href="category.php?cat=Pregnancy%20%26%20Genetics" class="category-link-btn">
                        Pregnancy & Genetics
                    </a>
                </div>
                
                <div class="text-center" style="margin-top: 2rem;">
                    <a href="index.php" class="btn-secondary">Back to All Resources</a>
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
                        <li><a href="index.php">Knowledge Hub</a></li>
                        <li><a href="../blog/">Blog</a></li>
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
