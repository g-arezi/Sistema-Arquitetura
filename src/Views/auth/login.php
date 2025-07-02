<?php 
$title = 'Login - Sistema de Arquitetura';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg bg-dark border-secondary">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-building display-4 text-primary mb-3"></i>
                        <h3 class="card-title text-light">Entrar no Sistema</h3>
                        <p class="text-muted">Acesse sua conta para continuar</p>
                    </div>
                    
                    <form method="POST" action="/login">
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">
                                <i class="bi bi-envelope text-primary"></i>
                                Email
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label text-light">
                                <i class="bi bi-lock text-primary"></i>
                                Senha
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Entrar
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0 text-light">Não tem uma conta?</p>
                        <a href="/register" class="btn btn-outline-primary">
                            <i class="bi bi-person-plus"></i>
                            Criar conta
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Demo Accounts -->
            <div class="card mt-4 bg-dark border-secondary">
                <div class="card-body">
                    <h6 class="card-title text-light">
                        <i class="bi bi-info-circle text-primary"></i>
                        Contas de Demonstração
                    </h6>
                    <div class="row g-2">
                        <div class="col-4">
                            <small class="text-muted d-block">Admin</small>
                            <small class="text-light">admin@sistema.com</small><br>
                            <small class="text-light">admin123</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Analista</small>
                            <small class="text-light">analista@sistema.com</small><br>
                            <small class="text-light">analista123</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Cliente</small>
                            <small class="text-light">cliente@sistema.com</small><br>
                            <small class="text-light">cliente123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
