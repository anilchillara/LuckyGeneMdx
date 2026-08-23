<?php
/**
 * claim-gift.php
 * Public landing page for gift kit redemption links.
 * URL: claim-gift.php?token=<gift_token>
 *
 * Flow:
 *  1. Show gift details (sender first name, personal message) — NO PII beyond that
 *  2. If not logged in: prompt login/register, preserving the token in session
 *  3. If logged in: auto-call api/redeem-gift.php and redirect to portal
 */
define('LuckyGenes', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/Order.php';
session_start();
setSecurityHeaders();

$token      = trim($_GET['token'] ?? '');
$error      = '';
$giftKit    = null;
$autoRedeem = false;

if (empty($token)) {
    $error = 'Invalid gift link. No token was provided.';
} else {
    $orderModel = new Order();
    $giftKit    = $orderModel->getGiftKitByToken($token);

    if (!$giftKit) {
        $error = 'This gift link is invalid or has expired. Please ask the sender to re-send it.';
    } elseif ($giftKit['gift_redeemed_at']) {
        $error = 'This gift has already been claimed. If you think this is a mistake, please contact support.';
    } elseif ($giftKit['gift_token_expires_at'] && strtotime($giftKit['gift_token_expires_at']) < time()) {
        $error = 'This gift link has expired (90-day limit). Please ask the sender to re-send it.';
    } else {
        // Token is valid. If user is logged in, auto-redeem now.
        if (isset($_SESSION['user_id'])) {
            $autoRedeem = true;
        } else {
            // Stash token in session so we can redeem after login/register
            $_SESSION['pending_gift_token'] = $token;
        }
    }
}

// Sender's first name only (privacy-safe)
$senderFirstName = '';
if ($giftKit) {
    $parts = explode(' ', $giftKit['purchaser_name'] ?? '');
    $senderFirstName = $parts[0] ?? 'Someone';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Your Gift Kit - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .gift-hero {
            text-align: center;
            padding: 3rem 2rem 2rem;
        }
        .gift-emoji {
            font-size: 5rem;
            display: block;
            margin-bottom: 1rem;
            animation: bounce 1.2s ease infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
        .gift-message-box {
            background: linear-gradient(135deg, rgba(0,179,164,0.08), rgba(0,77,105,0.05));
            border: 1px solid rgba(0,179,164,0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: var(--color-navy);
        }
        .auth-tabs {
            display: flex;
            border-bottom: 2px solid var(--color-border);
            margin-bottom: 2rem;
        }
        .auth-tab {
            flex: 1;
            padding: 0.9rem;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: var(--color-text-gray);
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .auth-tab.active {
            color: var(--color-medical-teal);
            border-bottom-color: var(--color-medical-teal);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">
        <div class="container" style="max-width: 600px; padding: 2rem 1rem;">

            <?php if ($error): ?>
                <div class="glass-card" style="text-align:center; padding: 3rem 2rem;">
                    <span style="font-size:3rem;">❌</span>
                    <h2 style="margin: 1rem 0;">Gift Link Issue</h2>
                    <p style="color:var(--color-text-gray);"><?php echo htmlspecialchars($error); ?></p>
                    <a href="contact.php" class="btn btn-outline" style="margin-top:1.5rem;">Contact Support</a>
                </div>

            <?php elseif ($autoRedeem): ?>
                <!-- Logged-in: auto-redeem via JS -->
                <div class="glass-card" style="text-align:center; padding: 3rem 2rem;" id="redeeming-card">
                    <span class="gift-emoji">🎁</span>
                    <h2>Claiming your gift kit…</h2>
                    <p style="color:var(--color-text-gray);">Please wait while we activate your kit.</p>
                </div>
                <script>
                (async function () {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrf     = csrfMeta ? csrfMeta.getAttribute('content') : '';

                    try {
                        const res = await fetch('api/redeem-gift.php', {
                            method:  'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body:    JSON.stringify({
                                csrf_token: csrf,
                                gift_token: <?php echo json_encode($token); ?>
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.location.href = 'user-portal/orders.php?gift_claimed=1&barcode=' + encodeURIComponent(data.kit_barcode);
                        } else {
                            document.getElementById('redeeming-card').innerHTML =
                                '<span style="font-size:3rem;">❌</span>' +
                                '<h2 style="margin:1rem 0;">Couldn\'t Claim Gift</h2>' +
                                '<p style="color:#666;">' + (data.message || 'Unknown error') + '</p>' +
                                '<a href="contact.php" class="btn btn-outline" style="margin-top:1.5rem;">Contact Support</a>';
                        }
                    } catch (e) {
                        document.getElementById('redeeming-card').innerHTML =
                            '<span style="font-size:3rem;">⚠️</span>' +
                            '<h2 style="margin:1rem 0;">Network Error</h2>' +
                            '<p style="color:#666;">Could not connect. Please refresh and try again.</p>';
                    }
                })();
                </script>

            <?php else: ?>
                <!-- Not logged in: show gift details + auth prompt -->
                <div class="glass-card">
                    <div class="gift-hero">
                        <span class="gift-emoji">🎁</span>
                        <h1 style="margin-bottom:0.5rem;">
                            <?php echo htmlspecialchars($senderFirstName); ?> sent you a gift!
                        </h1>
                        <p style="color:var(--color-text-gray); font-size:1.05rem;">
                            You've received a <strong>Comprehensive Carrier Screening Kit</strong> from LuckyGenes — a privacy-first genetic health test covering 300+ conditions.
                        </p>

                        <?php if (!empty($giftKit['gift_message'])): ?>
                        <div class="gift-message-box">
                            "<?php echo htmlspecialchars($giftKit['gift_message']); ?>"
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr style="margin: 1.5rem 0; border-color: var(--color-border);">

                    <h3 style="text-align:center; margin-bottom:1.5rem;">
                        Sign in or create an account to claim your kit
                    </h3>

                    <div class="auth-tabs">
                        <div class="auth-tab active" id="tab-login" onclick="switchTab('login')">Log In</div>
                        <div class="auth-tab" id="tab-register" onclick="switchTab('register')">Create Account</div>
                    </div>

                    <!-- Login Panel -->
                    <div id="panel-login">
                        <form action="user-portal/login.php" method="POST">
                            <input type="hidden" name="redirect_after_login" value="claim-gift.php?token=<?php echo urlencode($token); ?>">
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full" style="margin-top:1rem;">
                                Log In &amp; Claim Kit
                            </button>
                        </form>
                        <p style="text-align:center; margin-top:1rem; font-size:0.9rem;">
                            <a href="user-portal/forgot-username.php" style="color:var(--color-medical-teal);">Forgot your email or password?</a>
                        </p>
                    </div>

                    <!-- Register Panel -->
                    <div id="panel-register" style="display:none;">
                        <p style="color:var(--color-text-gray); margin-bottom:1.5rem; font-size:0.9rem;">
                            Create a free patient account to receive your kit results and track your screening.
                        </p>
                        <a href="user-portal/register.php?gift_token=<?php echo urlencode($token); ?>" class="btn btn-primary btn-full">
                            Create Account &amp; Claim Kit
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
    <script>
    function switchTab(tab) {
        document.getElementById('tab-login').classList.toggle('active', tab === 'login');
        document.getElementById('tab-register').classList.toggle('active', tab === 'register');
        document.getElementById('panel-login').style.display    = tab === 'login'    ? 'block' : 'none';
        document.getElementById('panel-register').style.display = tab === 'register' ? 'block' : 'none';
    }
    </script>
</body>
</html>
