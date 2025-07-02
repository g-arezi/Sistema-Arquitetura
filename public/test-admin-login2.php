<?php
// Auto-login script for testing admin features - simulates normal login
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Session;

// Start session
Session::start();

// Check if we're logging in or out
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    Session::destroy();
    header("Location: /test-admin-login2.php");
    exit;
}

// Create mock admin user session - using individual variables like AuthController
Session::set('user_id', 1);
Session::set('user_name', 'Admin Test');
Session::set('user_email', 'admin@test.com');
Session::set('user_type', 'admin');

// Redirect to admin activities
header("Location: /admin/activities");
exit;
