<?php
// Arquivo index.php para a raiz (public_html) da Hostinger
// Este arquivo gerencia as rotas diretamente na raiz

// Exibir erros para debug (remova em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Tentar carregar o autoloader
$autoloader_path = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloader_path)) {
    require_once $autoloader_path;
} else {
    die("Erro: Autoloader não encontrado. Verifique se a estrutura de diretórios está correta.");
}

use App\Core\Router;
use App\Core\Session;

// Inicializar sessão
Session::start();

// Configurar cabeçalhos de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Debug mode
$debugMode = isset($_GET['debug']);
if ($debugMode) {
    echo '<h2>Debug Mode Enabled</h2>';
    echo '<pre>';
    echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . "\n";
    echo 'PHP Version: ' . phpversion() . "\n";
    echo 'Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo 'Current Directory: ' . __DIR__ . "\n";
    echo '</pre>';
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

// Rota específica para debug-routes.php (adicionando para resolver o problema 404)
$router->get('/debug-routes.php', 'HelperController@debugRouter');
$router->get('/debug-routes', 'HelperController@debugRouter');

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
