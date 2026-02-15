<?php
/**
 * Validator Class
 * Input validation and sanitization
 * 
 * @package LuckyGeneMdx
 * @version 2.0
 */

class Validator {
    
    /**
     * Validate email address
     * 
     * @param string $email
     * @return bool
     */
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     * 
     * @param string $phone
     * @return bool
     */
    public static function phone(string $phone): bool {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
    
    /**
     * Validate date in Y-m-d format
     * 
     * @param string $date
     * @return bool
     */
    public static function date(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Validate age (date of birth)
     * 
     * @param string $dob
     * @param int $minAge
     * @param int $maxAge
     * @return bool
     */
    public static function age(string $dob, int $minAge = 18, int $maxAge = 120): bool {
        if (!self::date($dob)) {
            return false;
        }
        
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        
        return $age >= $minAge && $age <= $maxAge;
    }
    
    /**
     * Validate password strength
     * 
     * @param string $password
     * @param int $minLength
     * @return bool
     */
    public static function password(string $password, int $minLength = 8): bool {
        if (strlen($password) < $minLength) {
            return false;
        }
        
        // Check for at least one uppercase, lowercase, number
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        
        return $hasUpper && $hasLower && $hasNumber;
    }
    
    /**
     * Sanitize string input
     * 
     * @param string $input
     * @return string
     */
    public static function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate file upload
     * 
     * @param array $file $_FILES array element
     * @param array $allowedTypes
     * @param int $maxSize
     * @return array [success, message]
     */
    public static function file(array $file, array $allowedTypes = ['pdf'], int $maxSize = 5242880): array {
        if (!isset($file['error']) || is_array($file['error'])) {
            return [false, 'Invalid file upload'];
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [false, 'File size exceeds limit'];
            case UPLOAD_ERR_NO_FILE:
                return [false, 'No file uploaded'];
            default:
                return [false, 'Upload error occurred'];
        }
        
        if ($file['size'] > $maxSize) {
            return [false, 'File too large. Maximum ' . ($maxSize / 1024 / 1024) . 'MB'];
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        $validMimes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        
        $allowed = [];
        foreach ($allowedTypes as $type) {
            if (isset($validMimes[$type])) {
                $allowed[] = $validMimes[$type];
            }
        }
        
        if (!in_array($mimeType, $allowed)) {
            return [false, 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes)];
        }
        
        return [true, 'Valid file'];
    }
    
    /**
     * Validate order number format
     * 
     * @param string $orderNumber
     * @return bool
     */
    public static function orderNumber(string $orderNumber): bool {
        return preg_match('/^LGM-\d{4}-\d{5}$/', $orderNumber);
    }
    
    /**
     * Validate ZIP code
     * 
     * @param string $zip
     * @return bool
     */
    public static function zipCode(string $zip): bool {
        return preg_match('/^\d{5}(-\d{4})?$/', $zip);
    }
}
