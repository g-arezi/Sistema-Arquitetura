<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\ProjectFile as Project;
use App\Models\UserFile as User;
use App\Services\FileUploadService;

class ProjectController {
    public function index(): void {
        $userId = Session::get('user_id');
        $userType = Session::get('user_type');
        
        $projects = Project::getByUser($userId, $userType);
        
        View::make('projects.index')
            ->with('projects', $projects)
            ->with('user_type', $userType)
            ->display();
    }
    
    public function create(): void {
        $analysts = [];
        
        if (Session::get('user_type') === 'admin') {
            $analysts = User::findAll(['type' => 'analyst']);
        }
        
        View::make('projects.create')
            ->with('analysts', $analysts)
            ->display();
    }
    
    public function store(): void {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $deadline = $_POST['deadline'] ?? '';
        $analystId = $_POST['analyst_id'] ?? null;
        
        if (empty($title) || empty($description)) {
            Session::flash('error', 'Título e descrição são obrigatórios');
            header('Location: /projects/create');
            exit;
        }
        
        $data = [
            'title' => $title,
            'description' => $description,
            'user_id' => Session::get('user_id'),
            'status' => 'pending'
        ];
        
        if (!empty($deadline)) {
            $data['deadline'] = $deadline;
        }
        
        if (!empty($analystId)) {
            $data['analyst_id'] = $analystId;
        }
        
        $projectId = Project::create($data);
        
        Session::flash('success', 'Projeto criado com sucesso!');
        header("Location: /projects/{$projectId}");
        exit;
    }
    
    public function show(int $id): void {
        $project = Project::getWithUser($id);
        
        if (!$project) {
            Session::flash('error', 'Projeto não encontrado');
            header('Location: /projects');
            exit;
        }
        
        // Verificar permissões
        $userId = Session::get('user_id');
        $userType = Session::get('user_type');
        
        if ($userType !== 'admin' && 
            $project['user_id'] != $userId && 
            $project['analyst_id'] != $userId) {
            Session::flash('error', 'Acesso negado');
            header('Location: /projects');
            exit;
        }
        
        $documents = Project::getDocuments($id);
        
        View::make('projects.show')
            ->with('project', $project)
            ->with('documents', $documents)
            ->with('user_type', $userType)
            ->display();
    }
    
    public function uploadDocument(int $id): void {
        $project = Project::findById($id);
        
        if (!$project) {
            Session::flash('error', 'Projeto não encontrado');
            header('Location: /projects');
            exit;
        }
        
        // Verificar permissões
        $userId = Session::get('user_id');
        $userType = Session::get('user_type');
        
        if ($userType !== 'admin' && 
            $project['user_id'] != $userId && 
            $project['analyst_id'] != $userId) {
            Session::flash('error', 'Acesso negado');
            header("Location: /projects/{$id}");
            exit;
        }
        
        $uploadService = new FileUploadService();
        
        try {
            $result = $uploadService->upload($_FILES['document'], 'documents');
            
            Project::addDocument($id, [
                'name' => $result['original_name'],
                'file_path' => $result['file_path'],
                'file_size' => $result['file_size'],
                'mime_type' => $result['mime_type'],
                'uploaded_by' => $userId
            ]);
            
            Session::flash('success', 'Documento enviado com sucesso!');
        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao enviar documento: ' . $e->getMessage());
        }
        
        header("Location: /projects/{$id}");
        exit;
    }
    
    public function updateStatus(int $id): void {
        $project = Project::findById($id);
        
        if (!$project) {
            Session::flash('error', 'Projeto não encontrado');
            header('Location: /projects');
            exit;
        }
        
        // Verificar permissões - apenas admin e analista podem alterar status
        $userType = Session::get('user_type');
        if ($userType !== 'admin' && $userType !== 'analyst') {
            Session::flash('error', 'Acesso negado');
            header("Location: /projects/{$id}");
            exit;
        }
        
        $status = $_POST['status'] ?? '';
        $validStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            Session::flash('error', 'Status inválido');
            header("Location: /projects/{$id}");
            exit;
        }
        
        if (Project::updateStatus($id, $status)) {
            Session::flash('success', 'Status do projeto atualizado com sucesso!');
            
            // TODO: Enviar email de notificação
            // $emailService = new EmailService();
            // $emailService->sendProjectNotification($project['user_email'], $project['title'], $status);
        } else {
            Session::flash('error', 'Erro ao atualizar status do projeto');
        }
        
        header("Location: /projects/{$id}");
        exit;
    }
}
