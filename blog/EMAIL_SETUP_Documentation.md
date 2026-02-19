# LuckyGeneMDx — Email Setup Guide
> How to configure PHPMailer for development (Gmail) and switch to a production provider when ready.

---

## Table of Contents
1. [How It Works](#how-it-works)
2. [Step 1 — Install PHPMailer](#step-1--install-phpmailer)
3. [Step 2 — Gmail App Password (Dev Setup)](#step-2--gmail-app-password-dev-setup)
4. [Step 3 — Update config.php](#step-3--update-configphp)
5. [Step 4 — Update User.php](#step-4--update-userphp)
6. [Step 5 — Test It](#step-5--test-it)
7. [Troubleshooting](#troubleshooting)
8. [Switching to a Production Provider](#switching-to-a-production-provider)
9. [Provider Reference Table](#provider-reference-table)

---

## How It Works

PHP's built-in `mail()` function requires a configured mail server on the host — which most local dev environments and many shared hosts don't have. The fix is to use **PHPMailer**, a library that sends email over SMTP directly to a real mail provider (Gmail, Brevo, Mailgun, etc.).

The verification flow is:

```
User registers → account created (inactive) → token generated + stored in DB
→ PHPMailer sends email via Gmail SMTP → user clicks link
→ verify-email.php validates token → account activated → user can log in
```

---

## Step 1 — Install PHPMailer

### Option A: Composer (recommended)
If you have Composer installed, run this in your project root:

```bash
composer require phpmailer/phpmailer
```

This creates a `vendor/` folder. Make sure your project root has a `composer.json` — if not, run `composer init` first.

### Option B: Manual download (no Composer)
1. Download the latest release from [github.com/PHPMailer/PHPMailer/releases](https://github.com/PHPMailer/PHPMailer/releases)
2. Extract and copy the `src/` folder into your project:
   ```
   your-project/
   └── includes/
       └── phpmailer/
           └── src/
               ├── Exception.php
               ├── PHPMailer.php
               └── SMTP.php
   ```
3. The fallback loader in `User.php` will pick this up automatically — no other changes needed.

---

## Step 2 — Gmail App Password (Dev Setup)

> **Important:** Gmail requires an **App Password** — this is NOT your regular Gmail login password. Using your real password will fail.

### How to generate a Gmail App Password

**Prerequisites:** You must have 2-Step Verification enabled on your Google Account.

1. Go to [myaccount.google.com](https://myaccount.google.com)
2. Click **Security** in the left sidebar
3. Under "How you sign in to Google", click **2-Step Verification** and make sure it's **On**
4. Go back to Security and click **App passwords** (search for it if you don't see it)
5. Under "Select app", choose **Mail**
6. Under "Select device", choose **Other (custom name)** → type `LuckyGeneMDx`
7. Click **Generate**
8. Copy the **16-character password** shown (it looks like: `xxxx xxxx xxxx xxxx`)

> ⚠️ You will only see this password once. Save it somewhere safe before closing the dialog.

---

## Step 3 — Update config.php

Add the following constants to your existing `includes/config.php` file:

```php
// ── Email / SMTP Configuration ───────────────────────────────────
// DEV: Gmail SMTP
define('MAIL_HOST',      'smtp.gmail.com');
define('MAIL_PORT',      587);
define('MAIL_USERNAME',  'yourgmail@gmail.com');       // Your Gmail address
define('MAIL_PASSWORD',  'xxxx xxxx xxxx xxxx');       // App Password from Step 2
define('MAIL_FROM',      'yourgmail@gmail.com');       // Must match MAIL_USERNAME for Gmail
define('MAIL_FROM_NAME', 'LuckyGeneMDx');

// ── Base URL (used to build the verification link in emails) ─────
// Local dev example:
define('BASE_URL', 'http://localhost/luckygenemdx');
// Live server example:
// define('BASE_URL', 'https://yourdomain.com');
```

> **Note on `MAIL_FROM`:** Gmail requires the From address to match your authenticated Gmail account. Using a different From address will cause the email to fail or be rejected.

---

## Step 4 — Update User.php

Replace your existing `User.php` with the updated version provided alongside this guide. The key change is replacing the old `mail()` call in `sendVerificationEmail()` with:

```php
private function createMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;       // smtp.gmail.com
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;   // your Gmail
    $mail->Password   = MAIL_PASSWORD;   // App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;       // 587
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    return $mail;
}
```

No changes are needed to `register.php`, `verify-email.php`, or `resend-verification.php` — they call `sendVerificationEmail()` which now uses PHPMailer internally.

---

## Step 5 — Test It

1. Start your local dev server (XAMPP, Laragon, php artisan serve, etc.)
2. Register a new account using a **real email address you have access to**
3. Check your Gmail inbox — the verification email should arrive within **30 seconds**
4. Click the **Verify My Email** button in the email
5. You should be redirected to `verify-email.php` showing a success screen
6. Try logging in — it should now work

### Quick SMTP test script
Create a file `test-mail.php` in your project root to test the connection independently:

```php
<?php
require_once 'includes/config.php';
require_once 'vendor/autoload.php'; // or manual path

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;

    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_USERNAME); // send to yourself
    $mail->Subject = 'LuckyGeneMDx SMTP Test';
    $mail->Body    = '<h2>✅ SMTP is working!</h2><p>PHPMailer + Gmail is configured correctly.</p>';
    $mail->isHTML(true);
    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo '❌ Error: ' . $mail->ErrorInfo;
}
```

Run it by visiting `http://localhost/luckygenemdx/test-mail.php`. Delete it after testing.

---

## Troubleshooting

| Error | Cause | Fix |
|---|---|---|
| `SMTP connect() failed` | Wrong host/port or firewall blocking port 587 | Try port 465 with `ENCRYPTION_SMTPS` instead |
| `Username and Password not accepted` | Wrong App Password or using real Gmail password | Regenerate App Password in Google Account settings |
| `Please log in via your web browser` | 2-Step Verification not enabled | Enable 2FA on your Google Account first |
| `Could not instantiate mail function` | PHPMailer not found | Check Composer autoload path or manual file paths |
| Email arrives in spam | Gmail From address mismatch | Make sure `MAIL_FROM` matches `MAIL_USERNAME` exactly |
| Token expired immediately | Server timezone mismatch | Add `date_default_timezone_set('America/New_York');` to config.php |

### Enable SMTP debug output (temporarily)
Add this line inside `createMailer()` to see a full SMTP conversation log — useful for diagnosing connection issues:

```php
$mail->SMTPDebug = SMTP::DEBUG_SERVER; // 0 = off, 1 = client, 2 = server+client
$mail->Debugoutput = 'error_log';      // writes to PHP error log
```

Remove `SMTPDebug` again once working — it's very verbose.

---

## Switching to a Production Provider

When you're ready to go live, swap out the four config constants — no code changes required in `User.php`.

### Brevo (Sendinblue) — 300 free emails/day
```php
define('MAIL_HOST',     'smtp-relay.brevo.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'your-brevo-login@email.com'); // your Brevo account email
define('MAIL_PASSWORD', 'your-brevo-smtp-key');        // from Brevo → SMTP & API → SMTP tab
define('MAIL_FROM',     'noreply@yourdomain.com');
```

### Mailgun — 1,000 free emails/month
```php
define('MAIL_HOST',     'smtp.mailgun.org');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'postmaster@mg.yourdomain.com'); // from Mailgun dashboard
define('MAIL_PASSWORD', 'your-mailgun-smtp-password');
define('MAIL_FROM',     'noreply@yourdomain.com');
```

### Resend — 3,000 free emails/month
```php
define('MAIL_HOST',     'smtp.resend.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'resend');                  // literally the word "resend"
define('MAIL_PASSWORD', 're_xxxxxxxxxxxxxxxxxxxx'); // your Resend API key
define('MAIL_FROM',     'noreply@yourdomain.com');
```

### Postmark — best deliverability
```php
define('MAIL_HOST',     'smtp.postmarkapp.com');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'your-postmark-server-token'); // Server API Token
define('MAIL_PASSWORD', 'your-postmark-server-token'); // Same value for both
define('MAIL_FROM',     'noreply@yourdomain.com');
```

### SendGrid — 100 free emails/day
```php
define('MAIL_HOST',     'smtp.sendgrid.net');
define('MAIL_PORT',     587);
define('MAIL_USERNAME', 'apikey');                // literally the word "apikey"
define('MAIL_PASSWORD', 'SG.xxxxxxxxxxxxxxxxxxxx'); // your SendGrid API key
define('MAIL_FROM',     'noreply@yourdomain.com');
```

---

## Provider Reference Table

| Provider | Free Tier | Best For | Signup |
|---|---|---|---|
| **Gmail SMTP** | Unlimited (your account) | **Dev & testing only** | Already have it |
| **Brevo** | 300/day | General transactional | brevo.com |
| **Mailgun** | 1,000/month | Developer-friendly | mailgun.com |
| **Resend** | 3,000/month, 100/day | Modern API, easy setup | resend.com |
| **Postmark** | 100/month | Highest deliverability | postmarkapp.com |
| **SendGrid** | 100/day | Large scale | sendgrid.com |
| **Amazon SES** | 62,000/month (on AWS) | Cheapest at scale | aws.amazon.com |

> **For a medical/healthcare portal in production**, Postmark or Mailgun are recommended over Gmail — they offer delivery tracking, bounce handling, and better inbox placement.

---

## Files Changed Summary

| File | What Changed |
|---|---|
| `User.php` | `sendVerificationEmail()` now uses PHPMailer instead of `mail()` |
| `config.php` | Add `MAIL_*` and `BASE_URL` constants |
| `vendor/` | Added by Composer (PHPMailer library) |

No changes needed to `register.php`, `login.php`, `verify-email.php`, or `resend-verification.php`.