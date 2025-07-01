<?php

namespace App\Controllers;

use App\Core\View;

class HelpController {
    public function urls(): void {
        View::make('help.urls')->display();
    }
}
