<?php

namespace App\Models;

class DocumentFile {
    public static function getStats(): array {
        return [
            'total' => 8,
            'today' => 2,
            'this_week' => 5
        ];
    }
    
    public static function getRecentByUser(int $userId, int $limit = 5): array {
        return [
            [
                'id' => 1,
                'name' => 'Planta Baixa.pdf',
                'project_title' => 'Projeto Residencial Silva'
            ],
            [
                'id' => 2,
                'name' => 'Memorial Descritivo.docx',
                'project_title' => 'Reforma Comercial Santos'
            ]
        ];
    }
}
