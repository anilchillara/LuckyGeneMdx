-- LuckyGenes Cleanup Script
-- DANGER: This will delete all data in these tables and reset their AUTO_INCREMENT IDs.
-- Use ONLY in development/testing environments!

USE luckygenes_db;

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

SELECT 'All test data cleared and auto-increments reset.' as Status;
