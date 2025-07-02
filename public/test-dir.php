<?php
// Test script to debug PHP server settings
echo 'PHP Server Root Directory: ' . __DIR__ . PHP_EOL;
echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . PHP_EOL;
echo 'Script Name: ' . $_SERVER['SCRIPT_NAME'] . PHP_EOL;
echo 'Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . PHP_EOL;

echo 'File exists check:' . PHP_EOL;
echo ' - index.php: ' . (file_exists(__DIR__ . '/index.php') ? 'Yes' : 'No') . PHP_EOL;

// List all routes from the router
echo '<h3>Available Routes</h3>';
echo '<pre>';
echo 'To debug router, access the route with ?debug=1 parameter';
echo '</pre>';

// Debugging session info
echo '<h3>Session Data</h3>';
echo '<pre>';
session_start();
print_r($_SESSION);
echo '</pre>';
