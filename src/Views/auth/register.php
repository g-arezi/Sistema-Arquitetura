<?php 
$title = 'Cadastro - Sistema de Arquitetura';
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg bg-dark border-secondary">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus display-4 text-primary mb-3"></i>
                        <h3 class="card-title text-light">Criar Conta</h3>
                        <p class="text-muted">Preencha os dados para se cadastrar</p>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Informação:</strong> Após o cadastro, sua conta precisará ser aprovada por um administrador ou analista antes que você possa acessar o sistema.
                    </div>
                    
                    <form method="POST" action="/register">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label text-light">
                                    <i class="bi bi-person text-primary"></i>
                                    Nome Completo
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label text-light">
                                    <i class="bi bi-envelope text-primary"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label text-light">
                                    <i class="bi bi-lock text-primary"></i>
                                    Senha
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <div class="form-text text-muted">Mínimo 6 caracteres</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label text-light">
                                    <i class="bi bi-lock-fill text-primary"></i>
                                    Confirmar Senha
                                </label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="type" class="form-label text-light">
                                <i class="bi bi-person-badge text-primary"></i>
                                Tipo de Usuário
                            </label>
                            <select class="form-select" id="type" name="type">
                                <option value="client">Cliente</option>
                                <option value="analyst">Analista</option>
                            </select>
                            <div class="form-text text-muted">Administradores são criados apenas pelo sistema</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-person-plus"></i>
                            Criar Conta
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0 text-light">Já tem uma conta?</p>
                        <a href="/login" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Entrar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação de senha
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
