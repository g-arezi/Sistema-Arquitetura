<?php
// Script to test all admin routes with proper authentication
// Place in public folder for testing

// Set up basic environment
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\View;
use App\Core\Session;

// Start session
Session::start();

// Check if we're logging in or out
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    Session::destroy();
    header("Location: /admin-routes-test.php");
    exit;
}

// Create mock admin user session (both formats for compatibility)
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Rotas Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Teste de Rotas Admin</h1>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Informações da Sessão
            </div>
            <div class="card-body">
                <pre><?php print_r($_SESSION); ?></pre>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                Links para Rotas Admin
            </div>
            <div class="list-group list-group-flush">
                <a href="/admin" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Dashboard Admin
                    <span class="badge bg-primary rounded-pill">Principal</span>
                </a>
                <a href="/admin/users" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Gestão de Usuários
                    <span class="badge bg-info rounded-pill">Usuários</span>
                </a>
                <a href="/admin/projects" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Gestão de Projetos
                    <span class="badge bg-info rounded-pill">Projetos</span>
                </a>
                <a href="/admin/activities" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Histórico de Atividades
                    <span class="badge bg-info rounded-pill">Atividades</span>
                </a>
                <a href="/admin-routes-test.php?action=logout" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    Logout (Limpar Sessão)
                    <span class="badge bg-danger rounded-pill">Sair</span>
                </a>
            </div>
        </div>
        
        <div class="alert alert-info">
            <strong>Dica:</strong> Clique nos links acima para testar as diferentes rotas admin com a sessão de administrador já configurada.
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
