# 🧹 LuckyGeneMDx Test Data Cleanup Guide

## 📦 Available Cleanup Scripts

You have **3 cleanup scripts** to choose from based on your needs:

### 1. **cleanup_test_data.sql** - Complete Cleanup
**Use when:** You want to remove ALL test data and start fresh

**What it does:**
- ✅ Deletes ALL users
- ✅ Deletes ALL orders
- ✅ Deletes ALL results
- ✅ Deletes test admin accounts (keeps default admin)
- ✅ Deletes test testimonials (keeps original 4)
- ✅ Deletes ALL blog posts
- ✅ Deletes ALL educational resources
- ✅ Deletes ALL activity logs
- ✅ Deletes ALL login attempts
- ✅ Deletes ALL email queue entries
- ✅ Resets AUTO_INCREMENT counters
- ✅ Preserves database schema
- ✅ Preserves order_status reference data

**What it keeps:**
- ✅ Default admin account (username: admin)
- ✅ Original 4 testimonials
- ✅ Order status reference table
- ✅ All table structures

---

### 2. **cleanup_selective.sql** - Selective Cleanup
**Use when:** You want to remove specific data types only

**Features:**
- Configure what to delete via TRUE/FALSE flags
- Preview what will be deleted before execution
- Quick presets for common scenarios
- Detailed status messages for each operation

**Configuration Options:**
```sql
SET @cleanup_users = FALSE;           -- Users
SET @cleanup_orders = FALSE;          -- Orders
SET @cleanup_results = FALSE;         -- Results
SET @cleanup_test_admins = FALSE;     -- Test admins
SET @cleanup_testimonials = FALSE;    -- Test testimonials
SET @cleanup_blog_posts = FALSE;      -- Blog posts
SET @cleanup_resources = FALSE;       -- Resources
SET @cleanup_activity_logs = FALSE;   -- Activity logs
SET @cleanup_login_attempts = FALSE;  -- Login attempts
SET @cleanup_email_queue = FALSE;     -- Email queue
```

---

## 🚀 How to Use

### Complete Cleanup (cleanup_test_data.sql)

**Step 1: Safety Check**
The script includes a safety mechanism to prevent accidental execution.

**Step 2: Enable Cleanup**
Open the file and uncomment this line:
```sql
-- SET @cleanup_enabled = TRUE;
```
Change to:
```sql
SET @cleanup_enabled = TRUE;
```

**Step 3: Execute**
```bash
# Via command line
mysql -u your_user -p luckygenemdx_db < cleanup_test_data.sql

# Or import via phpMyAdmin
```

**Step 4: Verify**
The script will show:
- Data counts BEFORE cleanup
- Progress messages during cleanup
- Data counts AFTER cleanup
- Verification of remaining data
- Database size information

---

### Selective Cleanup (cleanup_selective.sql)

**Step 1: Configure**
Open the file and set TRUE for items you want to delete:
```sql
SET @cleanup_users = TRUE;       -- Will delete users
SET @cleanup_orders = TRUE;      -- Will delete orders
SET @cleanup_blog_posts = FALSE; -- Will keep blog posts
```

**Or use a quick preset:**
```sql
-- Clean user data only
SET @cleanup_users = TRUE, @cleanup_orders = TRUE, @cleanup_results = TRUE;

-- Clean content only
SET @cleanup_blog_posts = TRUE, @cleanup_resources = TRUE;

-- Clean logs only
SET @cleanup_activity_logs = TRUE, @cleanup_login_attempts = TRUE;
```

**Step 2: Preview**
The script shows what WILL be deleted vs kept before executing.

**Step 3: Execute**
```bash
mysql -u your_user -p luckygenemdx_db < cleanup_selective.sql
```

---

## 📊 What Gets Deleted vs Kept

### Complete Cleanup (cleanup_test_data.sql)

| Data Type | Action | Details |
|-----------|--------|---------|
| Users | ❌ ALL DELETED | All 20 test users removed |
| Orders | ❌ ALL DELETED | All 30 orders removed |
| Results | ❌ ALL DELETED | All 3 result files removed |
| Admins | ⚠️ PARTIAL | Test admins deleted, default kept |
| Testimonials | ⚠️ PARTIAL | Test deleted, original 4 kept |
| Blog Posts | ❌ ALL DELETED | All 10 posts removed |
| Resources | ❌ ALL DELETED | All 8 resources removed |
| Activity Logs | ❌ ALL DELETED | All logs cleared |
| Login Attempts | ❌ ALL DELETED | All attempts cleared |
| Email Queue | ❌ ALL DELETED | All queued emails cleared |
| Order Status | ✅ KEPT | Reference data preserved |
| Database Schema | ✅ KEPT | All tables intact |

### After Complete Cleanup:
- Users: 0
- Orders: 0
- Results: 0
- Admins: 1 (default only)
- Testimonials: 4 (originals)
- Blog Posts: 0
- Resources: 0
- Activity Logs: 0
- Login Attempts: 0
- Email Queue: 0

---

## ⚠️ Important Warnings

### Before Cleanup:

1. **Backup First!**
   ```bash
   mysqldump -u your_user -p luckygenemdx_db > backup_before_cleanup.sql
   ```

2. **Review What Will Be Deleted**
   - Both scripts show what will be deleted
   - Read the summary tables carefully

3. **Cannot Be Undone**
   - Deleted data is permanently removed
   - Only restore option is from backup

4. **Foreign Key Cascade**
   - Deleting orders automatically deletes related results
   - Deleting users cascades to their orders
   - This is by design but be aware

### After Cleanup:

1. **Change Default Password**
   ```sql
   -- The default admin password is: Admin@123
   -- CHANGE IT IMMEDIATELY
   UPDATE admins 
   SET password_hash = PASSWORD_HASH('your_new_password', PASSWORD_DEFAULT) 
   WHERE username = 'admin';
   ```

2. **Verify Database Integrity**
   ```sql
   -- Check for orphaned records
   SELECT * FROM results WHERE order_id NOT IN (SELECT order_id FROM orders);
   SELECT * FROM orders WHERE user_id NOT IN (SELECT user_id FROM users);
   ```

3. **Reset Auto-Increment**
   - Complete cleanup script does this automatically
   - Selective cleanup does NOT reset counters
   - Manual reset if needed:
   ```sql
   ALTER TABLE users AUTO_INCREMENT = 1;
   ALTER TABLE orders AUTO_INCREMENT = 1;
   -- etc.
   ```

---

## 🔄 Common Scenarios

### Scenario 1: Testing Complete, Going to Production
**Use:** Complete Cleanup
```bash
1. Backup database
2. Run cleanup_test_data.sql
3. Change admin password
4. Verify all test data removed
5. Begin production use
```

### Scenario 2: Keep Content, Clear User Data
**Use:** Selective Cleanup
```sql
SET @cleanup_users = TRUE;
SET @cleanup_orders = TRUE;
SET @cleanup_results = TRUE;
SET @cleanup_activity_logs = TRUE;
SET @cleanup_login_attempts = TRUE;
-- Keep blog_posts and resources
```

### Scenario 3: Keep Recent Data, Clear Old Logs
**Use:** Custom SQL (modify selective script)
```sql
-- Delete old activity logs (older than 30 days)
DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Delete old login attempts (older than 7 days)
DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### Scenario 4: Fresh Test Environment
```bash
1. Run cleanup_test_data.sql (complete cleanup)
2. Run test_data.sql (repopulate with test data)
3. Start testing again
```

---

## 🛠️ Manual Cleanup Commands

If you prefer manual control:

```sql
-- Delete specific user
DELETE FROM users WHERE email = 'john.doe@email.com';

-- Delete orders by date range
DELETE FROM orders WHERE order_date < '2024-02-01';

-- Delete inactive testimonials
DELETE FROM testimonials WHERE is_active = FALSE;

-- Clear specific email queue statuses
DELETE FROM email_queue WHERE status = 'failed';

-- Delete old activity logs
DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

## ✅ Verification Queries

After cleanup, verify with these queries:

```sql
-- Count all records
SELECT 
    'Users' as table_name, COUNT(*) as count FROM users
UNION ALL SELECT 'Orders', COUNT(*) FROM orders
UNION ALL SELECT 'Results', COUNT(*) FROM results
UNION ALL SELECT 'Admins', COUNT(*) FROM admins
UNION ALL SELECT 'Testimonials', COUNT(*) FROM testimonials
UNION ALL SELECT 'Blog Posts', COUNT(*) FROM blog_posts
UNION ALL SELECT 'Resources', COUNT(*) FROM educational_resources;

-- Check for orphaned records
SELECT 'Orphaned Results' as issue, COUNT(*) as count
FROM results r
LEFT JOIN orders o ON r.order_id = o.order_id
WHERE o.order_id IS NULL
UNION ALL
SELECT 'Orphaned Orders', COUNT(*)
FROM orders o
LEFT JOIN users u ON o.user_id = u.user_id
WHERE u.user_id IS NULL;

-- Verify remaining admin
SELECT admin_id, username, email, role 
FROM admins;

-- Verify database size
SELECT 
    table_name,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)',
    table_rows AS 'Rows'
FROM information_schema.TABLES 
WHERE table_schema = 'luckygenemdx_db'
ORDER BY (data_length + index_length) DESC;
```

---

## 📞 Troubleshooting

### "Foreign key constraint fails"
**Solution:** The scripts disable foreign key checks. If you see this error:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- Run your delete commands
SET FOREIGN_KEY_CHECKS = 1;
```

### "Access denied"
**Solution:** Ensure you have DELETE privileges:
```sql
GRANT DELETE ON luckygenemdx_db.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

### Script doesn't run
**Solution:** Check syntax:
```bash
# Test the script first
mysql -u your_user -p luckygenemdx_db --execute="SELECT 'Test' as status;"

# Then run cleanup
mysql -u your_user -p luckygenemdx_db < cleanup_test_data.sql
```

### Need to restore
**Solution:** Use your backup:
```bash
mysql -u your_user -p luckygenemdx_db < backup_before_cleanup.sql
```

---

## 📝 Best Practices

1. **Always Backup First** ⚠️
2. **Test in Development** - Try cleanup in dev environment first
3. **Review Preview Tables** - Both scripts show what will be deleted
4. **Run During Off-Hours** - If in production, clean during low traffic
5. **Verify After** - Use verification queries
6. **Document What You Did** - Keep notes on cleanup actions
7. **Change Passwords** - Update default admin password immediately
8. **Monitor Logs** - Check application logs after cleanup

---

## 🎯 Quick Reference

| Need | Script | Safety |
|------|--------|--------|
| Remove everything | cleanup_test_data.sql | ⚠️⚠️⚠️ Requires uncomment |
| Remove specific items | cleanup_selective.sql | ⚠️⚠️ Configure flags |
| Custom cleanup | Write custom SQL | ⚠️⚠️⚠️ Manual control |

**Remember:** 
- 🔴 **cleanup_test_data.sql** = Nuclear option (removes almost everything)
- 🟡 **cleanup_selective.sql** = Surgical option (you choose what to remove)
- 🔵 **test_data.sql** = Repopulation (adds test data back)

---

## 📅 Maintenance Schedule Suggestion

**Weekly:**
- Clear old login_attempts (>7 days)
- Clear old email_queue (>30 days, status='sent')

**Monthly:**
- Review and archive activity_log (>90 days)
- Clean test orders if in dev environment

**Before Production:**
- Run complete cleanup
- Change all default passwords
- Verify zero test data remains

---

**Need Help?** Check the verification queries section or review the summary output from the cleanup scripts.
