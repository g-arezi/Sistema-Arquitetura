<?php 
$title = 'Como Usar o Sistema - Sistema de Arquitetura';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Como Usar o Sistema
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>🔧 URLs Corretas do Sistema</h5>
                        <p class="mb-0">Este sistema usa URLs "limpas" sem extensões .php</p>
                    </div>
                    
                    <h5>✅ URLs Corretas:</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item">
                            <strong>Login:</strong> 
                            <a href="/login" class="text-primary">/login</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Dashboard:</strong> 
                            <a href="/dashboard" class="text-primary">/dashboard</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Projetos:</strong> 
                            <a href="/projects" class="text-primary">/projects</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Perfil:</strong> 
                            <a href="/profile" class="text-primary">/profile</a>
                        </li>
                        <li class="list-group-item">
                            <strong>Admin:</strong> 
                            <a href="/admin" class="text-primary">/admin</a>
                        </li>
                    </ul>
                    
                    <h5>❌ URLs Incorretas:</h5>
                    <ul class="text-muted">
                        <li>❌ /dashboard.php</li>
                        <li>❌ /login.php</li>
                        <li>❌ /projects.php</li>
                    </ul>
                    
                    <div class="alert alert-success mt-4">
                        <h6>🚀 Acesso Rápido</h6>
                        <p>Para começar a usar o sistema:</p>
                        <ol>
                            <li>Acesse <a href="/login">/login</a></li>
                            <li>Use uma das contas de demonstração</li>
                            <li>Será redirecionado automaticamente para o dashboard</li>
                        </ol>
                    </div>
                    
                    <div class="mt-4">
                        <h6>🔑 Contas de Demonstração:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h6 class="text-primary">Admin</h6>
                                        <small>admin@sistema.com</small><br>
                                        <small>admin123</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6 class="text-success">Analista</h6>
                                        <small>analyst@sistema.com</small><br>
                                        <small>analyst123</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h6 class="text-info">Cliente</h6>
                                        <small>client@sistema.com</small><br>
                                        <small>client123</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="/login" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Fazer Login Agora
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
