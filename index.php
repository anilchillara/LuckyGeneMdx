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
    <style>
        :root {
            --teal: #00B3A4; --teal-glow: rgba(0,179,164,0.35);
            --navy: #0A1F44; --navy2: #0d2554;
            --ice: #4ef0e6; --white: #ffffff; --off: #f4f6fa; --gray: #6b7a99;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: var(--white); color: var(--navy); overflow-x: hidden; }
        h1,h2,h3,h4,h5 { font-family: 'Poppins', sans-serif; }

        /* ── HERO ──────────────────────────────────── */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 700px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-video-wrap {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        .hero-video-wrap video {
            width: 100%; height: 100%;
            object-fit: cover;
            object-position: center 30%;
        }
        /* Layered overlay: strong navy bottom-left, teal hint top-right */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg,
                    rgba(10,31,68,0.88) 0%,
                    rgba(10,31,68,0.62) 45%,
                    rgba(0,179,164,0.22) 100%);
            z-index: 1;
        }
        /* Fine scanline texture over video */
        .hero-scan {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: repeating-linear-gradient(
                0deg, transparent, transparent 3px,
                rgba(0,179,164,0.018) 3px, rgba(0,179,164,0.018) 4px
            );
            pointer-events: none;
        }
        /* Atmospheric glow orb top-right */
        .hero-orb {
            position: absolute;
            width: 700px; height: 700px;
            top: -180px; right: -180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.22) 0%, transparent 68%);
            z-index: 2;
            animation: orbFloat 8s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes orbFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(-20px,30px) scale(1.05); }
            66%      { transform: translate(15px,-20px) scale(0.97); }
        }
        /* Bottom fade into next section */
        .hero-fade-bottom {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 160px;
            background: linear-gradient(to bottom, transparent, var(--navy));
            z-index: 3;
            pointer-events: none;
        }

        /* Hero content */
        .hero-content {
            position: relative;
            z-index: 4;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2.5rem;
            width: 100%;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(0,179,164,0.12);
            border: 1px solid rgba(0,179,164,0.45);
            color: var(--ice);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2rem;
            animation: slideUp 0.8s ease both;
        }
        .hero-pill-dot {
            width: 7px; height: 7px;
            background: var(--ice);
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.7)} }

        .hero h1 {
            font-size: clamp(3.2rem, 6.5vw, 6rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.02;
            letter-spacing: -2px;
            margin-bottom: 1.75rem;
            max-width: 850px;
            animation: slideUp 0.9s 0.12s ease both;
        }
        .hero h1 .accent {
            background: linear-gradient(90deg, var(--ice) 0%, var(--teal) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: clamp(1rem, 1.6vw, 1.2rem);
            color: rgba(255,255,255,0.72);
            max-width: 540px;
            line-height: 1.8;
            font-weight: 300;
            margin-bottom: 2.75rem;
            animation: slideUp 1s 0.25s ease both;
        }

        .hero-btns {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3.5rem;
            animation: slideUp 1s 0.38s ease both;
        }
        .btn-primary-hero {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #00cfbe, var(--teal));
            color: white;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            padding: 16px 38px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 0 50px rgba(0,179,164,0.5);
            transition: all 0.35s ease;
        }
        .btn-primary-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 70px rgba(0,179,164,0.7);
        }
        .btn-primary-hero .btn-arrow { transition: transform 0.3s ease; }
        .btn-primary-hero:hover .btn-arrow { transform: translateX(5px); }

        .btn-ghost-hero {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.82);
            font-weight: 500;
            font-size: 1rem;
            padding: 16px 30px;
            border-radius: 50px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .btn-ghost-hero:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.5);
            color: white;
        }

        .hero-trust {
            display: flex;
            flex-wrap: wrap;
            gap: 1.75rem;
            animation: slideUp 1s 0.5s ease both;
        }
        .hero-trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
        }
        .hero-trust-check {
            width: 18px; height: 18px;
            background: rgba(0,179,164,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .hero-trust-check::after {
            content: '';
            width: 8px; height: 5px;
            border-left: 1.5px solid var(--ice);
            border-bottom: 1.5px solid var(--ice);
            transform: rotate(-45deg) translateY(-1px);
        }

        /* Scroll indicator */
        .scroll-cue {
            position: absolute;
            bottom: 2.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            animation: slideUp 1s 1.2s ease both;
        }
        .scroll-cue-mouse {
            width: 24px; height: 38px;
            border: 1.5px solid rgba(255,255,255,0.25);
            border-radius: 12px;
            position: relative;
        }
        .scroll-cue-mouse::after {
            content: '';
            position: absolute;
            top: 6px; left: 50%;
            transform: translateX(-50%);
            width: 3px; height: 8px;
            background: rgba(255,255,255,0.4);
            border-radius: 2px;
            animation: scrollDot 2s ease-in-out infinite;
        }
        @keyframes scrollDot {
            0%   { top: 6px; opacity: 1; }
            100% { top: 20px; opacity: 0; }
        }

        @keyframes slideUp {
            from { opacity:0; transform: translateY(30px); }
            to   { opacity:1; transform: translateY(0); }
        }

        /* ── STATS BAND ────────────────────────────── */
        .stats-band {
            background: var(--navy);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .stats-band-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .stat-cell {
            padding: 2.75rem 2rem;
            text-align: center;
            border-right: 1px solid rgba(255,255,255,0.07);
            position: relative;
            transition: background 0.3s;
            cursor: default;
        }
        .stat-cell:last-child { border-right: none; }
        .stat-cell:hover { background: rgba(0,179,164,0.05); }
        .stat-cell::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 50%; height: 2px;
            background: var(--teal);
            transition: transform 0.4s ease;
        }
        .stat-cell:hover::after { transform: translateX(-50%) scaleX(1); }
        .stat-num {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--teal);
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .stat-lbl {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.5px;
        }

        /* ── AWARENESS ─────────────────────────────── */
        .sec-awareness {
            padding: 8rem 2.5rem;
            max-width: 1280px;
            margin: 0 auto;
        }
        .awareness-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6rem;
            align-items: center;
        }
        .sec-tag {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--teal);
            margin-bottom: 1.25rem;
        }
        .awareness-grid h2 {
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.5px;
            margin-bottom: 1.25rem;
        }
        .awareness-grid p {
            color: var(--gray);
            line-height: 1.85;
            margin-bottom: 1.1rem;
            font-size: 1rem;
        }
        .check-list {
            list-style: none;
            margin: 1.75rem 0 2.25rem;
        }
        .check-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 0.7rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.055);
            font-size: 0.97rem;
            line-height: 1.5;
        }
        .check-list li:last-child { border-bottom: none; }
        .check-icon {
            width: 22px; min-width: 22px; height: 22px;
            border-radius: 50%;
            background: rgba(0,179,164,0.12);
            display: flex; align-items: center; justify-content: center;
            margin-top: 1px;
        }
        .check-icon::after {
            content: '';
            width: 9px; height: 5.5px;
            border-left: 2px solid var(--teal);
            border-bottom: 2px solid var(--teal);
            transform: rotate(-45deg) translateY(-1px);
        }
        .btn-learn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--teal);
            font-weight: 600;
            font-size: 0.95rem;
            padding: 14px 26px;
            border-radius: 50px;
            text-decoration: none;
            border: 1.5px solid rgba(0,179,164,0.35);
            transition: all 0.3s ease;
        }
        .btn-learn:hover {
            background: rgba(0,179,164,0.07);
            border-color: var(--teal);
            transform: translateX(4px);
        }

        /* Diagram card */
        .diagram-card {
            background: var(--off);
            border-radius: 28px;
            padding: 3rem 2.5rem 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(10,31,68,0.06);
        }
        .diagram-card::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.1), transparent 70%);
        }
        .diagram-card h4 {
            font-size: 1.05rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.75rem;
            color: var(--navy);
        }

        /* ── STANDARDS ─────────────────────────────── */
        .sec-standards {
            background: var(--navy);
            padding: 7rem 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .sec-standards::before {
            content: '';
            position: absolute;
            top: -250px; left: -250px;
            width: 700px; height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.1), transparent 65%);
            pointer-events: none;
        }
        .sec-standards::after {
            content: '';
            position: absolute;
            bottom: -200px; right: -150px;
            width: 550px; height: 550px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.07), transparent 65%);
            pointer-events: none;
        }
        .standards-inner {
            position: relative; z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            text-align: center;
        }
        .standards-inner h2 {
            font-size: clamp(1.9rem, 3vw, 2.75rem);
            color: var(--white);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 1rem;
        }
        .standards-inner > p {
            color: rgba(255,255,255,0.55);
            font-size: 1.05rem;
            max-width: 620px;
            margin: 0 auto 3.5rem;
            line-height: 1.8;
        }
        .standards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .std-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 18px;
            padding: 2.75rem 2rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .std-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(0,179,164,0.08), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .std-card:hover { transform: translateY(-6px); border-color: rgba(0,179,164,0.3); }
        .std-card:hover::before { opacity: 1; }
        .std-num {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            color: var(--teal);
            margin-bottom: 0.5rem;
            position: relative; z-index: 1;
        }
        .std-card p {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            position: relative; z-index: 1;
        }
        .std-disclaimer {
            color: rgba(255,255,255,0.25);
            font-size: 0.78rem;
            font-style: italic;
        }

        /* ── TIMELINE ──────────────────────────────── */
        .sec-timeline {
            padding: 8rem 2.5rem;
            background: var(--white);
        }
        .timeline-wrap { max-width: 980px; margin: 0 auto; }
        .tl-header {
            text-align: center;
            margin-bottom: 5.5rem;
        }
        .tl-header h2 {
            font-size: clamp(1.9rem, 3.5vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 1rem;
        }
        .tl-header p { color: var(--gray); max-width: 560px; margin: 0 auto; line-height: 1.8; }

        .tl-track { position: relative; }
        .tl-track::before {
            content: '';
            position: absolute;
            left: 38px; top: 24px; bottom: 24px;
            width: 2px;
            background: linear-gradient(to bottom, var(--teal) 0%, rgba(0,179,164,0.08) 100%);
        }

        .tl-row {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            padding: 1.25rem 0;
            opacity: 0;
            transform: translateX(-24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .tl-row.in-view { opacity: 1; transform: none; }

        .tl-bubble {
            width: 78px; min-width: 78px; height: 78px;
            border-radius: 50%;
            background: var(--off);
            border: 2px solid var(--teal);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.7rem;
            position: relative; z-index: 1;
            box-shadow: 0 0 0 7px white;
            transition: all 0.35s ease;
            cursor: default;
        }
        .tl-row:hover .tl-bubble {
            background: var(--teal);
            transform: scale(1.12);
            box-shadow: 0 0 0 7px white, 0 0 24px rgba(0,179,164,0.3);
        }
        .tl-text { padding-top: 1.1rem; }
        .tl-text h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: var(--navy);
        }
        .tl-text p {
            color: var(--gray);
            font-size: 0.97rem;
            line-height: 1.72;
        }

        /* ── TESTIMONIALS ──────────────────────────── */
        .sec-testimonials {
            background: var(--off);
            padding: 7rem 2.5rem;
            overflow: hidden;
        }
        .test-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .test-header h2 {
            font-size: clamp(1.9rem, 3.5vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.75rem;
        }
        .test-header p { color: var(--gray); font-size: 1.02rem; }
        .test-track-wrap { max-width: 780px; margin: 0 auto; overflow: hidden; }
        .testimonials-track {
            display: flex;
            transition: transform 0.55s cubic-bezier(0.4,0,0.2,1);
        }
        .testimonial-slide {
            min-width: 100%;
            display: flex; justify-content: center;
        }
        .test-card {
            background: white;
            border-radius: 24px;
            padding: 3rem 3.5rem;
            width: 100%;
            box-shadow: 0 6px 48px rgba(10,31,68,0.07);
            text-align: center;
            position: relative;
        }
        .test-card::before {
            content: '"';
            position: absolute;
            top: 1.5rem; left: 2.5rem;
            font-family: 'Poppins', sans-serif;
            font-size: 7rem;
            color: var(--teal);
            opacity: 0.09;
            line-height: 1;
            pointer-events: none;
        }
        .test-quote {
            font-size: 1.1rem;
            line-height: 1.9;
            color: var(--navy);
            margin-bottom: 1.75rem;
            font-weight: 300;
            font-style: italic;
        }
        .test-author {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--teal);
            margin-bottom: 4px;
        }
        .test-location { font-size: 0.85rem; color: var(--gray); }

        .carousel-nav-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            margin-top: 2.5rem;
        }
        .carousel-nav-btn {
            width: 46px; height: 46px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--teal);
            color: var(--teal);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 16px rgba(0,179,164,0.12);
        }
        .carousel-nav-btn:hover:not(:disabled) {
            background: var(--teal);
            color: white;
            transform: scale(1.1);
        }
        .carousel-nav-btn:disabled { opacity: 0.2; cursor: not-allowed; }
        .carousel-indicator-dots { display: flex; gap: 8px; }
        .carousel-indicator-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #cdd5e0;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .carousel-indicator-dot.active {
            background: var(--teal);
            width: 28px; border-radius: 5px;
        }

        /* ── CTA SECTION ───────────────────────────── */
        .sec-cta {
            padding: 7rem 2.5rem;
            background: var(--white);
        }
        .cta-box {
            max-width: 920px;
            margin: 0 auto;
            background: var(--navy);
            border-radius: 32px;
            padding: 5.5rem 3.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .cta-box::before {
            content: '';
            position: absolute;
            top: -140px; right: -140px;
            width: 480px; height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.18), transparent 65%);
        }
        .cta-box::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -80px;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,179,164,0.1), transparent 65%);
        }
        .cta-box > * { position: relative; z-index: 1; }
        .cta-pill {
            display: inline-block;
            background: rgba(0,179,164,0.14);
            border: 1px solid rgba(0,179,164,0.42);
            color: var(--ice);
            padding: 7px 20px;
            border-radius: 50px;
            font-size: 0.73rem;
            font-weight: 700;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
        }
        .cta-box h2 {
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
            line-height: 1.1;
            margin-bottom: 1.25rem;
        }
        .cta-box > p {
            color: rgba(255,255,255,0.58);
            font-size: 1.08rem;
            max-width: 560px;
            margin: 0 auto 2.75rem;
            line-height: 1.8;
        }
        .cta-pricing {
            margin-bottom: 2.75rem;
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 12px;
        }
        .cta-price { font-family:'Poppins',sans-serif; font-size:4.5rem; font-weight:500; color:white; line-height:1; }
        .cta-strike { font-size:1.8rem; color:rgba(255,255,255,0.25); text-decoration:line-through; }
        .btn-cta-main {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #00cfbe, var(--teal));
            color: white;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            padding: 18px 46px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 0 50px rgba(0,179,164,0.45);
            transition: all 0.35s ease;
        }
        .btn-cta-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 70px rgba(0,179,164,0.65);
        }
        .btn-cta-main svg { transition: transform 0.3s ease; }
        .btn-cta-main:hover svg { transform: translateX(5px); }
        .cta-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2.25rem;
        }
        .cta-meta-item {
            display: flex;
            align-items: center;
            gap: 7px;
            color: rgba(255,255,255,0.45);
            font-size: 0.88rem;
        }
        .cta-meta-item::before {
            content: '';
            width: 16px; height: 16px;
            border-radius: 50%;
            background: rgba(0,179,164,0.18);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2300B3A4'%3E%3Cpath d='M13.78 4.22a.75.75 0 010 1.06l-7.25 7.25a.75.75 0 01-1.06 0L2.22 9.28a.75.75 0 011.06-1.06L6 10.94l6.72-6.72a.75.75 0 011.06 0z'/%3E%3C/svg%3E");
            background-size: 10px;
            background-repeat: no-repeat;
            background-position: center;
            flex-shrink: 0;
        }

        /* ── SCROLL REVEAL ─────────────────────────── */
        .reveal { opacity:0; transform:translateY(28px); transition:opacity 0.7s ease, transform 0.7s ease; }
        .reveal.in-view { opacity:1; transform:none; }
        .reveal-d1 { transition-delay:0.1s; }
        .reveal-d2 { transition-delay:0.2s; }
        .reveal-d3 { transition-delay:0.3s; }
        .reveal-d4 { transition-delay:0.4s; }

        /* ── RESPONSIVE ────────────────────────────── */
        @media(max-width:960px) {
            .stats-band-inner { grid-template-columns:repeat(2,1fr); }
            .stat-cell:nth-child(2) { border-right:none; }
            .stat-cell:nth-child(3) { border-top:1px solid rgba(255,255,255,0.07); }
            .awareness-grid { grid-template-columns:1fr; gap:3rem; }
            .standards-grid { grid-template-columns:1fr; }
        }
        @media(max-width:600px) {
            .hero h1 { letter-spacing:-0.5px; }
            .hero-trust { gap:1rem; }
            .stats-band-inner { grid-template-columns:repeat(2,1fr); }
            .stat-cell { padding:1.75rem 1rem; }
            .sec-awareness,.sec-timeline,.sec-cta { padding:5rem 1.5rem; }
            .test-card { padding:2.5rem 1.5rem; }
            .cta-box { padding:3.5rem 1.5rem; }
            .cta-price { font-size:4rem; }
        }
    </style>
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