<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
session_start();
setSecurityHeaders();
$page_title = 'Frequently Asked Questions';
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

    <section class="faq-hero">
        <div class="hero-dna-overlay"></div>
        <div class="container">
            <div class="hero-badge">Resource Center</div>
            <h1>Common Questions</h1>
            <p class="hero-subtitle">Clear answers about carrier screening, clinical accuracy, and your genetic journey.</p>
        </div>
    </section>

    <main class="faq-container">
        <div class="faq-tabs">
            <button class="tab-btn active" onclick="filterFaq('general')">General</button>
            <button class="tab-btn" onclick="filterFaq('clinical')">Clinical & Science</button>
            <button class="tab-btn" onclick="filterFaq('privacy')">Privacy</button>
        </div>

        <div id="faq-list">
            <div class="faq-item" data-category="general">
                <div class="faq-question">What exactly is carrier screening? <span class="plus-icon">+</span></div>
                <div class="faq-answer">
                    Carrier screening is a type of genetic test that can tell you whether you carry a gene for certain genetic disorders. When it is done before or during pregnancy, it allows you to find out the chances of having a child with a genetic disorder like Cystic Fibrosis or Spinal Muscular Atrophy.
                </div>
            </div>

            <div class="faq-item" data-category="general">
                <div class="faq-question">How is the sample collected? <span class="plus-icon">+</span></div>
                <div class="faq-answer">
                    LuckyGeneMDx uses non-invasive collection methods. You can provide a small saliva sample or a standard blood draw at one of our partner clinics. Our home collection kits use professional-grade stabilizing buffers to ensure DNA integrity during transit.
                </div>
            </div>

            <div class="faq-item" data-category="clinical">
                <div class="faq-question">How accurate are the MDx results? <span class="plus-icon">+</span></div>
                <div class="faq-answer">
                    Our molecular diagnostic (MDx) testing utilizes Next-Generation Sequencing (NGS) with 99.9% clinical sensitivity for the variants we screen. However, no genetic test can detect 100% of all possible mutations. This is referred to as "Residual Risk."
                </div>
            </div>

            <div class="faq-item" data-category="clinical">
                <div class="faq-question">What if I test positive as a carrier? <span class="plus-icon">+</span></div>
                <div class="faq-answer">
                    Being a carrier usually does not affect your own health. However, if your partner is also a carrier for the same condition, there is a 25% chance (1 in 4) that your child could be affected. We recommend discussing results with a board-certified genetic counselor.
                </div>
            </div>

            <div class="faq-item" data-category="privacy">
                <div class="faq-question">Who has access to my genetic data? <span class="plus-icon">+</span></div>
                <div class="faq-answer">
                    Under HIPAA regulations, your data is only accessible to you, the laboratory clinical team, and your ordering physician. LuckyGeneMDx does not sell your data to pharmaceutical companies or insurance providers.
                </div>
            </div>
        </div>

        <div class="cta-footer">
            <h3>Still have questions?</h3>
            <p>Our clinical support team is available Mon-Fri, 8AM - 8PM EST.</p>
            <div style="margin-top: 20px;">
                <a href="contact.php" class="nav-btn" style="background: var(--primary-blue); color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px;">Contact Support</a>
            </div>
        </div>
    </main>

    <script>
        // Accordion Toggle
        document.querySelectorAll('.faq-question').forEach(item => {
            item.addEventListener('click', () => {
                const parent = item.parentElement;
                parent.classList.toggle('active');
            });
        });

        // Filter Logic
        function filterFaq(category) {
            // Update Tab Buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            // Show/Hide Items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (category === 'all' || item.getAttribute('data-category') === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>