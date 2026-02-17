<?php
require_once 'includes/config.php';
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
    <style>
        :root {
            --primary-blue: #0A1F44;
            --medical-teal: #00B3A4;
            --text-gray: #4A5568;
            --light-bg: #F7FAFC;
            --white: #FFFFFF;
            --border-color: #E2E8F0;
        }

        /* ===== CONTACT HERO ===== */
        .contact-hero {
            position: relative;
            background: linear-gradient(135deg, var(--color-primary-deep-blue) 0%, var(--color-medical-teal) 75%, var(--color-soft-purple) 100%);
            color: var(--white);
            padding: 100px 20px 140px;
            text-align: center;
            overflow: hidden;
        }

        .hero-dna-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.05;
            background-image: radial-gradient(var(--medical-teal) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .contact-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 20px;
        }

        /* ===== TRIAGE CARDS (Patient vs Provider) ===== */
        .contact-container {
            max-width: 1100px;
            margin: -80px auto 80px;
            position: relative;
            z-index: 20;
            padding: 0 20px;
        }

        .contact-triage {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .triage-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(10, 31, 68, 0.08);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .triage-card:hover { transform: translateY(-5px); }

        .triage-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .triage-card h3 { color: var(--primary-blue); margin-bottom: 10px; }
        .triage-card p { font-size: 0.95rem; color: var(--text-gray); margin-bottom: 20px; }

        /* ===== CONTACT FORM & INFO ===== */
        .main-contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(10, 31, 68, 0.1);
        }

        .contact-info-sidebar {
            background: var(--light-bg);
            padding: 50px;
            border-right: 1px solid var(--border-color);
        }

        .contact-form-wrapper { padding: 50px; }

        /* ===== FORM STYLING ===== */
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-blue);
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--medical-teal);
            box-shadow: 0 0 0 3px rgba(0, 179, 164, 0.1);
        }

        .submit-btn {
            background: var(--medical-teal);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }

        .submit-btn:hover { background: #00968a; }

        /* ===== DETAILS ===== */
        .info-detail-item {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-detail-item strong { color: var(--primary-blue); display: block; }

        @media (max-width: 992px) {
            .main-contact-grid { grid-template-columns: 1fr; }
            .contact-info-sidebar { border-right: none; border-bottom: 1px solid var(--border-color); }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="contact-hero">
        <div class="hero-dna-overlay"></div>
        <div class="container">
            <div class="hero-badge">Get in Touch</div>
            <h1>How can we help?</h1>
            <p class="hero-subtitle">Whether you are a patient looking for results or a clinician seeking partnership, our MDx experts are here to assist.</p>
        </div>
    </section>

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

    <?php include 'includes/footer.php'; ?>
</body>
</html>