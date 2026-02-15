-- LuckyGeneMdx Test Data
-- Version 1.0
-- This file populates the database with realistic test data

USE luckygenemdx_db;

-- ============================================
-- TEST USERS (20 users)
-- ============================================
-- Password for all test users: Test@123
INSERT INTO users (email, password_hash, full_name, phone, dob, created_at, last_login) VALUES
('john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '555-0101', '1990-05-15', '2024-01-10 10:30:00', '2024-02-10 14:20:00'),
('sarah.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Johnson', '555-0102', '1988-08-22', '2024-01-12 09:15:00', '2024-02-12 16:45:00'),
('michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael Chen', '555-0103', '1992-03-30', '2024-01-15 11:20:00', '2024-02-08 10:30:00'),
('emily.williams@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily Williams', '555-0104', '1985-12-08', '2024-01-18 14:45:00', '2024-02-14 09:15:00'),
('david.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David Martinez', '555-0105', '1993-07-19', '2024-01-20 08:30:00', '2024-02-13 15:20:00'),
('jessica.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jessica Brown', '555-0106', '1991-11-25', '2024-01-22 16:00:00', '2024-02-11 11:40:00'),
('robert.taylor@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert Taylor', '555-0107', '1987-04-12', '2024-01-25 10:15:00', '2024-02-09 13:25:00'),
('amanda.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Amanda Anderson', '555-0108', '1994-09-03', '2024-01-28 13:30:00', '2024-02-14 10:50:00'),
('james.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James Wilson', '555-0109', '1989-06-17', '2024-02-01 09:45:00', '2024-02-12 14:15:00'),
('lisa.moore@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lisa Moore', '555-0110', '1986-02-28', '2024-02-03 11:20:00', '2024-02-13 16:30:00'),
('daniel.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Daniel Garcia', '555-0111', '1995-10-14', '2024-02-05 15:10:00', '2024-02-14 09:45:00'),
('jennifer.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jennifer Lee', '555-0112', '1990-01-20', '2024-02-06 10:30:00', '2024-02-13 12:20:00'),
('christopher.white@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Christopher White', '555-0113', '1988-07-08', '2024-02-07 14:15:00', '2024-02-14 15:10:00'),
('michelle.harris@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michelle Harris', '555-0114', '1992-05-26', '2024-02-08 09:00:00', '2024-02-12 10:35:00'),
('matthew.clark@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Matthew Clark', '555-0115', '1991-12-11', '2024-02-09 16:45:00', '2024-02-14 11:20:00'),
('laura.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura Rodriguez', '555-0116', '1987-03-05', '2024-02-10 11:30:00', '2024-02-13 14:40:00'),
('kevin.lewis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kevin Lewis', '555-0117', '1993-08-18', '2024-02-11 13:20:00', '2024-02-14 16:15:00'),
('stephanie.walker@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Stephanie Walker', '555-0118', '1989-11-02', '2024-02-12 08:45:00', '2024-02-13 09:30:00'),
('brian.hall@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Brian Hall', '555-0119', '1994-04-22', '2024-02-13 15:30:00', '2024-02-14 13:45:00'),
('nicole.allen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nicole Allen', '555-0120', '1990-09-14', '2024-02-14 10:00:00', '2024-02-14 17:20:00');

-- ============================================
-- TEST ORDERS (30 orders with various statuses)
-- ============================================
INSERT INTO orders (user_id, order_number, status_id, order_date, shipping_address_line1, shipping_address_line2, shipping_city, shipping_state, shipping_zip, tracking_number, price, payment_status, payment_id, notes) VALUES
-- Recent orders (Results Ready - status 5)
(1, 'LGM-2024-00001', 5, '2024-01-10 10:35:00', '123 Main St', 'Apt 4B', 'Boston', 'MA', '02108', '1Z999AA10123456784', 99.00, 'completed', 'pi_3abc123def456', 'First order - completed successfully'),
(2, 'LGM-2024-00002', 5, '2024-01-12 09:20:00', '456 Oak Ave', NULL, 'Austin', 'TX', '78701', '1Z999AA10123456785', 99.00, 'completed', 'pi_3abc123def457', NULL),
(3, 'LGM-2024-00003', 5, '2024-01-15 11:25:00', '789 Pine Rd', 'Suite 200', 'San Francisco', 'CA', '94102', '1Z999AA10123456786', 99.00, 'completed', 'pi_3abc123def458', 'Priority shipping requested'),

-- Processing orders (status 4)
(4, 'LGM-2024-00004', 4, '2024-02-01 14:50:00', '321 Elm St', NULL, 'Seattle', 'WA', '98101', '1Z999AA10123456787', 99.00, 'completed', 'pi_3abc123def459', NULL),
(5, 'LGM-2024-00005', 4, '2024-02-02 08:35:00', '654 Maple Dr', 'Unit 3', 'Portland', 'OR', '97201', '1Z999AA10123456788', 99.00, 'completed', 'pi_3abc123def460', NULL),
(6, 'LGM-2024-00006', 4, '2024-02-03 16:10:00', '987 Birch Ln', NULL, 'Denver', 'CO', '80202', '1Z999AA10123456789', 99.00, 'completed', 'pi_3abc123def461', 'Couple screening together'),
(7, 'LGM-2024-00007', 4, '2024-02-04 10:20:00', '147 Cedar Ave', 'Apt 12', 'Phoenix', 'AZ', '85001', '1Z999AA10123456790', 99.00, 'completed', 'pi_3abc123def462', NULL),

-- Sample Received (status 3)
(8, 'LGM-2024-00008', 3, '2024-02-05 13:45:00', '258 Spruce St', NULL, 'San Diego', 'CA', '92101', '1Z999AA10123456791', 99.00, 'completed', 'pi_3abc123def463', NULL),
(9, 'LGM-2024-00009', 3, '2024-02-06 09:15:00', '369 Willow Way', 'Suite 5', 'Las Vegas', 'NV', '89101', '1Z999AA10123456792', 99.00, 'completed', 'pi_3abc123def464', NULL),
(10, 'LGM-2024-00010', 3, '2024-02-07 15:30:00', '741 Ash Blvd', NULL, 'Miami', 'FL', '33101', '1Z999AA10123456793', 99.00, 'completed', 'pi_3abc123def465', 'Rush processing requested'),
(11, 'LGM-2024-00011', 3, '2024-02-08 11:05:00', '852 Palm St', 'Unit 7', 'Orlando', 'FL', '32801', '1Z999AA10123456794', 99.00, 'completed', 'pi_3abc123def466', NULL),
(12, 'LGM-2024-00012', 3, '2024-02-09 14:40:00', '963 Beech Rd', NULL, 'Tampa', 'FL', '33601', '1Z999AA10123456795', 99.00, 'completed', 'pi_3abc123def467', NULL),

-- Kit Shipped (status 2)
(13, 'LGM-2024-00013', 2, '2024-02-10 08:25:00', '159 Walnut Ave', 'Apt 2C', 'Atlanta', 'GA', '30301', '1Z999AA10123456796', 99.00, 'completed', 'pi_3abc123def468', NULL),
(14, 'LGM-2024-00014', 2, '2024-02-10 16:50:00', '357 Cherry Ln', NULL, 'Charlotte', 'NC', '28201', '1Z999AA10123456797', 99.00, 'completed', 'pi_3abc123def469', NULL),
(15, 'LGM-2024-00015', 2, '2024-02-11 10:15:00', '486 Hickory Dr', 'Suite 10', 'Raleigh', 'NC', '27601', '1Z999AA10123456798', 99.00, 'completed', 'pi_3abc123def470', NULL),
(16, 'LGM-2024-00016', 2, '2024-02-11 13:30:00', '597 Dogwood St', NULL, 'Nashville', 'TN', '37201', '1Z999AA10123456799', 99.00, 'completed', 'pi_3abc123def471', 'Gift order'),
(17, 'LGM-2024-00017', 2, '2024-02-12 09:45:00', '608 Poplar Blvd', 'Unit 15', 'Memphis', 'TN', '38101', '1Z999AA10123456800', 99.00, 'completed', 'pi_3abc123def472', NULL),
(18, 'LGM-2024-00018', 2, '2024-02-12 15:20:00', '719 Sycamore Way', NULL, 'Louisville', 'KY', '40201', '1Z999AA10123456801', 99.00, 'completed', 'pi_3abc123def473', NULL),

-- Order Received (status 1)
(19, 'LGM-2024-00019', 1, '2024-02-13 08:10:00', '820 Magnolia Ave', 'Apt 8', 'Indianapolis', 'IN', '46201', NULL, 99.00, 'completed', 'pi_3abc123def474', NULL),
(20, 'LGM-2024-00020', 1, '2024-02-13 11:35:00', '931 Redwood Rd', NULL, 'Columbus', 'OH', '43201', NULL, 99.00, 'completed', 'pi_3abc123def475', NULL),
(1, 'LGM-2024-00021', 1, '2024-02-13 14:50:00', '123 Main St', 'Apt 4B', 'Boston', 'MA', '02108', NULL, 99.00, 'completed', 'pi_3abc123def476', 'Second order from same customer'),
(11, 'LGM-2024-00022', 1, '2024-02-13 16:25:00', '852 Palm St', 'Unit 7', 'Orlando', 'FL', '32801', NULL, 99.00, 'completed', 'pi_3abc123def477', NULL),
(12, 'LGM-2024-00023', 1, '2024-02-14 09:00:00', '963 Beech Rd', NULL, 'Tampa', 'FL', '33601', NULL, 99.00, 'completed', 'pi_3abc123def478', NULL),
(13, 'LGM-2024-00024', 1, '2024-02-14 10:30:00', '159 Walnut Ave', 'Apt 2C', 'Atlanta', 'GA', '30301', NULL, 99.00, 'completed', 'pi_3abc123def479', NULL),
(14, 'LGM-2024-00025', 1, '2024-02-14 12:15:00', '357 Cherry Ln', NULL, 'Charlotte', 'NC', '28201', NULL, 99.00, 'completed', 'pi_3abc123def480', NULL),
(15, 'LGM-2024-00026', 1, '2024-02-14 13:45:00', '486 Hickory Dr', 'Suite 10', 'Raleigh', 'NC', '27601', NULL, 99.00, 'pending', NULL, 'Payment pending'),
(16, 'LGM-2024-00027', 1, '2024-02-14 15:20:00', '597 Dogwood St', NULL, 'Nashville', 'TN', '37201', NULL, 99.00, 'completed', 'pi_3abc123def481', NULL),
(17, 'LGM-2024-00028', 1, '2024-02-14 16:40:00', '608 Poplar Blvd', 'Unit 15', 'Memphis', 'TN', '38101', NULL, 99.00, 'completed', 'pi_3abc123def482', NULL),
(18, 'LGM-2024-00029', 1, '2024-02-14 17:55:00', '719 Sycamore Way', NULL, 'Louisville', 'KY', '40201', NULL, 99.00, 'completed', 'pi_3abc123def483', NULL),
(19, 'LGM-2024-00030', 1, '2024-02-14 18:30:00', '820 Magnolia Ave', 'Apt 8', 'Indianapolis', 'IN', '46201', NULL, 99.00, 'completed', 'pi_3abc123def484', 'Express shipping');

-- ============================================
-- TEST RESULTS (for completed orders only)
-- ============================================
INSERT INTO results (order_id, file_path, encrypted_filename, upload_date, uploaded_by, accessed_count, last_accessed, file_size, file_hash) VALUES
(1, 'results/2024/01/result_001.pdf', 'enc_a1b2c3d4e5f6g7h8.pdf', '2024-01-24 14:30:00', 1, 5, '2024-02-10 14:20:00', 245678, 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'),
(2, 'results/2024/01/result_002.pdf', 'enc_b2c3d4e5f6g7h8i9.pdf', '2024-01-26 16:45:00', 1, 3, '2024-02-12 16:45:00', 238945, 'b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7'),
(3, 'results/2024/01/result_003.pdf', 'enc_c3d4e5f6g7h8i9j0.pdf', '2024-01-29 10:20:00', 1, 2, '2024-02-08 10:30:00', 251234, 'c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8');

-- ============================================
-- ADDITIONAL ADMINS (lab techs and support)
-- ============================================
INSERT INTO admins (username, password_hash, email, role, is_active, created_at, last_login, created_by) VALUES
('lab_tech1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'labtech1@luckygenemdx.com', 'lab_tech', TRUE, '2024-01-05 09:00:00', '2024-02-14 08:30:00', 1),
('lab_tech2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'labtech2@luckygenemdx.com', 'lab_tech', TRUE, '2024-01-05 09:00:00', '2024-02-13 14:15:00', 1),
('support1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'support1@luckygenemdx.com', 'support', TRUE, '2024-01-05 09:00:00', '2024-02-14 16:45:00', 1),
('support2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'support2@luckygenemdx.com', 'support', TRUE, '2024-01-05 09:00:00', '2024-02-14 11:20:00', 1);

-- ============================================
-- BLOG POSTS (10 posts)
-- ============================================
INSERT INTO blog_posts (title, slug, content, excerpt, category, author_id, featured_image, published_at, is_published, views, created_at) VALUES
('Understanding Genetic Carrier Screening: A Complete Guide', 'understanding-genetic-carrier-screening-complete-guide', '<p>Genetic carrier screening is an important tool for family planning. This comprehensive guide explains everything you need to know about carrier screening, including what it tests for, who should consider it, and how to interpret results.</p><p>Carrier screening tests can identify whether you carry genetic variations that could be passed to your children...</p>', 'Learn everything about genetic carrier screening and why it matters for family planning.', 'Carrier Screening', 1, 'blog/carrier-screening-guide.jpg', '2024-01-15 10:00:00', TRUE, 1247, '2024-01-10 14:30:00'),

('The Top 5 Genetic Conditions Screened in Carrier Testing', 'top-5-genetic-conditions-carrier-testing', '<p>When considering carrier screening, it helps to understand the most common conditions tested. Here are the five most important genetic conditions that carrier screening can detect.</p><h3>1. Cystic Fibrosis</h3><p>Affecting 1 in 25 people of European descent...</p>', 'Discover the five most important genetic conditions detected through carrier screening.', 'Genetic Conditions', 1, 'blog/genetic-conditions.jpg', '2024-01-20 09:00:00', TRUE, 892, '2024-01-15 11:20:00'),

('Family Planning: When Should You Get Carrier Screening?', 'family-planning-when-carrier-screening', '<p>Timing is everything when it comes to carrier screening. This article discusses the optimal time to get tested and why early knowledge is crucial for family planning decisions.</p>', 'Find out the best time to get carrier screening for optimal family planning.', 'Family Planning', 1, 'blog/family-planning.jpg', '2024-01-25 11:30:00', TRUE, 1056, '2024-01-20 09:45:00'),

('What to Expect: The Carrier Screening Process', 'what-to-expect-carrier-screening-process', '<p>Wondering what happens during carrier screening? This step-by-step guide walks you through the entire process, from sample collection to receiving your results.</p>', 'A complete walkthrough of the carrier screening process from start to finish.', 'Testing Process', 1, 'blog/screening-process.jpg', '2024-02-01 10:00:00', TRUE, 734, '2024-01-28 13:15:00'),

('Interpreting Your Carrier Screening Results', 'interpreting-carrier-screening-results', '<p>Understanding your carrier screening results is crucial. This guide helps you make sense of your report and know what steps to take next.</p>', 'Learn how to read and understand your carrier screening results.', 'Results', 1, 'blog/results-interpretation.jpg', '2024-02-05 14:00:00', TRUE, 923, '2024-02-01 10:30:00'),

('Genetic Counseling: What It Is and Why It Matters', 'genetic-counseling-what-why-matters', '<p>Genetic counseling provides professional guidance for understanding genetic risks and making informed decisions. Learn why it is an important part of the carrier screening journey.</p>', 'Discover the role of genetic counseling in carrier screening.', 'Genetic Counseling', 1, 'blog/genetic-counseling.jpg', '2024-02-08 09:30:00', TRUE, 678, '2024-02-05 15:20:00'),

('Carrier Screening vs. Diagnostic Testing: What\'s the Difference?', 'carrier-screening-vs-diagnostic-testing', '<p>Many people confuse carrier screening with diagnostic testing. This article clarifies the key differences and when each type of test is appropriate.</p>', 'Understand the important differences between carrier screening and diagnostic testing.', 'Testing Information', 1, 'blog/screening-vs-diagnostic.jpg', '2024-02-10 11:00:00', TRUE, 845, '2024-02-07 14:45:00'),

('The Science Behind Carrier Screening Technology', 'science-behind-carrier-screening', '<p>Modern carrier screening uses advanced genetic sequencing technology. Learn about the scientific methods that make comprehensive carrier screening possible.</p>', 'Explore the cutting-edge technology that powers carrier screening.', 'Science', 1, 'blog/screening-technology.jpg', '2024-02-12 10:30:00', TRUE, 567, '2024-02-09 11:15:00'),

('Preparing for Pregnancy: A Carrier Screening Checklist', 'preparing-pregnancy-carrier-screening-checklist', '<p>Planning for pregnancy involves many steps. Use this comprehensive checklist to ensure you have addressed all aspects of genetic carrier screening.</p>', 'Your complete checklist for carrier screening before pregnancy.', 'Pregnancy', 1, 'blog/pregnancy-checklist.jpg', '2024-02-13 13:00:00', TRUE, 1134, '2024-02-11 09:30:00'),

('Common Myths About Genetic Carrier Screening', 'common-myths-genetic-carrier-screening', '<p>There are many misconceptions about carrier screening. This article debunks the most common myths and provides evidence-based facts.</p>', 'Separate fact from fiction with the truth about carrier screening myths.', 'Education', 1, 'blog/screening-myths.jpg', '2024-02-14 15:00:00', TRUE, 789, '2024-02-13 16:20:00');

-- ============================================
-- EDUCATIONAL RESOURCES (8 resources)
-- ============================================
INSERT INTO educational_resources (title, slug, content, excerpt, category, reading_time, views, published_at, is_published) VALUES
('What is a Genetic Carrier?', 'what-is-genetic-carrier', '<p>A genetic carrier is someone who has one copy of a gene mutation that, when present in two copies, causes a genetic disorder. Carriers are typically healthy but can pass the mutation to their children.</p><h3>Understanding Inheritance</h3><p>Most genetic conditions require two copies of a mutated gene...</p>', 'Learn the basics of what it means to be a genetic carrier.', 'Basic Concepts', 5, 2456, '2024-01-10 00:00:00', TRUE),

('How Genetic Inheritance Works', 'how-genetic-inheritance-works', '<p>Genetic inheritance follows specific patterns. Understanding these patterns helps you comprehend your carrier screening results and potential risks.</p>', 'Understand the principles of genetic inheritance.', 'Genetics Basics', 8, 1892, '2024-01-12 00:00:00', TRUE),

('Autosomal Recessive Conditions Explained', 'autosomal-recessive-conditions-explained', '<p>Autosomal recessive conditions are among the most common genetic disorders screened for in carrier testing. This guide explains how they are inherited and their implications.</p>', 'A comprehensive guide to autosomal recessive genetic conditions.', 'Inheritance Patterns', 10, 1567, '2024-01-15 00:00:00', TRUE),

('The Role of DNA in Carrier Screening', 'role-of-dna-carrier-screening', '<p>DNA contains the instructions for building and maintaining your body. Learn how analyzing DNA enables carrier screening.</p>', 'Discover how DNA analysis makes carrier screening possible.', 'Science', 7, 1234, '2024-01-18 00:00:00', TRUE),

('Ethnic-Specific Carrier Frequencies', 'ethnic-specific-carrier-frequencies', '<p>Certain genetic conditions are more common in specific ethnic populations. Understanding these differences is important for personalized screening.</p>', 'Learn about carrier frequency differences across ethnic groups.', 'Population Genetics', 6, 945, '2024-01-22 00:00:00', TRUE),

('Options When Both Partners Are Carriers', 'options-both-partners-carriers', '<p>If both partners are carriers for the same condition, several family planning options are available. This resource explains each option in detail.</p>', 'Explore family planning options when both partners are carriers.', 'Family Planning', 12, 1678, '2024-01-25 00:00:00', TRUE),

('Understanding Carrier Screening Reports', 'understanding-carrier-screening-reports', '<p>Your carrier screening report contains important genetic information. This guide helps you understand every section of your report.</p>', 'A detailed explanation of carrier screening report components.', 'Results', 9, 2134, '2024-01-28 00:00:00', TRUE),

('Genetic Terms You Should Know', 'genetic-terms-you-should-know', '<p>Genetics has its own vocabulary. This glossary defines common terms you will encounter in carrier screening.</p>', 'A helpful glossary of important genetic terminology.', 'Reference', 4, 1456, '2024-02-01 00:00:00', TRUE);

-- ============================================
-- ACTIVITY LOG (sample admin actions)
-- ============================================
INSERT INTO activity_log (admin_id, action, entity_type, entity_id, details, ip_address, created_at) VALUES
(1, 'login', NULL, NULL, 'Super admin login', '192.168.1.100', '2024-02-14 08:00:00'),
(1, 'upload_result', 'order', 1, 'Uploaded result for order LGM-2024-00001', '192.168.1.100', '2024-01-24 14:30:00'),
(1, 'upload_result', 'order', 2, 'Uploaded result for order LGM-2024-00002', '192.168.1.100', '2024-01-26 16:45:00'),
(2, 'login', NULL, NULL, 'Lab tech login', '192.168.1.105', '2024-02-14 08:30:00'),
(2, 'update_order_status', 'order', 4, 'Updated order status to Processing', '192.168.1.105', '2024-02-14 09:15:00'),
(3, 'login', NULL, NULL, 'Lab tech login', '192.168.1.106', '2024-02-14 09:00:00'),
(4, 'login', NULL, NULL, 'Support staff login', '192.168.1.110', '2024-02-14 10:00:00'),
(4, 'view_order', 'order', 20, 'Viewed order details', '192.168.1.110', '2024-02-14 10:30:00'),
(1, 'create_blog_post', 'blog', 10, 'Created new blog post', '192.168.1.100', '2024-02-13 16:20:00'),
(1, 'update_testimonial', 'testimonial', 1, 'Updated testimonial display order', '192.168.1.100', '2024-02-14 11:00:00');

-- ============================================
-- LOGIN ATTEMPTS (sample security data)
-- ============================================
INSERT INTO login_attempts (email, ip_address, attempted_at, success) VALUES
('admin@luckygenemdx.com', '192.168.1.100', '2024-02-14 08:00:00', TRUE),
('labtech1@luckygenemdx.com', '192.168.1.105', '2024-02-14 08:30:00', TRUE),
('wrong@email.com', '203.0.113.45', '2024-02-14 09:15:00', FALSE),
('wrong@email.com', '203.0.113.45', '2024-02-14 09:16:00', FALSE),
('wrong@email.com', '203.0.113.45', '2024-02-14 09:17:00', FALSE),
('support1@luckygenemdx.com', '192.168.1.110', '2024-02-14 10:00:00', TRUE),
('john.doe@email.com', '198.51.100.25', '2024-02-14 14:20:00', TRUE),
('sarah.johnson@email.com', '198.51.100.30', '2024-02-14 16:45:00', TRUE);

-- ============================================
-- EMAIL QUEUE (sample queued emails)
-- ============================================
INSERT INTO email_queue (recipient_email, subject, body, template, priority, status, attempts, created_at, sent_at) VALUES
('john.doe@email.com', 'Your Results Are Ready', 'Your carrier screening results are now available for viewing in your patient portal.', 'results_ready', 1, 'sent', 1, '2024-01-24 14:30:00', '2024-01-24 14:31:00'),
('sarah.johnson@email.com', 'Your Results Are Ready', 'Your carrier screening results are now available for viewing in your patient portal.', 'results_ready', 1, 'sent', 1, '2024-01-26 16:45:00', '2024-01-26 16:46:00'),
('michael.chen@email.com', 'Your Kit Has Shipped', 'Your LuckyGeneMdx carrier screening kit has been shipped. Tracking: 1Z999AA10123456786', 'kit_shipped', 3, 'sent', 1, '2024-01-17 10:00:00', '2024-01-17 10:01:00'),
('emily.williams@email.com', 'Order Confirmation', 'Thank you for your order! Order number: LGM-2024-00004', 'order_confirmation', 2, 'sent', 1, '2024-02-01 14:50:00', '2024-02-01 14:51:00'),
('brian.hall@email.com', 'Payment Reminder', 'Your payment for order LGM-2024-00026 is pending.', 'payment_reminder', 1, 'pending', 0, '2024-02-14 15:00:00', NULL),
('nicole.allen@email.com', 'Welcome to LuckyGeneMdx', 'Thank you for registering with LuckyGeneMdx!', 'welcome', 5, 'sent', 1, '2024-02-14 10:01:00', '2024-02-14 10:02:00');

-- ============================================
-- SUMMARY
-- ============================================
-- This test data includes:
-- - 20 test users with realistic information
-- - 30 orders across all status stages
-- - 3 completed results for finished orders
-- - 4 additional admin users (lab techs and support)
-- - 10 blog posts with various topics
-- - 8 educational resources
-- - Sample activity logs
-- - Login attempts (including failed attempts)
-- - Email queue entries (sent and pending)
-- 
-- All passwords are: Test@123
-- Admin password is: Admin@123
--
-- This provides a realistic test environment for:
-- - Testing the patient portal
-- - Testing the admin panel
-- - Testing order workflows
-- - Testing result delivery
-- - Testing email systems
-- - Testing content management
-- ============================================

SELECT 'Test data successfully inserted!' as Status;
SELECT 
    (SELECT COUNT(*) FROM users) as Users,
    (SELECT COUNT(*) FROM orders) as Orders,
    (SELECT COUNT(*) FROM results) as Results,
    (SELECT COUNT(*) FROM admins) as Admins,
    (SELECT COUNT(*) FROM testimonials) as Testimonials,
    (SELECT COUNT(*) FROM blog_posts) as BlogPosts,
    (SELECT COUNT(*) FROM educational_resources) as Resources,
    (SELECT COUNT(*) FROM activity_log) as ActivityLogs,
    (SELECT COUNT(*) FROM login_attempts) as LoginAttempts,
    (SELECT COUNT(*) FROM email_queue) as EmailQueue;
