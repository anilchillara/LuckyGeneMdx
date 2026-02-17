<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(255, 255, 255, 0.15);
        --accent-teal: #00f2fe;
        --accent-blue: #4facfe;
    }

    /* Next-Gen Hero Container */
    .hero {
        position: relative;
        height: 100vh;
        min-height: 700px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #ffffff;
        text-align: center;
    }

    /* Video/Background Layer */
    .hero-video-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        background: linear-gradient(45deg, #0a1f44, #1a237e); /* Fallback */
    }

    .hero-video-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.4) saturate(1.2);
    }

    /* Overlay Gradient for Readability */
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at center, transparent 0%, rgba(10, 31, 68, 0.8) 100%);
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 10;
        max-width: 900px;
        padding: 0 20px;
    }

    #hero-heading {
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1.1;
        background: linear-gradient(to right, #fff, var(--accent-teal));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem;
    }

    .hero-subtitle {
        font-size: clamp(1.1rem, 2vw, 1.4rem);
        opacity: 0.9;
        margin-bottom: 3rem;
        font-family: 'Inter', sans-serif;
        font-weight: 300;
        line-height: 1.6;
    }

    /* Glassmorphic CTA Section */
    .hero-cta {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-nextgen {
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
    }

    .btn-primary-glow {
        background: var(--accent-teal);
        color: #0a1f44;
        box-shadow: 0 0 20px rgba(0, 242, 254, 0.4);
    }

    .btn-primary-glow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 40px rgba(0, 242, 254, 0.6);
    }

    .btn-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        color: white;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }

    /* Trust Badges Glass Design */
    .trust-badges {
        margin-top: 4rem;
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .trust-badge-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(5px);
        border: 1px solid var(--glass-border);
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<section class="hero" aria-labelledby="hero-heading">
    <div class="hero-video-container">
        <video autoplay muted loop playsinline poster="assets/images/hero-fallback.jpg">
            <source src="assets/video/DNA_FULL.mp4" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>
    
    <div class="hero-content">
        <h1 id="hero-heading">Secure Your Family's <br>Genetic Future</h1>
        <p class="hero-subtitle">
            Experience the next generation of carrier screening. Over 300 genetic conditions analyzed with clinical precision for just $99.
        </p>
        
        <div class="hero-cta">
            <a href="request-kit.php" class="btn-nextgen btn-primary-glow">
                Request Kit - $99
            </a>
            <a href="about-genetic-screening.php" class="btn-nextgen btn-glass">
                Explainer Video
            </a>
        </div>
        
        <div class="trust-badges">
            <div class="trust-badge-glass">
                <span>🧬</span> ACMG Standards
            </div>
            <div class="trust-badge-glass">
                <span>🔒</span> HIPAA Compliant
            </div>
            <div class="trust-badge-glass">
                <span>⚡</span> 14-Day Results
            </div>
        </div>
    </div>
</section>