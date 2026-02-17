<style>
    /* CONFIGURABLE THEME SETTINGS 
       Change these values to update the UI instantly
    */
    :root {
        /* Colors */
        --brand-accent: #00f2fe;        /* Primary Glow Color */
        --brand-secondary: #4facfe;     /* Secondary Gradient */
        --bg-dark:rgb(62, 87, 64);             /* Base Background */
        
        /* Video Appearance */
        --video-opacity: 0.45;          /* 0.0 to 1.0 (Text legibility) */
        --video-grayscale: 0%;          /* 0% to 100% (Medical/Serious feel) */
        --video-blur: 2px;              /* Soften the video background */
        
        /* Glassmorphism Settings */
        --glass-blur: 25px;             /* Strength of the frosted glass */
        --glass-opacity: 0.08;          /* Transparency of the card */
        --glass-border: rgba(255, 255, 255, 0.15);
    }

    .hero {
        position: relative;
        height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: var(--bg-dark);
    }

    /* Configurable Video Layer */
    .hero-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
        opacity: var(--video-opacity);
        filter: blur(var(--video-blur)) grayscale(var(--video-grayscale));
        transition: all 0.5s ease;
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 2;
        background: radial-gradient(circle at center, transparent 0%, var(--bg-dark) 90%);
    }

    /* Configurable Glass Card */
    .hero-card {
        position: relative;
        z-index: 10;
        max-width: 800px;
        padding: 60px 40px;
        background: rgba(255, 255, 255, var(--glass-opacity));
        backdrop-filter: blur(var(--glass-blur));
        -webkit-backdrop-filter: blur(var(--glass-blur));
        border: 1px solid var(--glass-border);
        border-radius: 40px;
        text-align: center;
        margin: 20px;
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
    }

    .hero-card h1 {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(2rem, 5vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 20px;
        color: #fff;
    }

    .hero-card h1 span {
        background: linear-gradient(135deg, var(--brand-accent), var(--brand-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-mdx {
        display: inline-block;
        padding: 18px 45px;
        background: var(--brand-accent);
        color: var(--bg-dark);
        border-radius: 100px;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 30px rgba(0, 242, 254, 0.3);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-mdx:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0, 242, 254, 0.5);
    }
</style>

<section class="hero">
    <video id="mdxHeroVideo" class="hero-video" autoplay muted playsinline>
        <source src="assets/video/My580.mp4" type="video/mp4">
    </video>
    
    <div class="hero-overlay"></div>

    <div class="hero-card">
        <h1>Precision MDx<br><span>Reimagined.</span></h1>
        <p style="color: rgba(255,255,255,0.8); margin-bottom: 40px; font-size: 1.2rem;">
            Experience the next generation of genetic insights with clinical accuracy and zero friction.
        </p>
        <a href="request-kit.php" class="btn-mdx">Get Your Kit - $99</a>
    </div>
</section>

<script>
    /**
     * VIDEO CONTROLLER CONFIG
     */
    const videoConfig = {
        playbackSpeed: 0.65, // Slow for cinematic effect, Fast for high energy
        pingPongMode: true,  // When true, it resets smoothly; false = standard loop
        startAtSecond: 0     // If your video has a boring start, skip it
    };

    const videoElem = document.getElementById('mdxHeroVideo');

    // Apply speed
    videoElem.playbackRate = videoConfig.playbackSpeed;
    videoElem.currentTime = videoConfig.startAtSecond;

    // Handle "Forward and Backward" feel via smooth restart
    videoElem.addEventListener('ended', function() {
        if(videoConfig.pingPongMode) {
            // Smoothly restart from beginning
            this.currentTime = videoConfig.startAtSecond;
            this.play();
        }
    });

    // Handle visibility (Pause video when user changes tab to save CPU/Battery)
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) videoElem.pause();
        else videoElem.play();
    });
</script>