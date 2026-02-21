-- ============================================================
--  LuckyGeneMDx – Email Verification Schema Changes
--  Run this ONCE against your database before deploying.
-- ============================================================

-- 1. Add email verification columns to the users table
ALTER TABLE `users`
    ADD COLUMN `email_verified`     TINYINT(1)   NOT NULL DEFAULT 0
        COMMENT '0 = unverified, 1 = verified'
        AFTER `is_active`,

    ADD COLUMN `verification_token` VARCHAR(64)  NULL DEFAULT NULL
        COMMENT 'Secure random hex token for email verification'
        AFTER `email_verified`,

    ADD COLUMN `token_expires_at`   DATETIME     NULL DEFAULT NULL
        COMMENT 'Token expiry – set to NOW() + 24h on issue'
        AFTER `verification_token`;

-- 2. Index for fast token lookups (called on every link click)
CREATE INDEX `idx_verification_token`
    ON `users` (`verification_token`);

-- ============================================================
--  EXISTING USER MIGRATION
--  If you already have real users in the table, run the line
--  below to mark them all as verified so they can still log in.
--  Comment it out if all existing rows are test data.
-- ============================================================
-- UPDATE `users` SET `email_verified` = 1, `is_active` = 1;

-- ============================================================
--  ROLLBACK (undo everything above if needed)
-- ============================================================
-- DROP INDEX `idx_verification_token` ON `users`;
-- ALTER TABLE `users`
--     DROP COLUMN `email_verified`,
--     DROP COLUMN `verification_token`,
--     DROP COLUMN `token_expires_at`;
