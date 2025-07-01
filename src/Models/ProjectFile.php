<?php

namespace App\Models;

class ProjectFile {
    private static array $projects = [
        1 => [
            'id' => 1,
            'title' => 'Projeto Residencial Silva',
            'description' => 'Projeto arquitetônico para residência unifamiliar de 150m²',
            'status' => 'in_progress',
            'user_id' => 3,
            'client_id' => 3,
            'analyst_id' => 2,
            'deadline' => '2025-08-15',
            'created_at' => '2025-07-01 10:00:00',
            'updated_at' => '2025-07-01 10:00:00',
            'user_name' => 'Cliente Teste',
            'client_name' => 'Cliente Teste',
            'analyst_name' => 'Analista Sistema',
            'documents_count' => 3,
            'document_count' => 3
        ],
        2 => [
            'id' => 2,
            'title' => 'Reforma Comercial Santos',
            'description' => 'Reforma de loja comercial no centro da cidade',
            'status' => 'pending',
            'user_id' => 3,
            'client_id' => 3,
            'analyst_id' => null,
            'deadline' => '2025-09-30',
            'created_at' => '2025-07-01 11:00:00',
            'updated_at' => '2025-07-01 11:00:00',
            'user_name' => 'Cliente Teste',
            'client_name' => 'Cliente Teste',
            'analyst_name' => null,
            'documents_count' => 1,
            'document_count' => 1
        ],
        3 => [
            'id' => 3,
            'title' => 'Edifício Comercial Central',
            'description' => 'Projeto de edifício comercial de 5 andares',
            'status' => 'completed',
            'user_id' => 3,
            'client_id' => 3,
            'analyst_id' => 2,
            'deadline' => '2025-07-20',
            'created_at' => '2025-06-15 09:00:00',
            'updated_at' => '2025-07-01 16:00:00',
            'user_name' => 'Cliente Teste',
            'client_name' => 'Cliente Teste',
            'analyst_name' => 'Analista Sistema',
            'documents_count' => 8,
            'document_count' => 8
        ]
    ];
    
    public static function findById(int $id): ?array {
        return self::$projects[$id] ?? null;
    }
    
    public static function getWithUser(int $id): ?array {
        return self::findById($id);
    }
    
    public static function getByUser(int $userId, string $userType = 'client'): array {
        $projects = [];
        foreach (self::$projects as $project) {
            if ($userType === 'client' && $project['user_id'] == $userId) {
                $projects[] = $project;
            } elseif ($userType === 'analyst' && ($project['analyst_id'] == $userId || $project['user_id'] == $userId)) {
                $projects[] = $project;
            } elseif ($userType === 'admin') {
                $projects[] = $project;
            }
        }
        return $projects;
    }
    
    public static function findAll(): array {
        return array_values(self::$projects);
    }
    
    public static function getStats(): array {
        $total = count(self::$projects);
        $pending = 0;
        $progress = 0;
        $completed = 0;
        
        foreach (self::$projects as $project) {
            switch ($project['status']) {
                case 'pending': $pending++; break;
                case 'in_progress': $progress++; break;
                case 'completed': $completed++; break;
            }
        }
        
        return [
            'total' => $total,
            'pending' => $pending,
            'in_progress' => $progress,
            'completed' => $completed
        ];
    }
    
    public static function getDocuments(int $projectId): array {
        return [
            [
                'id' => 1,
                'name' => 'Planta Baixa.pdf',
                'file_path' => '/uploads/documents/planta_baixa.pdf',
                'uploaded_at' => '2025-07-01 12:00:00',
                'uploaded_by_name' => 'Cliente Teste'
            ],
            [
                'id' => 2,
                'name' => 'Fachada Principal.jpg',
                'file_path' => '/uploads/documents/fachada.jpg',
                'uploaded_at' => '2025-07-01 12:30:00',
                'uploaded_by_name' => 'Analista Sistema'
            ]
        ];
    }
}
