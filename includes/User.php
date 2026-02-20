<?php
/**
 * User Model
 * Handles user authentication, registration, and email verification.
 * Uses PHPMailer + Gmail SMTP for email delivery.
 *
 * Requires PHPMailer:
 *   composer require phpmailer/phpmailer
 *
 * Required constants in config.php:
 *   define('MAIL_HOST',      'smtp.gmail.com');
 *   define('MAIL_PORT',      587);
 *   define('MAIL_USERNAME',  'yourgmail@gmail.com');
 *   define('MAIL_PASSWORD',  'xxxx xxxx xxxx xxxx');  // Gmail App Password (16 chars, NOT your Gmail password)
 *   define('MAIL_FROM',      'yourgmail@gmail.com');
 *   define('MAIL_FROM_NAME', 'LuckyGeneMDx');
 *   define('BASE_URL',       'http://localhost/luckygenemdx'); // or your live domain
 *
 * Schema additions required — run schema_changes.sql first:
 *   ALTER TABLE users
 *     ADD COLUMN email_verified      TINYINT(1)  NOT NULL DEFAULT 0,
 *     ADD COLUMN verification_token  VARCHAR(64) NULL,
 *     ADD COLUMN token_expires_at    DATETIME    NULL;
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

// PHPMailer — Composer (preferred) or manual install fallback
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../includes/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../includes/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../includes/phpmailer/src/SMTP.php';
}

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────

    private function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validatePhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    private function validateDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }

    private function updateLastLogin($user_id) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
        } catch (PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Security / Rate Limiting
    // ─────────────────────────────────────────────────────────────

    private function isLockedOut($identifier, $ip) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = :identifier AND ip_address = :ip AND success = 0 AND attempted_at > (NOW() - INTERVAL " . LOCKOUT_TIME . " SECOND)");
        $stmt->execute([':identifier' => $identifier, ':ip' => $ip]);
        return $stmt->fetchColumn() >= MAX_LOGIN_ATTEMPTS;
    }

    private function logAttempt($identifier, $ip, $success) {
        $stmt = $this->db->prepare("INSERT INTO login_attempts (email, ip_address, success, attempted_at) VALUES (:identifier, :ip, :success, NOW())");
        $stmt->execute([':identifier' => $identifier, ':ip' => $ip, ':success' => $success ? 1 : 0]);
    }

    // ─────────────────────────────────────────────────────────────
    //  PHPMailer — Gmail SMTP factory
    // ─────────────────────────────────────────────────────────────

    /**
     * Returns a configured PHPMailer instance pointed at Gmail SMTP.
     * Settings are read from constants defined in config.php.
     */
    private function createMailer(): PHPMailer {
        $mail = new PHPMailer(true); // true = throw MailException on error

        $mail->isSMTP();
        $mail->Host       = defined('MAIL_HOST')     ? MAIL_HOST     : 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('MAIL_USERNAME') ? MAIL_USERNAME : '';
        $mail->Password   = defined('MAIL_PASSWORD') ? MAIL_PASSWORD : ''; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = defined('MAIL_PORT')     ? MAIL_PORT     : 587;

        $fromEmail = defined('MAIL_FROM')      ? MAIL_FROM      : (defined('MAIL_USERNAME') ? MAIL_USERNAME : '');
        $fromName  = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'LuckyGeneMDx';
        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo($fromEmail, $fromName);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    // ─────────────────────────────────────────────────────────────
    //  Registration
    // ─────────────────────────────────────────────────────────────

    /**
     * Register a new user.
     * Account is created inactive (email_verified = 0, is_active = 0).
     * Call sendVerificationEmail() immediately after to issue the token.
     * Phone is optional.
     */
    public function register($data) {
        try {
            if (empty($data['email']) || !$this->validateEmail($data['email'])) {
                return ['success' => false, 'message' => 'Invalid email address.', 'user_id' => null];
            }
            if (empty($data['password']) || strlen($data['password']) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters.', 'user_id' => null];
            }
            if (empty($data['full_name'])) {
                return ['success' => false, 'message' => 'Full name is required.', 'user_id' => null];
            }
            if (empty($data['dob']) || !$this->validateDate($data['dob'])) {
                return ['success' => false, 'message' => 'Invalid date of birth.', 'user_id' => null];
            }

            $phone = trim($data['phone'] ?? '');
            if ($phone !== '' && !$this->validatePhone($phone)) {
                return ['success' => false, 'message' => 'Invalid phone number format.', 'user_id' => null];
            }

            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already registered. Please <a href="login.php">sign in</a> instead.', 'user_id' => null];
            }

            $email     = $this->sanitize($data['email']);
            $full_name = $this->sanitize($data['full_name']);
            $phone     = $this->sanitize($phone);
            $dob       = $this->sanitize($data['dob']);
            $pw_hash   = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "INSERT INTO users (email, password_hash, full_name, phone, dob, created_at, is_active, email_verified)
                 VALUES (:email, :pw_hash, :full_name, :phone, :dob, NOW(), 0, 0)"
            );
            $stmt->execute([
                ':email'     => $email,
                ':pw_hash'   => $pw_hash,
                ':full_name' => $full_name,
                ':phone'     => $phone,
                ':dob'       => $dob,
            ]);

            $user_id = (int) $this->db->lastInsertId();
            error_log("User registered (pending verification): $email (ID: $user_id)");

            return ['success' => true, 'message' => 'Registration successful.', 'user_id' => $user_id];

        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.', 'user_id' => null];
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Email Verification
    // ─────────────────────────────────────────────────────────────

    /**
     * Generate a secure 64-char token, store it, then send the verification
     * email via Gmail SMTP using PHPMailer.
     */
    public function sendVerificationEmail($user_id, $email, $full_name) {
        try {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $stmt = $this->db->prepare(
                "UPDATE users SET verification_token = :token, token_expires_at = :expires WHERE user_id = :user_id"
            );
            $stmt->execute([':token' => $token, ':expires' => $expires, ':user_id' => $user_id]);

            $base_url   = defined('BASE_URL') ? rtrim(BASE_URL, '/') : 'http://localhost:9999';
            $verify_url = $base_url . '/user-portal/verify-email.php?token=' . urlencode($token);

            $mail = $this->createMailer();
            $mail->addAddress($email, $full_name);
            $mail->Subject = 'Verify your LuckyGeneMDx account';
            $mail->Body    = $this->buildVerificationEmailBody($full_name, $verify_url);
            $mail->AltBody = "Hi " . explode(' ', $full_name)[0] . ",\n\n"
                           . "Verify your email by visiting:\n$verify_url\n\n"
                           . "Link expires in 24 hours.\n\n— LuckyGeneMDx";

            $mail->send();
            error_log("Verification email sent to: $email (user_id: $user_id)");
            return ['success' => true, 'message' => 'Verification email sent.'];

        } catch (MailException $e) {
            error_log("PHPMailer error (sendVerificationEmail): " . $e->getMessage());
            return ['success' => false, 'message' => 'Could not send verification email — check SMTP settings.'];
        } catch (Exception $e) {
            error_log("sendVerificationEmail error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error sending verification email.'];
        }
    }

    /**
     * HTML body for the verification email.
     */
    private function buildVerificationEmailBody($full_name, $verify_url) {
        $first = htmlspecialchars(explode(' ', $full_name)[0]);
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0f172a;font-family:Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f172a;padding:40px 20px">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0"
             style="background:#0a1f44;border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:40px;max-width:100%">
        <tr><td align="center" style="padding-bottom:24px">
          <div style="font-size:48px">🧬</div>
          <h1 style="color:#00E0C6;font-size:22px;margin:12px 0 4px;font-family:Arial,sans-serif">LuckyGeneMDx</h1>
          <p style="color:#94a3b8;font-size:14px;margin:0;font-family:Arial,sans-serif">Patient Portal</p>
        </td></tr>
        <tr><td style="padding-bottom:24px">
          <p style="color:#ffffff;font-size:16px;margin:0 0 12px;font-family:Arial,sans-serif">Hi {$first},</p>
          <p style="color:#94a3b8;font-size:15px;line-height:1.7;margin:0;font-family:Arial,sans-serif">
            Thanks for registering with LuckyGeneMDx. Click the button below to verify
            your email address and activate your account.
            This link expires in <strong style="color:#ffffff">24 hours</strong>.
          </p>
        </td></tr>
        <tr><td align="center" style="padding:8px 0 28px">
          <a href="{$verify_url}"
             style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00B3A4,#00E0C6);color:#ffffff;text-decoration:none;border-radius:12px;font-weight:700;font-size:16px;font-family:Arial,sans-serif">
            Verify My Email
          </a>
        </td></tr>
        <tr><td style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px">
          <p style="color:#64748b;font-size:12px;margin:0 0 8px;font-family:Arial,sans-serif">
            If the button doesn't work, copy and paste this link into your browser:
          </p>
          <p style="color:#00B3A4;font-size:12px;word-break:break-all;margin:0;font-family:Arial,sans-serif">{$verify_url}</p>
        </td></tr>
        <tr><td style="padding-top:24px">
          <p style="color:#64748b;font-size:12px;margin:0;font-family:Arial,sans-serif">
            If you didn't create this account, you can safely ignore this email.
          </p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    /**
     * Validate the token from a verification link.
     * Activates the account on success.
     */
    public function verifyEmailToken($token) {
        try {
            if (empty($token) || strlen($token) !== 64) {
                return ['success' => false, 'message' => 'Invalid verification link.'];
            }

            $stmt = $this->db->prepare(
                "SELECT user_id, email_verified, token_expires_at FROM users WHERE verification_token = :token LIMIT 1"
            );
            $stmt->execute([':token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ['success' => false, 'message' => 'Verification link is invalid or has already been used.'];
            }
            if ($user['email_verified']) {
                return ['success' => false, 'message' => 'This email has already been verified. Please log in.'];
            }
            if (strtotime($user['token_expires_at']) < time()) {
                return ['success' => false, 'message' => 'Verification link has expired. Please request a new one.', 'expired' => true];
            }

            $stmt = $this->db->prepare(
                "UPDATE users
                 SET email_verified = 1, is_active = 1, verification_token = NULL, token_expires_at = NULL
                 WHERE user_id = :uid"
            );
            $stmt->execute([':uid' => $user['user_id']]);

            error_log("Email verified for user_id: " . $user['user_id']);
            return ['success' => true, 'message' => 'Email verified! Your account is now active.'];

        } catch (PDOException $e) {
            error_log("verifyEmailToken error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Verification failed. Please try again.'];
        }
    }

    /**
     * Resend a verification email to an unverified registered address.
     */
    public function resendVerificationEmail($email) {
        try {
            $email = $this->sanitize($email);
            $stmt  = $this->db->prepare(
                "SELECT user_id, full_name, email_verified FROM users WHERE email = :email"
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Don't reveal whether the address is registered
                return ['success' => true, 'message' => 'If that address is registered you will receive an email shortly.'];
            }
            if ($user['email_verified']) {
                return ['success' => false, 'message' => 'This account is already verified. Please log in.'];
            }

            return $this->sendVerificationEmail($user['user_id'], $email, $user['full_name']);

        } catch (PDOException $e) {
            error_log("resendVerificationEmail error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Could not resend email. Please try again.'];
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Login
    // ─────────────────────────────────────────────────────────────

    /**
     * Login with email + password.
     * Unverified accounts are blocked with a resend link in the error message.
     */
    public function login($email, $password) {
        $email = $this->sanitize($email);
        $ip = $_SERVER['REMOTE_ADDR'];

        if ($this->isLockedOut($email, $ip)) {
            return ['success' => false, 'message' => 'Too many failed login attempts. Please try again in 15 minutes.'];
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT user_id, email, password_hash, full_name, is_active, email_verified FROM users WHERE email = :email"
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $this->logAttempt($email, $ip, false);
                return ['success' => false, 'message' => 'Invalid email or password.'];
            }
            if (!$user['email_verified']) {
                return [
                    'success'    => false,
                    'message'    => 'Please verify your email before logging in. '
                                  . '<a href="resend-verification.php?email=' . urlencode($email) . '">Resend verification link</a>',
                    'unverified' => true,
                ];
            }
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive. Please contact support.'];
            }

            $this->updateLastLogin($user['user_id']);
            session_regenerate_id(true);

            $_SESSION['user_id']       = $user['user_id'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['user_name']     = $user['full_name'];
            $_SESSION['last_activity'] = time();

            return ['success' => true, 'user' => $user, 'message' => 'Login successful.'];

        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }

    /**
     * Login with order number + password.
     */
    public function loginWithOrderId($order_number, $password) {
        $order_number = $this->sanitize($order_number);
        $ip = $_SERVER['REMOTE_ADDR'];

        if ($this->isLockedOut($order_number, $ip)) {
            return ['success' => false, 'message' => 'Too many failed login attempts. Please try again in 15 minutes.'];
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT u.user_id, u.email, u.password_hash, u.full_name, u.is_active, u.email_verified
                 FROM users u
                 INNER JOIN orders o ON u.user_id = o.user_id
                 WHERE o.order_number = :order_number"
            );
            $stmt->execute([':order_number' => $order_number]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $this->logAttempt($order_number, $ip, false);
                return ['success' => false, 'message' => 'Invalid order number or password.'];
            }
            if (!$user['email_verified']) {
                return ['success' => false, 'message' => 'Please verify your email before logging in.', 'unverified' => true];
            }
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive. Please contact support.'];
            }

            $this->updateLastLogin($user['user_id']);
            session_regenerate_id(true);

            $_SESSION['user_id']       = $user['user_id'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['user_name']     = $user['full_name'];
            $_SESSION['last_activity'] = time();

            return ['success' => true, 'user' => $user, 'message' => 'Login successful.'];

        } catch (PDOException $e) {
            error_log("Order Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  Profile & Password
    // ─────────────────────────────────────────────────────────────

    public function getUserById($user_id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT user_id, email, full_name, phone, dob, created_at, last_login, is_active, email_verified
                 FROM users WHERE user_id = :user_id"
            );
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }

    public function updateProfile($user_id, $data) {
        $data = $this->sanitize($data);
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET full_name = :full_name, phone = :phone WHERE user_id = :user_id"
            );
            $stmt->execute([':full_name' => $data['full_name'], ':phone' => $data['phone'], ':user_id' => $user_id]);
            return ['success' => true, 'message' => 'Profile updated successfully.'];
        } catch (PDOException $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Update failed. Please try again.'];
        }
    }

    public function changePassword($user_id, $current_password, $new_password) {
        try {
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user)                                                       return ['success' => false, 'message' => 'User not found.'];
            if (!password_verify($current_password, $user['password_hash'])) return ['success' => false, 'message' => 'Current password is incorrect.'];
            if (strlen($new_password) < 8)                                    return ['success' => false, 'message' => 'Password must be at least 8 characters.'];

            $stmt = $this->db->prepare("UPDATE users SET password_hash = :pw WHERE user_id = :user_id");
            $stmt->execute([':pw' => password_hash($new_password, PASSWORD_DEFAULT), ':user_id' => $user_id]);
            return ['success' => true, 'message' => 'Password changed successfully.'];

        } catch (PDOException $e) {
            error_log("Password Change Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Password change failed. Please try again.'];
        }
    }

    public function resetPasswordVerifyInfo($email, $dob, $phone, $new_password) {
        try {
            if (!$this->validateEmail($email)) return ['success' => false, 'message' => 'Invalid email format.'];
            if (strlen($new_password) < 8)     return ['success' => false, 'message' => 'New password must be at least 8 characters.'];

            $email      = $this->sanitize($email);
            $dob        = $this->sanitize($dob);
            $phoneClean = preg_replace('/[^0-9]/', '', $phone);

            $stmt = $this->db->prepare(
                "SELECT user_id FROM users
                 WHERE email = :email AND dob = :dob
                   AND REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')','') = :phone
                   AND is_active = 1"
            );
            $stmt->execute([':email' => $email, ':dob' => $dob, ':phone' => $phoneClean]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) return ['success' => false, 'message' => 'Identity verification failed. Please check your details.'];

            $stmt = $this->db->prepare("UPDATE users SET password_hash = :pw WHERE user_id = :uid");
            $stmt->execute([':pw' => password_hash($new_password, PASSWORD_DEFAULT), ':uid' => $user['user_id']]);

            error_log("Password reset via identity verify for user_id: " . $user['user_id']);
            return ['success' => true, 'message' => 'Password has been successfully reset.'];

        } catch (PDOException $e) {
            error_log("Reset Password Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error. Please try again later.'];
        }
    }

    /**
     * Send results ready notification email.
     */
    public function sendResultsNotification($email, $full_name, $order_number) {
        try {
            $base_url = defined('BASE_URL') ? rtrim(BASE_URL, '/') : 'http://localhost/luckygenemdx';
            $login_url = $base_url . '/user-portal/login.php';

            $mail = $this->createMailer();
            $mail->addAddress($email, $full_name);
            $mail->Subject = "Your Results Are Ready - Order #$order_number";
            
            $first = htmlspecialchars(explode(' ', $full_name)[0]);
            $order_number = htmlspecialchars($order_number);
            
            $mail->Body = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0f172a;font-family:Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#0f172a;padding:40px 20px">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#0a1f44;border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:40px;max-width:100%">
        <tr><td align="center" style="padding-bottom:24px"><div style="font-size:48px">🧬</div><h1 style="color:#00E0C6;font-size:22px;margin:12px 0 4px;font-family:Arial,sans-serif">LuckyGeneMDx</h1><p style="color:#94a3b8;font-size:14px;margin:0;font-family:Arial,sans-serif">Patient Portal</p></td></tr>
        <tr><td style="padding-bottom:24px"><p style="color:#ffffff;font-size:16px;margin:0 0 12px;font-family:Arial,sans-serif">Hi {$first},</p><p style="color:#94a3b8;font-size:15px;line-height:1.7;margin:0;font-family:Arial,sans-serif">Great news! The results for your order <strong>#{$order_number}</strong> are now available. You can view and download your comprehensive report securely from the patient portal.</p></td></tr>
        <tr><td align="center" style="padding:8px 0 28px"><a href="{$login_url}" style="display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#00B3A4,#00E0C6);color:#ffffff;text-decoration:none;border-radius:12px;font-weight:700;font-size:16px;font-family:Arial,sans-serif">View My Results</a></td></tr>
        <tr><td style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px"><p style="color:#64748b;font-size:12px;margin:0;font-family:Arial,sans-serif">If you have any questions about your results, please contact our support team.</p></td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
            $mail->AltBody = "Hi $first,\n\nYour results for order #$order_number are ready.\n\nLog in to view them: $login_url\n\n— LuckyGeneMDx";

            $mail->send();
            return ['success' => true, 'message' => 'Notification email sent.'];

        } catch (Exception $e) {
            error_log("sendResultsNotification error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error sending notification email.'];
        }
    }

    /**
     * Recover email address using Order Number or Phone + DOB.
     */
    public function recoverEmail($order_number = null, $phone = null, $dob = null) {
        try {
            if ($order_number) {
                $order_number = $this->sanitize($order_number);
                $stmt = $this->db->prepare(
                    "SELECT u.email FROM users u 
                     INNER JOIN orders o ON u.user_id = o.user_id 
                     WHERE o.order_number = :order_number LIMIT 1"
                );
                $stmt->execute([':order_number' => $order_number]);
                $email = $stmt->fetchColumn();
                if ($email) return ['success' => true, 'email' => $email];
            }

            if ($phone && $dob) {
                $phoneClean = preg_replace('/[^0-9]/', '', $phone);
                $dob = $this->sanitize($dob);
                
                $stmt = $this->db->prepare(
                    "SELECT email FROM users 
                     WHERE dob = :dob 
                     AND REPLACE(REPLACE(REPLACE(REPLACE(phone,'-',''),' ',''),'(',''),')','') = :phone 
                     LIMIT 1"
                );
                $stmt->execute([':dob' => $dob, ':phone' => $phoneClean]);
                $email = $stmt->fetchColumn();
                if ($email) return ['success' => true, 'email' => $email];
            }

            return ['success' => false, 'message' => 'No account found with provided details.'];

        } catch (PDOException $e) {
            error_log("Recover Email Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'System error. Please try again.'];
        }
    }
}