<?php

namespace App\Middleware;

use App\Core\Session;

class AuthMiddleware {
    public function handle(): bool {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
        
        return true;
    }
}
