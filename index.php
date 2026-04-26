<?php
/**
 * ═══════════════════════════════════════════════════════════
 * ECOMMERCE CMS — Entry Point
 * ═══════════════════════════════════════════════════════════
 * All requests are routed through this file via .htaccess.
 * It loads configuration, core classes, helpers, and boots
 * the application.
 */

// ─── Define paths ───
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// ─── Load configuration ───
require_once CONFIG_PATH . '/app.php';
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/payment.php';

// ─── Load core classes ───
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Middleware.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/App.php';

// ─── Load helpers ───
require_once APP_PATH . '/helpers/functions.php';
require_once APP_PATH . '/helpers/format.php';
require_once APP_PATH . '/helpers/upload.php';
require_once APP_PATH . '/helpers/mailer.php';

// ─── Load models ───
$modelDir = APP_PATH . '/models';
if (is_dir($modelDir)) {
    foreach (glob($modelDir . '/*.php') as $modelFile) {
        require_once $modelFile;
    }
}

// ─── Load controllers ───
$controllerDirs = [
    APP_PATH . '/controllers/frontend',
    APP_PATH . '/controllers/admin',
];
foreach ($controllerDirs as $dir) {
    if (is_dir($dir)) {
        foreach (glob($dir . '/*.php') as $controllerFile) {
            require_once $controllerFile;
        }
    }
}

// ─── Start session ───
Session::start();

// ─── Run the application ───
try {
    App::run();
} catch (Exception $e) {
    if (ENVIRONMENT === 'development') {
        echo '<h1>Application Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        error_log('Application Error: ' . $e->getMessage());
        http_response_code(500);
        $errorPage = VIEW_PATH . '/frontend/500.php';
        if (file_exists($errorPage)) {
            require_once $errorPage;
        } else {
            echo '<h1>Server Error</h1><p>Something went wrong. Please try again later.</p>';
        }
    }
}
