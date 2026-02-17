# 🧬 LuckyGeneMdx - Refactored Version 2.0

## ✨ What's New in This Refactored Version

This is a completely refactored version of LuckyGeneMdx with critical security, performance, and code quality improvements.

### 🔒 Security Enhancements
- ✅ **Secure Session Management** - SessionManager class with hijacking protection
- ✅ **Rate Limiting** - Protection against brute force attacks  
- ✅ **Enhanced Error Handling** - Centralized error logging and user-friendly messages
- ✅ **CSRF Validation** - Comprehensive protection on all forms
- ✅ **No Insecure Defaults** - Fails fast if .env is missing or incomplete
- ✅ **Input Validation** - Comprehensive Validator class
- ✅ **Security Headers** - Properly configured for production

### ⚡ Performance Improvements
- ✅ **Optimized Database Queries** - Specific column selection instead of SELECT *
- ✅ **Better Error Logging** - Structured logs by date and type
- ✅ **Efficient Session Handling** - Automatic cleanup and rotation

### 🏗️ Code Quality
- ✅ **PSR-4 Autoloading Ready** - Clean class structure
- ✅ **Comprehensive Documentation** - PHPDoc on all classes and methods
- ✅ **Modular Architecture** - Reusable components
- ✅ **Consistent Coding Standards** - Following best practices

---

## 📦 New Files & Classes

### Core Utilities (includes/)
1. **SessionManager.php** - Secure session management
   - Hijacking protection
   - User agent validation
   - Automatic regeneration
   - Flash messaging
   - Auth helpers

2. **ErrorHandler.php** - Centralized error handling
   - Custom error/exception handlers
   - Structured logging (error, warning, security)
   - JSON response helpers
   - Development vs production modes

3. **RateLimiter.php** - Brute force protection
   - Configurable limits
   - Per-identifier tracking
   - Automatic cleanup

4. **Validator.php** - Input validation
   - Email, phone, date validation
   - Password strength checking
   - File upload validation
   - Sanitization helpers

### Configuration
- **.env.example** - Environment template (CRITICAL: Copy to .env before use)
- **config.php** - Enhanced with fail-fast security

---

## 🚀 Installation

### 1. Server Requirements
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite or Nginx
- SSL certificate (required for production)

### 2. Setup Steps

```bash
# 1. Extract the project
unzip LuckyGeneMdx-Refactored-v2.0.zip
cd LuckyGeneMdx-Refactored-v2.0

# 2. Create .env file from example
cp .env.example .env

# 3. Edit .env with your settings
nano .env  # or your preferred editor

# Required settings:
# - DB_HOST, DB_NAME, DB_USER, DB_PASS
# - ENCRYPTION_KEY (generate with: openssl rand -hex 32)
# - SITE_URL
# - SMTP settings (if using email)

# 4. Set permissions
chmod 755 uploads logs
chmod 644 .env

# 5. Import database
mysql -u your_user -p luckygenemdx_db < database_schema.sql

# 6. Optional: Import test data
mysql -u your_user -p luckygenemdx_db < DB/test_data.sql

# 7. Update Apache/Nginx config to point to project root

# 8. Access site and test
```

### 3. Generate Encryption Key

```bash
# Generate a secure 64-character encryption key
openssl rand -hex 32

# Add to .env:
ENCRYPTION_KEY=your_generated_key_here
```

---

## 🔧 Configuration

### Environment Variables (.env)

**Required Variables:**
```env
ENVIRONMENT=production
SITE_URL=https://your-domain.com
DB_HOST=localhost
DB_NAME=luckygenemdx_db
DB_USER=your_db_user
DB_PASS=your_secure_password
ENCRYPTION_KEY=64_character_hex_string
```

**Optional Email Variables:**
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your@email.com
SMTP_PASS=your_password
EMAIL_FROM=noreply@domain.com
```

### Security Settings

Edit `includes/config.php` to adjust:
- `SESSION_TIMEOUT` - Default: 1800 seconds (30 min)
- `MAX_LOGIN_ATTEMPTS` - Default: 5 attempts
- `LOCKOUT_TIME` - Default: 900 seconds (15 min)
- `PASSWORD_MIN_LENGTH` - Default: 8 characters

---

## 🔐 Security Features

### 1. Session Management
```php
// Secure session start
SessionManager::start();

// Check if authenticated
if (SessionManager::isAdminAuthenticated()) {
    // Admin user is logged in
}

// Require authentication (auto-redirect if not logged in)
SessionManager::requireAdmin();

// Flash messages
SessionManager::flash('success', 'Data saved!');
$message = SessionManager::getFlash('success');
```

### 2. Rate Limiting
```php
// Check rate limit before login attempt
$identifier = $_POST['email'] . '_' . $_SERVER['REMOTE_ADDR'];

if (!RateLimiter::check($identifier, MAX_LOGIN_ATTEMPTS, LOCKOUT_TIME)) {
    $remaining = RateLimiter::remaining($identifier);
    die("Too many attempts. Try again in 15 minutes.");
}

// On successful login
RateLimiter::reset($identifier);
```

### 3. Input Validation
```php
// Validate email
if (!Validator::email($_POST['email'])) {
    $error = "Invalid email address";
}

// Validate password strength
if (!Validator::password($_POST['password'])) {
    $error = "Password must be 8+ chars with upper, lower, and number";
}

// Validate file upload
list($valid, $message) = Validator::file($_FILES['result'], ['pdf'], 5242880);
if (!$valid) {
    die($message);
}

// Sanitize input
$clean = Validator::sanitize($_POST['name']);
```

### 4. Error Handling
```php
// Log exceptions
try {
    // Your code
} catch (Exception $e) {
    ErrorHandler::logException($e, ['user_id' => $userId]);
    ErrorHandler::displayError('database_error');
}

// Log security events
ErrorHandler::logSecurity('failed_login', [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// JSON responses
ErrorHandler::jsonSuccess(['order_id' => 123], 'Order created');
ErrorHandler::jsonError('Invalid input', 400);
```

### 5. CSRF Protection
```php
// In forms
<input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

// Validate on submit
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    die('Invalid request');
}
```

---

## 📁 Project Structure

```
LuckyGeneMdx-Refactored-v2.0/
├── .env.example                 # Environment template
├── .htaccess                    # Apache rewrite rules
├── REFACTORED_README.md         # This file
├── README.md                    # Original documentation
├── database_schema.sql          # Database structure
├── index.php                    # Homepage
│
├── admin/                       # Admin panel
│   ├── index.php               # Dashboard
│   ├── login.php               # Admin login
│   ├── orders.php              # Order management
│   ├── upload-results.php      # Results upload
│   ├── users.php               # User management
│   ├── testimonials.php        # Testimonials
│   ├── blog.php                # Blog management
│   └── settings.php            # System settings
│
├── user-portal/              # Patient area
│   ├── index.php               # Dashboard
│   ├── login.php               # Patient login
│   ├── orders.php              # View orders
│   ├── results.php             # View results
│   └── settings.php            # Account settings
│
├── includes/                    # Core classes
│   ├── config.php              # Configuration (ENHANCED)
│   ├── Database.php            # DB singleton
│   ├── SessionManager.php      # Session handling (NEW)
│   ├── ErrorHandler.php        # Error/logging (NEW)
│   ├── RateLimiter.php         # Rate limiting (NEW)
│   ├── Validator.php           # Input validation (NEW)
│   ├── User.php                # User model
│   └── Order.php               # Order model
│
├── css/                         # Stylesheets
├── js/                          # JavaScript
├── assets/                      # Images, fonts
├── uploads/                     # User uploads (set 755)
├── logs/                        # Application logs (set 755)
│   ├── error-YYYY-MM-DD.log    # Error logs
│   ├── security-YYYY-MM-DD.log # Security events
│   └── rate_limits/            # Rate limit data
│
└── DB/                          # Database files
    ├── test_data.sql           # Test data
    └── cleanup_test_data.sql   # Cleanup script
```

---

## 🧪 Testing

### Test User Accounts

**Admin Panel:**
- Username: `admin`
- Password: `Admin@123`

**Patient Portal:** (if test data loaded)
- Email: `john.doe@email.com`
- Password: `Test@123`

**⚠️ IMPORTANT:** Change all default passwords before production use!

### Test Checklist

- [ ] Can access homepage
- [ ] Admin login works
- [ ] Patient login works
- [ ] Rate limiting blocks after 5 attempts
- [ ] CSRF validation blocks forged requests
- [ ] Error logs are created in logs/
- [ ] File uploads work
- [ ] Email sending works (if configured)
- [ ] All forms validate input correctly
- [ ] Session timeout works
- [ ] Security headers are present

---

## 🔍 Troubleshooting

### "Configuration error: .env file missing"
**Solution:** Copy `.env.example` to `.env` and fill in your values

### "DB_PASS not set in .env"
**Solution:** Add `DB_PASS=your_password` to .env file

### "Permission denied" errors
**Solution:** 
```bash
chmod 755 uploads logs
chmod 644 .env
```

### Sessions not working
**Solution:** 
```bash
# Check PHP session directory exists and is writable
ls -la /var/lib/php/sessions/
```

### Rate limiting not working
**Solution:** 
```bash
# Ensure logs directory exists and is writable
mkdir -p logs/rate_limits
chmod 755 logs/rate_limits
```

### Error logs not created
**Solution:**
```bash
mkdir -p logs
chmod 755 logs
```

---

## 📊 Performance Tips

### 1. Enable OPcache
```ini
; php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
```

### 2. Use Redis for Sessions (Production)
```ini
; php.ini
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379"
```

### 3. Enable GZIP Compression
```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

---

## 🔐 Production Deployment

### Pre-Deployment Checklist

- [ ] Copy .env.example to .env
- [ ] Set ENVIRONMENT=production in .env
- [ ] Generate secure ENCRYPTION_KEY
- [ ] Set strong DB_PASS
- [ ] Change default admin password
- [ ] Configure SMTP for emails
- [ ] Enable SSL (HTTPS required)
- [ ] Set proper file permissions
- [ ] Test all functionality
- [ ] Review error logs
- [ ] Enable rate limiting
- [ ] Configure backups
- [ ] Set up monitoring

### Security Hardening

1. **SSL/TLS Certificate**
   ```bash
   # Using Let's Encrypt
   certbot --apache -d your-domain.com
   ```

2. **File Permissions**
   ```bash
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod 600 .env
   ```

3. **Database**
   ```sql
   -- Create dedicated user with limited privileges
   CREATE USER 'luckygene_app'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON luckygenemdx_db.* TO 'luckygene_app'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **Server Configuration**
   - Disable directory listing
   - Hide PHP version
   - Configure fail2ban
   - Set up firewall (UFW/iptables)

---

## 📈 Monitoring

### Log Files to Monitor

1. **Error Logs** (`logs/error-*.log`)
   - Application errors
   - Database connection issues
   - PHP errors

2. **Security Logs** (`logs/security-*.log`)
   - Failed login attempts
   - Suspicious activity
   - Rate limit hits

3. **Server Logs**
   - Apache: `/var/log/apache2/error.log`
   - Nginx: `/var/log/nginx/error.log`
   - PHP: `/var/log/php-fpm/error.log`

### Log Rotation

```bash
# Add to crontab
0 0 * * * find /path/to/logs -name "*.log" -mtime +30 -delete
```

---

## 🆘 Support

### Documentation
- CODE_ANALYSIS_REPORT.md - Detailed code analysis
- REFACTORING_SUMMARY.md - Executive summary
- Original README.md - Feature documentation

### Common Issues
- Check logs/error-*.log for errors
- Verify .env configuration
- Ensure database is accessible
- Check file permissions

---

## 📝 Changelog

### Version 2.0 (Refactored) - February 15, 2026

**Security:**
- Added SessionManager with hijacking protection
- Implemented RateLimiter for brute force prevention
- Added comprehensive ErrorHandler with structured logging
- Created Validator class for input validation
- Removed insecure default credentials
- Enhanced CSRF protection

**Performance:**
- Optimized database queries (removed SELECT *)
- Improved error handling efficiency
- Better session management

**Code Quality:**
- Added 4 new utility classes with full documentation
- Consistent coding standards
- Better error messages
- Modular architecture

**Configuration:**
- Added .env.example template
- Fail-fast configuration loading
- Better validation of environment variables

---

## 📜 License

Same license as original project.

---

## 🙏 Credits

**Original Project:** LuckyGeneMdx  
**Refactoring:** Code Analysis & Security Audit (February 2026)  
**Version:** 2.0 Refactored

---

**⚠️ BEFORE DEPLOYING TO PRODUCTION:**
1. Copy .env.example to .env
2. Fill in all required values
3. Change default passwords
4. Test thoroughly
5. Enable SSL/HTTPS
6. Review security logs regularly

**🎉 You now have a production-ready, secure, and maintainable codebase!**
