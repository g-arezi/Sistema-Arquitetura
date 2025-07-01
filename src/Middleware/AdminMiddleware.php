<?php

namespace App\Middleware;

use App\Core\Session;

class AdminMiddleware {
    public function handle(): bool {
        if (!Session::has('user_id') || Session::get('user_type') !== 'admin') {
            http_response_code(403);
            echo "Acesso negado. Apenas administradores podem acessar esta área.";
            exit;
        }
        
        return true;
    }
}
