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

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userModel        = new User();
    $registrationType = $_POST['registration_type'] ?? 'new';

    $email    = trim($_POST['email']     ?? '');
    $password = $_POST['password']       ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $phone    = trim($_POST['phone']     ?? '');
    $dob      = trim($_POST['dob']       ?? '');

    $userData = [
        'email'             => $email,
        'password'          => $password,
        'full_name'         => $fullName,
        'phone'             => $phone,
        'dob'               => $dob,
        'registration_type' => $registrationType,
        'order_number'      => trim($_POST['order_number'] ?? ''),
    ];

    $result = $userModel->register($userData);

    if ($result['success']) {
        // Send verification email
        $emailResult = $userModel->sendVerificationEmail($result['user_id'], $email, $fullName);
        // Redirect to a "check your email" page regardless of email send status
        header('Location: register.php?pending=1&email=' . urlencode($email));
        exit;
    } else {
        $error = $result['message'];
    }
}

// Show "check email" confirmation screen
$showPending = isset($_GET['pending']) && $_GET['pending'] === '1';
$pendingEmail = htmlspecialchars($_GET['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Join LuckyGeneMDx – Secure Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/portal.css">
</head>
<body class="auth-body">

<div class="auth-bg-video">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>

<div class="auth-card">
    <a href="../index.php" style="display:block; margin-bottom: 1rem; font-size: 0.9rem;">← Back to Main Site</a>

    <?php if ($showPending): ?>
        <!-- ── CHECK YOUR EMAIL SCREEN ── -->
        <div style="text-align:center;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📬</div>
            <h2>Check Your Inbox</h2>
            <p>We've sent a verification link to<br>
                <span style="font-weight:600; color:var(--ms-blue);"><?php echo $pendingEmail; ?></span>
            </p>
            <ul style="text-align:left; background:#f3f2f1; padding:1rem 1.5rem; border-radius:4px; margin:1.5rem 0; font-size:0.85rem;">
                <li>Click the link in the email to activate your account</li>
                <li>Link expires in 24 hours</li>
                <li>Check spam/junk if you don't see it</li>
            </ul>
            <a href="login.php" class="btn btn-full">Go to Login</a>
        </div>

    <?php else: ?>
        <!-- ── REGISTRATION FORM ── -->

        <div style="text-align:center; margin-bottom: 1.5rem;">
            <div style="font-size: 3rem;">🧬</div>
            <h1 id="reg-title">Create Account</h1>
            <p id="reg-desc">Join the LuckyGeneMDx Patient Portal</p>
        </div>

        <?php if ($error): ?>
        <div class="msg msg-error">⚠ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <input type="hidden" name="registration_type" id="registration_type" value="new">

            <!-- ── STEP 1: Email ── -->
            <div class="reg-step active" id="step-1" style="display:block;">
                <div style="display:flex; gap:10px; margin-bottom: 1.5rem;">
                    <button type="button" class="btn btn-outline btn-full active" onclick="setRegType('new', this)">New Patient</button>
                    <button type="button" class="btn btn-outline btn-full" onclick="setRegType('with_order', this)">Have Order #</button>
                </div>

                <div id="order-field" class="form-group" style="display:none">
                    <label>Order Number</label>
                    <input type="text" name="order_number" placeholder="LGM-2024-XXXXX">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" id="reg-email" placeholder="name@example.com" autocomplete="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <button type="button" class="btn btn-full" onclick="regNext(2)">Continue →</button>
            </div>

            <!-- ── STEP 2: Password ── -->
            <div class="reg-step" id="step-2" style="display:none;">
                <div class="form-group">
                    <label>Create Password</label>
                    <input type="password" name="password" id="reg-pw" placeholder="••••••••" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="reg-pw2" placeholder="••••••••" autocomplete="new-password">
                </div>
                <button type="button" class="btn btn-full" onclick="regNext(3)">Continue →</button>
                <button type="button" class="btn btn-outline btn-full" style="margin-top:10px;" onclick="regNext(1)">← Back</button>
            </div>

            <!-- ── STEP 3: Personal details ── -->
            <div class="reg-step" id="step-3" style="display:none;">
                <div class="form-group">
                    <label>Full Legal Name</label>
                    <input type="text" name="full_name" id="reg-name" placeholder="John Doe" autocomplete="name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div class="form-group">
                        <label>Phone <span style="font-weight:400;text-transform:none">(optional)</span></label>
                        <input type="tel" name="phone" placeholder="(555) 000-0000" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="reg-dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                    </div>
                </div>

                <label style="display:flex; gap:10px; align-items:start; font-size:0.8rem; margin: 1rem 0;">
                    <input type="checkbox" id="terms-chk" required>
                    <span>I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a></span>
                </label>

                <button type="submit" class="btn btn-full">Create Account & Verify Email →</button>
                <button type="button" class="btn btn-outline btn-full" style="margin-top:10px;" onclick="regNext(2)">← Back</button>
            </div>
        </form>

        <div style="text-align:center; margin-top: 2rem;">
            <a href="login.php" class="btn btn-outline">Already have an account? Sign In</a>
        </div>

    <?php endif; ?>
</div>

<script>
const TITLES = ['', 'Create Account', 'Secure Your Account', 'Almost Done'];
const DESCS  = ['', 'Join the Patient Portal', 'Choose a strong password', 'Tell us a bit about yourself'];

function regNext(step) {
    if (!validateStep(step)) return;

    document.querySelectorAll('.reg-step').forEach(s => s.style.display = 'none');
    document.getElementById('step-' + step).style.display = 'block';
    document.getElementById('reg-title').textContent = TITLES[step];
    document.getElementById('reg-desc').textContent  = DESCS[step];
}

function validateStep(goingTo) {
    const from = goingTo - 1;
    if (from === 1) {
        const email = document.getElementById('reg-email').value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            mark('reg-email', 'Please enter a valid email address.');
            return false;
        }
        clear('reg-email');
    }
    if (from === 2) {
        const p1 = document.getElementById('reg-pw').value;
        const p2 = document.getElementById('reg-pw2').value;
        if (p1.length < 8) { mark('reg-pw', 'Password must be at least 8 characters.'); return false; }
        if (p1 !== p2)    { mark('reg-pw2', 'Passwords do not match.'); return false; }
        clear('reg-pw'); clear('reg-pw2');
    }
    return true;
}

function mark(id, msg) { document.getElementById(id).focus(); alert(msg); }
function clear(id) {}

function setRegType(type, el) {
    document.getElementById('registration_type').value = type;
    document.getElementById('order-field').style.display = type === 'with_order' ? 'block' : 'none';
}

<?php if ($error): ?>
// Jump to the correct step after a PHP error
const errStep = <?php
    // Determine which step failed based on which fields are missing
    $postEmail = $_POST['email'] ?? '';
    $postPw    = $_POST['password'] ?? '';
    if (!$postPw) echo 1; elseif (empty($_POST['full_name'])) echo 2; else echo 3;
?>;
regNext(errStep + 1);
<?php endif; ?>
</script>
</body>
</html>
