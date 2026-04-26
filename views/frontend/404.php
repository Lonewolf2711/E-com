<?php
/**
 * 404 Not Found Page
 */
$page_title = '404 - Page Not Found';
$store_name = defined('APP_NAME') ? APP_NAME : 'Electro Store';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $page_title ?> | <?= $store_name ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            overflow: hidden;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #818cf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 1rem;
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }
        .error-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; color: #f1f5f9; }
        .error-message { color: #94a3b8; margin-bottom: 2rem; line-height: 1.6; }
        .btn-home {
            display: inline-block;
            padding: 0.875rem 2.5rem;
            background: linear-gradient(135deg, #818cf8, #a78bfa);
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(129, 140, 248, 0.3);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(129, 140, 248, 0.4);
            color: #fff;
        }
        .floating-shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: -1;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
            animation: float 20s ease-in-out infinite;
        }
        .shape-1 { width: 300px; height: 300px; background: #818cf8; top: 10%; left: -5%; animation-delay: 0s; }
        .shape-2 { width: 200px; height: 200px; background: #f472b6; bottom: 10%; right: -3%; animation-delay: -7s; }
        .shape-3 { width: 150px; height: 150px; background: #c084fc; top: 50%; right: 20%; animation-delay: -14s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
            Don't worry — you can find plenty more from our homepage.
        </p>
        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="btn-home">
            <i class="bi bi-house-door me-2"></i>Back to Home
        </a>
    </div>
</body>
</html>
