<?php
require_once 'includes/config.php';
$page_title = 'Privacy Policy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | LuckyGeneMdx</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="legal-page">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
            
            <section>
                <h2>1. Information We Collect</h2>
                <p>LuckyGeneMdx collects and processes personal and genetic information in compliance with HIPAA regulations.</p>
            </section>
            
            <section>
                <h2>2. How We Use Your Information</h2>
                <p>Your genetic data is used solely for carrier screening analysis and reporting.</p>
            </section>
            
            <section>
                <h2>3. Data Security</h2>
                <p>We employ industry-standard encryption and security measures to protect your information.</p>
            </section>
            
            <section>
                <h2>4. Your Rights</h2>
                <p>You have the right to access, correct, or delete your personal data at any time.</p>
            </section>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
