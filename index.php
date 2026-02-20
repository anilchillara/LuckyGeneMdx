<?php
define('luckygenemdx', true);
require_once 'includes/config.php';
require_once 'includes/Database.php';
session_start();
setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure Your Family's Genetic Future with LuckyGeneMDx comprehensive carrier screening. $99 genetic testing kit with results in 14-21 days.">
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    <title>LuckyGeneMDx - Comprehensive Genetic Carrier Screening | $99</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>
    <a href="#main-content" class="skip-link" style="position:absolute;top:-40px;left:0;padding:8px 16px;background:var(--teal);color:white;z-index:9999;border-radius:0 0 8px 0;transition:top 0.3s;text-decoration:none;font-size:0.9rem;">Skip to main content</a>
    <?php include 'includes/navbar.php'; ?>

    <main id="main-content">

        <!-- ══ HERO ════════════════════════════════════════ -->
        <section class="hero" aria-labelledby="hero-heading">
            <div class="hero-video-wrap">
                <!--
                    Place your DNA video at: assets/videos/dna-hero.mp4
                    The screenshot shows the gorgeous glass DNA double-helix
                    on a cool steel-blue background — this overlay preserves
                    those icy blues and teal glows from the video.
                -->
                <video autoplay muted loop playsinline preload="auto" aria-hidden="true">
                    <source src="assets/video/My580.webm" type="video/webm">    
                    <source src="assets/video/My580.mp4" type="video/mp4">
                </video>
            </div>
            <div class="hero-overlay" aria-hidden="true"></div>
            <div class="hero-scan"    aria-hidden="true"></div>
            <div class="hero-orb"     aria-hidden="true"></div>
            <div class="hero-fade-bottom" aria-hidden="true"></div>

            <div class="hero-content">
                <div class="hero-pill">
                    <span class="hero-pill-dot"></span>
                    Comprehensive Carrier Screening
                </div>

                <h1 id="hero-heading">
                    Secure Your<br>
                    Family's <span class="accent">Genetic</span><br>
                    Future
                </h1>

                <p class="hero-desc">
                    Carrier screening for over 300 genetic conditions.
                    Know your status before family planning begins — 
                    clear, private results in 14–21 days.
                </p>

                <div class="hero-btns">
                    <a href="request-kit.php" class="btn-primary-hero">
                        Request Screening Kit — $99
                        <svg class="btn-arrow" width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M3 9h12M9 3l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="about-genetic-screening.php" class="btn-ghost-hero">
                        Learn More
                    </a>
                </div>

                <div class="hero-trust">
                    <div class="hero-trust-item"><span class="hero-trust-check"></span>ACMG Aligned</div>
                    <div class="hero-trust-item"><span class="hero-trust-check"></span>Secure &amp; Private</div>
                    <div class="hero-trust-item"><span class="hero-trust-check"></span>CDC Reference Standards</div>
                    <div class="hero-trust-item"><span class="hero-trust-check"></span>Results in 14–21 Days</div>
                </div>
            </div>

            <div class="scroll-cue" aria-hidden="true">
                <div class="scroll-cue-mouse"></div>
                scroll
            </div>
        </section>

        <!-- ══ STATS BAND ═══════════════════════════════════ -->
        <div class="stats-band">
            <div class="stats-band-inner">
                <div class="stat-cell"><div class="stat-num" data-count="300">300+</div><div class="stat-lbl">Conditions Screened</div></div>
                <div class="stat-cell"><div class="stat-num">$99</div><div class="stat-lbl">Flat Transparent Price</div></div>
                <div class="stat-cell"><div class="stat-num" data-count="21">21</div><div class="stat-lbl">Day Results Maximum</div></div>
                <div class="stat-cell"><div class="stat-num">ACMG</div><div class="stat-lbl">Aligned Standards</div></div>
            </div>
        </div>

        <!-- ══ AWARENESS ════════════════════════════════════ -->
        <section aria-labelledby="awareness-heading" style="background:var(--white);">
            <div class="sec-awareness">
                <div class="awareness-grid">
                    <div class="reveal">
                        <span class="sec-tag">What Is It?</span>
                        <h2 id="awareness-heading">What is Genetic Carrier Screening?</h2>
                        <p>Carrier screening is a type of genetic test that identifies whether you carry a gene mutation for certain inherited disorders. Even if you don't have the condition yourself, you could pass it to your children.</p>
                        <p>Understanding your carrier status empowers you to make informed decisions about family planning — giving you the time to consult with healthcare providers and explore all your options.</p>
                        <ul class="check-list">
                            <li><span class="check-icon"></span><span><strong>Early Knowledge</strong> — Identify risks before pregnancy begins</span></li>
                            <li><span class="check-icon"></span><span><strong>Informed Decisions</strong> — Work with your doctor on family planning options</span></li>
                            <li><span class="check-icon"></span><span><strong>Peace of Mind</strong> — Understand your full genetic health status</span></li>
                            <li><span class="check-icon"></span><span><strong>Family Awareness</strong> — Information that benefits your entire family</span></li>
                        </ul>
                        <a href="about-genetic-screening.php" class="btn-learn">
                            Learn About Carrier Screening
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M8 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    <div class="diagram-card reveal reveal-d2">
                        <h4>Recessive Inheritance Pattern</h4>
                        <svg viewBox="0 0 400 290" style="width:100%;max-width:420px;display:block;margin:0 auto;">
                            <circle cx="100" cy="48" r="34" fill="#00B3A4" fill-opacity="0.15" stroke="#00B3A4" stroke-width="1.5"/>
                            <text x="100" y="44" text-anchor="middle" fill="#0A1F44" font-size="12" font-weight="700" font-family="Poppins,sans-serif">Parent 1</text>
                            <text x="100" y="61" text-anchor="middle" fill="#00B3A4" font-size="11" font-family="Inter,sans-serif">Carrier</text>
                            <circle cx="300" cy="48" r="34" fill="#00B3A4" fill-opacity="0.15" stroke="#00B3A4" stroke-width="1.5"/>
                            <text x="300" y="44" text-anchor="middle" fill="#0A1F44" font-size="12" font-weight="700" font-family="Poppins,sans-serif">Parent 2</text>
                            <text x="300" y="61" text-anchor="middle" fill="#00B3A4" font-size="11" font-family="Inter,sans-serif">Carrier</text>
                            <line x1="100" y1="82" x2="75"  y2="192" stroke="#c8d4e8" stroke-width="1.5"/>
                            <line x1="100" y1="82" x2="155" y2="192" stroke="#c8d4e8" stroke-width="1.5"/>
                            <line x1="300" y1="82" x2="245" y2="192" stroke="#c8d4e8" stroke-width="1.5"/>
                            <line x1="300" y1="82" x2="325" y2="192" stroke="#c8d4e8" stroke-width="1.5"/>
                            <circle cx="75"  cy="218" r="26" fill="#22c55e" fill-opacity="0.85"/>
                            <text x="75"  y="214" text-anchor="middle" fill="white" font-size="11" font-weight="700" font-family="Inter,sans-serif">25%</text>
                            <text x="75"  y="258" text-anchor="middle" fill="#6b7a99" font-size="10" font-family="Inter,sans-serif">Not Affected</text>
                            <circle cx="155" cy="218" r="26" fill="#00B3A4" fill-opacity="0.6"/>
                            <text x="155" y="214" text-anchor="middle" fill="#0A1F44" font-size="11" font-weight="700" font-family="Inter,sans-serif">50%</text>
                            <text x="155" y="258" text-anchor="middle" fill="#6b7a99" font-size="10" font-family="Inter,sans-serif">Carrier</text>
                            <circle cx="245" cy="218" r="26" fill="#00B3A4" fill-opacity="0.6"/>
                            <text x="245" y="214" text-anchor="middle" fill="#0A1F44" font-size="11" font-weight="700" font-family="Inter,sans-serif">50%</text>
                            <text x="245" y="258" text-anchor="middle" fill="#6b7a99" font-size="10" font-family="Inter,sans-serif">Carrier</text>
                            <circle cx="325" cy="218" r="26" fill="#ef4444" fill-opacity="0.8"/>
                            <text x="325" y="214" text-anchor="middle" fill="white" font-size="11" font-weight="700" font-family="Inter,sans-serif">25%</text>
                            <text x="325" y="258" text-anchor="middle" fill="#6b7a99" font-size="10" font-family="Inter,sans-serif">Affected</text>
                        </svg>
                        <p style="text-align:center;margin-top:1.5rem;font-size:0.85rem;color:var(--gray);line-height:1.65;">
                            When both parents are carriers, each pregnancy carries a 25% chance of the child being affected.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ STANDARDS ════════════════════════════════════ -->
        <section class="sec-standards">
            <div class="standards-inner">
                <span class="sec-tag" style="color:var(--ice);">Our Standards</span>
                <h2>Aligned with Medical Genetics Standards</h2>
                <p>Our comprehensive carrier screening follows guidelines from leading medical organizations, ensuring you receive accurate, reliable genetic information.</p>
                <div class="standards-grid">
                    <div class="std-card reveal reveal-d1"><div class="std-num">300+</div><p>Genetic Conditions Screened</p></div>
                    <div class="std-card reveal reveal-d2"><div class="std-num">ACMG</div><p>Standards Alignment</p></div>
                    <div class="std-card reveal reveal-d3"><div class="std-num">$99</div><p>Affordable, Transparent Pricing</p></div>
                </div>
                <p class="std-disclaimer"><em>LuckyGeneMDx is not affiliated with ACMG or CDC. Our testing aligns with standards established by these organizations.</em></p>
            </div>
        </section>

        <!-- ══ TIMELINE ══════════════════════════════════════ -->
        <section class="sec-timeline" aria-labelledby="timeline-heading">
            <div class="timeline-wrap">
                <div class="tl-header reveal">
                    <span class="sec-tag">Your Journey</span>
                    <h2 id="timeline-heading">Plan Today. Protect Tomorrow.</h2>
                    <p>Understanding your genetic carrier status is a powerful step in your family planning journey. Here's how knowledge empowers each life stage.</p>
                </div>
                <div class="tl-track">
                    <div class="tl-row"><div class="tl-bubble">👫</div><div class="tl-text"><h4>Pre-Marriage</h4><p>Learn your genetic status early in your relationship to make informed decisions together about your future family.</p></div></div>
                    <div class="tl-row"><div class="tl-bubble">💑</div><div class="tl-text"><h4>Before Trying to Conceive</h4><p>Get screened before pregnancy begins. This gives you maximum time to consult with genetic counselors and understand your options.</p></div></div>
                    <div class="tl-row"><div class="tl-bubble">📅</div><div class="tl-text"><h4>Family Planning</h4><p>Work with your healthcare provider to understand options and plan for a healthy pregnancy based on your results.</p></div></div>
                    <div class="tl-row"><div class="tl-bubble">🤰</div><div class="tl-text"><h4>During Pregnancy</h4><p>If you're already pregnant, carrier screening can still provide valuable information for your healthcare team.</p></div></div>
                    <div class="tl-row"><div class="tl-bubble">👶</div><div class="tl-text"><h4>Your Child's Future</h4><p>Your genetic knowledge can benefit your child's health throughout their lifetime and inform their own family planning journey.</p></div></div>
                </div>
            </div>
        </section>

        <!-- ══ TESTIMONIALS ══════════════════════════════════ -->
        <section class="sec-testimonials" aria-labelledby="testimonials-heading">
            <div class="test-header reveal">
                <span class="sec-tag">Stories</span>
                <h2 id="testimonials-heading">Trusted by Families Nationwide</h2>
                <p>Real families. Real decisions. Real peace of mind.</p>
            </div>
            <?php
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT name, age, location, quote FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
                $stmt->execute();
                $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($testimonials)):
            ?>
            <div class="test-track-wrap">
                <div class="testimonials-track" id="testimonialsTrack">
                    <?php foreach ($testimonials as $t): ?>
                    <div class="testimonial-slide">
                        <div class="test-card">
                            <div class="test-quote"><?php echo htmlspecialchars($t['quote']); ?></div>
                            <div class="test-author"><?php echo htmlspecialchars($t['name']); ?><?php echo !empty($t['age']) ? ', '.(int)$t['age'] : ''; ?></div>
                            <?php if (!empty($t['location'])): ?>
                            <div class="test-location"><?php echo htmlspecialchars($t['location']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (count($testimonials) > 1): ?>
            <div class="carousel-nav-controls">
                <button class="carousel-nav-btn" id="prevBtn" onclick="changeTest(-1)" aria-label="Previous">&#8249;</button>
                <div class="carousel-indicator-dots" id="testDots">
                    <?php for($i=0; $i<count($testimonials); $i++): ?>
                    <div class="carousel-indicator-dot <?php echo $i===0?'active':''; ?>" onclick="goTest(<?php echo $i; ?>)"></div>
                    <?php endfor; ?>
                </div>
                <button class="carousel-nav-btn" id="nextBtn" onclick="changeTest(1)" aria-label="Next">&#8250;</button>
            </div>
            <div style="text-align:center;margin-top:1rem;color:var(--gray);font-size:0.85rem;">
                <span id="testCounter">1 / <?php echo count($testimonials); ?></span>
            </div>
            <?php endif; ?>
            <?php
                else: echo '<p style="text-align:center;color:var(--gray);">Join hundreds of families securing their future today.</p>';
                endif;
            } catch(Exception $e){ error_log($e->getMessage()); }
            ?>
        </section>

        <!-- ══ CTA ══════════════════════════════════════════ -->
        <section class="sec-cta">
            <div class="cta-box reveal">
                <div class="cta-pill">Limited Time Pricing</div>
                <h2>Invest Today for<br>a Healthier Tomorrow</h2>
                <p>Take the first step toward informed family planning with comprehensive genetic carrier screening. Clear insights, delivered privately.</p>
                <div class="cta-pricing">
                    <span class="cta-price">$99</span>
                    <span class="cta-strike">$249</span>
                </div>
                <a href="request-kit.php" class="btn-cta-main">
                    Get Your Screening Kit
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 9h12M9 3l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <div class="cta-meta">
                    <div class="cta-meta-item">Secure checkout</div>
                    <div class="cta-meta-item">Private results</div>
                    <div class="cta-meta-item">Expert support</div>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
    <script>
    // ── Universal Intersection Observer ─────────────────
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in-view'); io.unobserve(e.target); }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal, .tl-row').forEach(el => io.observe(el));

    // ── Stats counter animation ──────────────────────────
    const counterIO = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const raw = el.dataset.count;
            if (!raw) return;
            const target = parseFloat(raw);
            const suffix = el.textContent.replace(/[\d.]/g, '');
            let startTime = null;
            const step = ts => {
                if (!startTime) startTime = ts;
                const p = Math.min((ts - startTime) / 1400, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(eased * target) + suffix;
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
            counterIO.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-num[data-count]').forEach(el => counterIO.observe(el));

    // ── Testimonials carousel ────────────────────────────
    let curT = 0, totalT = 0, timer = null;

    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('testimonialsTrack');
        if (!track) return;
        totalT = track.querySelectorAll('.testimonial-slide').length;
        if (totalT <= 1) return;
        updateT();
        startT();
        track.addEventListener('mouseenter', stopT);
        track.addEventListener('mouseleave', startT);
    });

    function changeTest(d) { curT = (curT + d + totalT) % totalT; updateT(); stopT(); startT(); }
    function goTest(i)      { curT = i; updateT(); stopT(); startT(); }

    function updateT() {
        const track   = document.getElementById('testimonialsTrack');
        const dots    = document.querySelectorAll('.carousel-indicator-dot');
        const counter = document.getElementById('testCounter');
        if (!track) return;
        track.style.transform = `translateX(-${curT * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === curT));
        if (counter) counter.textContent = `${curT + 1} / ${totalT}`;
    }

    function startT() { stopT(); if (totalT > 1) timer = setInterval(() => changeTest(1), 4500); }
    function stopT()  { if (timer) { clearInterval(timer); timer = null; } }

    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft')  changeTest(-1);
        if (e.key === 'ArrowRight') changeTest(1);
    });
    </script>
</body>
</html>