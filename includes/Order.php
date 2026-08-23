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
        
        // Razorpay payment IDs (if payment was completed)
        $razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
        $razorpay_order_id   = $data['razorpay_order_id'] ?? null;
        $payment_status      = ($razorpay_payment_id) ? 'paid' : 'pending';
        
        try {
            $sql = "INSERT INTO orders (user_id, order_number, shipping_address_line1, 
                    shipping_address_line2, shipping_city, shipping_state, shipping_zip, 
                    price, status_id, order_date, payment_status, razorpay_payment_id, razorpay_order_id) 
                    VALUES (:user_id, :order_number, :address1, :address2, :city, :state, 
                    :zip, :price, 1, NOW(), :payment_status, :razorpay_payment_id, :razorpay_order_id)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id'             => $user_id,
                ':order_number'        => $order_number,
                ':address1'            => $data['address_line1'],
                ':address2'            => $data['address_line2'] ?? '',
                ':city'                => $data['city'],
                ':state'               => $data['state'],
                ':zip'                 => $data['zip'],
                ':price'               => KIT_PRICE,
                ':payment_status'      => $payment_status,
                ':razorpay_payment_id' => $razorpay_payment_id,
                ':razorpay_order_id'   => $razorpay_order_id,
            ]);
            
            $order_id = $this->db->lastInsertId();
            
            // Send confirmation email (implement separately)
            $this->sendOrderConfirmation($order_id);
            
            return [
                'success'      => true,
                'order_id'     => $order_id,
                'order_number' => $order_number,
                'message'      => 'Order created successfully'
            ];
            
        } catch(PDOException $e) {
            // Check if razorpay columns missing — fall back to insert without them
            if (strpos($e->getMessage(), 'razorpay') !== false || $e->getCode() == '42S22') {
                try {
                    $sql2 = "INSERT INTO orders (user_id, order_number, shipping_address_line1, 
                            shipping_address_line2, shipping_city, shipping_state, shipping_zip, 
                            price, status_id, order_date, payment_status) 
                            VALUES (:user_id, :order_number, :address1, :address2, :city, :state, 
                            :zip, :price, 1, NOW(), :payment_status)";
                    $stmt2 = $this->db->prepare($sql2);
                    $stmt2->execute([
                        ':user_id'        => $user_id,
                        ':order_number'   => $order_number,
                        ':address1'       => $data['address_line1'],
                        ':address2'       => $data['address_line2'] ?? '',
                        ':city'           => $data['city'],
                        ':state'          => $data['state'],
                        ':zip'            => $data['zip'],
                        ':price'          => KIT_PRICE,
                        ':payment_status' => $payment_status,
                    ]);
                    $order_id = $this->db->lastInsertId();
                    $this->sendOrderConfirmation($order_id);
                    return ['success' => true, 'order_id' => $order_id, 'order_number' => $order_number, 'message' => 'Order created successfully'];
                } catch(PDOException $e2) {
                    error_log("Order Creation Fallback Error: " . $e2->getMessage());
                }
            }
            error_log("Order Creation Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Order creation failed. Please try again.'];
        }
    }

    /**
     * Provision a kit for an existing order.
     * Generates a cryptographically random barcode (12 chars, uppercase alphanumeric).
     * If $gift_opts is provided, also generates a one-time redemption token (64 hex chars).
     *
     * @param  int        $order_id
     * @param  array|null $gift_opts  Keys: is_gift, recipient_email, recipient_name, message
     * @return array  { success, kit_id, kit_barcode, gift_token|null }
     */
    public function createKitForOrder(int $order_id, ?array $gift_opts = null): array {
        try {
            // --- Generate unique barcode ---
            do {
                $barcode = strtoupper(substr(bin2hex(random_bytes(9)), 0, 12));
                $check   = $this->db->prepare("SELECT kit_id FROM kits WHERE kit_barcode = :b");
                $check->execute([':b' => $barcode]);
            } while ($check->fetch());

            $is_gift       = !empty($gift_opts['is_gift']);
            $gift_token    = null;
            $gift_expires  = null;

            if ($is_gift) {
                // 64-char hex token, unique
                do {
                    $gift_token = bin2hex(random_bytes(32));
                    $tc = $this->db->prepare("SELECT kit_id FROM kits WHERE gift_token = :t");
                    $tc->execute([':t' => $gift_token]);
                } while ($tc->fetch());
                $gift_expires = date('Y-m-d H:i:s', strtotime('+90 days'));
            }

            $sql = "INSERT INTO kits
                    (order_id, kit_barcode, kit_status_id,
                     is_gift, gift_recipient_email, gift_recipient_name,
                     gift_message, gift_token, gift_token_expires_at)
                    VALUES
                    (:order_id, :barcode, 1,
                     :is_gift, :rec_email, :rec_name,
                     :message, :token, :expires)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':order_id'  => $order_id,
                ':barcode'   => $barcode,
                ':is_gift'   => $is_gift ? 1 : 0,
                ':rec_email' => $gift_opts['recipient_email'] ?? null,
                ':rec_name'  => $gift_opts['recipient_name']  ?? null,
                ':message'   => $gift_opts['message']         ?? null,
                ':token'     => $gift_token,
                ':expires'   => $gift_expires,
            ]);

            return [
                'success'     => true,
                'kit_id'      => (int) $this->db->lastInsertId(),
                'kit_barcode' => $barcode,
                'gift_token'  => $gift_token,
            ];

        } catch (PDOException $e) {
            error_log("createKitForOrder Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Kit provisioning failed.'];
        }
    }

    /**
     * Fetch a kit by its barcode — intentionally returns NO user PII.
     * Used by the admin lab-upload flow.
     *
     * @param  string $barcode
     * @return array|false
     */
    public function getKitByBarcode(string $barcode) {
        try {
            $sql = "SELECT k.kit_id, k.kit_barcode, k.kit_status_id, k.assigned_to,
                           k.is_gift, k.gift_redeemed_at,
                           k.created_at, k.order_id,
                           os.status_name, os.display_order
                    FROM   kits k
                    LEFT JOIN order_status os ON os.status_id = k.kit_status_id
                    WHERE  k.kit_barcode = :barcode
                    LIMIT  1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':barcode' => $barcode]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("getKitByBarcode Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a single kit (independent of the parent order status).
     *
     * @param  int $kit_id
     * @param  int $status_id
     * @return array { success, message }
     */
    public function updateKitStatus(int $kit_id, int $status_id): array {
        try {
            $stmt = $this->db->prepare(
                "UPDATE kits SET kit_status_id = :status_id WHERE kit_id = :kit_id"
            );
            $stmt->execute([':status_id' => $status_id, ':kit_id' => $kit_id]);
            return ['success' => true, 'message' => 'Kit status updated'];
        } catch (PDOException $e) {
            error_log("updateKitStatus Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Status update failed'];
        }
    }

    /**
     * Redeem a gift kit: validate the token, check expiry and not-already-redeemed,
     * then bind the kit to the claimant's user_id.
     *
     * @param  string $gift_token
     * @param  int    $user_id     The authenticated recipient
     * @return array  { success, message, kit_id?, kit_barcode? }
     */
    public function redeemGiftKit(string $gift_token, int $user_id): array {
        try {
            // Fetch the kit row
            $stmt = $this->db->prepare(
                "SELECT kit_id, kit_barcode, is_gift, gift_token_expires_at,
                        gift_redeemed_at, gift_redeemed_by, order_id
                 FROM   kits
                 WHERE  gift_token = :token
                 LIMIT  1"
            );
            $stmt->execute([':token' => $gift_token]);
            $kit = $stmt->fetch();

            if (!$kit) {
                return ['success' => false, 'message' => 'Invalid gift link. Please check your email and try again.'];
            }
            if (!$kit['is_gift']) {
                return ['success' => false, 'message' => 'This kit is not a gift.'];
            }
            if ($kit['gift_redeemed_at'] !== null) {
                return ['success' => false, 'message' => 'This gift has already been claimed.'];
            }
            if ($kit['gift_token_expires_at'] && strtotime($kit['gift_token_expires_at']) < time()) {
                return ['success' => false, 'message' => 'This gift link has expired. Please ask the sender to re-send it.'];
            }

            // Mark as redeemed
            $upd = $this->db->prepare(
                "UPDATE kits
                 SET    gift_redeemed_at = NOW(),
                        gift_redeemed_by = :user_id
                 WHERE  kit_id = :kit_id"
            );
            $upd->execute([':user_id' => $user_id, ':kit_id' => $kit['kit_id']]);

            return [
                'success'     => true,
                'kit_id'      => (int) $kit['kit_id'],
                'kit_barcode' => $kit['kit_barcode'],
                'message'     => 'Gift kit successfully claimed!',
            ];

        } catch (PDOException $e) {
            error_log("redeemGiftKit Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Could not redeem gift. Please try again.'];
        }
    }

    /**
     * Fetch a gift kit by its redemption token (for the claim-gift.php page).
     * Returns safe display info only (recipient name, sender's first name, message).
     */
    public function getGiftKitByToken(string $token) {
        try {
            $sql = "SELECT k.kit_id, k.gift_recipient_name, k.gift_message,
                           k.gift_token_expires_at, k.gift_redeemed_at,
                           u.full_name AS purchaser_name
                    FROM   kits k
                    JOIN   orders o ON o.order_id = k.order_id
                    JOIN   users  u ON u.user_id  = o.user_id
                    WHERE  k.gift_token = :token
                    LIMIT  1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("getGiftKitByToken Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all kits belonging to an order (used by track-order and user portal).
     */
    public function getKitsByOrderId(int $order_id): array {
        try {
            $sql = "SELECT k.*, os.status_name, os.display_order
                    FROM   kits k
                    LEFT JOIN order_status os ON os.status_id = k.kit_status_id
                    WHERE  k.order_id = :order_id
                    ORDER  BY k.kit_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getKitsByOrderId Error: " . $e->getMessage());
            return [];
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
            
            $order = $stmt->fetch();
            if ($order) {
                $order['kits'] = $this->getKitsByOrderId((int) $order['order_id']);
            }
            return $order;
            
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
            
            $order = $stmt->fetch();
            if ($order) {
                $order['kits'] = $this->getKitsByOrderId((int) $order['order_id']);
            }
            return $order;
            
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
            
            $orders = $stmt->fetchAll();
            foreach ($orders as &$order) {
                $order['kits'] = $this->getKitsByOrderId((int) $order['order_id']);
            }
            return $orders;
            
        } catch(PDOException $e) {
            error_log("Get User Orders Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch all kits received as gifts by a user (gift_redeemed_by = $user_id).
     * Used in the user portal's "Received Gifts" section.
     */
    public function getReceivedGiftKits(int $user_id): array {
        try {
            $sql = "SELECT k.*, os.status_name, os.display_order,
                           u.full_name AS gifted_by_name
                    FROM   kits k
                    LEFT JOIN order_status os ON os.status_id = k.kit_status_id
                    JOIN   orders o ON o.order_id = k.order_id
                    JOIN   users  u ON u.user_id  = o.user_id
                    WHERE  k.gift_redeemed_by = :user_id
                    ORDER  BY k.gift_redeemed_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getReceivedGiftKits Error: " . $e->getMessage());
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
