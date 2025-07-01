<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Core\Session;

// Inicializar sessão
Session::start();

// Configurar cabeçalhos de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Inicializar roteador
$router = new Router();

// Definir rotas públicas
$router->get('/', 'AuthController@loginForm');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Página de ajuda
$router->get('/help', 'HelpController@urls');
$router->get('/help/urls', 'HelpController@urls');

// Redirecionamentos para URLs comuns com .php
$router->get('/dashboard.php', 'RedirectController@dashboardPhp');
$router->get('/login.php', 'RedirectController@loginPhp');
$router->get('/projects.php', 'RedirectController@projectsPhp');

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
});

// Executar roteamento
$router->run();
