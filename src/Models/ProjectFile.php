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
    
    public static function create(array $data): int {
        // Em uma implementação real, salvaria no banco ou arquivo
        $newId = max(array_keys(self::$projects)) + 1;
        $newProject = [
            'id' => $newId,
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'pending',
            'user_id' => $data['user_id'] ?? $data['client_id'],
            'client_id' => $data['client_id'] ?? $data['user_id'],
            'analyst_id' => $data['analyst_id'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'user_name' => self::getUserName($data['user_id'] ?? $data['client_id']),
            'client_name' => self::getUserName($data['client_id'] ?? $data['user_id']),
            'analyst_name' => isset($data['analyst_id']) && $data['analyst_id'] ? self::getUserName($data['analyst_id']) : null,
            'documents_count' => 0,
            'document_count' => 0
        ];
        
        self::$projects[$newId] = $newProject;
        return $newId;
    }
    
    public static function update(int $id, array $data): bool {
        if (!isset(self::$projects[$id])) {
            return false;
        }
        
        foreach ($data as $key => $value) {
            if (isset(self::$projects[$id][$key])) {
                self::$projects[$id][$key] = $value;
            }
        }
        
        self::$projects[$id]['updated_at'] = date('Y-m-d H:i:s');
        
        // Atualizar nomes se IDs mudaram
        if (isset($data['client_id'])) {
            self::$projects[$id]['user_name'] = self::getUserName($data['client_id']);
            self::$projects[$id]['client_name'] = self::getUserName($data['client_id']);
        }
        
        if (isset($data['analyst_id'])) {
            self::$projects[$id]['analyst_name'] = $data['analyst_id'] ? self::getUserName($data['analyst_id']) : null;
        }
        
        return true;
    }
    
    public static function delete(int $id): bool {
        if (isset(self::$projects[$id])) {
            unset(self::$projects[$id]);
            return true;
        }
        return false;
    }
    
    public static function assignAnalyst(int $projectId, int $analystId): bool {
        if (!isset(self::$projects[$projectId])) {
            return false;
        }
        
        self::$projects[$projectId]['analyst_id'] = $analystId;
        self::$projects[$projectId]['analyst_name'] = self::getUserName($analystId);
        self::$projects[$projectId]['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    public static function changeStatus(int $projectId, string $status): bool {
        if (!isset(self::$projects[$projectId])) {
            return false;
        }
        
        self::$projects[$projectId]['status'] = $status;
        self::$projects[$projectId]['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    public static function addDocument(int $projectId, array $documentData): bool {
        if (!isset(self::$projects[$projectId])) {
            return false;
        }
        
        // Em uma implementação real, isso seria salvo em uma tabela de documentos
        // Por enquanto, vamos apenas incrementar o contador de documentos
        if (!isset(self::$projects[$projectId]['documents'])) {
            self::$projects[$projectId]['documents'] = [];
        }
        
        $documentId = count(self::$projects[$projectId]['documents']) + 1;
        $document = array_merge($documentData, [
            'id' => $documentId,
            'project_id' => $projectId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        self::$projects[$projectId]['documents'][] = $document;
        self::$projects[$projectId]['documents_count'] = count(self::$projects[$projectId]['documents']);
        self::$projects[$projectId]['document_count'] = count(self::$projects[$projectId]['documents']);
        self::$projects[$projectId]['updated_at'] = date('Y-m-d H:i:s');
        
        return true;
    }
    
    public static function updateStatus(int $projectId, string $status): bool {
        return self::changeStatus($projectId, $status);
    }

    private static function getUserName(int $userId): string {
        // Simulação simples - em implementação real buscaria do UserFile
        $userNames = [
            1 => 'Administrador',
            2 => 'Analista Sistema', 
            3 => 'Cliente Teste'
        ];
        
        return $userNames[$userId] ?? 'Usuário Desconhecido';
    }
}
