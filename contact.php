<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();
$page_title = 'Contact Us';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | LuckyGeneMDx</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">
        <section class="page-header">
        <div class="container">
            <h1>How can we help?</h1>
            <p>Whether you are a patient looking for results or a clinician seeking partnership, our experts are here to assist.</p>
        </div>
    </section>

    <section class="section">
    <div class="contact-container">
        <div class="contact-triage">
            <div class="triage-card">
                <span class="triage-icon">👋</span>
                <h3>For Patients</h3>
                <p>Track your screening status, manage your results, or update your personal health profile.</p>
                <a href="user-portal/login.php" style="color: var(--medical-teal); font-weight: 600; text-decoration: none;">Go to Patient Portal →</a>
            </div>
            <div class="triage-card">
                <span class="triage-icon">🩺</span>
                <h3>For Clinicians</h3>
                <p>Request clinical consultation with our geneticists or inquire about institutional screening programs.</p>
                <a href="#form" style="color: var(--medical-teal); font-weight: 600; text-decoration: none;">Connect with MDx Team →</a>
            </div>
        </div>

        <div class="main-contact-grid" id="form">
            <div class="contact-info-sidebar">
                <h2 style="color: var(--primary-blue); margin-bottom: 30px;">Contact Information</h2>
                
                <div class="info-detail-item">
                    <span>📍</span>
                    <div>
                        <strong>Global Headquarters</strong>
                        1234 Genomic Way, Suite 500<br>
                        Cambridge, MA 02139
                    </div>
                </div>

                <div class="info-detail-item">
                    <span>📞</span>
                    <div>
                        <strong>Clinical Support</strong>
                        1-800-GENE-TEST (436-3837)<br>
                        Mon-Fri: 8AM - 8PM EST
                    </div>
                </div>

                <div class="info-detail-item">
                    <span>📧</span>
                    <div>
                        <strong>Email Us</strong>
                        support@luckygenemdx.com<br>
                        providers@luckygenemdx.com
                    </div>
                </div>

                <div style="margin-top: 40px; padding: 20px; background: white; border-radius: 12px;">
                    <p style="font-size: 0.8rem; color: var(--text-gray); margin: 0;">
                        <strong>Note for Patients:</strong> To protect your privacy, please do not include specific genetic variants or medical history in this contact form.
                    </p>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <form action="process-contact.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="fname" class="form-control" placeholder="Jane" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="lname" class="form-control" placeholder="Doe" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Professional Role</label>
                        <select name="role" class="form-control">
                            <option value="patient">Patient / Individual</option>
                            <option value="provider">Healthcare Provider</option>
                            <option value="partner">Corporate / Research Partner</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="jane.doe@example.com" required>
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
                    </div>

                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Please describe your inquiry..." required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
    </section>
    </main>


    <?php include 'includes/footer.php'; ?>
</body>
</html>