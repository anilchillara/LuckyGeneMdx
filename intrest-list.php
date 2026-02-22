<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $interest = trim($_POST['interest'] ?? '');

    if (empty($name))  $errors[] = 'Full name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (empty($role))  $errors[] = 'Please select your role.';

    if (empty($errors)) {
        // TODO: save to DB / send notification email
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interest List — Carrier Screening</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/main.css">
<style>
  /* Overrides for this specific landing page */

  html { scroll-behavior: smooth; }

  body {
    background: var(--color-light-gray);
    color: var(--color-primary-deep-blue);
    min-height: 100vh;
  }

  /* ── BACKGROUND TEXTURE ── */
  body::before {
    content: '';
    position: fixed; inset: 0;
    background-image:
      radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0, 179, 164, 0.08) 0%, transparent 60%),
      radial-gradient(ellipse 60% 80% at 85% 80%, rgba(10, 31, 68, 0.05) 0%, transparent 55%);
    pointer-events: none;
    z-index: 0;
  }

  .page-wrap { position: relative; z-index: 1; }

  /* ── HEADER ── */
  header {
    text-align: center;
    padding: 72px 24px 0;
    animation: fadeUp .9s ease both;
  }

  .badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--color-medical-teal);
    color: var(--white);
    font-size: 11px; letter-spacing: .14em; text-transform: uppercase;
    padding: 6px 18px; border-radius: 100px;
    font-family: 'Inter', sans-serif; font-weight: 600;
    margin-bottom: 28px;
  }
  .badge::before {
    content: '';
    width: 6px; height: 6px; border-radius: 50%;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
    animation: pulse 2s ease infinite;
  }

  h1 {
    font-family: 'Poppins', sans-serif;
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 300;
    line-height: 1.12;
    letter-spacing: -.01em;
    color: var(--color-primary-deep-blue);
    max-width: 720px;
    margin: 0 auto 20px;
  }
  h1 em { font-style: italic; color: var(--color-medical-teal); }

  .subtitle {
    font-size: 1.05rem;
    color: var(--color-dark-gray);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.7;
    font-weight: 400;
  }

  /* ── PARTNER STRIP ── */
  .partner-strip {
    max-width: 820px;
    margin: 52px auto 0;
    background: var(--color-white);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 28px 36px;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 20px;
    align-items: start;
    animation: fadeUp .9s .15s ease both;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  }

  .partner-icon {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, var(--color-medical-teal), #008c7a);
    border-radius: 12px;
    display: grid; place-items: center;
    font-size: 22px;
    flex-shrink: 0;
    color: white;
  }

  .partner-text strong {
    display: block;
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem; font-weight: 600;
    color: var(--color-primary-deep-blue);
    margin-bottom: 4px;
  }
  .partner-text p {
    font-size: .9rem; color: var(--color-dark-gray); line-height: 1.65;
  }

  /* ── STATS ROW ── */
  .stats {
    max-width: 820px;
    margin: 32px auto 0;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    animation: fadeUp .9s .25s ease both;
  }

  .stat-card {
    background: var(--color-white);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 22px 20px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
  }
  .stat-card .num {
    font-family: 'Poppins', sans-serif;
    font-size: 2.4rem; font-weight: 600;
    color: var(--color-medical-teal);
    line-height: 1;
  }
  .stat-card .label {
    font-size: .78rem; letter-spacing: .08em; text-transform: uppercase;
    color: var(--color-dark-gray); margin-top: 6px;
  }

  /* ── DIVIDER ── */
  .divider {
    max-width: 820px;
    margin: 56px auto 0;
    display: flex; align-items: center; gap: 20px;
  }
  .divider::before, .divider::after {
    content: ''; flex: 1; height: 1px;
    background: linear-gradient(to right, transparent, rgba(10,31,68,.15));
  }
  .divider::after { background: linear-gradient(to left, transparent, rgba(10,31,68,.15)); }
  .divider span {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem; font-style: italic;
    color: var(--color-dark-gray); white-space: nowrap;
  }

  /* ── FORM CARD ── */
  .form-card {
    max-width: 820px;
    margin: 40px auto 80px;
    background: var(--color-white);
    border: 1px solid var(--border-color);
    border-radius: 28px;
    padding: clamp(32px, 6vw, 56px);
    box-shadow: 0 8px 64px rgba(10,31,68,.06), 0 2px 8px rgba(10,31,68,.04);
    animation: fadeUp .9s .35s ease both;
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }
  .form-grid .full { grid-column: 1 / -1; }

  label {
    display: block;
    font-size: .8rem; letter-spacing: .1em; text-transform: uppercase;
    color: var(--color-primary-deep-blue); font-weight: 600;
    margin-bottom: 8px;
  }

  .errors {
    background: #fdf3f3; border: 1px solid #f5c6c6;
    border-radius: 12px; padding: 16px 20px;
    margin-bottom: 24px;
    font-size: .875rem; color: #b94a4a; line-height: 1.6;
  }

  /* Custom overrides for form elements to match landing page style */
  .form-input, .form-select { background: var(--light-bg); border-radius: 12px; padding: 14px 18px; }
  textarea.form-input { min-height: 100px; resize: vertical; }

  .privacy-note {
    text-align: center; margin-top: 16px;
    font-size: .78rem; color: var(--color-dark-gray);
  }
  .privacy-note svg { vertical-align: middle; margin-right: 4px; }

  /* ── SUCCESS ── */
  .success-state {
    text-align: center; padding: 20px 0;
  }
  .success-icon {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, var(--color-medical-teal), #008c7a);
    border-radius: 50%;
    margin: 0 auto 28px;
    display: grid; place-items: center;
    font-size: 36px;
    color: white;
    box-shadow: 0 8px 32px rgba(0, 179, 164, 0.3);
    animation: popIn .5s cubic-bezier(.175,.885,.32,1.275) both;
  }
  .success-state h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 2.2rem; font-weight: 400;
    margin-bottom: 12px;
  }
  .success-state p { color: var(--color-dark-gray); line-height: 1.7; max-width: 400px; margin: 0 auto; }

  /* ── ANIMATIONS ── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(255,255,255,0.3); }
    50%       { box-shadow: 0 0 0 6px rgba(255,255,255,0.1); }
  }
  @keyframes popIn {
    from { opacity: 0; transform: scale(.5); }
    to   { opacity: 1; transform: scale(1); }
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 600px) {
    .form-grid { grid-template-columns: 1fr; }
    .stats { grid-template-columns: 1fr; }
    .partner-strip { grid-template-columns: 1fr; }
  }

  /* Dark Mode Support */
  body.dark-theme {
    background: #0f1216;
    color: #e9ecef;
  }
  
  body.dark-theme::before {
    background-image:
      radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0, 179, 164, 0.1) 0%, transparent 60%),
      radial-gradient(ellipse 60% 80% at 85% 80%, rgba(108, 99, 255, 0.08) 0%, transparent 55%);
  }

  body.dark-theme .partner-strip,
  body.dark-theme .stat-card,
  body.dark-theme .form-card {
    border-color: #343a40;
    box-shadow: none;
  }

  body.dark-theme .divider::before, 
  body.dark-theme .divider::after {
    background: linear-gradient(to right, transparent, rgba(255,255,255,.15));
  }
  body.dark-theme .divider::after {
    background: linear-gradient(to left, transparent, rgba(255,255,255,.15));
  }

  body.dark-theme .form-input, 
  body.dark-theme .form-select {
    background: #1a1d21;
    border-color: #495057;
    color: #e9ecef;
  }
  body.dark-theme .stat-card .label, body.dark-theme .partner-text p, body.dark-theme .subtitle, body.dark-theme .privacy-note { color: #adb5bd; }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="page-wrap">

  <header>
    <div class="badge">Now Accepting Interest</div>
    <h1>Be First in Line for <em>Affordable</em><br>Carrier Screening</h1>
    <p class="subtitle">
      We're building something that matters — making comprehensive genetic carrier screening
      accessible and affordable for every family. Join our early interest list today.
    </p>
  </header>

  <!-- Partner Strip -->
  <div class="partner-strip">
    <div class="partner-icon">🤝</div>
    <div class="partner-text">
      <strong>Working With Leading Industry Partners</strong>
      <p>
        We are actively collaborating with multiple partners across the genetics and diagnostics field
        to negotiate the best possible pricing for carrier screening — so you get world-class testing
        without the world-class price tag. Our partnerships span accredited labs, clinical networks,
        and genetic counseling services to deliver end-to-end care at scale.
      </p>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stat-card">
      <div class="num">300+</div>
      <div class="label">Conditions Screened</div>
    </div>
    <div class="stat-card">
      <div class="num">10+</div>
      <div class="label">Lab Partners</div>
    </div>
    <div class="stat-card">
      <div class="num">Up to 60%</div>
      <div class="label">Projected Savings</div>
    </div>
  </div>

  <div class="divider"><span>Join the Interest List</span></div>

  <!-- Form Card -->
  <div class="form-card">

    <?php if ($success): ?>
    <div class="success-state">
      <div class="success-icon">✓</div>
      <h2>You're on the list!</h2>
      <p>Thank you for your interest. We'll be in touch as soon as we're ready to launch, with exclusive early-access pricing just for you.</p>
    </div>

    <?php else: ?>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="#form" id="form" novalidate>
      <div class="form-grid">

        <div>
          <label for="name">Full Name *</label>
          <input type="text" id="name" name="name" class="form-input" placeholder="Jane Smith"
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div>
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="jane@example.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>

        <div>
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 000-0000"
            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>

        <div>
          <label for="role">I am a *</label>
          <select id="role" name="role" class="form-select" required>
            <option value="" disabled <?= empty($_POST['role']) ? 'selected' : '' ?>>Select your role</option>
            <option value="patient" <?= ($_POST['role'] ?? '') === 'patient' ? 'selected' : '' ?>>Patient / Individual</option>
            <option value="couple" <?= ($_POST['role'] ?? '') === 'couple' ? 'selected' : '' ?>>Couple / Family</option>
            <option value="provider" <?= ($_POST['role'] ?? '') === 'provider' ? 'selected' : '' ?>>Healthcare Provider</option>
            <option value="ob-gyn" <?= ($_POST['role'] ?? '') === 'ob-gyn' ? 'selected' : '' ?>>OB-GYN / Midwife</option>
            <option value="genetic-counselor" <?= ($_POST['role'] ?? '') === 'genetic-counselor' ? 'selected' : '' ?>>Genetic Counselor</option>
            <option value="clinic" <?= ($_POST['role'] ?? '') === 'clinic' ? 'selected' : '' ?>>Clinic / Health System</option>
            <option value="other" <?= ($_POST['role'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
          </select>
        </div>

        <div class="full">
          <label for="interest">What matters most to you? <span style="opacity:.5;font-style:italic;text-transform:none;letter-spacing:0">optional</span></label>
          <textarea id="interest" name="interest" class="form-input" placeholder="e.g. Affordable pricing, specific conditions covered, counseling support…"><?= htmlspecialchars($_POST['interest'] ?? '') ?></textarea>
        </div>

      </div>

      <button type="submit" class="btn btn-primary btn-large" style="width: 100%; margin-top: 12px; border-radius: 14px;">Reserve My Spot — It's Free</button>

      <p class="privacy-note">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        No spam, ever. Your information is private and secure.
      </p>
    </form>

    <?php endif; ?>
  </div>

</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>