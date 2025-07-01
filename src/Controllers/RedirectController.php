<?php

namespace App\Controllers;

class RedirectController {
    public function dashboardPhp(): void {
        $this->showRedirectMessage('/dashboard', 'Dashboard');
    }
    
    public function loginPhp(): void {
        $this->showRedirectMessage('/login', 'Login');
    }
    
    public function projectsPhp(): void {
        $this->showRedirectMessage('/projects', 'Projetos');
    }
    
    private function showRedirectMessage(string $correctUrl, string $pageName): void {
        echo "<!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Redirecionamento - Sistema de Arquitetura</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css' rel='stylesheet'>
            <meta http-equiv='refresh' content='3;url=$correctUrl'>
        </head>
        <body class='bg-light'>
            <div class='container d-flex align-items-center justify-content-center min-vh-100'>
                <div class='card shadow-lg' style='max-width: 500px'>
                    <div class='card-body text-center p-5'>
                        <div class='mb-4'>
                            <i class='bi bi-arrow-right-circle display-1 text-warning'></i>
                        </div>
                        <h3 class='card-title mb-3'>URL Incorreta</h3>
                        <p class='text-muted mb-4'>
                            Este sistema usa URLs limpas sem extensões <code>.php</code>
                        </p>
                        <div class='alert alert-info'>
                            <strong>Redirecionando para:</strong><br>
                            <code>$correctUrl</code>
                        </div>
                        <p class='small text-muted mb-4'>
                            Redirecionamento automático em 3 segundos...
                        </p>
                        <div class='d-grid gap-2'>
                            <a href='$correctUrl' class='btn btn-primary btn-lg'>
                                <i class='bi bi-arrow-right'></i>
                                Ir para $pageName Agora
                            </a>
                            <a href='/help' class='btn btn-outline-secondary'>
                                <i class='bi bi-info-circle'></i>
                                Ver Todas as URLs Corretas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
}
