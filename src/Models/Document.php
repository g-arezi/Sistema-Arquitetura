<?php

namespace App\Models;

use App\Core\Database;

class Document {
    public static function create(array $data): int {
        $data['uploaded_at'] = date('Y-m-d H:i:s');
        
        return Database::insert('documents', $data);
    }
    
    public static function findById(int $id): ?array {
        return Database::find('documents', $id);
    }
    
    public static function findAll(array $conditions = []): array {
        return Database::findAll('documents', $conditions);
    }
    
    public static function update(int $id, array $data): bool {
        return Database::update('documents', $id, $data);
    }
    
    public static function delete(int $id): bool {
        $document = self::findById($id);
        if ($document && file_exists($document['file_path'])) {
            unlink($document['file_path']);
        }
        
        return Database::delete('documents', $id);
    }
    
    public static function getByProject(int $projectId): array {
        $sql = "SELECT d.*, u.name as uploaded_by_name
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.project_id = ?
                ORDER BY d.uploaded_at DESC";
        
        $stmt = Database::query($sql, [$projectId]);
        return $stmt->fetchAll();
    }
    
    public static function getStats(): array {
        $total = Database::query("SELECT COUNT(*) as count FROM documents")->fetch();
        $today = Database::query("SELECT COUNT(*) as count FROM documents WHERE DATE(uploaded_at) = CURDATE()")->fetch();
        $thisWeek = Database::query("SELECT COUNT(*) as count FROM documents WHERE WEEK(uploaded_at) = WEEK(NOW())")->fetch();
        
        return [
            'total' => $total['count'],
            'today' => $today['count'],
            'this_week' => $thisWeek['count']
        ];
    }
    
    public static function getRecentByUser(int $userId, int $limit = 5): array {
        $sql = "SELECT d.*, p.title as project_title
                FROM documents d
                JOIN projects p ON d.project_id = p.id
                WHERE p.user_id = ? OR p.analyst_id = ?
                ORDER BY d.uploaded_at DESC
                LIMIT ?";
        
        $stmt = Database::query($sql, [$userId, $userId, $limit]);
        return $stmt->fetchAll();
    }
}
