-- LuckyGeneMDx Selective Test Data Cleanup Script
-- Version 1.0
-- This script allows you to selectively clean specific data types

USE luckygenemdx_db;

-- ============================================
-- CONFIGURATION - SET WHAT TO CLEAN
-- ============================================
-- Set to TRUE for items you want to delete
-- Set to FALSE to keep them

SET @cleanup_users = FALSE;           -- Delete all test users
SET @cleanup_orders = FALSE;          -- Delete all orders
SET @cleanup_results = FALSE;         -- Delete all results
SET @cleanup_test_admins = FALSE;     -- Delete test admins (keep default)
SET @cleanup_testimonials = FALSE;    -- Delete test testimonials (keep original 4)
SET @cleanup_blog_posts = FALSE;      -- Delete all blog posts
SET @cleanup_resources = FALSE;       -- Delete all educational resources
SET @cleanup_activity_logs = FALSE;   -- Delete all activity logs
SET @cleanup_login_attempts = FALSE;  -- Delete all login attempts
SET @cleanup_email_queue = FALSE;     -- Delete all email queue entries
SET @cleanup_site_settings = FALSE;   -- Delete all site settings

-- Quick presets - uncomment ONE of these to use
-- SET @cleanup_users = TRUE, @cleanup_orders = TRUE, @cleanup_results = TRUE; -- Clean user data only
-- SET @cleanup_blog_posts = TRUE, @cleanup_resources = TRUE; -- Clean content only
-- SET @cleanup_activity_logs = TRUE, @cleanup_login_attempts = TRUE, @cleanup_email_queue = TRUE; -- Clean logs only

-- ============================================
-- SAFETY CHECK
-- ============================================
SELECT 
    'Users' as data_type,
    IF(@cleanup_users, 'WILL DELETE', 'WILL KEEP') as action,
    COUNT(*) as current_count
FROM users
UNION ALL
SELECT 
    'Orders',
    IF(@cleanup_orders, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM orders
UNION ALL
SELECT 
    'Results',
    IF(@cleanup_results, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM results
UNION ALL
SELECT 
    'Test Admins',
    IF(@cleanup_test_admins, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*) - 1
FROM admins
UNION ALL
SELECT 
    'Test Testimonials',
    IF(@cleanup_testimonials, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*) - 4
FROM testimonials
WHERE testimonial_id > 4
UNION ALL
SELECT 
    'Blog Posts',
    IF(@cleanup_blog_posts, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM blog_posts
UNION ALL
SELECT 
    'Educational Resources',
    IF(@cleanup_resources, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM educational_resources
UNION ALL
SELECT 
    'Activity Logs',
    IF(@cleanup_activity_logs, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM activity_log
UNION ALL
SELECT 
    'Login Attempts',
    IF(@cleanup_login_attempts, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM login_attempts
UNION ALL
SELECT 
    'Email Queue',
    IF(@cleanup_email_queue, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM email_queue
UNION ALL
SELECT 
    'Site Settings',
    IF(@cleanup_site_settings, 'WILL DELETE', 'WILL KEEP'),
    COUNT(*)
FROM email_queue;

-- Pause for review
SELECT 'Review the above table. Press any key to continue or Ctrl+C to cancel...' as notice;

-- ============================================
-- EXECUTE SELECTIVE CLEANUP
-- ============================================
SET FOREIGN_KEY_CHECKS = 0;

-- Clean Site Settings
DELETE FROM site_settings WHERE @cleanup_site_settings = TRUE;
SELECT IF(@cleanup_site_settings, 
    CONCAT('Deleted ', ROW_COUNT(), ' site settings entries'), 
    'Kept site settings entries') as site_settings_status;

-- Clean Email Queue
DELETE FROM email_queue WHERE @cleanup_email_queue = TRUE;
SELECT IF(@cleanup_email_queue, 
    CONCAT('Deleted ', ROW_COUNT(), ' email queue entries'), 
    'Kept email queue entries') as email_queue_status;

-- Clean Login Attempts
DELETE FROM login_attempts WHERE @cleanup_login_attempts = TRUE;
SELECT IF(@cleanup_login_attempts, 
    CONCAT('Deleted ', ROW_COUNT(), ' login attempts'), 
    'Kept login attempts') as login_attempts_status;

-- Clean Activity Logs
DELETE FROM activity_log WHERE @cleanup_activity_logs = TRUE;
SELECT IF(@cleanup_activity_logs, 
    CONCAT('Deleted ', ROW_COUNT(), ' activity logs'), 
    'Kept activity logs') as activity_log_status;

-- Clean Educational Resources
DELETE FROM educational_resources WHERE @cleanup_resources = TRUE;
SELECT IF(@cleanup_resources, 
    CONCAT('Deleted ', ROW_COUNT(), ' educational resources'), 
    'Kept educational resources') as resources_status;

-- Clean Blog Posts
DELETE FROM blog_posts WHERE @cleanup_blog_posts = TRUE;
SELECT IF(@cleanup_blog_posts, 
    CONCAT('Deleted ', ROW_COUNT(), ' blog posts'), 
    'Kept blog posts') as blog_posts_status;

-- Clean Test Testimonials
DELETE FROM testimonials WHERE testimonial_id > 4 AND @cleanup_testimonials = TRUE;
SELECT IF(@cleanup_testimonials, 
    CONCAT('Deleted ', ROW_COUNT(), ' test testimonials (kept original 4)'), 
    'Kept all testimonials') as testimonials_status;

-- Clean Results
DELETE FROM results WHERE @cleanup_results = TRUE;
SELECT IF(@cleanup_results, 
    CONCAT('Deleted ', ROW_COUNT(), ' results'), 
    'Kept results') as results_status;

-- Clean Orders
DELETE FROM orders WHERE @cleanup_orders = TRUE;
SELECT IF(@cleanup_orders, 
    CONCAT('Deleted ', ROW_COUNT(), ' orders'), 
    'Kept orders') as orders_status;

-- Clean Test Admins
DELETE FROM admins WHERE admin_id > 1 AND @cleanup_test_admins = TRUE;
SELECT IF(@cleanup_test_admins, 
    CONCAT('Deleted ', ROW_COUNT(), ' test admin users (kept default)'), 
    'Kept all admin users') as admins_status;

-- Clean Users
DELETE FROM users WHERE @cleanup_users = TRUE;
SELECT IF(@cleanup_users, 
    CONCAT('Deleted ', ROW_COUNT(), ' test users'), 
    'Kept users') as users_status;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- FINAL SUMMARY
-- ============================================
SELECT '=== CLEANUP COMPLETE ===' as status;
SELECT 
    'Users' as table_name,
    COUNT(*) as remaining_count
FROM users
UNION ALL
SELECT 'Orders', COUNT(*) FROM orders
UNION ALL
SELECT 'Results', COUNT(*) FROM results
UNION ALL
SELECT 'Admins', COUNT(*) FROM admins
UNION ALL
SELECT 'Testimonials', COUNT(*) FROM testimonials
UNION ALL
SELECT 'Blog Posts', COUNT(*) FROM blog_posts
UNION ALL
SELECT 'Educational Resources', COUNT(*) FROM educational_resources
UNION ALL
SELECT 'Activity Logs', COUNT(*) FROM activity_log
UNION ALL
SELECT 'Login Attempts', COUNT(*) FROM login_attempts
UNION ALL
SELECT 'Email Queue', COUNT(*) FROM email_queue
UNION ALL
SELECT 'Site Settings', COUNT(*) FROM site_settings;
