<?php 
$title = 'Gerenciar Usuários - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 

use App\Core\Session;
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-people"></i>
                Gerenciar Usuários
            </h1>
            <p class="text-muted">Controle de usuários do sistema</p>
        </div>
        <div>
            <a href="/admin" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar ao Admin
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filtrar por tipo:</label>
                    <select class="form-select" id="filterType">
                        <option value="">Todos os tipos</option>
                        <option value="admin">Administrador</option>
                        <option value="analyst">Analista</option>
                        <option value="client">Cliente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status:</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar:</label>
                    <input type="text" class="form-control" id="searchUsers" placeholder="Nome ou email...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="filtrarUsuarios()">
                        <i class="bi bi-search"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list"></i>
                Lista de Usuários
            </h5>
            <span class="badge bg-primary"><?= count($users) ?> usuários</span>
        </div>
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Cadastrado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr data-type="<?= $user['type'] ?>" data-status="<?= $user['active'] ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">
                                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($user['name']) ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?= $user['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-envelope"></i>
                                        <?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $typeClass = match($user['type']) {
                                            'admin' => 'danger',
                                            'analyst' => 'success',
                                            'client' => 'info',
                                            default => 'secondary'
                                        };
                                        
                                        $typeText = match($user['type']) {
                                            'admin' => 'Administrador',
                                            'analyst' => 'Analista',
                                            'client' => 'Cliente',
                                            default => 'Desconhecido'
                                        };
                                        
                                        $typeIcon = match($user['type']) {
                                            'admin' => 'shield-check',
                                            'analyst' => 'person-check',
                                            'client' => 'person',
                                            default => 'question'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $typeClass ?>">
                                            <i class="bi bi-<?= $typeIcon ?>"></i>
                                            <?= $typeText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['active']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i>
                                                Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i>
                                                Inativo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/users/<?= $user['id'] ?>/view" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($user['id'] != Session::get('user_id')): ?>
                                                <form method="POST" action="/admin/users/<?= $user['id'] ?>/toggle" class="d-inline">
                                                    <button type="submit" class="btn btn-outline-<?= $user['active'] ? 'warning' : 'success' ?>" 
                                                            onclick="return confirm('Tem certeza que deseja <?= $user['active'] ? 'desativar' : 'ativar' ?> este usuário?')">
                                                        <i class="bi bi-<?= $user['active'] ? 'pause' : 'play' ?>"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-people display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum usuário encontrado</h5>
                    <p class="text-muted">Não há usuários cadastrados no sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
}
</style>

<script>
function filtrarUsuarios() {
    const filterType = document.getElementById('filterType').value;
    const filterStatus = document.getElementById('filterStatus').value;
    const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
    
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const status = row.getAttribute('data-status');
        const text = row.textContent.toLowerCase();
        
        let show = true;
        
        if (filterType && type !== filterType) show = false;
        if (filterStatus && status !== filterStatus) show = false;
        if (searchTerm && !text.includes(searchTerm)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

// Filtro em tempo real na busca
document.getElementById('searchUsers').addEventListener('input', filtrarUsuarios);
document.getElementById('filterType').addEventListener('change', filtrarUsuarios);
document.getElementById('filterStatus').addEventListener('change', filtrarUsuarios);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
