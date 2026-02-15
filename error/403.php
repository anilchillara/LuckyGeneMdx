<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Access Denied (403) | LuckyGeneMdx</title>
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
            animation: shake 3s ease-in-out infinite;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
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
        
        .security-notice {
            background: rgba(255, 107, 107, 0.1);
            border: 2px solid rgba(255, 107, 107, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .security-notice h3 {
            margin: 0 0 0.5rem 0;
            color: #ff6b6b;
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
        
        .help-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @keyframes shake {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-icon">🔒</div>
            <h1 class="error-code">403</h1>
            <h2 class="error-title">Access Denied</h2>
            <p class="error-description">
                You don't have permission to access this resource. 
                This could be due to authentication requirements or security restrictions.
            </p>
            
            <div class="security-notice">
                <h3>🛡️ Security Notice</h3>
                <p>This page is protected and requires proper authorization. If you believe you should have access, please verify your login credentials.</p>
            </div>
            
            <div class="error-actions">
                <a href="../index.php" class="error-btn error-btn-primary">
                    Go to Homepage
                </a>
                <a href="../patient-portal/login.php" class="error-btn error-btn-secondary">
                    Patient Login
                </a>
            </div>
            
            <div class="help-section">
                <h3>Need Help?</h3>
                <p style="color: rgba(255, 255, 255, 0.7);">
                    If you're trying to access your test results or patient portal:
                </p>
                <ul style="text-align: left; display: inline-block; margin-top: 1rem;">
                    <li>Make sure you're logged in with the correct credentials</li>
                    <li>Check that you're using the Order ID provided in your confirmation email</li>
                    <li>Results are only available after laboratory processing is complete</li>
                    <li>Contact support if you continue to experience issues</li>
                </ul>
                <div style="margin-top: 2rem;">
                    <p><strong>Support:</strong> support@luckygenemmdx.com | 1-800-LUCKYGENE</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
