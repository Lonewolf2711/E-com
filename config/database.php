<?php
/**
 * Database Configuration
 * ─────────────────────
 * MySQL credentials for PDO connection.
 * Update these values for your environment.
 */

// ─── Database Credentials ───
// Check for Railway environment variables first (or other host environment)
define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'ecommerce_cms');
define('DB_USER', getenv('MYSQLUSER') ?: 'root');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// Note: If using PDO, you may need to update your connection string 
// in app/core/Database.php to include the port if it's dynamic.
