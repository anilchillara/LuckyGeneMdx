-- UPDATE: Modify orders table to allow guest checkout
-- Run this after creating the initial database

USE luckygenemdx_db;

-- Modify orders table to allow NULL user_id (for guest orders)
ALTER TABLE orders MODIFY user_id INT NULL;

-- Add guest email column to track guest orders
ALTER TABLE orders ADD COLUMN guest_email VARCHAR(255) NULL AFTER user_id;
ALTER TABLE orders ADD COLUMN guest_name VARCHAR(255) NULL AFTER guest_email;

-- Add index for guest email lookups
ALTER TABLE orders ADD INDEX idx_guest_email (guest_email);

-- Add constraint: Either user_id OR guest_email must be present
-- ALTER TABLE orders ADD CONSTRAINT chk_user_or_guest 
-- CHECK ((user_id IS NOT NULL) OR (guest_email IS NOT NULL));
