-- LuckyGeneMdx Test Data Cleanup Script
-- Version 1.0
-- This script removes all test data while preserving the database schema and default admin account

USE luckygenemdx_db;

-- ============================================
-- SAFETY CHECK
-- ============================================
-- Uncomment the line below to enable cleanup
-- SET @cleanup_enabled = TRUE;

-- Safety check - prevents accidental execution
SELECT 
    CASE 
        WHEN @cleanup_enabled IS NULL THEN
            'CLEANUP BLOCKED: Please uncomment the SET @cleanup_enabled = TRUE; line above to enable cleanup'
        ELSE
            'Cleanup enabled - proceeding...'
    END AS safety_check;

-- Stop execution if not enabled
SET @continue = IF(@cleanup_enabled IS NULL, (SELECT 'STOP'), 'CONTINUE');

-- ============================================
-- BEFORE CLEANUP - DATA SUMMARY
-- ============================================
SELECT '=== DATA BEFORE CLEANUP ===' as status;
SELECT 
    'Users' as table_name,
    COUNT(*) as record_count,
    CONCAT('Will delete: ', COUNT(*), ' records') as action
FROM users
UNION ALL
SELECT 
    'Orders',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' orders')
FROM orders
UNION ALL
SELECT 
    'Results',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' results')
FROM results
UNION ALL
SELECT 
    'Admins (excluding default)',
    COUNT(*) - 1,
    CONCAT('Will keep 1 admin, delete: ', COUNT(*) - 1)
FROM admins
UNION ALL
SELECT 
    'Testimonials (excluding defaults)',
    COUNT(*) - 4,
    CONCAT('Will keep 4 defaults, delete: ', COUNT(*) - 4)
FROM testimonials
WHERE testimonial_id > 4
UNION ALL
SELECT 
    'Blog Posts',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' posts')
FROM blog_posts
UNION ALL
SELECT 
    'Educational Resources',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' resources')
FROM educational_resources
UNION ALL
SELECT 
    'Activity Logs',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' logs')
FROM activity_log
UNION ALL
SELECT 
    'Login Attempts',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' attempts')
FROM login_attempts
UNION ALL
SELECT 
    'Email Queue',
    COUNT(*),
    CONCAT('Will delete: ', COUNT(*), ' emails')
FROM email_queue;

-- ============================================
-- DISABLE FOREIGN KEY CHECKS
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- DELETE TEST DATA
-- ============================================

-- Delete email queue entries
DELETE FROM email_queue;
SELECT 'Deleted all email queue entries' as status;

-- Delete login attempts
DELETE FROM login_attempts;
SELECT 'Deleted all login attempts' as status;

-- Delete activity logs
DELETE FROM activity_log;
SELECT 'Deleted all activity logs' as status;

-- Delete educational resources
DELETE FROM educational_resources;
SELECT 'Deleted all educational resources' as status;

-- Delete blog posts
DELETE FROM blog_posts;
SELECT 'Deleted all blog posts' as status;

-- Delete test testimonials (keep the original 4)
DELETE FROM testimonials WHERE testimonial_id > 4;
SELECT 'Deleted test testimonials (kept original 4)' as status;

-- Delete results (will cascade properly once FK checks re-enabled)
DELETE FROM results;
SELECT 'Deleted all results' as status;

-- Delete orders (this will also clean up related results due to CASCADE)
DELETE FROM orders;
SELECT 'Deleted all orders' as status;

-- Delete test admin users (keep the default admin with admin_id = 1)
DELETE FROM admins WHERE admin_id > 1;
SELECT 'Deleted test admin users (kept default admin)' as status;

-- Delete all test users
DELETE FROM users;
SELECT 'Deleted all test users' as status;

-- ============================================
-- RESET AUTO_INCREMENT COUNTERS
-- ============================================
-- This ensures new data starts with clean IDs

ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE orders AUTO_INCREMENT = 1;
ALTER TABLE results AUTO_INCREMENT = 1;
ALTER TABLE admins AUTO_INCREMENT = 2; -- Start at 2 since admin is ID 1
ALTER TABLE testimonials AUTO_INCREMENT = 5; -- Start at 5 since we kept 4
ALTER TABLE blog_posts AUTO_INCREMENT = 1;
ALTER TABLE educational_resources AUTO_INCREMENT = 1;
ALTER TABLE activity_log AUTO_INCREMENT = 1;
ALTER TABLE login_attempts AUTO_INCREMENT = 1;
ALTER TABLE email_queue AUTO_INCREMENT = 1;

SELECT 'Reset all AUTO_INCREMENT counters' as status;

-- ============================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- ============================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- AFTER CLEANUP - DATA SUMMARY
-- ============================================
SELECT '=== DATA AFTER CLEANUP ===' as status;
SELECT 
    'Users' as table_name,
    COUNT(*) as remaining_records
FROM users
UNION ALL
SELECT 
    'Orders',
    COUNT(*)
FROM orders
UNION ALL
SELECT 
    'Results',
    COUNT(*)
FROM results
UNION ALL
SELECT 
    'Admins',
    COUNT(*)
FROM admins
UNION ALL
SELECT 
    'Testimonials',
    COUNT(*)
FROM testimonials
UNION ALL
SELECT 
    'Blog Posts',
    COUNT(*)
FROM blog_posts
UNION ALL
SELECT 
    'Educational Resources',
    COUNT(*)
FROM educational_resources
UNION ALL
SELECT 
    'Activity Logs',
    COUNT(*)
FROM activity_log
UNION ALL
SELECT 
    'Login Attempts',
    COUNT(*)
FROM login_attempts
UNION ALL
SELECT 
    'Email Queue',
    COUNT(*)
FROM email_queue;

-- ============================================
-- VERIFY REMAINING DATA
-- ============================================
SELECT '=== VERIFICATION ===' as status;

-- Show remaining admin
SELECT 
    admin_id,
    username,
    email,
    role,
    'Default admin account' as note
FROM admins;

-- Show remaining testimonials
SELECT 
    testimonial_id,
    name,
    location,
    'Default testimonial' as note
FROM testimonials
ORDER BY display_order;

-- Show order status table (should remain intact)
SELECT 
    status_id,
    status_name,
    display_order,
    'Order status reference data' as note
FROM order_status
ORDER BY display_order;

-- ============================================
-- CLEANUP COMPLETE
-- ============================================
SELECT 
    'CLEANUP COMPLETE!' as status,
    'Database is now clean and ready for production use' as message,
    'Default admin credentials: username=admin, password=Admin@123' as reminder,
    'IMPORTANT: Change the default admin password immediately!' as security_warning;

-- ============================================
-- OPTIONAL: VERIFY DATABASE SIZE
-- ============================================
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
    table_rows
FROM information_schema.TABLES 
WHERE table_schema = 'luckygenemdx_db'
ORDER BY (data_length + index_length) DESC;
