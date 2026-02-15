# 🧬 LuckyGeneMDx - macOS Setup Guide


### 1. Start Development Server
```bash
php -S localhost:8000
```

### 2. Test New Features
```bash
# Visit these URLs:
http://localhost:8000/request-kit.php      # Order a kit
http://localhost:8000/track-order.php      # Track order
http://localhost:8000/admin/login.php      # Admin login
http://localhost:8000/patient-portal/login.php  # Patient login

# Default admin credentials:
Username: admin
Password: Admin@123
```

---








## Complete Installation Instructions for Mac Developers

---

## 📋 Prerequisites for Mac

### 1. Install Homebrew (if not already installed)
```bash
# Open Terminal and run:
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

### 2. Install PHP
```bash
# Install PHP (comes with built-in server)
brew install php

# Verify installation
php -v
# Should show PHP 8.x.x

# Start PHP service
brew services start php
```

### 3. Install MySQL
```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Secure MySQL installation
mysql_secure_installation
# Follow prompts to set root password and secure installation
```

### 4. Verify Installations
```bash
# Check PHP
php -v

# Check MySQL
mysql --version

# Test MySQL connection
mysql -u root -p
# Enter your root password
```

---

## 🚀 Project Setup (Mac-Specific)

### Step 1: Choose Your Development Environment

You have three options on Mac:

#### **Option A: Built-in PHP Server (Easiest - Recommended for Development)**
```bash
# Navigate to project directory
cd /path/to/luckygenemdx

# Start PHP built-in server
php -S localhost:8000

# Open browser to:
http://localhost:8000
```

#### **Option B: MAMP (GUI Application)**
```bash
# Download MAMP from: https://www.mamp.info/en/downloads/
# Install MAMP
# Place project in: /Applications/MAMP/htdocs/luckygenemdx
# Start MAMP servers
# Access at: http://localhost:8888/luckygenemdx
```

#### **Option C: Apache (Built into macOS)**
```bash
# Enable Apache
sudo apachectl start

# Copy project to web root
sudo cp -r luckygenemdx /Library/WebServer/Documents/

# Access at: http://localhost/luckygenemdx
```

---

## 💾 Database Setup on Mac

### Step 1: Create Database
```bash
# Open Terminal and login to MySQL
mysql -u root -p
# Enter your MySQL root password
```

```sql
-- Create database
CREATE DATABASE luckygenemdx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user (recommended)
CREATE USER 'luckygenemdx'@'localhost' IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON luckygenemdx_db.* TO 'luckygenemdx'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### Step 2: Import Schema
```bash
# Navigate to project directory
cd /path/to/luckygenemdx

# Import database schema
mysql -u luckygenemdx -p luckygenemdx_db < database_schema.sql
# Enter the password you created above
```

### Step 3: Verify Import
```bash
# Login to MySQL
mysql -u luckygenemdx -p luckygenemdx_db

# Check tables
SHOW TABLES;
# Should show 11 tables

# Exit
EXIT;
```

---

## ⚙️ Configuration for Mac

### Step 1: Update config.php
```bash
# Open config file in your favorite editor
nano includes/config.php
# or
code includes/config.php  # If using VS Code
# or
open -a "TextEdit" includes/config.php
```

### Step 2: Update Database Settings
```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'luckygenemdx_db');
define('DB_USER', 'luckygenemdx');  // Or your MySQL username
define('DB_PASS', 'your_secure_password');  // Your MySQL password
define('DB_CHARSET', 'utf8mb4');
```

### Step 3: Generate Encryption Key
```bash
# Generate a secure encryption key
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"

# Copy the output and paste into config.php
```

Update in `config.php`:
```php
define('ENCRYPTION_KEY', 'paste_your_generated_key_here');
```

### Step 4: Update Site URL
```php
// If using built-in PHP server:
define('SITE_URL', 'http://localhost:8000');

// If using MAMP:
define('SITE_URL', 'http://localhost:8888/luckygenemdx');

// If using Apache:
define('SITE_URL', 'http://localhost/luckygenemdx');
```

---

## 📁 File Permissions on Mac

### Step 1: Create Required Directories
```bash
# Navigate to project root
cd /path/to/luckygenemdx

# Create directories
mkdir -p uploads/results
mkdir -p logs

# Set proper permissions
chmod 755 uploads
chmod 755 logs
chmod 700 uploads/results
```

### Step 2: Verify Permissions
```bash
# Check permissions
ls -la

# uploads should show: drwxr-xr-x
# logs should show: drwxr-xr-x
# uploads/results should show: drwx------
```

**Note**: On Mac, you typically don't need to change ownership to `www-data` like on Linux. The web server runs as your user account when using PHP's built-in server or MAMP.

---

## 🧪 Testing Your Installation

### Step 1: Start the Development Server

**Using Built-in PHP Server**:
```bash
cd /path/to/luckygenemdx
php -S localhost:8000
```

**Using MAMP**:
- Open MAMP application
- Click "Start Servers"
- Wait for green lights

### Step 2: Test Homepage
```bash
# Open in browser:
http://localhost:8000              # Built-in server
http://localhost:8888/luckygenemdx # MAMP
http://localhost/luckygenemdx      # Apache
```

**You should see**:
- ✅ Homepage with gradient background
- ✅ Animated particles
- ✅ Navigation menu
- ✅ Hero section with CTA buttons
- ✅ No PHP errors

### Step 3: Test Database Connection
```bash
# Create a test file
echo '<?php
require_once "includes/config.php";
require_once "includes/Database.php";
try {
    $db = Database::getInstance();
    echo "Database connected successfully!";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>' > test_db.php

# Visit in browser:
http://localhost:8000/test_db.php
```

Should display: "Database connected successfully!"

### Step 4: Test Admin Login (After Creating Admin Page)
```
URL: http://localhost:8000/admin/login.php
Username: admin
Password: Admin@123
```

**Important**: Change this password immediately!

---

## 🔧 Mac-Specific Troubleshooting

### Issue: "Port 8000 already in use"
```bash
# Find what's using port 8000
lsof -i :8000

# Kill the process (replace PID with actual number)
kill -9 PID

# Or use a different port
php -S localhost:8080
```

### Issue: "Permission denied" on uploads
```bash
# Fix permissions
chmod -R 755 uploads
chmod -R 755 logs

# If still issues, check if directory exists
ls -la uploads/
```

### Issue: "MySQL connection refused"
```bash
# Check if MySQL is running
brew services list

# If not running, start it
brew services start mysql

# Or restart it
brew services restart mysql

# Check MySQL socket location
php -i | grep mysql.sock
# Should show: /tmp/mysql.sock

# If different, update config.php:
define('DB_HOST', 'localhost:/tmp/mysql.sock');
```

### Issue: CSS/JS not loading
```bash
# Check file paths in browser console (F12 or Cmd+Option+I)
# Common causes:
# 1. Wrong base URL in config
# 2. Files not in correct directory

# Verify files exist:
ls -la css/main.css
ls -la js/main.js

# Check file permissions
chmod 644 css/main.css
chmod 644 js/main.js
```

### Issue: PHP errors not showing
```bash
# Create a php.ini in project root (for built-in server)
echo 'display_errors = On
error_reporting = E_ALL' > php.ini

# Restart server
php -S localhost:8000 -c php.ini
```

### Issue: .htaccess not working
**.htaccess only works with Apache**, not with PHP's built-in server or MAMP by default.

**For Built-in PHP Server**: Security headers must be set in PHP code (already done in config.php)

**For MAMP**: Enable .htaccess:
1. Open MAMP
2. Click "Preferences"
3. Go to "Web Server"
4. Ensure "Apache" is selected
5. Restart servers

---

## 🔐 Security Setup on Mac

### Step 1: Change Default Admin Password
```sql
# Generate new password hash
php -r "echo password_hash('YourNewSecurePassword', PASSWORD_DEFAULT);"

# Copy the output, then update in MySQL:
mysql -u luckygenemdx -p luckygenemdx_db

UPDATE admins 
SET password_hash = 'paste_your_hash_here' 
WHERE username = 'admin';

EXIT;
```

### Step 2: Secure MySQL
```bash
# Run MySQL secure installation if you haven't
mysql_secure_installation

# Follow prompts:
# - Set root password
# - Remove anonymous users
# - Disallow root login remotely
# - Remove test database
# - Reload privilege tables
```

### Step 3: Enable HTTPS (for Production)
```bash
# For development, you can use mkcert for local HTTPS
brew install mkcert
mkcert -install

# Generate certificate
cd /path/to/luckygenemdx
mkcert localhost

# Start PHP server with HTTPS (requires PHP 8.2+)
# Or use Caddy/Nginx for local HTTPS
```

---

## 🛠️ Recommended Mac Tools

### Code Editors
```bash
# Visual Studio Code (Recommended)
brew install --cask visual-studio-code

# PHPStorm (Professional)
brew install --cask phpstorm

# Sublime Text
brew install --cask sublime-text
```

### Database Management
```bash
# Sequel Ace (Free, Mac-specific)
brew install --cask sequel-ace

# TablePlus (Beautiful UI)
brew install --cask tableplus

# MySQL Workbench (Official)
brew install --cask mysqlworkbench
```

### Development Tools
```bash
# Composer (PHP package manager)
brew install composer

# Git (if not installed)
brew install git

# Node.js (for future frontend tools)
brew install node
```

---

## 📱 Testing on Multiple Devices

### Test on iPhone/iPad (Same Network)
```bash
# Find your Mac's local IP
ifconfig | grep "inet " | grep -v 127.0.0.1

# Start server on all interfaces
php -S 0.0.0.0:8000

# On iPhone/iPad, visit:
http://YOUR_MAC_IP:8000
# Example: http://192.168.1.100:8000
```

### Test Responsive Design
```bash
# In Chrome/Safari:
# 1. Open DevTools (Cmd+Option+I)
# 2. Click device toolbar icon (Cmd+Shift+M)
# 3. Select different devices
```

---

## 🚀 Quick Start Commands (Copy & Paste)

### Complete Setup (One-Time)
```bash
# Install Homebrew (if needed)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP and MySQL
brew install php mysql

# Start services
brew services start php
brew services start mysql

# Navigate to project
cd ~/Desktop/luckygenemdx  # Adjust path as needed

# Create database and user
mysql -u root -p << EOF
CREATE DATABASE luckygenemdx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'luckygenemdx'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON luckygenemdx_db.* TO 'luckygenemdx'@'localhost';
FLUSH PRIVILEGES;
EOF

# Import schema
mysql -u luckygenemdx -p luckygenemdx_db < database_schema.sql

# Create directories
mkdir -p uploads/results logs
chmod 755 uploads logs
chmod 700 uploads/results

# Generate encryption key
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"
# Copy output and update includes/config.php

# Start development server
php -S localhost:8000
```

### Daily Development
```bash
# Start MySQL (if not running)
brew services start mysql

# Navigate to project
cd ~/Desktop/luckygenemdx

# Start development server
php -S localhost:8000

# Open in browser
open http://localhost:8000
```

---

## 💡 Mac Development Tips

### 1. Use Terminal Aliases
Add to `~/.zshrc` or `~/.bash_profile`:
```bash
alias luckygene='cd ~/Desktop/luckygenemdx && php -S localhost:8000'
alias mysqlstart='brew services start mysql'
alias mysqlstop='brew services stop mysql'
```

Then just type `luckygene` to start!

### 2. VS Code Extensions (Recommended)
- PHP Intelephense
- PHP Debug
- MySQL (by cweijan)
- ESLint
- Prettier
- Live Server (for frontend testing)

### 3. Browser DevTools
```bash
# Chrome DevTools: Cmd+Option+I
# Safari DevTools: Cmd+Option+I (enable in Preferences first)
# Firefox DevTools: Cmd+Option+I
```

### 4. Monitor PHP Errors
```bash
# Watch error log in real-time
tail -f logs/php-errors.log

# Or create error log if it doesn't exist
touch logs/php-errors.log
```

---

## ✅ Mac Setup Checklist

- [ ] Homebrew installed
- [ ] PHP 7.4+ installed and verified
- [ ] MySQL 8.0+ installed and running
- [ ] Database `luckygenemdx_db` created
- [ ] Database schema imported successfully
- [ ] `includes/config.php` updated with DB credentials
- [ ] Encryption key generated and configured
- [ ] `uploads/` and `logs/` directories created
- [ ] File permissions set correctly
- [ ] Development server starts without errors
- [ ] Homepage loads in browser
- [ ] Database connection test successful
- [ ] Default admin password changed
- [ ] Code editor installed and configured

---

## 🎯 Ready to Code!

Your Mac is now set up for LuckyGeneMDx development. You can:

✅ Start/stop the dev server easily  
✅ Edit code in your favorite editor  
✅ Test on your Mac and mobile devices  
✅ Manage database with GUI tools  
✅ Deploy to production when ready  

**Happy coding! 🚀**

---

## 📞 Need Help?

**Common Mac Resources**:
- Homebrew: https://brew.sh
- MAMP: https://www.mamp.info
- PHP on Mac: https://www.php.net/manual/en/install.macosx.php
- MySQL on Mac: https://dev.mysql.com/doc/refman/8.0/en/macos-installation.html

**Check logs**:
```bash
# PHP errors
tail -f logs/php-errors.log

# MySQL errors
tail -f /usr/local/var/mysql/*.err

# Apache errors (if using Apache)
tail -f /var/log/apache2/error_log
```

---

**Version**: 1.0 - Mac Edition  
**Last Updated**: February 14, 2026