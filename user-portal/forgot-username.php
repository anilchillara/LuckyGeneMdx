<?php
define('luckygenemdx', true);
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
    <title>Recover Username - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/portal.css">
</head>
<body class="auth-body">

    <div class="auth-bg-video">
        <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
    </div>

    <div class="auth-card">
        <div style="margin-bottom: 20px;">
            <a href="login.php" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8rem;">← Back to Login</a>
        </div>
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="font-size:3rem;">📧</div>
            <h1 id="title">Recover Email</h1>
            <p id="desc">Find your account email address</p>
        </div>

        <?php if ($error): ?>
            <div class="msg msg-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="msg msg-success">✓ <?php echo $success; ?></div>
            <div style="text-align:center; margin-top:1.5rem;">
                <a href="login.php" class="btn btn-full">Sign In Now</a>
            </div>
        <?php else: ?>

        <div style="display:flex; gap:10px; margin-bottom: 1.5rem;">
            <button type="button" class="btn btn-outline btn-full active" id="btn-order" onclick="switchMethod('order')">By Order #</button>
            <button type="button" class="btn btn-outline btn-full" id="btn-phone" onclick="switchMethod('phone')">By Phone</button>
        </div>

        <form method="POST" id="recoverForm">
            <input type="hidden" name="method" id="method" value="order">
            
            <div id="order-group">
                <div class="form-group">
                    <label>Order Number</label>
                    <input type="text" name="order_number" placeholder="LGM-2024-XXXXX">
                </div>
            </div>

            <div id="phone-group" style="display:none;">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" placeholder="(555) 000-0000">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob">
                </div>
            </div>

            <button type="submit" class="btn btn-full">Find Email</button>
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