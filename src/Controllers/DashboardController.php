<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\UserFile as User;
use App\Models\ProjectFile as Project;
use App\Models\DocumentFile as Document;

class DashboardController {
    public function index(): void {
        // Verificar autenticação
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
        
        $userId = Session::get('user_id');
        $userType = Session::get('user_type');
        
        $data = [
            'user_type' => $userType,
            'user_name' => Session::get('user_name')
        ];
        
        if ($userType === 'admin') {
            $data['stats'] = [
                'users' => count(User::findAll()),
                'projects' => Project::getStats(),
                'documents' => Document::getStats()
            ];
            $data['recent_projects'] = Project::findAll();
        } else {
            $data['projects'] = Project::getByUser($userId, $userType);
            $data['recent_documents'] = Document::getRecentByUser($userId);
            $data['user_stats'] = User::getUserStats($userId);
        }
        
        View::make('dashboard.index')->withData($data)->display();
    }
}
