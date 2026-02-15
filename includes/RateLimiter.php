<?php
/**
 * RateLimiter Class
 * Prevents brute force attacks and excessive requests
 * 
 * @package LuckyGeneMdx
 * @version 2.0
 */

class RateLimiter {
    private static $storePath;
    
    /**
     * Initialize rate limiter
     */
    public static function init(): void {
        self::$storePath = __DIR__ . '/../logs/rate_limits/';
        if (!is_dir(self::$storePath)) {
            mkdir(self::$storePath, 0755, true);
        }
    }
    
    /**
     * Check if rate limit exceeded
     * 
     * @param string $identifier Unique identifier (IP, email, etc)
     * @param int $limit Maximum attempts
     * @param int $window Time window in seconds
     * @return bool True if within limits, false if exceeded
     */
    public static function check(string $identifier, int $limit = 5, int $window = 900): bool {
        self::init();
        
        $key = md5($identifier);
        $file = self::$storePath . $key . '.json';
        
        $attempts = self::getAttempts($file);
        $now = time();
        
        // Remove old attempts outside window
        $attempts = array_filter($attempts, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        // Check if limit exceeded
        if (count($attempts) >= $limit) {
            return false;
        }
        
        // Record new attempt
        $attempts[] = $now;
        file_put_contents($file, json_encode($attempts));
        
        return true;
    }
    
    /**
     * Record failed attempt
     * 
     * @param string $identifier
     * @return int Number of attempts
     */
    public static function recordAttempt(string $identifier): int {
        self::init();
        
        $key = md5($identifier);
        $file = self::$storePath . $key . '.json';
        
        $attempts = self::getAttempts($file);
        $attempts[] = time();
        
        file_put_contents($file, json_encode($attempts));
        
        return count($attempts);
    }
    
    /**
     * Get remaining attempts
     * 
     * @param string $identifier
     * @param int $limit
     * @param int $window
     * @return int
     */
    public static function remaining(string $identifier, int $limit = 5, int $window = 900): int {
        self::init();
        
        $key = md5($identifier);
        $file = self::$storePath . $key . '.json';
        
        $attempts = self::getAttempts($file);
        $now = time();
        
        $attempts = array_filter($attempts, function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        return max(0, $limit - count($attempts));
    }
    
    /**
     * Reset rate limit for identifier
     * 
     * @param string $identifier
     * @return void
     */
    public static function reset(string $identifier): void {
        self::init();
        
        $key = md5($identifier);
        $file = self::$storePath . $key . '.json';
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Get attempts from file
     * 
     * @param string $file
     * @return array
     */
    private static function getAttempts(string $file): array {
        if (!file_exists($file)) {
            return [];
        }
        
        $content = file_get_contents($file);
        $attempts = json_decode($content, true);
        
        return is_array($attempts) ? $attempts : [];
    }
    
    /**
     * Clean old rate limit files
     * 
     * @param int $days
     * @return void
     */
    public static function cleanup(int $days = 1): void {
        self::init();
        
        $files = glob(self::$storePath . '*.json');
        $now = time();
        
        foreach ($files as $file) {
            if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                unlink($file);
            }
        }
    }
}
