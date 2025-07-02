<?php
// Router para servidor embutido do PHP
// Este arquivo é usado automaticamente quando rodamos: php -S localhost:8000 -t public router.php

// Ativar log de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Debug information
error_log("Request: $requestMethod $requestUri");

// For debugging - write to a log file
file_put_contents(__DIR__ . '/router_log.txt', date('[Y-m-d H:i:s] ') . "Request: $requestMethod $requestUri\n", FILE_APPEND);

// Arquivos estáticos (CSS, JS, images, etc.)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
    error_log("Static file: $requestUri");
    file_put_contents(__DIR__ . '/router_log.txt', date('[Y-m-d H:i:s] ') . "Static file: $requestUri\n", FILE_APPEND);
    return false; // Serve o arquivo estático
}

// Test/direct access PHP scripts (allow .php files to be served directly)
if (preg_match('/\.(php)$/', $requestUri) && file_exists(__DIR__ . $requestUri)) {
    error_log("PHP file: $requestUri");
    file_put_contents(__DIR__ . '/router_log.txt', date('[Y-m-d H:i:s] ') . "PHP file: $requestUri\n", FILE_APPEND);
    return false; // Serve the PHP file directly
}

// Arquivos específicos que devem ser servidos diretamente
if (file_exists(__DIR__ . $requestUri) && is_file(__DIR__ . $requestUri)) {
    error_log("Specific file: $requestUri");
    file_put_contents(__DIR__ . '/router_log.txt', date('[Y-m-d H:i:s] ') . "Specific file: $requestUri\n", FILE_APPEND);
    return false;
}

// Redirecionar tudo para index.php
error_log("Routing through index.php: $requestUri");
file_put_contents(__DIR__ . '/router_log.txt', date('[Y-m-d H:i:s] ') . "Routing through index.php: $requestUri\n", FILE_APPEND);

// Create debug output for the request
if (isset($_GET['debug_router'])) {
    echo "<h1>Router Debug</h1>";
    echo "<pre>";
    echo "Request URI: " . htmlspecialchars($requestUri) . "\n";
    echo "Request Method: " . htmlspecialchars($requestMethod) . "\n";
    echo "File exists check:\n";
    echo " - index.php: " . (file_exists(__DIR__ . '/index.php') ? 'Yes' : 'No') . "\n";
    echo "</pre>";
    exit;
}

require_once __DIR__ . '/index.php';
