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
        $pendingUsers = User::getPendingApproval();
        
        View::make('admin.users')
            ->with('users', $users)
            ->with('pendingUsers', $pendingUsers)
            ->with('pendingCount', count($pendingUsers))
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
    
    public function activities(): void {
        // Simulação de atividades recentes do sistema
        $activities = [
            [
                'id' => 1,
                'type' => 'user_created',
                'description' => 'Novo usuário cadastrado',
                'subject' => 'Cliente Teste',
                'icon' => 'bi-person-plus',
                'icon_color' => 'success',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'id' => 2,
                'type' => 'project_updated',
                'description' => 'Projeto atualizado',
                'subject' => 'Casa Residencial',
                'icon' => 'bi-pencil',
                'icon_color' => 'info',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours'))
            ],
            [
                'id' => 3,
                'type' => 'document_uploaded',
                'description' => 'Documento enviado',
                'subject' => 'Planta baixa.pdf',
                'icon' => 'bi-file-earmark',
                'icon_color' => 'primary',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ],
            [
                'id' => 4,
                'type' => 'status_changed',
                'description' => 'Status alterado',
                'subject' => 'Projeto concluído',
                'icon' => 'bi-check-circle',
                'icon_color' => 'success',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'id' => 5,
                'type' => 'analyst_assigned',
                'description' => 'Analista atribuído',
                'subject' => 'Reforma Comercial Santos',
                'icon' => 'bi-person-check',
                'icon_color' => 'info',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'id' => 6,
                'type' => 'project_created',
                'description' => 'Novo projeto criado',
                'subject' => 'Edifício Residencial Aurora',
                'icon' => 'bi-plus-circle',
                'icon_color' => 'primary',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'id' => 7,
                'type' => 'user_updated',
                'description' => 'Usuário atualizado',
                'subject' => 'Analista Sistema',
                'icon' => 'bi-person-gear',
                'icon_color' => 'warning',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'id' => 8,
                'type' => 'document_deleted',
                'description' => 'Documento removido',
                'subject' => 'Orçamento preliminar.xlsx',
                'icon' => 'bi-trash',
                'icon_color' => 'danger',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'id' => 9,
                'type' => 'comment_added',
                'description' => 'Comentário adicionado',
                'subject' => 'Revisão de fachada',
                'icon' => 'bi-chat-left-text',
                'icon_color' => 'secondary',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
            ],
            [
                'id' => 10,
                'type' => 'deadline_changed',
                'description' => 'Prazo alterado',
                'subject' => 'Casa Residencial Silva',
                'icon' => 'bi-calendar-check',
                'icon_color' => 'warning',
                'created_at' => date('Y-m-d H:i:s', strtotime('-7 days'))
            ]
        ];
        
        View::make('admin.activities')
            ->with('activities', $activities)
            ->display();
    }
    
    public function approveUser(int $id): void {
        if (User::approve($id)) {
            Session::flash('success', 'Usuário aprovado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao aprovar usuário');
        }
        
        header('Location: /admin/users');
        exit;
    }
    
    public function rejectUser(int $id): void {
        if (User::reject($id)) {
            Session::flash('success', 'Usuário rejeitado com sucesso!');
        } else {
            Session::flash('error', 'Erro ao rejeitar usuário');
        }
        
        header('Location: /admin/users');
        exit;
    }
}
