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
<style>
    :root {
        --glass-dark:   rgba(10, 31, 68, 0.82);
        --glass-border: rgba(255,255,255,0.12);
        --teal:         #00B3A4;
        --teal-bright:  #00E0C6;
        --teal-glow:    rgba(0,224,198,0.25);
        --text-white:   #ffffff;
        --text-dim:     #94a3b8;
        --input-bg:     rgba(15,23,42,0.6);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { font-family:'Inter',sans-serif; background:#0f172a; }
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:1.5rem; overflow-x:hidden; }

    /* Background */
    .video-bg { position:fixed; inset:0; z-index:0; }
    .video-bg video { width:100%; height:100%; object-fit:cover; filter:brightness(.3) blur(8px); }
    .video-bg::after { content:''; position:absolute; inset:0; background:radial-gradient(circle at 50% 50%, rgba(10,31,68,.3), rgba(10,31,68,.9)); }
    .particles { position:fixed; inset:0; z-index:1; pointer-events:none; }
    .particle { position:absolute; background:rgba(0,224,198,.4); border-radius:50%; animation:float linear infinite; }
    @keyframes float { 0%{transform:translateY(110vh) scale(0);opacity:0} 10%{opacity:.8} 90%{opacity:.8} 100%{transform:translateY(-10vh) scale(1);opacity:0} }

    /* Card */
    .card-wrap { position:relative; z-index:10; width:100%; max-width:490px; }
    .glass-card { background:var(--glass-dark); backdrop-filter:blur(24px); -webkit-backdrop-filter:blur(24px); border:1px solid var(--glass-border); border-radius:28px; padding:2.5rem; box-shadow:0 25px 60px rgba(0,0,0,.5); }

    /* Logo */
    .logo-section { text-align:center; margin-bottom:1.5rem; }
    .logo-icon { font-size:2.6rem; margin-bottom:.5rem; display:inline-block; animation:pulse 3s ease-in-out infinite; }
    @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.05)} }
    h1 { font-family:'Poppins',sans-serif; color:var(--teal-bright); font-size:1.5rem; font-weight:700; }
    .logo-section p { color:var(--text-dim); font-size:.88rem; margin-top:.2rem; }

    /* Progress dots */
    .stepper { display:flex; justify-content:center; gap:8px; margin-bottom:2rem; }
    .dot { height:4px; border-radius:10px; transition:.4s; }
    .dot.active { background:var(--teal-bright); box-shadow:0 0 8px var(--teal-bright); }
    .dot.done { background:rgba(0,224,198,.45); }
    .dot.pending { background:rgba(255,255,255,.1); }

    /* Tabs */
    .tab-nav { display:flex; background:rgba(0,0,0,.22); padding:4px; border-radius:13px; margin-bottom:1.5rem; gap:4px; }
    .tab-btn { flex:1; padding:9px 8px; text-align:center; color:var(--text-dim); font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; cursor:pointer; border-radius:9px; transition:.3s; }
    .tab-btn.active { color:var(--text-white); background:rgba(0,224,198,.18); box-shadow:0 2px 10px rgba(0,224,198,.2); }

    /* Inputs */
    .form-group { margin-bottom:1.2rem; }
    label { display:block; color:var(--text-dim); font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; }
    .input-wrap { position:relative; }
    input[type=email], input[type=password], input[type=text], input[type=tel], input[type=date] {
        width:100%; padding:13px 40px 13px 15px; background:var(--input-bg);
        border:1.5px solid rgba(255,255,255,.07); border-radius:13px;
        color:var(--text-white); font-size:.93rem; font-family:'Inter',sans-serif; transition:.3s;
    }
    input:focus { outline:none; border-color:var(--teal-bright); background:rgba(15,23,42,.85); box-shadow:0 0 0 3px rgba(0,224,198,.1); }
    input.field-error { border-color:#ef4444 !important; box-shadow:0 0 0 3px rgba(239,68,68,.1) !important; }
    .pw-toggle { position:absolute; right:13px; top:50%; transform:translateY(-50%); cursor:pointer; color:var(--text-dim); user-select:none; }

    /* Password strength */
    .pw-strength { display:flex; gap:4px; margin-top:8px; }
    .pw-seg { height:3px; flex:1; border-radius:3px; background:rgba(255,255,255,.1); transition:.4s; }
    .pw-seg.weak { background:#ef4444; }
    .pw-seg.fair { background:#f59e0b; }
    .pw-seg.good { background:var(--teal-bright); }
    .pw-label { font-size:.7rem; margin-top:4px; text-align:right; transition:.3s; }

    /* Two-col */
    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

    /* Terms */
    .terms-row { display:flex; gap:10px; align-items:flex-start; margin-top:1rem; cursor:pointer; }
    .terms-row input[type=checkbox] { width:auto; margin-top:3px; accent-color:var(--teal-bright); flex-shrink:0; }
    .terms-row span { font-size:.77rem; color:var(--text-dim); line-height:1.5; }
    .terms-row a { color:var(--teal-bright); text-decoration:none; }

    /* Buttons */
    .btn-primary { width:100%; padding:14px; background:linear-gradient(135deg,var(--teal) 0%,var(--teal-bright) 100%); border:none; border-radius:14px; color:#fff; font-weight:700; font-size:.93rem; cursor:pointer; transition:.3s; box-shadow:0 6px 24px var(--teal-glow); margin-top:.75rem; }
    .btn-primary:hover { transform:translateY(-2px); box-shadow:0 8px 32px rgba(0,224,198,.38); }
    .btn-outline { width:100%; padding:12px; background:none; border:1px solid var(--glass-border); border-radius:13px; color:var(--text-dim); cursor:pointer; margin-top:8px; transition:.3s; font-size:.9rem; }
    .btn-outline:hover { border-color:rgba(0,224,198,.4); color:var(--text-white); }

    /* Social */
    .divider { display:flex; align-items:center; margin:1.2rem 0; color:var(--text-dim); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; }
    .divider::before,.divider::after { content:''; flex:1; height:1px; background:rgba(255,255,255,.09); }
    .divider span { padding:0 10px; }
    .social-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
    .social-btn { background:var(--input-bg); border:1px solid var(--glass-border); border-radius:12px; padding:11px; cursor:pointer; display:flex; justify-content:center; transition:.3s; }
    .social-btn:hover { background:rgba(255,255,255,.09); border-color:var(--teal-bright); }
    .social-btn svg { width:20px; height:20px; fill:white; }

    /* Error / info */
    .error-msg { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.3); border-radius:12px; padding:11px 14px; margin-bottom:1rem; color:#fca5a5; font-size:.84rem; text-align:center; }

    /* Steps */
    .reg-step { display:none; }
    .reg-step.active { display:block; animation:fadeUp .38s ease forwards; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

    /* Pending screen */
    .pending-screen { text-align:center; padding:1rem 0; }
    .pending-icon { font-size:4rem; margin-bottom:1rem; display:inline-block; animation:pulse 2s ease-in-out infinite; }
    .pending-screen h2 { font-family:'Poppins',sans-serif; color:var(--text-white); font-size:1.4rem; margin-bottom:.75rem; }
    .pending-screen p { color:var(--text-dim); font-size:.9rem; line-height:1.7; margin-bottom:1rem; }
    .email-highlight { color:var(--teal-bright); font-weight:600; }
    .tips { background:rgba(0,0,0,.2); border:1px solid var(--glass-border); border-radius:14px; padding:1rem 1.25rem; margin:1.25rem 0; text-align:left; }
    .tips li { color:var(--text-dim); font-size:.82rem; line-height:1.8; margin-left:1.1rem; }
    .tips li span { color:var(--text-white); }
</style>
</head>
<body>

<div class="video-bg">
    <video autoplay muted loop playsinline><source src="../assets/video/My580.mp4" type="video/mp4"></video>
</div>
<div class="particles" id="particles"></div>

<div class="card-wrap">
    <a href="../index.php" style="color:var(--text-dim);text-decoration:none;font-size:.84rem;display:flex;align-items:center;gap:5px;margin-bottom:1rem;transition:.3s" onmouseover="this.style.color='var(--teal-bright)'" onmouseout="this.style.color='var(--text-dim)'">← Back to Main Site</a>

    <div class="glass-card">

        <?php if ($showPending): ?>
        <!-- ── CHECK YOUR EMAIL SCREEN ── -->
        <div class="pending-screen">
            <div class="pending-icon">📬</div>
            <h2>Check Your Inbox</h2>
            <p>We've sent a verification link to<br>
                <span class="email-highlight"><?php echo $pendingEmail; ?></span>
            </p>
            <ul class="tips">
                <li><span>Click the link</span> in the email to activate your account</li>
                <li><span>Link expires in 24 hours</span> — request a new one if needed</li>
                <li><span>Check spam/junk</span> if you don't see it within a minute</li>
            </ul>
            <p style="font-size:.8rem">Sent to the wrong address?
                <a href="register.php" style="color:var(--teal-bright);text-decoration:none;font-weight:600">Start over</a>
            </p>
            <a href="login.php" class="btn-primary" style="display:block;text-decoration:none;text-align:center;margin-top:1.25rem">Go to Login</a>
        </div>

        <?php else: ?>
        <!-- ── REGISTRATION FORM ── -->

        <div class="logo-section">
            <div class="logo-icon">🧬</div>
            <h1 id="reg-title">Create Account</h1>
            <p id="reg-desc">Join the LuckyGeneMDx Patient Portal</p>
        </div>

        <!-- Progress dots -->
        <div class="stepper">
            <div class="dot active" id="dot-1" style="width:40px"></div>
            <div class="dot pending" id="dot-2" style="width:24px"></div>
            <div class="dot pending" id="dot-3" style="width:40px"></div>
        </div>

        <?php if ($error): ?>
        <div class="error-msg">⚠ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="regForm">
            <input type="hidden" name="registration_type" id="registration_type" value="new">

            <!-- ── STEP 1: Email ── -->
            <div class="reg-step active" id="step-1">
                <div class="tab-nav">
                    <div class="tab-btn active" onclick="setRegType('new', this)">New Patient</div>
                    <div class="tab-btn" onclick="setRegType('with_order', this)">Have Order #</div>
                </div>

                <div id="order-field" class="form-group" style="display:none">
                    <label>Order Number</label>
                    <div class="input-wrap">
                        <input type="text" name="order_number" placeholder="LGM-2024-XXXXX">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <input type="email" name="email" id="reg-email"
                               placeholder="name@example.com" autocomplete="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <button type="button" class="btn-primary" onclick="regNext(2)">Continue →</button>

                <div class="divider"><span>or quick register</span></div>
                <div class="social-grid">
                    <button type="button" class="social-btn" title="Google">
                        <svg viewBox="0 0 24 24"><path d="M12.48 10.92v3.28h7.84c-.24 1.84-1.92 5.36-7.84 5.36-5.12 0-9.28-4.24-9.28-9.48s4.16-9.48 9.28-9.48c2.92 0 4.88 1.2 6 2.28l2.56-2.48C18.92 1.92 15.96 0 12.48 0 5.52 0 0 5.52 0 12.48s5.52 12.48 12.48 12.48c7.28 0 12.12-5.12 12.12-12.32 0-.84-.08-1.48-.2-2.12h-11.92z"/></svg>
                    </button>
                    <button type="button" class="social-btn" title="Apple">
                        <svg viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05 1.78-3.14 1.72-1.09-.06-1.49-.72-2.74-.72-1.25 0-1.7.7-2.71.75-1.02.05-2.2-.95-3.18-1.89-2-1.92-3.53-5.41-3.53-8.7 0-5.32 3.44-8.13 6.71-8.13 1.73 0 3.37.59 4.41 1.25 1.04.66 1.83.66 2.87 0 1.04-.66 2.68-1.25 4.41-1.25 1.54 0 3.01.55 4.14 1.56-4.6 2.13-3.87 8.71.74 10.46-.86 2.12-1.99 4.01-2.98 4.95zM12.04 4.54c-.11-2.43 1.89-4.54 4.31-4.54.12 2.54-2.11 4.75-4.31 4.54z"/></svg>
                    </button>
                    <button type="button" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24"><path d="M22.675 0h-21.35C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24h11.495v-8.791h-2.96v-3.429h2.96v-2.528c0-2.937 1.793-4.535 4.412-4.535 1.254 0 2.333.093 2.646.136v3.069h-1.815c-1.424 0-1.7.677-1.7 1.67v2.188h3.398l-.441 3.429h-2.957V24h6.09C23.407 24 24 23.407 24 22.676V1.325C24 .593 23.407 0 22.675 0z"/></svg>
                    </button>
                </div>
            </div>

            <!-- ── STEP 2: Password ── -->
            <div class="reg-step" id="step-2">
                <div class="form-group">
                    <label>Create Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="reg-pw"
                               placeholder="••••••••" autocomplete="new-password"
                               oninput="checkStrength(this.value)">
                        <span class="pw-toggle" onclick="togglePw('reg-pw')">👁</span>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-seg" id="seg1"></div>
                        <div class="pw-seg" id="seg2"></div>
                        <div class="pw-seg" id="seg3"></div>
                        <div class="pw-seg" id="seg4"></div>
                    </div>
                    <div class="pw-label" id="pw-label" style="color:var(--text-dim)"></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <input type="password" name="confirm_password" id="reg-pw2"
                               placeholder="••••••••" autocomplete="new-password">
                        <span class="pw-toggle" onclick="togglePw('reg-pw2')">👁</span>
                    </div>
                </div>
                <button type="button" class="btn-primary" onclick="regNext(3)">Continue →</button>
                <button type="button" class="btn-outline" onclick="regNext(1)">← Back</button>
            </div>

            <!-- ── STEP 3: Personal details ── -->
            <div class="reg-step" id="step-3">
                <div class="form-group">
                    <label>Full Legal Name</label>
                    <div class="input-wrap">
                        <input type="text" name="full_name" id="reg-name"
                               placeholder="John Doe" autocomplete="name"
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="two-col">
                    <div class="form-group">
                        <label>Phone <span style="font-weight:400;text-transform:none">(optional)</span></label>
                        <div class="input-wrap">
                            <input type="tel" name="phone" placeholder="(555) 000-0000"
                                   style="padding-right:15px"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <div class="input-wrap">
                            <input type="date" name="dob" id="reg-dob" style="padding-right:15px"
                                   value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <label class="terms-row">
                    <input type="checkbox" id="terms-chk" required>
                    <span>I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a></span>
                </label>

                <button type="submit" class="btn-primary" style="margin-top:1.2rem">Create Account & Verify Email →</button>
                <button type="button" class="btn-outline" onclick="regNext(2)">← Back</button>
            </div>
        </form>

        <div style="text-align:center;margin-top:1.75rem;display:flex;flex-direction:column;gap:10px">
            <a href="login.php" style="color:var(--teal-bright);font-size:.85rem;text-decoration:none;font-weight:600">Already have an account? Sign In</a>
            <a href="../index.php" style="color:var(--text-dim);font-size:.75rem;text-decoration:none">Exit to LuckyGeneMDx Home</a>
        </div>

        <?php endif; ?>
    </div><!-- /glass-card -->
</div><!-- /card-wrap -->

<script>
(function(){
    const c = document.getElementById('particles');
    for(let i = 0; i < 10; i++){
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `left:${Math.random()*100}%;width:${2+Math.random()*3}px;height:${2+Math.random()*3}px;animation-duration:${15+Math.random()*20}s;animation-delay:${-Math.random()*20}s`;
        c.appendChild(p);
    }
})();

const TITLES = ['', 'Create Account', 'Secure Your Account', 'Almost Done'];
const DESCS  = ['', 'Join the Patient Portal', 'Choose a strong password', 'Tell us a bit about yourself'];

function regNext(step) {
    if (!validateStep(step)) return;

    document.querySelectorAll('.reg-step').forEach(s => s.classList.remove('active'));
    document.getElementById('step-' + step).classList.add('active');
    document.getElementById('reg-title').textContent = TITLES[step];
    document.getElementById('reg-desc').textContent  = DESCS[step];

    // Update dots
    ['dot-1','dot-2','dot-3'].forEach((id, i) => {
        const d = document.getElementById(id);
        d.className = 'dot ' + (i + 1 < step ? 'done' : i + 1 === step ? 'active' : 'pending');
    });
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

function mark(id, msg) {
    const el = document.getElementById(id);
    el.classList.add('field-error');
    el.focus();
    // Simple inline hint using title
    el.title = msg;
}
function clear(id) {
    const el = document.getElementById(id);
    el.classList.remove('field-error');
    el.title = '';
}

function setRegType(type, el) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('registration_type').value = type;
    document.getElementById('order-field').style.display = type === 'with_order' ? 'block' : 'none';
}

function checkStrength(pw) {
    let score = 0;
    if (pw.length >= 8) score++;
    if (/[A-Z]/.test(pw))   score++;
    if (/\d/.test(pw))       score++;
    if (/[^A-Za-z\d]/.test(pw)) score++;

    const cls   = ['','weak','weak','fair','good'];
    const label = ['','Weak','Weak','Fair','Strong'];
    const color = ['','#ef4444','#ef4444','#f59e0b','var(--teal-bright)'];

    ['seg1','seg2','seg3','seg4'].forEach((id, i) => {
        document.getElementById(id).className = 'pw-seg ' + (i < score ? cls[score] : '');
    });
    const lbl = document.getElementById('pw-label');
    lbl.textContent  = pw.length ? label[score] : '';
    lbl.style.color  = color[score];
}

function togglePw(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
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
