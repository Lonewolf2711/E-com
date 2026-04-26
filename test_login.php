<?php
require_once 'config/app.php';
require_once 'config/database.php';

// Quick auto-loader for core and models
spl_autoload_register(function ($class) {
    if (file_exists('app/core/' . $class . '.php')) {
        require_once 'app/core/' . $class . '.php';
    } elseif (file_exists('app/models/' . $class . '.php')) {
        require_once 'app/models/' . $class . '.php';
    }
});

Session::start();

echo "Testing login with admin@ecommerce.com / Admin@123\n";

if (Auth::login('admin@ecommerce.com', 'Admin@123')) {
    echo "SUCCESS: Login worked!\n";
    echo "Session Data:\n";
    print_r($_SESSION);
    
    echo "Is Admin? " . (Auth::isAdmin() ? 'Yes' : 'No') . "\n";
} else {
    echo "FAILED: Login failed!\n";
    
    // Check why
    $sql = "SELECT * FROM users WHERE email = 'admin@ecommerce.com' LIMIT 1";
    $stmt = Database::getInstance()->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "Reason: User not found in DB\n";
    } else {
        echo "User found. Status: " . $user['status'] . "\n";
        echo "Password matches? " . (password_verify('Admin@123', $user['password']) ? 'Yes' : 'No') . "\n";
    }
}

// Clear rate limits just in case
Session::remove('login_attempts');
Session::remove('login_attempt_time');
echo "\nRate limits cleared!\n";
