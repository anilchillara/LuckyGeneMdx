<?php
require_once 'includes/config.php';
$page_title = 'Terms of Service';
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
            <h1>Terms of Service</h1>
            <p class="last-updated">Effective Date: <?php echo date('F j, Y'); ?></p>
            
            <section>
                <h2>1. Acceptance of Terms</h2>
                <p>By using LuckyGeneMdx services, you agree to these terms.</p>
            </section>
            
            <section>
                <h2>2. Service Description</h2>
                <p>LuckyGeneMdx provides genetic carrier screening services.</p>
            </section>
            
            <section>
                <h2>3. Disclaimer</h2>
                <p>Carrier screening is not diagnostic and should not replace medical advice.</p>
            </section>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
