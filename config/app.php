<?php
/**
 * Application Configuration
 * ─────────────────────────
 * Core settings for the Ecommerce CMS.
 */

// ─── Environment ───
// Set to 'production' on live server, 'development' for local
define('ENVIRONMENT', 'development');

// ─── Application ───
define('APP_NAME', 'Ecommerce CMS');
define('APP_VERSION', '1.0.0');

// ─── Base URL ───
// Change this to your production domain (no trailing slash)
// e.g. https://yourdomain.com
define('BASE_URL', 'http://localhost/E-com');

// ─── Debug Mode ───
// true = show errors on screen (development only)
// false = log errors to file (production)
define('DEBUG', ENVIRONMENT === 'development');

// ─── Timezone ───
define('APP_TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set(APP_TIMEZONE);

// ─── Error Reporting ───
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__DIR__) . '/logs/error.log');
}

// ─── Session Configuration ───
define('SESSION_LIFETIME', 3600); // 1 hour

// ─── Upload Configuration ───
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ─── Pagination ───
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 15);

// ─── Currency ───
define('DEFAULT_CURRENCY', 'INR');
define('DEFAULT_CURRENCY_SYMBOL', '₹');
