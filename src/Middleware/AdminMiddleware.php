<?php

namespace App\Middleware;

use App\Core\Session;

class AdminMiddleware {
    public function handle(): bool {
        // Debug information - show what's in the session
        if (isset($_GET['debug'])) {
            echo '<pre>';
            echo "Session contents: \n";
            var_dump($_SESSION);
            echo '</pre>';
        }

        // Support both session formats - user object or individual user_ variables
        if (
            // Either check for a user object
            (Session::has('user') && isset(Session::get('user')['type']) && Session::get('user')['type'] === 'admin')
            ||
            // Or check for individual user_ variables
            (Session::has('user_type') && Session::get('user_type') === 'admin')
        ) {
            return true;
        }
        
        // If we get here, access is denied
        http_response_code(403);
        echo "Acesso negado. Apenas administradores podem acessar esta área.";
        exit;
    }
}
