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
        
        // Verificar se a conta foi aprovada
        if (!isset($user['approved']) || !$user['approved']) {
            Session::flash('error', 'Sua conta ainda não foi aprovada. Aguarde a aprovação de um administrador ou analista.');
            header('Location: /login');
            exit;
        }
        
        // Set both formats for session - this ensures compatibility with all parts of the code
        // 1. Individual keys
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_type', $user['type']);
        
        // 2. User object (preferred format)
        Session::set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'type' => $user['type'],
            'active' => $user['active']
        ]);
        
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
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validação básica
        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Todos os campos são obrigatórios');
            header('Location: /register');
            exit;
        }
        
        if ($password !== $confirm_password) {
            Session::flash('error', 'As senhas não conferem');
            header('Location: /register');
            exit;
        }
        
        // Verificar se o e-mail já existe
        if (User::findByEmail($email)) {
            Session::flash('error', 'Este e-mail já está cadastrado');
            header('Location: /register');
            exit;
        }
        
        // Criar o usuário
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'type' => 'client' // Todos os novos usuários são clientes
        ];
        
        $userId = User::create($userData);
        
        if ($userId) {
            Session::flash('success', 'Cadastro realizado com sucesso! Aguarde a aprovação de um administrador ou analista para acessar o sistema.');
            header('Location: /login');
        } else {
            Session::flash('error', 'Erro ao criar usuário. Tente novamente.');
            header('Location: /register');
        }
        exit;
    }
    
    public function logout(): void {
        Session::destroy();
        header('Location: /');
        exit;
    }
}
