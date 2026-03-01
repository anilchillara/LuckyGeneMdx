<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Color Palette Preview</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f5f7fa;
}

.section {
    padding: 60px 20px;
}

.container {
    max-width: 1100px;
    margin: auto;
}

h1 {
    margin-bottom: 10px;
}

p {
    max-width: 600px;
    opacity: 0.9;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 20px;
    transition: 0.3s ease;
}

.card {
    padding: 20px;
    border-radius: 12px;
    margin-top: 30px;
}

/* -------------------------------
   1. Analogous Harmony
--------------------------------*/
.analogous {
    background: #C1C8E4;
    color: #1c1c1c;
}
.analogous h1 { color: #5680E9; }
.analogous .btn { background: #5680E9; color: white; }
.analogous .btn:hover { background: #5AB9EA; }
.analogous .card {
    background: #84CEEB;
}

/* -------------------------------
   2. Complementary Contrast
--------------------------------*/
.complementary {
    background: #C1C8E4;
}
.complementary h1 { color: #8860D0; }
.complementary .btn { background: #8860D0; color: white; }
.complementary .btn:hover { background: #5AB9EA; }
.complementary .card {
    background: #5AB9EA;
    color: white;
}

/* -------------------------------
   3. Triadic Energy
--------------------------------*/
.triadic {
    background: #C1C8E4;
}
.triadic h1 { color: #5680E9; }
.triadic .btn { background: #A382D9; color: white; } /* lightened purple */
.triadic .btn:hover { background: #8FD0EE; } /* pastel cerulean */
.triadic .card {
    background: #5680E9;
    color: white;
}

/* -------------------------------
   4. Monochromatic
--------------------------------*/
.mono {
    background: #C1C8E4;
}
.mono h1 { color: #5680E9; }
.mono .btn { background: #2E5CCF; color: white; } /* darker */
.mono .btn:hover { background: #7FA0F2; } /* lighter */
.mono .card {
    background: #7FA0F2;
    color: white;
}

/* -------------------------------
   5. Pastel Dream
--------------------------------*/
.pastel {
    background: #FFFFFF;
}
.pastel h1 { color: #84CEEB; }
.pastel .btn { background: #5AB9EA; color: white; }
.pastel .btn:hover { background: #84CEEB; }
.pastel .card {
    background: #C1C8E4;
}

footer {
    text-align: center;
    padding: 30px;
    background: #111;
    color: white;
}
</style>
</head>

<body>

<!-- 1 -->
<section class="section analogous">
    <div class="container">
        <h1>Analogous Harmony</h1>
        <p>A soft, harmonious palette using neighboring blues for a calm and unified look.</p>
        <button class="btn">Primary Action</button>
        <div class="card">
            <h3>Feature Card</h3>
            <p>This design feels gentle and cohesive — great for healthcare, wellness, or education platforms.</p>
        </div>
    </div>
</section>

<!-- 2 -->
<section class="section complementary">
    <div class="container">
        <h1>Complementary Contrast</h1>
        <p>A bold contrast between purple and cerulean that grabs attention and feels energetic.</p>
        <button class="btn">Primary Action</button>
        <div class="card">
            <h3>Feature Card</h3>
            <p>This scheme is vibrant and high-impact — ideal for marketing or call-to-action focused pages.</p>
        </div>
    </div>
</section>

<!-- 3 -->
<section class="section triadic">
    <div class="container">
        <h1>Triadic Energy</h1>
        <p>A dynamic combination balanced carefully to avoid overwhelming the viewer.</p>
        <button class="btn">Primary Action</button>
        <div class="card">
            <h3>Feature Card</h3>
            <p>This palette feels modern, tech-forward, and innovative.</p>
        </div>
    </div>
</section>

<!-- 4 -->
<section class="section mono">
    <div class="container">
        <h1>Monochromatic Sophistication</h1>
        <p>A clean and modern gradient of a single base color.</p>
        <button class="btn">Primary Action</button>
        <div class="card">
            <h3>Feature Card</h3>
            <p>This is perfect for premium, minimal, or SaaS-style websites.</p>
        </div>
    </div>
</section>

<!-- 5 -->
<section class="section pastel">
    <div class="container">
        <h1>Pastel Dream</h1>
        <p>Light, airy, and soft — focused on gentle pastel tones.</p>
        <button class="btn">Primary Action</button>
        <div class="card">
            <h3>Feature Card</h3>
            <p>Ideal for lifestyle brands, pediatric health platforms, or calming user experiences.</p>
        </div>
    </div>
</section>

<footer>
    Palette Demo Preview
</footer>

</body>
</html>