<?php
/**
 * SessionManager Class
 * Handles secure session management
 * 
 * @package LuckyGeneMdx
 * @version 2.0
 */

class SessionManager {
    
    /**
     * Start a secure session
     * 
     * @return void
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Secure session configuration
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1'); // Requires HTTPS
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        
        session_start([
            'cookie_lifetime' => 0,
            'cookie_httponly' => true,
            'cookie_secure' => true,
            'use_strict_mode' => true,
            'sid_length' => 48,
            'sid_bits_per_character' => 6
        ]);
        
        // Regenerate session ID on first start
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
            $_SESSION['created_at'] = time();
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        
        // Validate session
        self::validateSession();
    }
    
    /**
     * Validate current session for security
     * 
     * @return bool
     */
    public static function validateSession(): bool {
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > SESSION_TIMEOUT) {
                self::destroy();
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
        
        // Validate user agent hasn't changed (prevent session hijacking)
        if (isset($_SESSION['user_agent'])) {
            $current_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if ($_SESSION['user_agent'] !== $current_agent) {
                self::destroy();
                return false;
            }
        }
        
        // Validate IP address hasn't changed (optional, can break legitimate roaming)
        if (ENVIRONMENT === 'production' && isset($_SESSION['ip_address'])) {
            $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
            if ($_SESSION['ip_address'] !== $current_ip) {
                self::destroy();
                return false;
            }
        }
        
        // Regenerate session ID periodically (every 30 minutes)
        if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at']) > 1800) {
            self::regenerate();
        }
        
        return true;
    }
    
    /**
     * Regenerate session ID
     * 
     * @return void
     */
    public static function regenerate(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $old_session_id = session_id();
            session_regenerate_id(true);
            $_SESSION['created_at'] = time();
            
            // Log regeneration for audit
            error_log("Session regenerated: {$old_session_id} -> " . session_id());
        }
    }
    
    /**
     * Destroy session securely
     * 
     * @return void
     */
    public static function destroy(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            session_destroy();
        }
    }
    
    /**
     * Set session variable
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session variable exists
     * 
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session variable
     * 
     * @param string $key
     * @return void
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    /**
     * Flash message - set message that will be deleted after next retrieval
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function flash(string $key, $value): void {
        $_SESSION['flash'][$key] = $value;
    }
    
    /**
     * Get and remove flash message
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getFlash(string $key, $default = null) {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
    
    /**
     * Check if user is authenticated (admin)
     * 
     * @return bool
     */
    public static function isAdminAuthenticated(): bool {
        return self::has('admin_id') && self::validateSession();
    }
    
    /**
     * Check if user is authenticated (patient)
     * 
     * @return bool
     */
    public static function isPatientAuthenticated(): bool {
        return self::has('user_id') && self::validateSession();
    }
    
    /**
     * Require admin authentication or redirect
     * 
     * @param string $redirectUrl
     * @return void
     */
    public static function requireAdmin(string $redirectUrl = '/admin/login.php'): void {
        if (!self::isAdminAuthenticated()) {
            self::flash('error', 'Please login to continue');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Require patient authentication or redirect
     * 
     * @param string $redirectUrl
     * @return void
     */
    public static function requirePatient(string $redirectUrl = '/patient-portal/login.php'): void {
        if (!self::isPatientAuthenticated()) {
            self::flash('error', 'Please login to continue');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}
