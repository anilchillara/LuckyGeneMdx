<?php
define('luckygenemdx', true);
require_once 'includes/config.php';

// Redirect to home if maintenance mode is disabled
if (empty($dbSettings['maintenance_mode'])) {
    header("Location: index.php");
    exit;
}

// Send 503 Service Unavailable header so search engines know to come back later
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--color-light-gray);
            margin: 0;
        }
        .maintenance-card {
            background: var(--color-white);
            padding: 4rem 2rem;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(10, 31, 68, 0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
            border: 1px solid var(--color-medium-gray);
        }
        .icon { font-size: 4rem; margin-bottom: 1.5rem; animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="icon">🛠️</div>
        <h1 style="color: var(--color-primary-deep-blue); margin-bottom: 1rem;">We'll be right back</h1>
        <p style="color: var(--color-dark-gray); line-height: 1.6; margin-bottom: 2rem; font-size: 1.1rem;">
            <?php echo htmlspecialchars(SITE_NAME); ?> is currently undergoing scheduled maintenance to improve your experience. 
            We apologize for the inconvenience and will be back shortly.
        </p>
        
        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--color-medium-gray);">
            <div style="text-align: left;">
                <div style="font-size: 0.85rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 1px;">Contact Support</div>
                <a href="mailto:<?php echo htmlspecialchars(SUPPORT_EMAIL); ?>" style="color: var(--color-medical-teal); font-weight: 600; text-decoration: none;"><?php echo htmlspecialchars(SUPPORT_EMAIL); ?></a>
            </div>
        </div>
    </div>
</body>
</html>