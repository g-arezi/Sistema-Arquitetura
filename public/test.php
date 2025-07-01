<?php
// Página de teste para diagnosticar problemas

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Session;
use App\Models\UserFile as User;

Session::start();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Teste Sistema</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>🔧 Diagnóstico do Sistema</h1>
        
        <div class='row'>
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>Informações da Sessão</div>
                    <div class='card-body'>
                        <p><strong>Sessão ativa:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'Sim' : 'Não') . "</p>
                        <p><strong>User ID:</strong> " . (Session::has('user_id') ? Session::get('user_id') : 'Não logado') . "</p>
                        <p><strong>User Name:</strong> " . (Session::has('user_name') ? Session::get('user_name') : 'N/A') . "</p>
                        <p><strong>User Type:</strong> " . (Session::has('user_type') ? Session::get('user_type') : 'N/A') . "</p>
                    </div>
                </div>
            </div>
            
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>Teste de Login</div>
                    <div class='card-body'>
                        <form method='POST'>
                            <div class='mb-3'>
                                <input type='email' class='form-control' name='email' value='admin@sistema.com' placeholder='Email'>
                            </div>
                            <div class='mb-3'>
                                <input type='password' class='form-control' name='password' value='admin123' placeholder='Senha'>
                            </div>
                            <button type='submit' name='test_login' class='btn btn-primary'>Teste Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='mt-4'>
            <a href='/' class='btn btn-secondary'>← Voltar ao Sistema</a>
            <a href='/login' class='btn btn-primary'>Login Normal</a>
            <a href='/dashboard' class='btn btn-success'>Dashboard</a>
        </div>
    </div>
</body>
</html>";

// Teste de login
if (isset($_POST['test_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $user = User::findByEmail($email);
    
    if ($user && User::verifyPassword($password, $user['password'])) {
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_type', $user['type']);
        
        echo "<script>alert('Login realizado com sucesso!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Credenciais inválidas!');</script>";
    }
}
?>
