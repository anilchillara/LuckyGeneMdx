-- LuckyGenes Test Data Load Script
-- Version 2.0
-- Includes core features, guest orders, gifts, interest list, etc.

USE luckygenes_db;

-- ============================================
-- 1. CLEANUP FIRST (Optional but safe)
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;

-- Delete children first
DELETE FROM activity_log; ALTER TABLE activity_log AUTO_INCREMENT = 1;
DELETE FROM results; ALTER TABLE results AUTO_INCREMENT = 1;
DELETE FROM kits; ALTER TABLE kits AUTO_INCREMENT = 1;
DELETE FROM orders; ALTER TABLE orders AUTO_INCREMENT = 1;
DELETE FROM blog_posts; ALTER TABLE blog_posts AUTO_INCREMENT = 1;

-- Then parents
DELETE FROM admins; ALTER TABLE admins AUTO_INCREMENT = 1;
DELETE FROM users; ALTER TABLE users AUTO_INCREMENT = 1;

-- Independent tables
DELETE FROM login_attempts; ALTER TABLE login_attempts AUTO_INCREMENT = 1;
DELETE FROM email_queue; ALTER TABLE email_queue AUTO_INCREMENT = 1;
DELETE FROM educational_resources; ALTER TABLE educational_resources AUTO_INCREMENT = 1;
DELETE FROM interest_list; ALTER TABLE interest_list AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 2. USERS
-- ============================================
-- Password for all test users: Test@123 (Bcrypt Hash: $2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK)
INSERT INTO users (user_id, email, password_hash, full_name, is_active, phone, dob, created_at, last_login) VALUES
(1, 'john.doe@email.com', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'John Doe', 1, '555-0101', '1990-05-15', '2024-01-10 10:30:00', '2024-02-10 14:20:00'),
(2, 'sarah.johnson@email.com', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'Sarah Johnson', 1, '555-0102', '1988-08-22', '2024-01-12 09:15:00', '2024-02-12 16:45:00'),
(3, 'michael.chen@email.com', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'Michael Chen', 1, '555-0103', '1992-03-30', '2024-01-15 11:20:00', '2024-02-08 10:30:00'),
(4, 'emily.williams@email.com', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'Emily Williams', 1, '555-0104', '1985-12-08', '2024-01-18 14:45:00', '2024-02-14 09:15:00'),
(5, 'david.martinez@email.com', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'David Martinez', 1, '555-0105', '1993-07-19', '2024-01-20 08:30:00', '2024-02-13 15:20:00');

-- ============================================
-- 3. ORDERS (Includes Guest Orders)
-- ============================================
-- Note: guest orders have user_id=NULL and use guest_email and guest_name
INSERT INTO orders (order_id, user_id, guest_email, guest_name, order_number, status_id, order_date, shipping_address_line1, shipping_city, shipping_state, shipping_zip, tracking_number, price, payment_status) VALUES
-- Completed orders (status 5) for registered users
(1, 1, NULL, NULL, 'LGM-2024-00001', 5, '2024-01-10 10:35:00', '123 Main St', 'Boston', 'MA', '02108', '1Z999AA10123456784', 99.00, 'completed'),
(2, 2, NULL, NULL, 'LGM-2024-00002', 5, '2024-01-12 09:20:00', '456 Oak Ave', 'Austin', 'TX', '78701', '1Z999AA10123456785', 99.00, 'completed'),
-- Order in progress (status 2 = shipped) for registered user
(3, 3, NULL, NULL, 'LGM-2024-00003', 2, '2024-02-01 11:25:00', '789 Pine Rd', 'San Francisco', 'CA', '94102', '1Z999AA10123456786', 99.00, 'completed'),
-- Guest Order (completed)
(4, NULL, 'guest_user@email.com', 'Guest User', 'LGM-2024-00004', 5, '2024-01-20 14:50:00', '321 Elm St', 'Seattle', 'WA', '98101', '1Z999AA10123456787', 99.00, 'completed'),
-- Gift Order from John Doe to someone else (status 1 = Order Placed)
(5, 1, NULL, NULL, 'LGM-2024-00005', 1, '2024-02-14 08:35:00', '654 Maple Dr', 'Portland', 'OR', '97201', NULL, 99.00, 'completed');

-- ============================================
-- 4. KITS (Includes Gifts)
-- ============================================
INSERT INTO kits (kit_id, order_id, kit_barcode, kit_status_id, assigned_to, is_gift, gift_recipient_email, gift_recipient_name, gift_message, gift_token, gift_token_expires_at, gift_redeemed_at) VALUES
-- Standard kits
(1, 1, 'BARCODE00001', 5, 'John Doe', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 'BARCODE00002', 5, 'Sarah Johnson', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 3, 'BARCODE00003', 2, 'Michael Chen', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 4, 'BARCODE00004', 5, 'Guest User', 0, NULL, NULL, NULL, NULL, NULL, NULL),
-- Gift Kit (from John Doe to Alice) - Pending claim
(5, 5, 'BARCODE00005', 1, NULL, 1, 'alice.recipient@email.com', 'Alice Smith', 'Happy Birthday Alice! Stay healthy.', 'gift_token_xyz987', '2024-05-14 00:00:00', NULL);

-- ============================================
-- 5. RESULTS (Tied to Kits)
-- ============================================
INSERT INTO results (order_id, kit_id, file_path, encrypted_filename, upload_date, uploaded_by, file_size, file_hash) VALUES
(1, 1, 'results/2024/01/result_001.pdf', 'enc_a1b2c3d4e5f6.pdf', '2024-01-24 14:30:00', 1, 245678, 'a1b2c3d4e5f6g7h8i9j0'),
(2, 2, 'results/2024/01/result_002.pdf', 'enc_b2c3d4e5f6g7.pdf', '2024-01-26 16:45:00', 1, 238945, 'b2c3d4e5f6g7h8i9j0k1'),
(4, 4, 'results/2024/01/result_003.pdf', 'enc_c3d4e5f6g7h8.pdf', '2024-01-30 10:20:00', 1, 251234, 'c3d4e5f6g7h8i9j0k1l2');

-- ============================================
-- 6. ADMINS
-- ============================================
INSERT INTO admins (admin_id, username, password_hash, email, role, is_active) VALUES
(1, 'superadmin', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'admin@LuckyGenes.com', 'super_admin', TRUE),
(2, 'lab_tech1', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'labtech1@LuckyGenes.com', 'lab_tech', TRUE),
(3, 'support1', '$2y$10$iHCDLhvZgn6TRIHJadwhLuc6UG5p/iP/BoIlNkmqIUoD1gISl6ueK', 'support1@LuckyGenes.com', 'support', TRUE);

-- ============================================
-- 7. BLOG POSTS & EDUCATIONAL RESOURCES
-- ============================================
INSERT INTO blog_posts (title, slug, content, excerpt, category, author_id, published_at, is_published, views) VALUES
('Understanding Carrier Screening', 'understanding-carrier-screening', '<p>Test content.</p>', 'Excerpt text.', 'Carrier Screening', 1, '2024-01-15 10:00:00', TRUE, 1247);

INSERT INTO educational_resources (title, slug, content, excerpt, category, reading_time, is_published) VALUES
('What is a Genetic Carrier?', 'what-is-genetic-carrier', '<p>Test content.</p>', 'Excerpt text.', 'Basic Concepts', 5, TRUE);

-- ============================================
-- 8. INTEREST LIST
-- ============================================
INSERT INTO interest_list (email, name, created_at, role) VALUES
('waitlist1@email.com', 'Wait List1', '2024-02-14 10:00:00', ''),
('waitlist2@email.com', 'Wait List2', '2024-02-14 11:00:00', '');

-- ============================================
-- 9. LOGS (Login & Emails)
-- ============================================
INSERT INTO login_attempts (email, ip_address, attempted_at, success) VALUES
('wrong@email.com', '203.0.113.45', '2024-02-14 09:15:00', FALSE),
('admin@LuckyGenes.com', '192.168.1.100', '2024-02-14 08:00:00', TRUE);

INSERT INTO email_queue (recipient_email, subject, body, template, priority, status, attempts) VALUES
('john.doe@email.com', 'Your Results Are Ready', 'Results body', 'results_ready', 1, 'sent', 1),
('alice.recipient@email.com', 'You received a gift!', 'Gift body', 'gift_received', 1, 'pending', 0);

SELECT 'Test data successfully inserted!' as Status;