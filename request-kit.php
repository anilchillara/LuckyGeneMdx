<?php
define('LuckyGenes', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/User.php';
require_once 'includes/Order.php';
session_start();
setSecurityHeaders();

$success     = false;
$error       = '';
$orderNumber = '';
$kitBarcode  = '';
$isGiftOrder = false;
$giftSentTo  = '';
$isLoggedIn  = isset($_SESSION['user_id']);
$user        = null;

// If logged in, get user data
if ($isLoggedIn) {
    $db = Database::getInstance()->getConnection();
    // Explicitly name all columns to guarantee phone and dob are returned
    $stmt = $db->prepare("
        SELECT user_id, full_name, email, phone, dob, created_at 
        FROM users 
        WHERE user_id = :user_id 
        LIMIT 1
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Sync session with latest DB values
    if ($user) {
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['user_dob'] = $user['dob'];
    }
}

// NOTE: Order creation now happens in api/razorpay-verify-payment.php
// after Razorpay signature verification. This page only renders the form
// OR displays the success screen when redirected back with ?success=1.

if (isset($_GET['success']) && $_GET['success'] === '1') {
    $success     = true;
    $orderNumber = trim($_GET['order'] ?? '');
    $kitBarcode  = trim($_GET['barcode'] ?? '');
    $isGiftOrder = !empty($_GET['gift']) && $_GET['gift'] === '1';
    $giftSentTo  = trim($_GET['sent_to'] ?? '');
}


$indianStates = [
    'AN' => 'Andaman & Nicobar Islands',
    'AP' => 'Andhra Pradesh',
    'AR' => 'Arunachal Pradesh',
    'AS' => 'Assam',
    'BR' => 'Bihar',
    'CH' => 'Chandigarh',
    'CG' => 'Chhattisgarh',
    'DN' => 'Dadra & Nagar Haveli and Daman & Diu',
    'DL' => 'Delhi',
    'GA' => 'Goa',
    'GJ' => 'Gujarat',
    'HR' => 'Haryana',
    'HP' => 'Himachal Pradesh',
    'JK' => 'Jammu & Kashmir',
    'JH' => 'Jharkhand',
    'KA' => 'Karnataka',
    'KL' => 'Kerala',
    'LA' => 'Ladakh',
    'LD' => 'Lakshadweep',
    'MP' => 'Madhya Pradesh',
    'MH' => 'Maharashtra',
    'MN' => 'Manipur',
    'ML' => 'Meghalaya',
    'MZ' => 'Mizoram',
    'NL' => 'Nagaland',
    'OD' => 'Odisha',
    'PY' => 'Puducherry',
    'PB' => 'Punjab',
    'RJ' => 'Rajasthan',
    'SK' => 'Sikkim',
    'TN' => 'Tamil Nadu',
    'TS' => 'Telangana',
    'TR' => 'Tripura',
    'UP' => 'Uttar Pradesh',
    'UK' => 'Uttarakhand',
    'WB' => 'West Bengal',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Request Screening Kit - <?php echo htmlspecialchars(SITE_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    
    <main id="main-content">
        <?php if ($success): ?>
            <div class="container">
                <!-- Success Message -->
                <div class="glass-card success-container">
                    <div class="icon-box-lg mb-2"><?php echo $isGiftOrder ? '🎁' : '✅'; ?></div>
                    <h1 class="text-teal mb-2"><?php echo $isGiftOrder ? 'Gift Kit Sent!' : 'Order Confirmed!'; ?></h1>

                    <?php if ($isGiftOrder): ?>
                        <p class="font-lg mb-4">
                            Your gift kit is on its way to <strong><?php echo htmlspecialchars($giftSentTo); ?></strong>! They'll receive an email with a link to claim and activate their kit.
                        </p>
                        <div class="glass-card order-number-box">
                            <div class="font-sm text-dark-gray mb-1">Your Order Number</div>
                            <div class="font-xl font-bold text-deep-blue ls-2"><?php echo htmlspecialchars($orderNumber); ?></div>
                            <div class="font-sm text-dark-gray mt-1">Keep this for your records</div>
                        </div>
                    <?php else: ?>
                        <p class="font-lg mb-4">
                            Thank you for your order. Your screening kit will be shipped within 3-5 business days.
                        </p>
                        <div class="glass-card order-number-box">
                            <div class="font-sm text-dark-gray mb-1">Your Order Number</div>
                            <div class="font-xl font-bold text-deep-blue ls-2"><?php echo htmlspecialchars($orderNumber); ?></div>
                            <div class="font-sm text-dark-gray mt-1">Please save this number for tracking</div>
                        </div>
                        <?php if (!empty($kitBarcode)): ?>
                        <div class="glass-card order-number-box" style="margin-top:1rem;">
                            <div class="font-sm text-dark-gray mb-1">Your Kit Barcode</div>
                            <div class="font-xl font-bold text-medical-teal ls-2" style="font-family:monospace;letter-spacing:3px;"><?php echo htmlspecialchars($kitBarcode); ?></div>
                            <div class="font-sm text-dark-gray mt-1">This code is printed on your kit — use it if the lab contacts you</div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <h3 class="mt-4 mb-2">What's Next?</h3>
                    <div class="next-steps-list">
                        <?php if ($isGiftOrder): ?>
                        <ol class="lh-1-8">
                            <li><strong>Recipient gets an email</strong> with a link to create their account and claim the kit</li>
                            <li><strong>Kit ships</strong> within 3-5 business days</li>
                            <li><strong>They collect their sample</strong> following the included instructions</li>
                            <li><strong>Results in 14-21 days</strong> — delivered to their patient portal</li>
                        </ol>
                        <?php else: ?>
                        <ol class="lh-1-8">
                            <li><strong>Check your email</strong> for order confirmation and instructions</li>
                            <li><strong>Receive your kit</strong> within 3-5 business days</li>
                            <li><strong>Collect your sample</strong> following the included instructions</li>
                            <li><strong>Return to lab</strong> using the prepaid shipping label</li>
                            <li><strong>Get results</strong> in 14-21 days via your patient portal</li>
                        </ol>
                        <?php endif; ?>
                    </div>

                    <div class="mt-5">
                        <?php if (!$isGiftOrder): ?>
                        <a href="track-order.php?order=<?php echo urlencode($orderNumber); ?>" class="btn btn-primary btn-large">Track Your Order</a>
                        <?php endif; ?>
                        <a href="user-portal/" class="btn btn-outline btn-large ml-2">Go to Patient Portal</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Order Form -->
            <section class="page-header">
                <div class="container">
                    <h1>
                        <?php echo $isLoggedIn ? 'Order Another Screening Kit' : 'Request Your Screening Kit'; ?>
                    </h1>
                    <p>
                        <?php if ($isLoggedIn): ?>
                            Welcome back, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>! Complete your shipping information to place your order.
                        <?php else: ?>
                            Complete your order below. Your kit will ship within 3-5 business days, and results will be available in 14-21 days.
                        <?php endif; ?>
                    </p>
                </div>
            </section>

            <div class="container">
                <?php if ($error): ?>
                    <div class="glass-card glass-card-error p-3 mb-4">
                        <strong class="text-error">Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Product Summary -->
                    <div class="col col-2">
                        <div class="glass-card sticky-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <div class="summary-flex">
                                    <strong>Comprehensive Carrier Screening Kit</strong>
                                </div>
                                <div class="font-sm text-dark-gray mb-2">
                                    Tests for 300+ genetic conditions
                                </div>
                            </div>
                            
                            <div class="summary-row">
                                <div class="summary-flex">
                                    <span>Subtotal</span>
                                    <span><?php echo CURRENCY_SYMBOL . number_format(KIT_PRICE, 2); ?></span>
                                </div>
                                <div class="summary-flex">
                                    <span>Shipping</span>
                                    <span class="text-teal">FREE</span>
                                </div>
                            </div>
                            
                            <div class="summary-row">
                                <div class="summary-flex font-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-teal"><?php echo CURRENCY_SYMBOL . number_format(KIT_PRICE, 2); ?></span>
                                </div>
                            </div>
                            
                            <?php if ($isLoggedIn): ?>
                                <!-- Account Info -->
                                <div class="account-info-box">
                                    <div class="font-sm text-dark-gray mb-1">Ordering as:</div>
                                    <div class="font-semibold text-deep-blue"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                    <div class="font-sm text-dark-gray"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="trust-badges trust-badges-vertical">
                                <div class="trust-badge">
                                    <span>🔒</span>
                                    <span>Secure Checkout</span>
                                </div>
                                <div class="trust-badge">
                                    <span>📦</span>
                                    <span>Free Shipping</span>
                                </div>
                                <div class="trust-badge">
                                    <span>🔐</span>
                                    <span>Private Results</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Checkout Form -->
                    <div class="col col-2">
                        <div class="glass-card">
                            <form id="checkout-form" data-validate>
                                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <?php if (!$isLoggedIn): ?>
                                    <!-- Personal Information - ONLY for guest users -->
                                    <h3 class="mb-3">Personal Information</h3>
                                    
                                    <div class="form-group">
                                        <label for="full_name" class="form-label required">Full Name</label>
                                        <input type="text" id="full_name" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="email" class="form-label required">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control" required data-validate="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="phone" class="form-label required">Phone Number</label>
                                            <input type="tel" id="phone" name="phone" class="form-control" required data-validate="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dob" class="form-label required">Date of Birth</label>
                                        <input type="date" id="dob" name="dob" class="form-control" required value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="password" class="form-label required">Create Password</label>
                                        <input type="password" id="password" name="password" class="form-control" required data-validate="password" minlength="8">
                                        <small class="text-dark-gray">Minimum 8 characters for your patient portal account</small>
                                    </div>
                                    
                                    <h3 class="mt-4 mb-3">Shipping Address</h3>
                                <?php else: ?>
                                    <!-- Logged in user - show info summary -->
                                    <?php
                                        // Safe display values with fallback chain
                                        $displayName  = $user['full_name'] ?? $_SESSION['user_name']  ?? 'N/A';
                                        $displayEmail = $user['email']      ?? $_SESSION['user_email'] ?? 'N/A';
                                        $displayPhone = $user['phone']      ?? $_SESSION['user_phone'] ?? '';
                                        $displayPhone = !empty($displayPhone) ? $displayPhone : 'Not on file';
                                        $displayDoB = $user['dob']      ?? $_SESSION['user_dob'] ?? '';
                                        $displayDoB = !empty($displayDoB) ? $displayDoB : 'Not on file';
                                    ?>
                                    <div class="ordering-as-box">
                                        <h3 class="mb-3 text-deep-blue font-lg">
                                            ✅ Ordering as
                                        </h3>
                                        <div class="ordering-grid">
                                            <div>
                                                <div class="ordering-label">Name</div>
                                                <div class="ordering-value"><?php echo htmlspecialchars($displayName); ?></div>
                                            </div>
                                            <div>
                                                <div class="ordering-label">Phone</div>
                                                <div class="ordering-value"><?php echo htmlspecialchars($displayPhone); ?></div>
                                            </div>
                                            <div style="grid-column: 1 / -1;">
                                                <div class="ordering-label">Email</div>
                                                <div class="ordering-value"><?php echo htmlspecialchars($displayEmail); ?></div>
                                            </div>
                                            <div style="grid-column: 1 / -1;">
                                                <div class="ordering-label">Date of Birth</div>
                                                <div class="ordering-value"><?php echo htmlspecialchars($displayDoB); ?></div>
                                            </div>
                                        </div>
                                        <div class="ordering-footer">
                                            <small class="text-dark-gray">
                                                Not you? <a href="user-portal/logout.php" class="text-teal font-semibold">Log out</a> to order with a different account.
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <h3 class="mb-3">Shipping Address</h3>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="address_line1" class="form-label required">Street Address</label>
                                    <input type="text" id="address_line1" name="address_line1" class="form-control" required value="<?php echo htmlspecialchars($_POST['address_line1'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="address_line2" class="form-label">Apartment, Suite, etc. (Optional)</label>
                                    <input type="text" id="address_line2" name="address_line2" class="form-control" value="<?php echo htmlspecialchars($_POST['address_line2'] ?? ''); ?>">
                                </div>
                                
                                <div class="address-grid">
                                    <div class="form-group">
                                        <label for="city" class="form-label required">City</label>
                                        <input type="text" id="city" name="city" class="form-control" required value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="state" class="form-label required">State / UT</label>
                                        <select id="state" name="state" class="form-control" required>
                                            <option value="">Select State / UT</option>
                                            <?php foreach($indianStates as $code => $name): ?>
                                                <option value="<?php echo $code; ?>" <?php echo (($_POST['state'] ?? '') === $code) ? 'selected' : ''; ?>>
                                                    <?php echo $name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="zip" class="form-label required">PIN Code</label>
                                        <input type="text" id="zip" name="zip" class="form-control" required pattern="[0-9]{6}" maxlength="6" placeholder="6-digit PIN" value="<?php echo htmlspecialchars($_POST['zip'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <h3 class="mt-4 mb-3">Consent</h3>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="consent" name="consent" required>
                                    <label for="consent" class="font-sm">
                                        I understand that this is a carrier screening test, not a diagnostic test. This test does not replace genetic counseling or physician consultation. I consent to genetic testing and agree to the <a href="terms-of-service.php" target="_blank">Terms of Service</a> and <a href="privacy-policy.php" target="_blank">Privacy Policy</a>.
                                    </label>
                                </div>

                                <!-- Gift Option -->
                                <div class="form-checkbox" style="margin-top:1.25rem;">
                                    <input type="checkbox" id="is_gift_toggle" name="is_gift_toggle">
                                    <label for="is_gift_toggle" class="font-sm font-semibold" style="display:flex;align-items:center;gap:0.5rem;">
                                        🎁 <span>This kit is a gift for someone else</span>
                                    </label>
                                </div>

                                <div id="gift-fields" style="display:none; margin-top:1rem; padding:1.25rem; border:2px dashed var(--color-medical-teal); border-radius:12px; background:rgba(0,179,164,0.04);">
                                    <h4 style="margin-bottom:1rem; color:var(--color-medical-teal);">🎁 Gift Details</h4>
                                    <div class="form-group">
                                        <label for="gift_recipient_name" class="form-label required">Recipient's Name</label>
                                        <input type="text" id="gift_recipient_name" name="gift_recipient_name" class="form-control" placeholder="e.g. Sarah Johnson">
                                    </div>
                                    <div class="form-group">
                                        <label for="gift_recipient_email" class="form-label required">Recipient's Email</label>
                                        <input type="email" id="gift_recipient_email" name="gift_recipient_email" class="form-control" placeholder="recipient@email.com">
                                        <small class="text-dark-gray">They'll receive a link to claim and activate their kit</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="gift_message" class="form-label">Personal Message <span class="text-dark-gray">(optional)</span></label>
                                        <textarea id="gift_message" name="gift_message" class="form-control" rows="3" maxlength="500" placeholder="Write a short personal note..."></textarea>
                                        <small class="text-dark-gray"><span id="gift_msg_count">0</span>/500 characters</small>
                                    </div>
                                </div>
                                
                                <!-- Error display area for JS errors -->
                                <div id="payment-error" class="glass-card glass-card-error p-3 mb-4" style="display:none;">
                                    <strong class="text-error">Error:</strong> <span id="payment-error-msg"></span>
                                </div>

                                <button type="submit" id="pay-btn" class="btn btn-primary btn-full btn-large mt-4">
                                    🔒 Pay <?php echo CURRENCY_SYMBOL . number_format(KIT_PRICE, 0); ?> Securely
                                </button>
                                
                                <p class="text-center mt-2 font-sm text-dark-gray">
                                    Powered by <strong>Razorpay</strong> · 100% secure · UPI, Cards, Net Banking accepted
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Footer -->
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <?php if (!$success): ?>
    <!-- Razorpay Checkout SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
    (function () {
        'use strict';

        const form        = document.getElementById('checkout-form');
        const payBtn      = document.getElementById('pay-btn');
        const errorBox    = document.getElementById('payment-error');
        const errorMsg    = document.getElementById('payment-error-msg');
        const isLoggedIn  = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

        // Pre-fill user info for Razorpay modal (logged-in)
        const prefillName  = <?php echo json_encode($isLoggedIn ? ($user['full_name'] ?? '') : ''); ?>;
        const prefillEmail = <?php echo json_encode($isLoggedIn ? ($user['email'] ?? '') : ''); ?>;
        const prefillPhone = <?php echo json_encode($isLoggedIn ? ($user['phone'] ?? '') : ''); ?>;

        function showError(msg) {
            errorBox.style.display = 'block';
            errorMsg.textContent   = msg;
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideError() {
            errorBox.style.display = 'none';
            errorMsg.textContent   = '';
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            hideError();

            // Run existing form validation
            if (window.LuckyGenes && !window.LuckyGenes.validateForm(form)) {
                return;
            }

            // Show loading state
            if (window.showLoading) window.showLoading(payBtn);

            const csrfToken = document.getElementById('csrf_token').value;

            // ── STEP 1: Create Razorpay Order on backend ──
            let createResult;
            try {
                const res = await fetch('api/razorpay-create-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ csrf_token: csrfToken })
                });
                createResult = await res.json();
            } catch (err) {
                if (window.hideLoading) window.hideLoading(payBtn);
                showError('Could not connect to payment gateway. Please check your internet connection and try again.');
                return;
            }

            if (!createResult.success) {
                if (window.hideLoading) window.hideLoading(payBtn);
                showError(createResult.message || 'Failed to initiate payment. Please try again.');
                return;
            }

            // Collect form data to send after payment verification
            const formData = {};
            new FormData(form).forEach((val, key) => { formData[key] = val; });

            // ── STEP 2: Open Razorpay Modal ──
            const rzpOptions = {
                key:          createResult.key_id,
                amount:       createResult.amount,
                currency:     'INR',
                name:         '<?php echo addslashes(SITE_NAME); ?>',
                description:  'Comprehensive Carrier Screening Kit',
                order_id:     createResult.razorpay_order_id,
                prefill: {
                    name:    prefillName  || (formData['full_name'] || ''),
                    email:   prefillEmail || (formData['email'] || ''),
                    contact: prefillPhone || (formData['phone'] || '')
                },
                theme: { color: '#00B3A4' },
                modal: {
                    ondismiss: function () {
                        if (window.hideLoading) window.hideLoading(payBtn);
                        showError('Payment was cancelled. You can try again whenever you are ready.');
                    }
                },
                handler: async function (response) {
                    // ── STEP 3: Verify payment + create DB order ──
                    let verifyResult;
                    try {
                        const vRes = await fetch('api/razorpay-verify-payment.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                csrf_token:           csrfToken,
                                razorpay_payment_id:  response.razorpay_payment_id,
                                razorpay_order_id:    response.razorpay_order_id,
                                razorpay_signature:   response.razorpay_signature,
                                form_data:            formData
                            })
                        });
                        verifyResult = await vRes.json();
                    } catch (err) {
                        showError('Payment was received but order confirmation failed. Please contact support with your payment ID: ' + response.razorpay_payment_id);
                        if (window.hideLoading) window.hideLoading(payBtn);
                        return;
                    }

                    if (verifyResult.success) {
                        // Build redirect with barcode + gift info
                        let redirectUrl = 'track-order.php?order='
                            + encodeURIComponent(verifyResult.order_number)
                            + '&paid=1';
                        if (verifyResult.is_gift) {
                            redirectUrl = 'request-kit.php?success=1'
                                + '&order=' + encodeURIComponent(verifyResult.order_number)
                                + '&gift=1'
                                + '&sent_to=' + encodeURIComponent(verifyResult.gift_sent_to || '');
                        } else if (verifyResult.kit_barcode) {
                            redirectUrl = 'request-kit.php?success=1'
                                + '&order=' + encodeURIComponent(verifyResult.order_number)
                                + '&barcode=' + encodeURIComponent(verifyResult.kit_barcode);
                        }
                        window.location.href = redirectUrl;
                    } else {
                        if (window.hideLoading) window.hideLoading(payBtn);
                        showError(verifyResult.message || 'Payment verification failed. Please contact support.');
                    }
                }
            };

            try {
                const rzp = new Razorpay(rzpOptions);
                rzp.on('payment.failed', function (resp) {
                    if (window.hideLoading) window.hideLoading(payBtn);
                    const reason = resp.error.description || 'Payment failed';
                    showError('Payment failed: ' + reason + '. Please try again or use a different payment method.');
                });
                rzp.open();
                // Note: button loading state is managed by modal callbacks
            } catch (err) {
                if (window.hideLoading) window.hideLoading(payBtn);
                showError('Could not open payment window. Please refresh the page and try again.');
            }
        });
    })();
    </script>
    <!-- Gift toggle JS -->
    <script>
    (function () {
        const toggle     = document.getElementById('is_gift_toggle');
        const giftFields = document.getElementById('gift-fields');
        const recEmail   = document.getElementById('gift_recipient_email');
        const recName    = document.getElementById('gift_recipient_name');
        const msgArea    = document.getElementById('gift_message');
        const msgCount   = document.getElementById('gift_msg_count');

        if (!toggle) return;

        toggle.addEventListener('change', function () {
            const on = this.checked;
            giftFields.style.display = on ? 'block' : 'none';
            if (recEmail) recEmail.required = on;
            if (recName)  recName.required  = on;
        });

        if (msgArea && msgCount) {
            msgArea.addEventListener('input', function () {
                msgCount.textContent = this.value.length;
            });
        }
    })();
    </script>
    <?php endif; ?>
</body>
</html>