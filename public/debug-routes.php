<?php
// Debug script to test all routes
// This bypasses the normal routing mechanism and provides direct access to controllers

// Set up the environment
require_once __DIR__ . '/../vendor/autoload.php';

// Import necessary classes
use App\Core\View;
use App\Core\Session;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\TestController;

// Start session
Session::start();

// Set admin user in session (both formats)
Session::set('user_id', 1);
Session::set('user_name', 'Admin Test');
Session::set('user_email', 'admin@test.com');
Session::set('user_type', 'admin');

Session::set('user', [
    'id' => 1,
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'type' => 'admin',
    'active' => true
]);

// Determine which controller and method to call
$controller = $_GET['controller'] ?? 'test';
$method = $_GET['method'] ?? 'activities';

// Create the controller
switch ($controller) {
    case 'admin':
        $controllerInstance = new AdminController();
        break;
    case 'auth':
        $controllerInstance = new AuthController();
        break;
    case 'test':
    default:
        $controllerInstance = new TestController();
        break;
}

// Call the method if it exists
if (method_exists($controllerInstance, $method)) {
    // Header with links to other controllers/methods
    echo '<div style="background: #f8f9fa; padding: 10px; margin-bottom: 15px; border-radius: 5px;">';
    echo '<h3>Debug Navigation</h3>';
    echo '<p><a href="/debug-routes.php?controller=admin&method=activities">AdminController@activities</a> | ';
    echo '<a href="/debug-routes.php?controller=test&method=activities">TestController@activities</a> | ';
    echo '<a href="/debug-routes.php?controller=admin&method=index">AdminController@index</a></p>';
    echo '<hr>';
    echo '<p>Current: ' . get_class($controllerInstance) . '@' . $method . '</p>';
    echo '</div>';
    
    // Call the method
    $controllerInstance->$method();
} else {
    echo '<div style="background: #f8d7da; padding: 20px; border-radius: 5px; color: #721c24;">';
    echo '<h2>Error: Method not found</h2>';
    echo '<p>The method "' . htmlspecialchars($method) . '" does not exist in controller "' . get_class($controllerInstance) . '".</p>';
    echo '<p><a href="/debug-routes.php">Go back to default</a></p>';
    echo '</div>';
}
