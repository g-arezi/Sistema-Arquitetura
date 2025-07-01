<?php 
$title = 'Perfil - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person"></i>
                Meu Perfil
            </h1>
            <p class="text-muted">Gerencie suas informações pessoais</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="/profile">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person"></i>
                                    Nome Completo
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">
                                    <i class="bi bi-person-badge"></i>
                                    Tipo de Usuário
                                </label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['type']) ?>" readonly>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="created_at" class="form-label">
                                    <i class="bi bi-calendar"></i>
                                    Membro desde
                                </label>
                                <input type="text" class="form-control" 
                                       value="<?= date('d/m/Y', strtotime($user['created_at'])) ?>" readonly>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">
                            <i class="bi bi-lock"></i>
                            Alterar Senha
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Senha Atual</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="new_password" class="form-label">Nova Senha</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                <div class="form-text">Deixe em branco para manter a senha atual</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Salvar Alterações
                            </button>
                            <a href="/dashboard" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i>
                                Voltar ao Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-shield-check"></i>
                        Segurança da Conta
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Email verificado
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Conta ativa
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield text-primary me-2"></i>
                            Senha criptografada
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-info-circle"></i>
                        Dicas de Segurança
                    </h5>
                    <ul class="small">
                        <li>Use uma senha forte com pelo menos 8 caracteres</li>
                        <li>Inclua letras maiúsculas, minúsculas e números</li>
                        <li>Não compartilhe sua senha com outras pessoas</li>
                        <li>Faça logout ao usar computadores públicos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação de senha
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword && confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Senhas não coincidem');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Validar campos de senha juntos
document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    const currentPassword = document.getElementById('current_password');
    
    if (this.value && !currentPassword.value) {
        currentPassword.setCustomValidity('Senha atual é obrigatória para alterar a senha');
        currentPassword.classList.add('is-invalid');
    } else {
        currentPassword.setCustomValidity('');
        currentPassword.classList.remove('is-invalid');
    }
    
    // Revalidar confirmação
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
