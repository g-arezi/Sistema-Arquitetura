<?php

namespace App\Models;

use App\Core\Database;

class Project {
    public static function create(array $data): int {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'pending';
        
        return Database::insert('projects', $data);
    }
    
    public static function findById(int $id): ?array {
        return Database::find('projects', $id);
    }
    
    public static function findAll(array $conditions = []): array {
        return Database::findAll('projects', $conditions);
    }
    
    public static function update(int $id, array $data): bool {
        return Database::update('projects', $id, $data);
    }
    
    public static function delete(int $id): bool {
        return Database::delete('projects', $id);
    }
    
    public static function getWithUser(int $id): ?array {
        $sql = "SELECT p.*, u.name as user_name, u.email as user_email,
                a.name as analyst_name, a.email as analyst_email
                FROM projects p 
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN users a ON p.analyst_id = a.id
                WHERE p.id = ?";
        
        $stmt = Database::query($sql, [$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function getDocuments(int $projectId): array {
        return Database::findAll('documents', ['project_id' => $projectId]);
    }
    
    public static function addDocument(int $projectId, array $data): int {
        $data['project_id'] = $projectId;
        $data['uploaded_at'] = date('Y-m-d H:i:s');
        
        return Database::insert('documents', $data);
    }
    
    public static function getByUser(int $userId, string $userType = 'client'): array {
        $sql = "SELECT p.*, u.name as user_name, a.name as analyst_name,
                COUNT(d.id) as documents_count
                FROM projects p 
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN users a ON p.analyst_id = a.id
                LEFT JOIN documents d ON p.id = d.project_id";
        
        $params = [];
        
        if ($userType === 'client') {
            $sql .= " WHERE p.user_id = ?";
            $params[] = $userId;
        } elseif ($userType === 'analyst') {
            $sql .= " WHERE p.analyst_id = ? OR p.user_id = ?";
            $params[] = $userId;
            $params[] = $userId;
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";
        
        $stmt = Database::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function getStats(): array {
        $total = Database::query("SELECT COUNT(*) as count FROM projects")->fetch();
        $pending = Database::query("SELECT COUNT(*) as count FROM projects WHERE status = 'pending'")->fetch();
        $progress = Database::query("SELECT COUNT(*) as count FROM projects WHERE status = 'in_progress'")->fetch();
        $completed = Database::query("SELECT COUNT(*) as count FROM projects WHERE status = 'completed'")->fetch();
        
        return [
            'total' => $total['count'],
            'pending' => $pending['count'],
            'in_progress' => $progress['count'],
            'completed' => $completed['count']
        ];
    }
    
    public static function updateStatus(int $id, string $status): bool {
        return self::update($id, ['status' => $status]);
    }
}
