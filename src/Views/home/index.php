<?php 
$title = 'Página Inicial - Sistema de Arquitetura';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <!-- Hero Section -->
    <div class="row min-vh-100 align-items-center">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-primary mb-4">
                Sistema de Gestão de
                <span class="text-warning">Arquitetura</span>
            </h1>
            <p class="lead mb-4">
                Gerencie seus projetos arquitetônicos, documentos e colaborações de forma simples e eficiente. 
                Uma plataforma moderna para arquitetos, analistas e clientes.
            </p>
            
            <div class="d-flex gap-3 mb-5">
                <a href="/login" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Entrar
                </a>
                <a href="/register" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-person-plus"></i>
                    Cadastrar
                </a>
            </div>
            
            <!-- Features -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle p-2 me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Múltiplos Usuários</h6>
                            <small class="text-muted">Admin, Analistas e Clientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle p-2 me-3">
                            <i class="bi bi-folder"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Gestão de Projetos</h6>
                            <small class="text-muted">Acompanhamento completo</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle p-2 me-3">
                            <i class="bi bi-cloud-upload"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Upload Seguro</h6>
                            <small class="text-muted">Documentos protegidos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle p-2 me-3">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Notificações</h6>
                            <small class="text-muted">Email automático</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="text-center">
                <div class="bg-gradient p-5 rounded-4 shadow-lg">
                    <i class="bi bi-building display-1 text-primary mb-4"></i>
                    <h3 class="text-dark mb-3">Tecnologia Moderna</h3>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>PHP 8+ com Composer</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Bootstrap 5 Responsivo</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>MySQL Database</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>PHPMailer Integration</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Framework MVC Próprio</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
