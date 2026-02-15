<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Server Error (500) | LuckyGeneMdx</title>
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
        
        .error-icon {
            font-size: 100px;
            margin-bottom: 1rem;
            animation: glitch 2s ease-in-out infinite;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, #ffa500, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        
        .technical-notice {
            background: rgba(255, 165, 0, 0.1);
            border: 2px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .technical-notice h3 {
            margin: 0 0 0.5rem 0;
            color: #ffa500;
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
        
        .status-check {
            margin-top: 3rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #ffa500;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
            margin-right: 0.5rem;
        }
        
        @keyframes glitch {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-5px); }
            40% { transform: translateX(5px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-icon">⚠️</div>
            <h1 class="error-code">500</h1>
            <h2 class="error-title">Internal Server Error</h2>
            <p class="error-description">
                Something went wrong on our end. We're working to fix the issue. 
                Please try again in a few moments.
            </p>
            
            <div class="technical-notice">
                <h3>🔧 What Happened?</h3>
                <p>Our servers encountered an unexpected condition. This is a temporary issue and our technical team has been automatically notified.</p>
            </div>
            
            <div class="error-actions">
                <a href="javascript:window.location.reload()" class="error-btn error-btn-primary">
                    Try Again
                </a>
                <a href="../index.php" class="error-btn error-btn-secondary">
                    Go to Homepage
                </a>
            </div>
            
            <div class="status-check">
                <h3><span class="status-indicator"></span> Server Status</h3>
                <p style="color: rgba(255, 255, 255, 0.7); margin-top: 1rem;">
                    If this error persists, it may indicate a temporary service disruption. 
                    Our team monitors all systems 24/7 and will resolve any issues quickly.
                </p>
                <div style="margin-top: 2rem;">
                    <strong>What You Can Do:</strong>
                    <ul style="text-align: left; display: inline-block; margin-top: 0.5rem; color: rgba(255, 255, 255, 0.7);">
                        <li>Wait a few minutes and refresh the page</li>
                        <li>Clear your browser cache and cookies</li>
                        <li>Try accessing the site from a different browser</li>
                        <li>Contact our support team if the issue continues</li>
                    </ul>
                </div>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <h3>Need Immediate Assistance?</h3>
                <p style="margin-top: 1rem;">
                    <strong>Email:</strong> support@luckygenemmdx.com<br>
                    <strong>Phone:</strong> 1-800-LUCKYGENE<br>
                    <small style="color: rgba(255, 255, 255, 0.6);">Available Monday-Friday, 9AM-5PM EST</small>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Log error to console for debugging (in production, this would send to error tracking)
        console.error('500 Internal Server Error - Timestamp:', new Date().toISOString());
        
        // Auto-retry after 10 seconds (optional)
        setTimeout(() => {
            const retryBanner = document.createElement('div');
            retryBanner.style.cssText = `
                position: fixed;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 179, 164, 0.9);
                color: white;
                padding: 1rem 2rem;
                border-radius: 8px;
                z-index: 1000;
                animation: slideUp 0.3s ease;
            `;
            retryBanner.textContent = 'Attempting to reconnect...';
            document.body.appendChild(retryBanner);
            
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }, 10000);
    </script>
</body>
</html>
