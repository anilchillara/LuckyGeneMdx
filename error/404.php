<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Page Not Found (404) | LuckyGeneMdx</title>
    <link rel="stylesheet" href="../css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0A1F44 0%, #1a3a5f 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .error-content {
            max-width: 600px;
            position: relative;
            z-index: 2;
        }
        
        .error-code {
            font-size: 150px;
            font-weight: 700;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, #00B3A4, #6C63FF);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 2s ease-in-out infinite;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 600;
            margin: 1rem 0;
        }
        
        .error-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            margin: 1rem 0 2rem;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .error-btn {
            padding: 0.875rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .error-btn-primary {
            background: #00B3A4;
            color: white;
        }
        
        .error-btn-primary:hover {
            background: #009688;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 179, 164, 0.3);
        }
        
        .error-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .error-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .helpful-links {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .helpful-links h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .helpful-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .helpful-link {
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .helpful-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .dna-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0.1;
        }
        
        @keyframes glow {
            0%, 100% {
                filter: drop-shadow(0 0 20px rgba(0, 179, 164, 0.5));
            }
            50% {
                filter: drop-shadow(0 0 40px rgba(108, 99, 255, 0.5));
            }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="dna-particles"></div>
        
        <div class="error-content">
            <h1 class="error-code">404</h1>
            <h2 class="error-title">Page Not Found</h2>
            <p class="error-description">
                The page you're looking for doesn't exist or has been moved. 
                Don't worry, we'll help you find what you need.
            </p>
            
            <div class="error-actions">
                <a href="../index.php" class="error-btn error-btn-primary">
                    Go to Homepage
                </a>
                <a href="../about-genetic-screening.php" class="error-btn error-btn-secondary">
                    Learn About Screening
                </a>
            </div>
            
            <div class="helpful-links">
                <h3>Popular Pages</h3>
                <div class="helpful-links-grid">
                    <a href="../how-it-works.php" class="helpful-link">
                        <strong>How It Works</strong><br>
                        <small>5-step process</small>
                    </a>
                    <a href="../request-kit.php" class="helpful-link">
                        <strong>Request Kit</strong><br>
                        <small>Order screening</small>
                    </a>
                    <a href="../track-order.php" class="helpful-link">
                        <strong>Track Order</strong><br>
                        <small>Check status</small>
                    </a>
                    <a href="../resources/" class="helpful-link">
                        <strong>Resources</strong><br>
                        <small>Knowledge hub</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Simple DNA particle animation
        const container = document.querySelector('.dna-particles');
        for (let i = 0; i < 20; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.width = '4px';
            particle.style.height = '4px';
            particle.style.background = 'white';
            particle.style.borderRadius = '50%';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.animation = `float ${3 + Math.random() * 3}s ease-in-out infinite`;
            container.appendChild(particle);
        }
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-20px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
