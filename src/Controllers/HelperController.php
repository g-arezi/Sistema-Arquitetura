<?php

namespace App\Controllers;

class HelperController {
    public function activitiesRedirect(): void {
        header('Location: /debug-routes.php?controller=admin&method=activities');
        exit;
    }
    
    public function debugRouter(): void {
        header('Location: /debug-routes.php');
        exit;
    }
}
