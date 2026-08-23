-- =============================================================
-- LuckyGenes Migration #6 — Per-Kit Barcode Tracking + Gift Kits
-- Run ONCE, after all previous migrations (1–5).
-- =============================================================
USE luckygenes_db;

-- ---------------------------------------------------------------
-- 1. Create the kits table
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kits (
    kit_id                INT AUTO_INCREMENT PRIMARY KEY,
    order_id              INT NOT NULL,

    -- Lab-safe identifier: cryptographically random, no link to PII
    kit_barcode           VARCHAR(20) NOT NULL UNIQUE,

    -- Per-kit status (mirrors order_status values, tracked independently)
    kit_status_id         INT NOT NULL DEFAULT 1,

    -- Optional human label ("Mom", "Dad", "Child")
    assigned_to           VARCHAR(100) NULL,

    -- ── Gift Kit Fields ─────────────────────────────────────────
    is_gift               BOOLEAN NOT NULL DEFAULT FALSE,
    gift_recipient_email  VARCHAR(255) NULL,   -- where the claim email goes
    gift_recipient_name   VARCHAR(255) NULL,   -- personalises the email
    gift_message          TEXT NULL,           -- optional personal note ≤ 500 chars
    gift_token            VARCHAR(64) NULL UNIQUE,  -- one-time redemption secret
    gift_token_expires_at TIMESTAMP NULL,           -- 90-day expiry from creation
    gift_redeemed_at      TIMESTAMP NULL,           -- set on claim
    gift_redeemed_by      INT NULL,                 -- user_id of claimant
    -- ────────────────────────────────────────────────────────────

    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id)         REFERENCES orders(order_id)       ON DELETE CASCADE,
    FOREIGN KEY (kit_status_id)    REFERENCES order_status(status_id),
    FOREIGN KEY (gift_redeemed_by) REFERENCES users(user_id)         ON DELETE SET NULL,

    INDEX idx_kit_barcode  (kit_barcode),
    INDEX idx_kit_order_id (order_id),
    INDEX idx_gift_token   (gift_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- 2. Migrate results table: add kit_id column + index
--    (keep order_id for backward compat until all rows migrated)
-- ---------------------------------------------------------------
ALTER TABLE results
    ADD COLUMN IF NOT EXISTS kit_id INT NULL AFTER order_id;

-- ---------------------------------------------------------------
-- 3. Back-fill: create one kit per existing order, generate a
--    barcode from a hash of the order_id (deterministic for
--    back-fill only; new kits use random bytes at runtime).
--    Then link existing results rows to the new kit records.
-- ---------------------------------------------------------------

-- 3a. Insert one kit per existing order (if not already present)
INSERT IGNORE INTO kits (order_id, kit_barcode, kit_status_id, created_at)
SELECT
    o.order_id,
    -- Deterministic 12-char barcode for backfill only
    UPPER(SUBSTRING(SHA2(CONCAT('legacy-', o.order_id, '-', o.order_date), 256), 1, 12)),
    o.status_id,
    o.order_date
FROM orders o
WHERE NOT EXISTS (
    SELECT 1 FROM kits k WHERE k.order_id = o.order_id
);

-- 3b. Link existing results rows to their kit
UPDATE results r
JOIN   kits k ON k.order_id = r.order_id
SET    r.kit_id = k.kit_id
WHERE  r.kit_id IS NULL;

-- ---------------------------------------------------------------
-- 4. Add unique index on results.kit_id (allow NULL for safety
--    during partial migration, enforce once all rows are linked)
-- ---------------------------------------------------------------
-- Only add the index if it doesn't exist
SET @idx_exists = (
    SELECT COUNT(*) FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name   = 'results'
      AND index_name   = 'unique_kit_result'
);

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE results ADD UNIQUE KEY unique_kit_result (kit_id)',
    'SELECT ''index already exists'' AS note'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
