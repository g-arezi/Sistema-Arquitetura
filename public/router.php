<?php
// Router para servidor embutido do PHP
// Este arquivo é usado automaticamente quando rodamos: php -S localhost:8000 -t public router.php

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Arquivos estáticos (CSS, JS, images, etc.)
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
    return false; // Serve o arquivo estático
}

// Arquivos específicos que devem ser servidos diretamente
if (file_exists(__DIR__ . $requestUri) && is_file(__DIR__ . $requestUri)) {
    return false;
}

// Redirecionar tudo para index.php
require_once __DIR__ . '/index.php';
