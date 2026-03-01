<?php
define('LuckyGenesMDx', true);
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
<title>Join LuckyGenesMDx – Secure Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/portal.css">
    <link rel="stylesheet" href="../css/custom.css">
</head>
<body class="auth-body">

<div class="auth-bg-video">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>

<div class="auth-card">
    <a href="../index.php" class="back-link">← Back to Main Site</a>

    <?php if ($showPending): ?>
        <!-- ── CHECK YOUR EMAIL SCREEN ── -->
        <div class="text-center">
            <img src="../assets/images/logo_small.png" alt="Logo" class="auth-logo">
            <div class="fs-4 mb-1">📬</div>
            <h2>Check Your Inbox</h2>
            <p>We've sent a verification link to<br>
                <span class="font-weight-600 text-primary"><?php echo $pendingEmail; ?></span>
            </p>
            <ul class="info-box">
                <li>Click the link in the email to activate your account</li>
                <li>Link expires in 24 hours</li>
                <li>Check spam/junk if you don't see it</li>
            </ul>
            <a href="login.php" class="btn btn-full">Go to Login</a>
        </div>

    <?php else: ?>
        <!-- ── REGISTRATION FORM ── -->

        <div class="text-center mb-1-5">
            <img src="../assets/images/logo_small.png" alt="Logo" class="auth-logo">
            <h1 id="reg-title">Create Account</h1>
            <p id="reg-desc">Join the LuckyGenesMDx Patient Portal</p>
        </div>

        <?php if ($error): ?>
        <div class="msg msg-error">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <input type="hidden" name="registration_type" id="registration_type" value="new">

            <!-- ── STEP 1: Email ── -->
            <div class="reg-step active d-block" id="step-1">
                <div class="auth-toggle-group">
                    <button type="button" class="btn btn-outline btn-full active" onclick="setRegType('new', this)">New Patient</button>
                    <button type="button" class="btn btn-outline btn-full" onclick="setRegType('with_order', this)">Have Order #</button>
                </div>

                <div id="order-field" class="form-group hidden">
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
            <div class="reg-step" id="step-2" class="hidden">
                <div class="form-group">
                    <label>Create Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="reg-pw" placeholder="••••••••" autocomplete="new-password" class="pr-40" oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePw('reg-pw')" class="password-toggle" title="Show Password">👁️</button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-seg" id="seg1"></div>
                        <div class="pw-seg" id="seg2"></div>
                        <div class="pw-seg" id="seg3"></div>
                        <div class="pw-seg" id="seg4"></div>
                    </div>
                    <div class="pw-label" id="pw-label"></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="reg-pw2" placeholder="••••••••" autocomplete="new-password" class="pr-40">
                        <button type="button" onclick="togglePw('reg-pw2')" class="password-toggle" title="Show Password">👁️</button>
                    </div>
                </div>
                <button type="button" class="btn btn-full" onclick="regNext(3)">Continue →</button>
                <button type="button" class="btn btn-outline btn-full mt-10" onclick="regNext(1)">← Back</button>
            </div>

            <!-- ── STEP 3: Personal details ── -->
            <div class="reg-step" id="step-3" class="hidden">
                <div class="form-group">
                    <label>Full Legal Name</label>
                    <input type="text" name="full_name" id="reg-name" placeholder="John Doe" autocomplete="name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone <span class="font-weight-400 text-transform-none">(optional)</span></label>
                        <input type="tel" name="phone" placeholder="(555) 000-0000" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="reg-dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                    </div>
                </div>

                <label class="terms-label">
                    <input type="checkbox" id="terms-chk" required>
                    <span>I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a></span>
                </label>

                <button type="submit" class="btn btn-full">Create Account & Verify Email →</button>
                <button type="button" class="btn btn-outline btn-full mt-10" onclick="regNext(2)">← Back</button>
            </div>
        </form>

        <div class="text-center mt-2">
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

function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkStrength(pw) {
    let score = 0;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw))   score++;
    if (/\d/.test(pw))       score++;
    if (/[^A-Za-z\d]/.test(pw)) score++;

    const cls   = ['','weak','weak','fair','good'];
    const label = ['','Weak','Weak','Fair','Strong'];

    for (let i = 1; i <= 4; i++) {
        const seg = document.getElementById('seg' + i);
        seg.className = 'pw-seg ' + (i <= score && score > 0 ? cls[score] : '');
    }
    document.getElementById('pw-label').textContent = pw.length ? label[score] : '';
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
