<?php
/**
 * ErrorHandler Class
 * Centralized error handling and logging
 * 
 * @package LuckyGeneMdx
 * @version 2.0
 */

class ErrorHandler {
    
    private static $logPath;
    private static $initialized = false;
    
    /**
     * Initialize error handler
     * 
     * @return void
     */
    public static function init(): void {
        if (self::$initialized) {
            return;
        }
        
        self::$logPath = __DIR__ . '/../../logs/';
        
        // Ensure log directory exists
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
        
        // Set custom error and exception handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
        
        self::$initialized = true;
    }
    
    /**
     * Handle PHP errors
     * 
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool {
        $error = [
            'type' => 'PHP Error',
            'severity' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'time' => date('Y-m-d H:i:s')
        ];
        
        self::logError($error);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception
     * @return void
     */
    public static function handleException(Throwable $exception): void {
        $error = [
            'type' => 'Uncaught Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'time' => date('Y-m-d H:i:s')
        ];
        
        self::logError($error);
        self::displayError('system_error');
    }
    
    /**
     * Handle fatal errors on shutdown
     * 
     * @return void
     */
    public static function handleShutdown(): void {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
            
            self::displayError('system_error');
        }
    }
    
    /**
     * Log error to file
     * 
     * @param array $error
     * @return void
     */
    private static function logError(array $error): void {
        $logFile = self::$logPath . 'error-' . date('Y-m-d') . '.log';
        $message = json_encode($error, JSON_PRETTY_PRINT) . PHP_EOL . str_repeat('-', 80) . PHP_EOL;
        
        error_log($message, 3, $logFile);
        
        // Also log to PHP error log
        error_log("[LuckyGeneMdx] {$error['type']}: {$error['message']} in {$error['file']}:{$error['line']}");
    }
    
    /**
     * Log exception
     * 
     * @param Throwable $exception
     * @param array $context
     * @return void
     */
    public static function logException(Throwable $exception, array $context = []): void {
        $error = [
            'type' => 'Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'time' => date('Y-m-d H:i:s'),
            'url' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
        ];
        
        self::logError($error);
    }
    
    /**
     * Log info message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logInfo(string $message, array $context = []): void {
        $logFile = self::$logPath . 'info-' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] INFO: %s %s\n",
            date('Y-m-d H:i:s'),
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        error_log($logMessage, 3, $logFile);
    }
    
    /**
     * Log warning message
     * 
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logWarning(string $message, array $context = []): void {
        $logFile = self::$logPath . 'warning-' . date('Y-m-d') . '.log';
        $logMessage = sprintf(
            "[%s] WARNING: %s %s\n",
            date('Y-m-d H:i:s'),
            $message,
            !empty($context) ? json_encode($context) : ''
        );
        
        error_log($logMessage, 3, $logFile);
    }
    
    /**
     * Log security event
     * 
     * @param string $event
     * @param array $details
     * @return void
     */
    public static function logSecurity(string $event, array $details = []): void {
        $logFile = self::$logPath . 'security-' . date('Y-m-d') . '.log';
        $logMessage = [
            'time' => date('Y-m-d H:i:s'),
            'event' => $event,
            'details' => $details,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'url' => $_SERVER['REQUEST_URI'] ?? 'N/A'
        ];
        
        error_log(json_encode($logMessage) . PHP_EOL, 3, $logFile);
    }
    
    /**
     * Display user-friendly error page
     * 
     * @param string $type
     * @param string|null $message
     * @return void
     */
    public static function displayError(string $type, ?string $message = null): void {
        $errors = [
            'database_error' => [
                'title' => 'Database Error',
                'message' => 'We\'re experiencing technical difficulties. Please try again later.',
                'code' => 500
            ],
            'not_found' => [
                'title' => 'Page Not Found',
                'message' => 'The page you\'re looking for doesn\'t exist.',
                'code' => 404
            ],
            'access_denied' => [
                'title' => 'Access Denied',
                'message' => 'You don\'t have permission to access this resource.',
                'code' => 403
            ],
            'system_error' => [
                'title' => 'System Error',
                'message' => 'An unexpected error occurred. Our team has been notified.',
                'code' => 500
            ],
            'validation_error' => [
                'title' => 'Validation Error',
                'message' => $message ?? 'Please check your input and try again.',
                'code' => 422
            ]
        ];
        
        $error = $errors[$type] ?? $errors['system_error'];
        
        if (ENVIRONMENT !== 'development') {
            http_response_code($error['code']);
            
            // Check if it's an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                self::jsonError($error['message'], $error['code']);
            } else {
                include __DIR__ . '/../../error/' . $error['code'] . '.php';
            }
        } else {
            // In development, show detailed error
            echo "<h1>{$error['title']}</h1>";
            echo "<p>{$error['message']}</p>";
            if ($message) {
                echo "<p><strong>Details:</strong> {$message}</p>";
            }
        }
        
        exit;
    }
    
    /**
     * Return JSON error response
     * 
     * @param string $message
     * @param int $code
     * @param array $details
     * @return void
     */
    public static function jsonError(string $message, int $code = 400, array $details = []): void {
        http_response_code($code);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'error' => $message,
            'code' => $code,
            'details' => $details
        ]);
        
        exit;
    }
    
    /**
     * Return JSON success response
     * 
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return void
     */
    public static function jsonSuccess($data = null, string $message = 'Success', int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code
        ]);
        
        exit;
    }
    
    /**
     * Clean up old log files
     * 
     * @param int $days Keep logs for this many days
     * @return void
     */
    public static function cleanOldLogs(int $days = 30): void {
        $files = glob(self::$logPath . '*.log');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                }
            }
        }
    }
}

// Initialize error handler
ErrorHandler::init();
