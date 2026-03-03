<?php
define('LuckyGenesMDx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();
setSecurityHeaders();

$error = '';
$success = '';
$recoveredEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel = new User();
    $method = $_POST['method'] ?? 'order';
    
    if ($method === 'order') {
        $orderNumber = trim($_POST['order_number'] ?? '');
        if ($orderNumber) {
            $result = $userModel->recoverEmail($orderNumber);
            if ($result['success']) {
                $recoveredEmail = $result['email'];
                $success = "Your email address is: <strong>" . htmlspecialchars($recoveredEmail) . "</strong>";
            } else {
                $error = $result['message'];
            }
        } else {
            $error = "Please enter your order number.";
        }
    } elseif ($method === 'phone') {
        $phone = trim($_POST['phone'] ?? '');
        $dob = $_POST['dob'] ?? '';
        if ($phone && $dob) {
            $result = $userModel->recoverEmail(null, $phone, $dob);
            if ($result['success']) {
                $recoveredEmail = $result['email'];
                $success = "Your email address is: <strong>" . htmlspecialchars($recoveredEmail) . "</strong>";
            } else {
                $error = $result['message'];
            }
        } else {
            $error = "Please enter your phone number and date of birth.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Username - LuckyGenesMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="login.php" class="text-dark-gray" style="font-size: 0.9rem;">← Back to Login</a>
        </div>
        <div class="mb-4">
            <img src="../assets/images/logo_small.png" alt="Logo" style="height: 48px; margin-bottom: 1rem;">
            <h1 class="font-xl mb-2" id="title">Recover Email</h1>
            <p class="auth-title" id="desc">Find your account email address</p>
        </div>

        <?php if ($error): ?>
            <div class="glass-card-error p-3 mb-3 text-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="glass-card-teal-left p-3 mb-3 text-teal">✓ <?php echo $success; ?></div>
            <div class="mt-4 text-center">
                <a href="login.php" class="btn btn-primary btn-full">Sign In Now</a>
            </div>
        <?php else: ?>

        <div class="auth-toggle-group">
            <button type="button" class="btn btn-outline btn-full active" id="btn-order" onclick="switchMethod('order')">By Order #</button>
            <button type="button" class="btn btn-outline btn-full" id="btn-phone" onclick="switchMethod('phone')">By Phone</button>
        </div>

        <form method="POST" id="recoverForm">
            <input type="hidden" name="method" id="method" value="order">
            
            <div id="order-group">
                <div class="form-group">
                    <label>Order Number</label>
                    <input type="text" name="order_number" placeholder="LGM-2024-XXXXX" class="form-control">
                </div>
            </div>

            <div id="phone-group" style="display:none;">
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="(555) 000-0000" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Find Email</button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        function switchMethod(method) {
            document.getElementById('method').value = method;
            
            document.getElementById('btn-order').classList.remove('active');
            document.getElementById('btn-phone').classList.remove('active');
            document.getElementById('btn-' + method).classList.add('active');
            
            if (method === 'order') {
                document.getElementById('order-group').style.display = 'block';
                document.getElementById('phone-group').style.display = 'none';
            } else {
                document.getElementById('order-group').style.display = 'none';
                document.getElementById('phone-group').style.display = 'block';
            }
        }
    </script>
</body>
</html>