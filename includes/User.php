<?php
/**
 * User Model
 * Handles user authentication and management
 */

require_once 'Database.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function register($data) {
        $data = $this->sanitize($data);
        
        // Validate inputs
        if (!$this->validateEmail($data['email'])) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        
        if (!$this->validatePhone($data['phone'])) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }
        
        if (!$this->validateDate($data['dob'])) {
            return ['success' => false, 'message' => 'Invalid date of birth'];
        }
        
        // Check if email already exists
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO users (email, password_hash, full_name, phone, dob, created_at) 
                    VALUES (:email, :password_hash, :full_name, :phone, :dob, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $data['email'],
                ':password_hash' => $password_hash,
                ':full_name' => $data['full_name'],
                ':phone' => $data['phone'],
                ':dob' => $data['dob']
            ]);
            
            return [
                'success' => true, 
                'message' => 'Registration successful',
                'user_id' => $this->db->lastInsertId()
            ];
            
        } catch(PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    public function login($email, $password, $order_id = null) {
        $email = $this->sanitize($email);
        
        try {
            $sql = "SELECT user_id, email, password_hash, full_name FROM users WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                $this->updateLastLogin($user['user_id']);
                
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['last_activity'] = time();
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    public function loginWithOrderId($order_id, $password) {
        $order_id = $this->sanitize($order_id);
        
        try {
            $sql = "SELECT u.user_id, u.email, u.password_hash, u.full_name 
                    FROM users u 
                    INNER JOIN orders o ON u.user_id = o.user_id 
                    WHERE o.order_number = :order_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $this->updateLastLogin($user['user_id']);
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['last_activity'] = time();
                
                session_regenerate_id(true);
                
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
        } catch(PDOException $e) {
            error_log("Order Login Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    private function emailExists($email) {
        $sql = "SELECT user_id FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }
    
    private function updateLastLogin($user_id) {
        $sql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
    }
    
    public function getUserById($user_id) {
        $sql = "SELECT user_id, email, full_name, phone, dob, created_at, last_login 
                FROM users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch();
    }
    
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
    
    public function changePassword($user_id, $current_password, $new_password) {
        try {
            $sql = "SELECT password_hash FROM users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch();
            
            if (!password_verify($current_password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            if (strlen($new_password) < PASSWORD_MIN_LENGTH) {
                return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
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
