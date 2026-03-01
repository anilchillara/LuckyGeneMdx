<?php
define('LuckyGenesMDx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/User.php';
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot check: if this hidden field is filled, it's likely a bot
    if (!empty($_POST['website_hp'])) {
        $success = true; // Fail silently (fake success) to discourage retries
    } else {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $role     = trim($_POST['role'] ?? '');
    $interest = trim($_POST['interest'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;

    $userModel = new User();

    if (empty($name))  $errors[] = 'Full name is required.';
    if (empty($email) || !$userModel->validateEmail($email)) $errors[] = 'A valid email address is required.';
    if (empty($role))  $errors[] = 'Please select your role.';

    if (empty($errors)) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM interest_list WHERE email = ?");
            $stmt->execute([$email]);
            
            if (!$stmt->fetch()) {
                // Send welcome email first
                $emailResult = $userModel->sendInterestWelcomeEmail($email, $name);
                
                if ($emailResult['success']) {
                    // Insert new record only if email sent successfully
                    $stmt = $db->prepare("INSERT INTO interest_list (name, email, phone, role, interest, newsletter_opt_in) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $phone, $role, $interest, $newsletter]);
                    $success = true;
                } else {
                    $errors[] = "Unable to send confirmation email. Please check your email address.";
                }
            } else {
                $success = true;
            }
        } catch (Exception $e) {
            error_log("Interest List Error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }
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
</head>
<body class="interest-list-page">
<?php include 'includes/navbar.php'; ?>
<div class="page-wrap">

  <header>
    <div class="badge">Now Accepting Interest</div>
    <h1>Be First in Line for <em>AFFORDABLE</em><br>Carrier Screening</h1>
    <p class="subtitle">
      Empower your family planning with clinical-grade genetic insights. We are making comprehensive 
      carrier screening accessible to everyone. Sign up for exclusive early-bird pricing and launch updates.
    </p>
  </header>

  <!-- Partner Strip -->
  <div class="partner-strip">
    <div class="partner-icon">🤝</div>
    <div class="partner-text">
      <strong>Partnering for Excellence & Affordability</strong>
      <p>
        We collaborate with top-tier CLIA-certified and CAP-accredited laboratories to bring you 
        hospital-grade testing at a fraction of the cost. By negotiating directly with diagnostic 
        leaders and clinical networks, we ensure you receive comprehensive results and genetic 
        counseling support without the traditional markup.
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
      <div class="num">CLIA</div>
      <div class="label">Certified Labs</div>
    </div>
    <div class="stat-card">
      <div class="num">Up to 60%</div>
      <div class="label">Lower Cost</div>
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
      <!-- Honeypot Field (Hidden from humans) -->
      <div style="opacity: 0; position: absolute; top: 0; left: 0; height: 0; width: 0; z-index: -1;">
          <label for="website_hp">Website</label>
          <input type="text" id="website_hp" name="website_hp" tabindex="-1" autocomplete="off">
      </div>

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
          <textarea id="interest" name="interest" class="form-input" placeholder="e.g. Pricing, speed of results, specific genetic conditions, or access to genetic counselors..."><?= htmlspecialchars($_POST['interest'] ?? '') ?></textarea>
        </div>

        <div class="full" style="margin-top: 5px;">
            <div class="form-checkbox">
                <input type="checkbox" id="newsletter" name="newsletter" value="1" <?= (!isset($_POST['newsletter']) && $_SERVER['REQUEST_METHOD'] === 'POST') ? '' : 'checked' ?>>
                <label for="newsletter" style="text-transform:none; letter-spacing:0; font-weight:400; font-size:0.9rem; color: inherit;">Subscribe to our newsletter for updates</label>
            </div>
        </div>

      </div>

      <button type="submit" class="btn btn-primary btn-large" style="width: 100%; margin-top: 12px; border-radius: 14px;">Join our Interest list</button>

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