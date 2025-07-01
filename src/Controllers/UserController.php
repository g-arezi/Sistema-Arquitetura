<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\UserFile as User;

class UserController {
    public function profile(): void {
        $userId = Session::get('user_id');
        $user = User::findById($userId);
        
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado');
            header('Location: /dashboard');
            exit;
        }
        
        View::make('user.profile')
            ->with('user', $user)
            ->display();
    }
    
    public function updateProfile(): void {
        $userId = Session::get('user_id');
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validações básicas
        if (empty($name) || empty($email)) {
            Session::flash('error', 'Nome e email são obrigatórios');
            header('Location: /profile');
            exit;
        }
        
        $user = User::findById($userId);
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado');
            header('Location: /dashboard');
            exit;
        }
        
        // Verificar se email já existe para outro usuário
        $existingUser = User::findByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            Session::flash('error', 'Este email já está em uso por outro usuário');
            header('Location: /profile');
            exit;
        }
        
        $updateData = [
            'name' => $name,
            'email' => $email
        ];
        
        // Atualizar senha se fornecida
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                Session::flash('error', 'Senha atual é obrigatória para alterar a senha');
                header('Location: /profile');
                exit;
            }
            
            if (!User::verifyPassword($currentPassword, $user['password'])) {
                Session::flash('error', 'Senha atual incorreta');
                header('Location: /profile');
                exit;
            }
            
            if ($newPassword !== $confirmPassword) {
                Session::flash('error', 'Nova senha e confirmação não coincidem');
                header('Location: /profile');
                exit;
            }
            
            if (strlen($newPassword) < 6) {
                Session::flash('error', 'Nova senha deve ter pelo menos 6 caracteres');
                header('Location: /profile');
                exit;
            }
            
            $updateData['password'] = $newPassword;
        }
        
        if (User::update($userId, $updateData)) {
            // Atualizar dados da sessão
            Session::set('user_name', $name);
            Session::set('user_email', $email);
            
            Session::flash('success', 'Perfil atualizado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao atualizar perfil');
        }
        
        header('Location: /profile');
        exit;
    }
}
