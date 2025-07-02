<?php
// Auto-login script for testing admin features
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Session;

// Start session
Session::start();

// Check if we're logging in or out
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    Session::destroy();
    header("Location: /test-admin-login.php");
    exit;
}

// Create mock admin user session
Session::set('user', [
    'id' => 1,
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'type' => 'admin',
    'active' => true
]);

// Redirect to admin activities
header("Location: /admin/activities");
exit;
