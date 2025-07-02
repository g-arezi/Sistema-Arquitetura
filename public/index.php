<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Core\Session;

// Detectar ambiente
$isProduction = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') 
    || (isset($_SERVER['HTTP_HOST']) && !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']))
    || file_exists(__DIR__ . '/../.env.production');

// Configurar tratamento de erros baseado no ambiente
if ($isProduction) {
    // Produção: Desabilitar exibição de erros
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '/var/log/sistema-arquitetura/php_errors.log');
} else {
    // Desenvolvimento: Exibir erros apenas em debug mode
    $debugMode = isset($_GET['debug']);
    if ($debugMode) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        echo '<h2>Debug Mode Enabled</h2>';
        echo '<pre>';
        echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . "\n";
        echo 'PHP Version: ' . phpversion() . "\n";
        echo 'Environment: ' . ($isProduction ? 'Production' : 'Development') . "\n";
        echo '</pre>';
    }
}

// Inicializar sessão
Session::start();

// Configurar cabeçalhos de segurança aprimorados
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if ($isProduction) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; style-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net; font-src \'self\' https://cdn.jsdelivr.net; img-src \'self\' data: https:; connect-src \'self\';');
}

// Inicializar roteador
$router = new Router();

// Definir rotas públicas
$router->get('/', 'HelperController@debugRouter'); // Temporário para facilitar o desenvolvimento
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Página de ajuda
$router->get('/help', 'HelpController@urls');
$router->get('/help/urls', 'HelpController@urls');

// Rota de teste para a página de atividades
$router->get('/test-activities', 'TestController@activities');

// Redirecionamentos para URLs comuns com .php
$router->get('/dashboard.php', 'RedirectController@dashboardPhp');
$router->get('/login.php', 'RedirectController@loginPhp');
$router->get('/projects.php', 'RedirectController@projectsPhp');

// Rota temporária para facilitar o acesso à página de atividades (desenvolvimento)
$router->get('/activities', 'HelperController@activitiesRedirect');
$router->get('/debug', 'HelperController@debugRouter');

// Rotas protegidas com middleware de autenticação
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/dashboard', 'DashboardController@index');
    $router->get('/profile', 'UserController@profile');
    $router->post('/profile', 'UserController@updateProfile');
    
    // Projetos
    $router->get('/projects', 'ProjectController@index');
    $router->get('/projects/create', 'ProjectController@create');
    $router->post('/projects', 'ProjectController@store');
    $router->get('/projects/{id}', 'ProjectController@show');
    $router->post('/projects/{id}/upload', 'ProjectController@uploadDocument');
    $router->post('/projects/{id}/status', 'ProjectController@updateStatus');
});

// Rotas de admin
$router->group(['middleware' => 'admin'], function($router) {
    $router->get('/admin', 'AdminController@index');
    $router->get('/admin/users', 'AdminController@users');
    $router->get('/admin/projects', 'AdminController@projects');
    $router->get('/admin/activities', 'AdminController@activities');
    $router->post('/admin/users/{id}/toggle', 'AdminController@toggleUser');
    
    // Gestão de projetos
    $router->get('/admin/projects/create', 'AdminController@createProject');
    $router->post('/admin/projects', 'AdminController@storeProject');
    $router->get('/admin/projects/{id}/edit', 'AdminController@editProject');
    $router->post('/admin/projects/{id}', 'AdminController@updateProject');
    $router->post('/admin/projects/{id}/delete', 'AdminController@deleteProject');
    $router->post('/admin/projects/assign-analyst', 'AdminController@assignAnalyst');
    $router->post('/admin/projects/change-status', 'AdminController@changeProjectStatus');
    
    // Gestão de usuários  
    $router->get('/admin/users/{id}/view', 'AdminController@viewUser');
    $router->get('/admin/users/{id}/edit', 'AdminController@editUser');
    $router->post('/admin/users/{id}/update', 'AdminController@updateUser');
    $router->post('/admin/users/{id}/approve', 'AdminController@approveUser');
    $router->post('/admin/users/{id}/reject', 'AdminController@rejectUser');
});

// Executar roteamento
$router->run();
