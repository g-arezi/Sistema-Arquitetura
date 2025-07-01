<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\UserFile as User;
use App\Models\ProjectFile as Project;
use App\Models\DocumentFile as Document;

class AdminController {
    public function index(): void {
        $stats = [
            'users' => count(User::findAll()),
            'projects' => Project::getStats(),
            'documents' => Document::getStats()
        ];
        
        $recentProjects = array_slice(Project::findAll(), 0, 10);
        
        View::make('admin.index')
            ->with('stats', $stats)
            ->with('recent_projects', $recentProjects)
            ->display();
    }
    
    public function users(): void {
        $users = User::findAll();
        
        View::make('admin.users')
            ->with('users', $users)
            ->display();
    }
    
    public function projects(): void {
        $projects = Project::findAll();
        $stats = Project::getStats();
        
        // Buscar clientes para o filtro
        $allUsers = User::findAll();
        $clients = array_filter($allUsers, function($user) {
            return $user['type'] === 'client';
        });
        
        View::make('admin.projects')
            ->with('projects', $projects)
            ->with('stats', $stats)
            ->with('clients', $clients)
            ->display();
    }
    
    public function toggleUser(int $id): void {
        if (User::toggleStatus($id)) {
            Session::flash('success', 'Status do usuário alterado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao alterar status do usuário');
        }
        
        header('Location: /admin/users');
        exit;
    }
}
