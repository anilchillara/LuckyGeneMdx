<?php
define('LuckyGenesMDx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';

session_start();
setSecurityHeaders();

$page_title = 'Unsubscribe';
$success = false;
$error = '';
$email = $_GET['email'] ?? '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Delete from interest list
            $stmt = $db->prepare("DELETE FROM interest_list WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $success = true;
            } else {
                $error = "This email address was not found in our list.";
            }
        } catch (Exception $e) {
            error_log("Unsubscribe Error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    } else {
        $error = "Invalid email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content" style="padding: 80px 0; min-height: 60vh; background: var(--color-light-gray);">
        <div class="container">
            <div class="glass-card" style="max-width: 600px; margin: 0 auto; padding: 3rem; text-align: center;">
                
                <?php if ($success): ?>
                    <div style="font-size: 4rem; margin-bottom: 1.5rem;">✅</div>
                    <h1 style="margin-bottom: 1rem;">Unsubscribed</h1>
                    <p style="color: var(--color-dark-gray); margin-bottom: 2rem;">
                        <strong><?php echo htmlspecialchars($email); ?></strong> has been successfully removed from the LuckyGenesMDx interest list.
                    </p>
                    <a href="index.php" class="btn btn-primary">Return to Homepage</a>
                    
                <?php else: ?>
                    <h1 style="margin-bottom: 1.5rem;">Unsubscribe</h1>
                    
                    <?php if ($error): ?>
                        <div class="glass-card glass-card-error p-3 mb-4" style="text-align: left;">
                            <strong class="text-error">Error:</strong> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($email): ?>
                        <p style="color: var(--color-dark-gray); margin-bottom: 2rem;">
                            Are you sure you want to remove <strong><?php echo htmlspecialchars($email); ?></strong> from our interest list? You will no longer receive updates about our launch.
                        </p>
                        
                        <form method="POST">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            <div style="display: flex; gap: 1rem; justify-content: center;">
                                <button type="submit" class="btn btn-primary">Confirm Unsubscribe</button>
                                <a href="index.php" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <p style="color: var(--color-dark-gray); margin-bottom: 2rem;">
                            Please enter your email address to unsubscribe from our list.
                        </p>
                        
                        <form method="POST">
                            <div class="form-group" style="text-align: left;">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" required placeholder="name@example.com">
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">Unsubscribe</button>
                        </form>
                    <?php endif; ?>
                    
                <?php endif; ?>
                
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>