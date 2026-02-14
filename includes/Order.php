<?php
/**
 * Order Model
 * Handles order creation, tracking, and management
 */

require_once 'Database.php';

class Order extends BaseModel {
    protected $table = 'orders';
    
    public function createOrder($user_id, $data) {
        $data = $this->sanitize($data);
        
        // Generate unique order number
        $order_number = $this->generateOrderNumber();
        
        try {
            $sql = "INSERT INTO orders (user_id, order_number, shipping_address_line1, 
                    shipping_address_line2, shipping_city, shipping_state, shipping_zip, 
                    price, status_id, order_date, payment_status) 
                    VALUES (:user_id, :order_number, :address1, :address2, :city, :state, 
                    :zip, :price, 1, NOW(), 'pending')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':order_number' => $order_number,
                ':address1' => $data['address_line1'],
                ':address2' => $data['address_line2'] ?? '',
                ':city' => $data['city'],
                ':state' => $data['state'],
                ':zip' => $data['zip'],
                ':price' => KIT_PRICE
            ]);
            
            $order_id = $this->db->lastInsertId();
            
            // Send confirmation email (implement separately)
            $this->sendOrderConfirmation($order_id);
            
            return [
                'success' => true,
                'order_id' => $order_id,
                'order_number' => $order_number,
                'message' => 'Order created successfully'
            ];
            
        } catch(PDOException $e) {
            error_log("Order Creation Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Order creation failed. Please try again.'];
        }
    }
    
    private function generateOrderNumber() {
        $prefix = 'LGM';
        $timestamp = date('ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        return $prefix . $timestamp . $random;
    }
    
    public function getOrderByNumber($order_number) {
        $order_number = $this->sanitize($order_number);
        
        try {
            $sql = "SELECT o.*, os.status_name, os.display_order 
                    FROM orders o 
                    LEFT JOIN order_status os ON o.status_id = os.status_id 
                    WHERE o.order_number = :order_number";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_number' => $order_number]);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            error_log("Get Order Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getOrderById($order_id) {
        try {
            $sql = "SELECT o.*, os.status_name, os.display_order, u.email, u.full_name 
                    FROM orders o 
                    LEFT JOIN order_status os ON o.status_id = os.status_id 
                    LEFT JOIN users u ON o.user_id = u.user_id 
                    WHERE o.order_id = :order_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            
            return $stmt->fetch();
            
        } catch(PDOException $e) {
            error_log("Get Order Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserOrders($user_id) {
        try {
            $sql = "SELECT o.*, os.status_name 
                    FROM orders o 
                    LEFT JOIN order_status os ON o.status_id = os.status_id 
                    WHERE o.user_id = :user_id 
                    ORDER BY o.order_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            error_log("Get User Orders Error: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateOrderStatus($order_id, $status_id, $tracking_number = null) {
        try {
            $sql = "UPDATE orders SET status_id = :status_id";
            
            if ($tracking_number) {
                $sql .= ", tracking_number = :tracking_number";
            }
            
            $sql .= " WHERE order_id = :order_id";
            
            $stmt = $this->db->prepare($sql);
            $params = [
                ':status_id' => $status_id,
                ':order_id' => $order_id
            ];
            
            if ($tracking_number) {
                $params[':tracking_number'] = $tracking_number;
            }
            
            $stmt->execute($params);
            
            // Send status update email
            $this->sendStatusUpdateEmail($order_id, $status_id);
            
            return ['success' => true, 'message' => 'Order status updated'];
            
        } catch(PDOException $e) {
            error_log("Update Order Status Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Status update failed'];
        }
    }
    
    public function getOrderStatuses() {
        try {
            $sql = "SELECT * FROM order_status ORDER BY display_order ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            error_log("Get Order Statuses Error: " . $e->getMessage());
            return [];
        }
    }
    
    private function sendOrderConfirmation($order_id) {
        // Implement email sending logic
        // This would use PHPMailer or similar
        return true;
    }
    
    private function sendStatusUpdateEmail($order_id, $status_id) {
        // Implement email notification logic
        return true;
    }
    
    public function getAllOrders($limit = 50, $offset = 0, $filters = []) {
        try {
            $sql = "SELECT o.*, os.status_name, u.full_name, u.email 
                    FROM orders o 
                    LEFT JOIN order_status os ON o.status_id = os.status_id 
                    LEFT JOIN users u ON o.user_id = u.user_id";
            
            $where = [];
            $params = [];
            
            if (!empty($filters['status_id'])) {
                $where[] = "o.status_id = :status_id";
                $params[':status_id'] = $filters['status_id'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(o.order_number LIKE :search OR u.full_name LIKE :search OR u.email LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY o.order_date DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch(PDOException $e) {
            error_log("Get All Orders Error: " . $e->getMessage());
            return [];
        }
    }
}
