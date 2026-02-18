<?php
define('luckygenemdx', true);
require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/User.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$loginType = 'email';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginType = $_POST['login_type'] ?? 'email';
    $userModel = new User();
    
    if ($loginType === 'order') {
        $orderNumber = trim($_POST['order_number'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($orderNumber && $password) {
            $result = $userModel->loginWithOrderId($orderNumber, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else { $error = $result['message']; }
        } else { $error = 'Please enter both order number and password.'; }
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($email && $password) {
            $result = $userModel->login($email, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else { $error = $result['message']; }
        } else { $error = 'Please enter both email and password.'; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
            --neon-teal:rgb(11, 180, 189);
            --neon-blue: #4facfe;
            --text-main: #ffffff;
            --text-dim: rgba(255, 255, 255, 0.6);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: #000; overflow: hidden; }

        /* Full Background Video Engine */
        .viewport-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-engine {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .video-engine video {
            min-width: 100%; min-height: 100%;
            object-fit: cover;
            filter: brightness(0.6) saturate(1.2);
        }

        .video-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at center, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.7) 100%);
            z-index: -1;
        }

        /* Next-Gen Glass Card */
        .glass-card {
            width: 100%;
            max-width: 440px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px) saturate(160%);
            -webkit-backdrop-filter: blur(30px) saturate(160%);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 3rem;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5), inset 0 0 20px rgba(255,255,255,0.05);
            animation: cardEntrance 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .logo-header { text-align: center; margin-bottom: 2.5rem; }
        .logo-header h1 { 
            color: #fff; font-size: 2rem; font-weight: 700; letter-spacing: -1px; margin-bottom: 0.5rem;
            background: linear-gradient(to right, #fff, var(--neon-teal));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .logo-header p { color: var(--text-dim); font-size: 0.9rem; }

        /* Glossy Tab Navigation */
        .tab-nav {
            display: flex;
            background: rgba(0,0,0,0.2);
            padding: 5px;
            border-radius: 16px;
            margin-bottom: 2.5rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .tab-btn {
            flex: 1; padding: 12px; text-align: center; color: var(--text-dim);
            font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: 0.3s;
            border-radius: 12px;
        }

        .tab-btn.active {
            background: rgba(255,255,255,0.1);
            color: var(--neon-teal);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Sci-Fi Input Styling */
        .input-group { margin-bottom: 1.5rem; position: relative; }
        .input-group label { display: block; color: var(--text-dim); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; margin-left: 4px; }
        
        .input-group input {
            width: 100%; padding: 14px 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            color: #fff; font-size: 1rem; transition: 0.3s;
        }

        .input-group input:focus {
            outline: none; border-color: var(--neon-teal);
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 20px rgba(0, 242, 254, 0.15);
        }

        /* The Hero Button */
        .submit-btn {
            width: 100%; padding: 16px; border: none; border-radius: 14px;
            background: linear-gradient(145deg, #03193c 5%, #00B3A4 100%);
            color:rgb(255, 255, 255); font-weight: 800; font-size: 1rem; text-transform: uppercase;
            letter-spacing: 1px; cursor: pointer; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: 1rem; box-shadow: 0 10px 30px rgba(0, 242, 254, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 242, 254, 0.5);
        }

        .footer-links { text-align: center; margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; }
        .footer-links a { color: var(--text-dim); text-decoration: none; font-size: 0.85rem; transition: 0.3s; }
        .footer-links a:hover { color: #fff; }
        .register-btn { 
            display: inline-block; margin-top: 1rem; padding: 10px 24px; 
            border: 1px solid var(--neon-teal); border-radius: 12px; color: var(--neon-teal); 
            font-weight: 600; text-decoration: none; transition: 0.3s;
        }
        .register-btn:hover { background: var(--neon-teal); color: #00334e; }

        .error-msg { background: rgba(255, 50, 50, 0.1); border: 1px solid rgba(255, 50, 50, 0.3); color: #ff8080; padding: 12px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.5rem; text-align: center; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="viewport-wrapper">
        <div class="video-engine">
            <video autoplay muted loop playsinline preload="auto">
                <source src="../assets/video/My580.mp4" type="video/mp4">
            </video>
        </div>
        <div class="video-overlay"></div>

        <div class="glass-card">
            <div class="logo-header">
                <h1>🧬 Patient Portal</h1>
                <p>Welcome back to LuckyGeneMDx</p>
            </div>

            <div class="tab-nav">
                <div class="tab-btn <?php echo $loginType === 'email' ? 'active' : ''; ?>" onclick="switchTab('email', this)">Email Login</div>
                <div class="tab-btn <?php echo $loginType === 'order' ? 'active' : ''; ?>" onclick="switchTab('order', this)">Order Login</div>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div id="email-tab" class="tab-content <?php echo $loginType === 'email' ? 'active' : ''; ?>">
                <form method="POST">
                    <input type="hidden" name="login_type" value="email">
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="name@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="submit-btn">Authorize Access</button>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="password-reset.php" style="color: var(--text-dim); font-size: 0.8rem;">Forgot password?</a>
                    </div>
                </form>
            </div>

            <div id="order-tab" class="tab-content <?php echo $loginType === 'order' ? 'active' : ''; ?>">
                <form method="POST">
                    <input type="hidden" name="login_type" value="order">
                    <div class="input-group">
                        <label>Order Number</label>
                        <input type="text" name="order_number" placeholder="LGM-XXXXX" required>
                    </div>
                    <div class="input-group">
                        <label>Checkout Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="submit-btn">Trace Results</button>
                </form>
            </div>

            <div class="footer-links">
                <p style="color: var(--text-dim); font-size: 0.85rem;">New to our laboratory?</p>
                <a href="register.php" class="register-btn">Create Secure Account</a>
                <div style="margin-top: 1.5rem;">
                    <a href="../index.php">← Exit to Website</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(type, el) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            document.getElementById(type + '-tab').classList.add('active');
        }
    </script>
</body>
</html>