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
    
    public function createProject(): void {
        $users = User::findAll();
        $clients = array_filter($users, fn($u) => $u['type'] === 'client');
        $analysts = array_filter($users, fn($u) => $u['type'] === 'analyst');
        
        View::make('admin.create-project')
            ->with('clients', $clients)
            ->with('analysts', $analysts)
            ->display();
    }
    
    public function storeProject(): void {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $clientId = $_POST['client_id'] ?? '';
        $analystId = $_POST['analyst_id'] ?? null;
        $deadline = $_POST['deadline'] ?? '';
        
        if (empty($title) || empty($description) || empty($clientId)) {
            Session::flash('error', 'Todos os campos obrigatórios devem ser preenchidos.');
            header('Location: /admin/projects/create');
            exit;
        }
        
        $projectData = [
            'title' => $title,
            'description' => $description,
            'client_id' => $clientId,
            'analyst_id' => $analystId,
            'deadline' => $deadline,
            'status' => 'pending'
        ];
        
        if (Project::create($projectData)) {
            Session::flash('success', 'Projeto criado com sucesso!');
            header('Location: /admin/projects');
        } else {
            Session::flash('error', 'Erro ao criar projeto.');
            header('Location: /admin/projects/create');
        }
        exit;
    }
    
    public function editProject(int $id): void {
        $project = Project::findById($id);
        if (!$project) {
            Session::flash('error', 'Projeto não encontrado.');
            header('Location: /admin/projects');
            exit;
        }
        
        $users = User::findAll();
        $clients = array_filter($users, fn($u) => $u['type'] === 'client');
        $analysts = array_filter($users, fn($u) => $u['type'] === 'analyst');
        
        View::make('admin.edit-project')
            ->with('project', $project)
            ->with('clients', $clients)
            ->with('analysts', $analysts)
            ->display();
    }
    
    public function updateProject(int $id): void {
        $project = Project::findById($id);
        if (!$project) {
            Session::flash('error', 'Projeto não encontrado.');
            header('Location: /admin/projects');
            exit;
        }
        
        $updateData = [
            'title' => $_POST['title'] ?? $project['title'],
            'description' => $_POST['description'] ?? $project['description'],
            'client_id' => $_POST['client_id'] ?? $project['client_id'],
            'analyst_id' => $_POST['analyst_id'] ?? $project['analyst_id'],
            'deadline' => $_POST['deadline'] ?? $project['deadline'],
            'status' => $_POST['status'] ?? $project['status']
        ];
        
        if (Project::update($id, $updateData)) {
            Session::flash('success', 'Projeto atualizado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao atualizar projeto.');
        }
        
        header('Location: /admin/projects');
        exit;
    }
    
    public function deleteProject(int $id): void {
        if (Project::delete($id)) {
            Session::flash('success', 'Projeto excluído com sucesso!');
        } else {
            Session::flash('error', 'Erro ao excluir projeto.');
        }
        
        header('Location: /admin/projects');
        exit;
    }
    
    public function assignAnalyst(): void {
        $projectId = $_POST['project_id'] ?? '';
        $analystId = $_POST['analyst_id'] ?? '';
        
        if (empty($projectId) || empty($analystId)) {
            Session::flash('error', 'Dados inválidos.');
            header('Location: /admin/projects');
            exit;
        }
        
        if (Project::assignAnalyst($projectId, $analystId)) {
            Session::flash('success', 'Analista atribuído com sucesso!');
        } else {
            Session::flash('error', 'Erro ao atribuir analista.');
        }
        
        header('Location: /admin/projects');
        exit;
    }
    
    public function changeProjectStatus(): void {
        $projectId = $_POST['project_id'] ?? '';
        $status = $_POST['status'] ?? '';
        
        if (empty($projectId) || empty($status)) {
            Session::flash('error', 'Dados inválidos.');
            header('Location: /admin/projects');
            exit;
        }
        
        if (Project::changeStatus($projectId, $status)) {
            Session::flash('success', 'Status alterado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao alterar status.');
        }
        
        header('Location: /admin/projects');
        exit;
    }
    
    public function viewUser(int $id): void {
        $user = User::findById($id);
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            header('Location: /admin/users');
            exit;
        }
        
        $userProjects = Project::getByUser($id, $user['type']);
        $userStats = User::getUserStats($id);
        
        View::make('admin.view-user')
            ->with('user', $user)
            ->with('projects', $userProjects)
            ->with('stats', $userStats)
            ->display();
    }
    
    public function editUser(int $id): void {
        $user = User::findById($id);
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            header('Location: /admin/users');
            exit;
        }
        
        View::make('admin.edit-user')
            ->with('user', $user)
            ->display();
    }
    
    public function updateUser(int $id): void {
        $user = User::findById($id);
        if (!$user) {
            Session::flash('error', 'Usuário não encontrado.');
            header('Location: /admin/users');
            exit;
        }
        
        $updateData = [
            'name' => $_POST['name'] ?? $user['name'],
            'email' => $_POST['email'] ?? $user['email'],
            'type' => $_POST['type'] ?? $user['type']
        ];
        
        // Atualizar senha se fornecida
        if (!empty($_POST['password'])) {
            $updateData['password'] = User::hashPassword($_POST['password']);
        }
        
        if (User::update($id, $updateData)) {
            Session::flash('success', 'Usuário atualizado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao atualizar usuário.');
        }
        
        header('Location: /admin/users');
        exit;
    }
}
