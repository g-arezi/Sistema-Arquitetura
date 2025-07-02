<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Session;

class TestController {
    public function activities(): void {
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
        
        // Set mock admin user
        Session::set('user', [
            'id' => 1,
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'type' => 'admin'
        ]);
        
        View::make('admin.activities')
            ->with('activities', $activities)
            ->display();
    }
}
