-- ============================================================
-- Razorpay Integration Migration
-- Run this on your database to add Razorpay payment columns
-- ============================================================

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS razorpay_payment_id VARCHAR(100) DEFAULT NULL COMMENT 'Razorpay payment ID (pay_xxxxx)',
    ADD COLUMN IF NOT EXISTS razorpay_order_id   VARCHAR(100) DEFAULT NULL COMMENT 'Razorpay order ID (order_xxxxx)';

-- Index for quick lookup by payment ID
CREATE INDEX IF NOT EXISTS idx_orders_razorpay_payment_id ON orders(razorpay_payment_id);
