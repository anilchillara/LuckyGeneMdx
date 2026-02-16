<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Sanitize input data
     */
    private function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     */
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    private function validatePhone($phone) {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Check if it's 10 digits
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
    
    /**
     * Validate date
     */
    private function validateDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        try {
            $sql = "SELECT user_id FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Don't sanitize yet - we need raw data for validation
            
            // Validate inputs
            if (!isset($data['email']) || !$this->validateEmail($data['email'])) {
                return ['success' => false, 'message' => 'Invalid email address', 'user_id' => null];
            }
            
            if (!isset($data['phone']) || !$this->validatePhone($data['phone'])) {
                return ['success' => false, 'message' => 'Invalid phone number', 'user_id' => null];
            }
            
            if (!isset($data['dob']) || !$this->validateDate($data['dob'])) {
                return ['success' => false, 'message' => 'Invalid date of birth', 'user_id' => null];
            }
            
            if (!isset($data['password']) || strlen($data['password']) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters', 'user_id' => null];
            }
            
            if (!isset($data['full_name']) || empty(trim($data['full_name']))) {
                return ['success' => false, 'message' => 'Full name is required', 'user_id' => null];
            }
            
            // Check if email already exists
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'message' => 'Email already registered. Please <a href="login.php">login</a> instead.', 'user_id' => null];
            }
            
            // Now sanitize for database
            $email = $this->sanitize($data['email']);
            $full_name = $this->sanitize($data['full_name']);
            $phone = $this->sanitize($data['phone']);
            $dob = $this->sanitize($data['dob']);
            
            // Hash password (don't sanitize password - use as-is)
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (email, password_hash, full_name, phone, dob, created_at, is_active) 
                    VALUES (:email, :password_hash, :full_name, :phone, :dob, NOW(), 1)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':dob' => $dob
            ]);
            
            if ($result) {
                $user_id = $this->db->lastInsertId();
                error_log("User registered successfully: $email (ID: $user_id)");
                
                return [
                    'success' => true, 
                    'message' => 'Registration successful',
                    'user_id' => $user_id
                ];
            } else {
                error_log("Registration failed for: $email");
                return ['success' => false, 'message' => 'Registration failed. Please try again.', 'user_id' => null];
            }
            
        } catch(PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage(), 'user_id' => null];
        } catch(Exception $e) {
            error_log("Registration Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage(), 'user_id' => null];
        }
    }
    
    /**
     * Login with email and password
     */
    public function login($email, $password) {
        $email = $this->sanitize($email);
        
        try {
            $sql = "SELECT user_id, email, password_hash, full_name, is_active FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive. Please contact support.'];
            }
            
            if (password_verify($password, $user['password_hash'])) {
                // Update last login
                $this->updateLastLogin($user['user_id']);
                
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['last_activity'] = time();
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                return ['success' => true, 'user' => $user, 'message' => 'Login successful'];
            } else {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Login with order ID and password
     */
    public function loginWithOrderId($order_number, $password) {
        $order_number = $this->sanitize($order_number);
        
        try {
            $sql = "SELECT u.user_id, u.email, u.password_hash, u.full_name, u.is_active
                    FROM users u 
                    INNER JOIN orders o ON u.user_id = o.user_id 
                    WHERE o.order_number = :order_number";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_number' => $order_number]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid order number or password'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive'];
            }
            
            if (password_verify($password, $user['password_hash'])) {
                $this->updateLastLogin($user['user_id']);
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['last_activity'] = time();
                
                session_regenerate_id(true);
                
                return ['success' => true, 'user' => $user, 'message' => 'Login successful'];
            } else {
                return ['success' => false, 'message' => 'Invalid order number or password'];
            }
            
        } catch(PDOException $e) {
            error_log("Order Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($user_id) {
        try {
            $sql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
        } catch(PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        try {
            $sql = "SELECT user_id, email, full_name, phone, dob, created_at, last_login, is_active
                    FROM users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($user_id, $data) {
        $data = $this->sanitize($data);
        
        try {
            $sql = "UPDATE users SET full_name = :full_name, phone = :phone WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':full_name' => $data['full_name'],
                ':phone' => $data['phone'],
                ':user_id' => $user_id
            ]);
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
            
        } catch(PDOException $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Update failed. Please try again.'];
        }
    }
    
    /**
     * Change password
     */
    public function changePassword($user_id, $current_password, $new_password) {
        try {
            $sql = "SELECT password_hash FROM users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            if (!password_verify($current_password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            if (strlen($new_password) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters'];
            }
            
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password_hash = :password_hash WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':password_hash' => $new_hash,
                ':user_id' => $user_id
            ]);
            
            return ['success' => true, 'message' => 'Password changed successfully'];
            
        } catch(PDOException $e) {
            error_log("Password Change Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Password change failed. Please try again.'];
        }
    }
}