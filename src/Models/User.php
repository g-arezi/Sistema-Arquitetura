<?php

namespace App\Models;

use App\Core\Database;

class User {
    public static function create(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return Database::insert('users', $data);
    }
    
    public static function findById(int $id): ?array {
        return Database::find('users', $id);
    }
    
    public static function findByEmail(string $email): ?array {
        return Database::findBy('users', ['email' => $email]);
    }
    
    public static function findAll(): array {
        return Database::findAll('users');
    }
    
    public static function update(int $id, array $data): bool {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return Database::update('users', $id, $data);
    }
    
    public static function delete(int $id): bool {
        return Database::delete('users', $id);
    }
    
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public static function toggleStatus(int $id): bool {
        $user = self::findById($id);
        if (!$user) {
            return false;
        }
        
        $newStatus = $user['active'] ? 0 : 1;
        return Database::update('users', $id, ['active' => $newStatus]);
    }
    
    public static function getProjectsByUser(int $userId): array {
        $sql = "SELECT p.*, 
                COUNT(d.id) as documents_count,
                MAX(d.created_at) as last_document_date
                FROM projects p 
                LEFT JOIN documents d ON p.id = d.project_id
                WHERE p.user_id = ? OR p.analyst_id = ?
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        
        $stmt = Database::query($sql, [$userId, $userId]);
        return $stmt->fetchAll();
    }
    
    public static function getUserStats(int $userId): array {
        $projects = Database::query("SELECT COUNT(*) as count FROM projects WHERE user_id = ?", [$userId])->fetch();
        $documents = Database::query("SELECT COUNT(*) as count FROM documents d JOIN projects p ON d.project_id = p.id WHERE p.user_id = ?", [$userId])->fetch();
        
        return [
            'projects' => $projects['count'],
            'documents' => $documents['count']
        ];
    }
}
