<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\UserFile as User;

class AuthController {
    public function loginForm(): void {
        if (Session::has('user_id')) {
            header('Location: /dashboard');
            exit;
        }
        
        View::make('auth.login')->display();
    }
    
    public function login(): void {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email e senha são obrigatórios');
            header('Location: /login');
            exit;
        }
        
        $user = User::findByEmail($email);
        
        if (!$user || !User::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Credenciais inválidas');
            header('Location: /login');
            exit;
        }
        
        if (!$user['active']) {
            Session::flash('error', 'Conta desativada. Entre em contato com o administrador.');
            header('Location: /login');
            exit;
        }
        
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_type', $user['type']);
        
        Session::regenerate();
        
        header('Location: /dashboard');
        exit;
    }
    
    public function registerForm(): void {
        if (Session::has('user_id')) {
            header('Location: /dashboard');
            exit;
        }
        
        View::make('auth.register')->display();
    }
    
    public function register(): void {
        Session::flash('error', 'Cadastro temporariamente desabilitado. Use as contas de demonstração.');
        header('Location: /login');
        exit;
    }
    
    public function logout(): void {
        Session::destroy();
        header('Location: /');
        exit;
    }
}
