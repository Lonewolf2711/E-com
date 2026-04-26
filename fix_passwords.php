<?php
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'app/core/Database.php';

$db = Database::getInstance();
$hash = password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);

// Update both users
$stmt = $db->prepare("UPDATE users SET password = ? WHERE email IN ('admin@ecommerce.com', 'customer@example.com')");
if ($stmt->execute([$hash])) {
    echo "SUCCESS: Passwords updated correctly! Hash used: " . $hash . "\n";
} else {
    echo "FAILED: Could not update passwords.\n";
}

// Clear rate limits
session_start();
unset($_SESSION['login_attempts']);
unset($_SESSION['login_attempt_time']);
echo "Rate limits cleared.\n";
