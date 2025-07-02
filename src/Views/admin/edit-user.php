<?php 
$title = 'Editar Usuário - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-gear"></i>
                Editar Usuário
            </h1>
            <p class="text-muted">Atualize as informações do usuário</p>
        </div>
        <div>
            <a href="/admin/users" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="/admin/users/<?= $user['id'] ?>/update" method="POST" data-loading>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="type" class="form-label">Tipo de Usuário</label>
                        <select class="form-select" id="type" name="type">
                            <option value="client" <?= $user['type'] === 'client' ? 'selected' : '' ?>>Cliente</option>
                            <option value="analyst" <?= $user['type'] === 'analyst' ? 'selected' : '' ?>>Analista</option>
                            <option value="admin" <?= $user['type'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Nova Senha (deixe em branco para manter atual)</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Preencha apenas se deseja alterar a senha</div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    Informações da Conta
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>ID do Usuário:</strong> <?= $user['id'] ?>
                                </div>
                                <div class="mb-3">
                                    <strong>Data de Criação:</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                </div>
                                <div>
                                    <strong>Status:</strong>
                                    <?php if ($user['active']): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-lock"></i>
                                    Ações de Segurança
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="resetPassword" name="reset_password">
                                    <label class="form-check-label" for="resetPassword">
                                        Forçar redefinição de senha no próximo login
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="twoFactor" name="two_factor">
                                    <label class="form-check-label" for="twoFactor">
                                        Habilitar autenticação de dois fatores
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="/admin/users" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmação ao alterar tipo de usuário
    const typeSelect = document.getElementById('type');
    const originalType = typeSelect.value;
    
    typeSelect.addEventListener('change', function() {
        if (this.value !== originalType) {
            if (!confirm(`Tem certeza que deseja alterar o tipo de usuário de ${originalType.toUpperCase()} para ${this.value.toUpperCase()}?`)) {
                this.value = originalType;
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
