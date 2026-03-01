<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>State of the Rare — 2026 | LuckyGenesMDx</title>

<style>
body {
  margin: 0;
  font-family: 'Segoe UI', Arial, sans-serif;
  background: #f4f7fa;
}

.rare-stats-section {
  padding: 20px 20px;
  background: linear-gradient(145deg, #00e5ff 0%, #2979ff 45%, #9177C7 100%);
  color: #ffffff;
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: auto;
}

h1 {
  font-size: 48px;
  margin-bottom: 10px;
  color: white;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

h2 {
  font-size: 38px;
  margin-bottom: 5px;
}

.subtitle {
  font-size: 18px;
  opacity: 0.85;
  margin-bottom: 60px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  padding: 35px 25px;
  border-radius: 18px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.counter {
  font-size: 46px;
  font-weight: 700;
  letter-spacing: 1px;
}

.suffix {
  font-size: 22px;
  margin-left: 4px;
}

.stat-card p {
  margin-top: 15px;
  font-size: 15px;
  opacity: 0.9;
}

/* Glow effect for key stats */
.highlight {
  color:rgb(253, 253, 253);
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

/* Responsive tweaks */
@media (max-width: 768px) {
  h2 {
    font-size: 28px;
  }
  h1 { font-size: 36px; }
  .counter {
    font-size: 34px;
  }
}

.logo {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
  text-decoration: none;
  letter-spacing: 1px;
}

.logo img {
  filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0));
}


.specialGlow {
  padding: 20px 20px;
  background: linear-gradient(45deg, #00e5ff 0%, #2979ff 45%, #9177C7 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: none;
}


/* Apply this class to some text to add 3d text effect! */
.text3d {
  font-family:Garamond, serif;
  line-height:1em;
  color:#109de8;
  font-weight:bold;
  font-size:45px;
  text-shadow:none;
}


</style>
</head>

<body>

<div class="container" style="background:#f4f7fa; display: flex; justify-content: space-between; align-items: center;">
      <span class="logo specialGlow"><img src="assets/images/logo_small.png" alt="Logo" style="height: 32px; width: auto;">LuckyGenesMDx</span>
      <!-- <span class="text3d">World Rare Disease Day</span> -->
      <!-- <span class="logo specialGlow"> FEBRUARY 28</span> -->
  </div>


<section class="rare-stats-section">
  
  <div class="container">
    <h1>Rare is not Scarce</h1>
    <h2>2026 Global Impact</h2>
    <p class="subtitle">Individually Rare. Collectively a Global Health Priority.</p>

    <div class="stats-grid">

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="400">0</span>
          <span class="suffix">Million+</span>
        </div>
        <p>People Worldwide Affected</p>
      </div>

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="10000">0</span>
          <span class="suffix">+</span>
        </div>
        <p>Identified Rare Diseases</p>
      </div>

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="72">0</span>
          <span class="suffix">%</span>
        </div>
        <p>Have Genetic Origin</p>
      </div>

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="95">0</span>
          <span class="suffix">%</span>
        </div>
        <p>Have No FDA-Approved Treatment</p>
      </div>

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="4">0</span>
          <span class="suffix"> Years+</span>
        </div>
        <p>Average Time to Diagnosis</p>
      </div>

      <div class="stat-card">
        <div>
          <span class="counter highlight" data-target="15">0</span>
          <span class="suffix">x</span>
        </div>
        <p>Higher Medical Costs vs. Common Diseases</p>
      </div>

      

    </div>

    <!-- Footer -->
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.2); font-size: 12px; opacity: 0.8;">
        <p>Sources: World Economic Forum 2026 | Rare Disease Day Global Report 2026 | FDA ARC Program | Global Genes Fact Sheet (Feb 2026)</p>
      </div>
  </div>
</section>

<script>
// Counter animation
const counters = document.querySelectorAll('.counter');
let started = false;

function animateCounters() {
  counters.forEach(counter => {
    const target = +counter.getAttribute('data-target');
    const duration = 2000;
    const stepTime = 20;
    const totalSteps = duration / stepTime;
    const increment = target / totalSteps;

    let current = 0;

    const update = () => {
      current += increment;
      if (current < target) {
        counter.innerText = Math.ceil(current);
        setTimeout(update, stepTime);
      } else {
        counter.innerText = target;
      }
    };

    update();
  });
}

// Trigger on scroll
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting && !started) {
      animateCounters();
      started = true;
    }
  });
});

observer.observe(document.querySelector('.rare-stats-section'));
</script>

</body>
</html>