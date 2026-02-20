<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/User.php';
require_once 'includes/Order.php';
session_start();
setSecurityHeaders();

$success = false;
$error = '';
$orderNumber = '';
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
    } else {
        if ($isLoggedIn) {
            // LOGGED IN USER - Only shipping address required
            // Fallback chain: DB row -> session -> empty string (never null)
            $data = [
                'full_name'     => $user['full_name']  ?? $_SESSION['user_name']  ?? '',
                'email'         => $user['email']       ?? $_SESSION['user_email'] ?? '',
                'phone'         => $user['phone']       ?? $_SESSION['user_phone'] ?? '',
                'dob'           => $user['dob']         ?? $_SESSION['user_dob'] ?? '',
                'address_line1' => trim($_POST['address_line1'] ?? ''),
                'address_line2' => trim($_POST['address_line2'] ?? ''),
                'city'          => trim($_POST['city']  ?? ''),
                'state'         => trim($_POST['state'] ?? ''),
                'zip'           => trim($_POST['zip']   ?? ''),
                'consent'       => isset($_POST['consent'])
            ];
            
            // Validation
            if (!$data['consent']) {
                $error = 'You must agree to the consent statement.';
            } else {
                // Create order for logged in user
                $orderModel = new Order();
                $orderResult = $orderModel->createOrder($_SESSION['user_id'], $data);
                
                if ($orderResult['success']) {
                    $success = true;
                    $orderNumber = $orderResult['order_number'];
                } else {
                    $error = $orderResult['message'];
                }
            }
            
        } else {
            // GUEST USER - Full registration required
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'dob' => $_POST['dob'] ?? '',
                'address_line1' => trim($_POST['address_line1'] ?? ''),
                'address_line2' => trim($_POST['address_line2'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'state' => trim($_POST['state'] ?? ''),
                'zip' => trim($_POST['zip'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'consent' => isset($_POST['consent'])
            ];
            
            // Validation
            if (!$data['consent']) {
                $error = 'You must agree to the consent statement.';
            } elseif (strlen($data['password']) < 8) {
                $error = 'Password must be at least 8 characters.';
            } else {
                // Create user account first
                $userModel = new User();
                $userResult = $userModel->register($data);
                
                if ($userResult['success']) {
                    $userId = $userResult['user_id'];
                    
                    // Create order
                    $orderModel = new Order();
                    $orderResult = $orderModel->createOrder($userId, $data);
                    
                    if ($orderResult['success']) {
                        $success = true;
                        $orderNumber = $orderResult['order_number'];
                        
                        // Auto-login the user
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_email'] = $data['email'];
                        $_SESSION['user_name'] = $data['full_name'];
                        $_SESSION['last_activity'] = time();
                    } else {
                        $error = $orderResult['message'];
                    }
                } else {
                    $error = $userResult['message'];
                }
            }
        }
    }
}

$usStates = ['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>Request Screening Kit - LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    
    <main d="main-content" >
    <!-- <main d="main-content" style="padding-top: 40px; min-height: 100vh; background: var(--color-light-gray);"> -->
        <div class="container">
            
            <?php if ($success): ?>
                <!-- Success Message -->
                <div class="glass-card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">✅</div>
                    <h1 style="color: var(--color-medical-teal); margin-bottom: 1rem;">Order Confirmed!</h1>
                    <p style="font-size: 1.125rem; margin-bottom: 2rem;">
                        Thank you for your order. Your screening kit will be shipped within 3-5 business days.
                    </p>
                    
                    <div class="glass-card" style="background: rgba(0, 179, 164, 0.1); padding: 2rem; margin: 2rem 0;">
                        <div style="font-size: 0.9rem; color: var(--color-dark-gray); margin-bottom: 0.5rem;">Your Order Number</div>
                        <div style="font-size: 2rem; font-weight: 700; color: var(--color-primary-deep-blue); letter-spacing: 2px;">
                            <?php echo htmlspecialchars($orderNumber); ?>
                        </div>
                        <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-top: 0.5rem;">
                            Please save this number for tracking
                        </div>
                    </div>
                    
                    <h3 style="margin: 2rem 0 1rem;">What's Next?</h3>
                    <div style="text-align: left; max-width: 600px; margin: 0 auto;">
                        <ol style="line-height: 1.8;">
                            <li><strong>Check your email</strong> for order confirmation and instructions</li>
                            <li><strong>Receive your kit</strong> within 3-5 business days</li>
                            <li><strong>Collect your sample</strong> following the included instructions</li>
                            <li><strong>Return to lab</strong> using the prepaid shipping label</li>
                            <li><strong>Get results</strong> in 14-21 days via your patient portal</li>
                        </ol>
                    </div>
                    
                    <div style="margin-top: 3rem;">
                        <a href="track-order.php?order=<?php echo urlencode($orderNumber); ?>" class="btn btn-primary btn-large">Track Your Order</a>
                        <a href="user-portal/" class="btn btn-outline btn-large" style="margin-left: 1rem;">Go to Patient Portal</a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Order Form -->
                <section class="page-header" style="background: var(--gradient-primary); color: var(--color-white); padding: 4rem 0 3rem; text-align: center;">
                    <div class="container">
                        <h1 style="color: var(--color-white); margin-bottom: 1rem;">
                            <?php echo $isLoggedIn ? 'Order Another Screening Kit' : 'Request Your Screening Kit'; ?>
                        </h1>


                        <p style="font-size: 1.25rem; opacity: 0.95; max-width: 800px; margin: 0 auto;">
                        
                            <?php if ($isLoggedIn): ?>
                                Welcome back, <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>! Complete your shipping information to place your order.
                            <?php else: ?>
                                Complete your order below. Your kit will ship within 3-5 business days, and results will be available in 14-21 days.
                            <?php endif; ?>



                        </p>
                    </div>
                </section>

                
                <?php if ($error): ?>
                    <div class="glass-card" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); padding: 1rem; margin-bottom: 2rem;">
                        <strong style="color: #c33;">Error:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Product Summary -->
                    <div class="col col-2">
                        <div class="glass-card" style="position: sticky; top: 100px;">
                            <h3>Order Summary</h3>
                            <div style="padding: 1.5rem 0; border-bottom: 1px solid var(--color-medium-gray);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <strong>Comprehensive Carrier Screening Kit</strong>
                                </div>
                                <div style="font-size: 0.9rem; color: var(--color-dark-gray); margin-bottom: 1rem;">
                                    Tests for 300+ genetic conditions
                                </div>
                            </div>
                            
                            <div style="padding: 1.5rem 0; border-bottom: 1px solid var(--color-medium-gray);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Subtotal</span>
                                    <span>$99.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Shipping</span>
                                    <span style="color: var(--color-medical-teal);">FREE</span>
                                </div>
                            </div>
                            
                            <div style="padding: 1.5rem 0;">
                                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700;">
                                    <span>Total</span>
                                    <span style="color: var(--color-medical-teal);">$99.00</span>
                                </div>
                            </div>
                            
                            <?php if ($isLoggedIn): ?>
                                <!-- Account Info -->
                                <div style="background: rgba(0, 179, 164, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                                    <div style="font-size: 0.85rem; color: var(--color-dark-gray); margin-bottom: 0.5rem;">Ordering as:</div>
                                    <div style="font-weight: 600; color: var(--color-primary-deep-blue);"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                    <div style="font-size: 0.9rem; color: var(--color-dark-gray);"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="trust-badges" style="flex-direction: column; align-items: flex-start; margin-top: 1.5rem;">
                                <div class="trust-badge" style="width: 100%; justify-content: flex-start;">
                                    <span>🔒</span>
                                    <span>Secure Checkout</span>
                                </div>
                                <div class="trust-badge" style="width: 100%; justify-content: flex-start;">
                                    <span>📦</span>
                                    <span>Free Shipping</span>
                                </div>
                                <div class="trust-badge" style="width: 100%; justify-content: flex-start;">
                                    <span>🔐</span>
                                    <span>Private Results</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Checkout Form -->
                    <div class="col col-2">
                        <div class="glass-card">
                            <form method="POST" action="" data-validate>
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                
                                <?php if (!$isLoggedIn): ?>
                                    <!-- Personal Information - ONLY for guest users -->
                                    <h3 style="margin-bottom: 1.5rem;">Personal Information</h3>
                                    
                                    <div class="form-group">
                                        <label for="full_name" class="form-label required">Full Name</label>
                                        <input type="text" id="full_name" name="full_name" class="form-input" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col col-2">
                                            <div class="form-group">
                                                <label for="email" class="form-label required">Email Address</label>
                                                <input type="email" id="email" name="email" class="form-input" required data-validate="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col col-2">
                                            <div class="form-group">
                                                <label for="phone" class="form-label required">Phone Number</label>
                                                <input type="tel" id="phone" name="phone" class="form-input" required data-validate="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="dob" class="form-label required">Date of Birth</label>
                                        <input type="date" id="dob" name="dob" class="form-input" required value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="password" class="form-label required">Create Password</label>
                                        <input type="password" id="password" name="password" class="form-input" required data-validate="password" minlength="8">
                                        <small style="color: var(--color-dark-gray);">Minimum 8 characters for your patient portal account</small>
                                    </div>
                                    
                                    <h3 style="margin: 2rem 0 1.5rem;">Shipping Address</h3>
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
                                    <div style="background: rgba(0, 179, 164, 0.1); border-left: 4px solid var(--color-medical-teal); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                                        <h3 style="margin-bottom: 1.25rem; color: var(--color-primary-deep-blue); font-size: 1.1rem;">
                                            ✅ Ordering as
                                        </h3>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem 2rem;">
                                            <div>
                                                <div style="font-size: 0.8rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.2rem;">Name</div>
                                                <div style="font-weight: 600; color: var(--color-primary-deep-blue);"><?php echo htmlspecialchars($displayName); ?></div>
                                            </div>
                                            <div>
                                                <div style="font-size: 0.8rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.2rem;">Phone</div>
                                                <div style="font-weight: 600; color: var(--color-primary-deep-blue);"><?php echo htmlspecialchars($displayPhone); ?></div>
                                            </div>
                                            <div style="grid-column: 1 / -1;">
                                                <div style="font-size: 0.8rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.2rem;">Email</div>
                                                <div style="font-weight: 600; color: var(--color-primary-deep-blue);"><?php echo htmlspecialchars($displayEmail); ?></div>
                                            </div>
                                            <div style="grid-column: 1 / -1;">
                                                <div style="font-size: 0.8rem; color: var(--color-dark-gray); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.2rem;">Date of Birth</div>
                                                <div style="font-weight: 600; color: var(--color-primary-deep-blue);"><?php echo htmlspecialchars($displayDoB); ?></div>
                                            </div>
                                        </div>
                                        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid rgba(0, 179, 164, 0.2);">
                                            <small style="color: var(--color-dark-gray);">
                                                Not you? <a href="user-portal/logout.php" style="color: var(--color-medical-teal); font-weight: 600;">Log out</a> to order with a different account.
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <h3 style="margin-bottom: 1.5rem;">Shipping Address</h3>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="address_line1" class="form-label required">Street Address</label>
                                    <input type="text" id="address_line1" name="address_line1" class="form-input" required value="<?php echo htmlspecialchars($_POST['address_line1'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="address_line2" class="form-label">Apartment, Suite, etc. (Optional)</label>
                                    <input type="text" id="address_line2" name="address_line2" class="form-input" value="<?php echo htmlspecialchars($_POST['address_line2'] ?? ''); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col col-2">
                                        <div class="form-group">
                                            <label for="city" class="form-label required">City</label>
                                            <input type="text" id="city" name="city" class="form-input" required value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col col-4">
                                        <div class="form-group">
                                            <label for="state" class="form-label required">State</label>
                                            <select id="state" name="state" class="form-select" required>
                                                <option value="">Select State</option>
                                                <?php foreach($usStates as $code => $name): ?>
                                                    <option value="<?php echo $code; ?>" <?php echo (($_POST['state'] ?? '') === $code) ? 'selected' : ''; ?>>
                                                        <?php echo $name; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col col-4">
                                        <div class="form-group">
                                            <label for="zip" class="form-label required">ZIP Code</label>
                                            <input type="text" id="zip" name="zip" class="form-input" required pattern="[0-9]{5}" value="<?php echo htmlspecialchars($_POST['zip'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <h3 style="margin: 2rem 0 1.5rem;">Consent</h3>
                                
                                <div class="form-checkbox">
                                    <input type="checkbox" id="consent" name="consent" required>
                                    <label for="consent" style="font-size: 0.9rem;">
                                        I understand that this is a carrier screening test, not a diagnostic test. This test does not replace genetic counseling or physician consultation. I consent to genetic testing and agree to the <a href="terms-of-service.php" target="_blank">Terms of Service</a> and <a href="privacy-policy.php" target="_blank">Privacy Policy</a>.
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-full btn-large" style="margin-top: 2rem;">
                                    Complete Order - $99.00
                                </button>
                                
                                <p style="text-align: center; margin-top: 1rem; font-size: 0.85rem; color: var(--color-dark-gray);">
                                    🔒 Your information is encrypted and secure
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>