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
                <div class="col-md-2">
                    <label class="form-label">Tipo de usuário:</label>
                    <select class="form-select" id="filterType">
                        <option value="">Todos os tipos</option>
                        <option value="admin">Administrador</option>
                        <option value="analyst">Analista</option>
                        <option value="client">Cliente</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status:</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Aprovação:</label>
                    <select class="form-select" id="filterApproval">
                        <option value="">Todos</option>
                        <option value="1">Aprovados</option>
                        <option value="0">Pendentes</option>
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
                                <th>Aprovação</th>
                                <th>Cadastrado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr data-type="<?= $user['type'] ?>" data-status="<?= $user['active'] ?>" data-approval="<?= $user['approved'] ?? 0 ?>">
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
                                        <?php if (isset($user['approved']) && $user['approved']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-lg"></i>
                                                Aprovado
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock"></i>
                                                Pendente
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

    <!-- Seção de Aprovações Pendentes -->
    <?php if (isset($pendingUsers) && count($pendingUsers) > 0): ?>
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-clock-history me-2"></i>
                    Usuários Pendentes de Aprovação
                </div>
                <div>
                    <span class="badge bg-dark"><?= count($pendingUsers) ?> pendente(s)</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingUsers as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['type'] === 'admin' ? 'danger' : ($user['type'] === 'analyst' ? 'info' : 'success') ?>">
                                            <?= ucfirst(htmlspecialchars($user['type'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form action="/admin/users/<?= $user['id'] ?>/approve" method="post" style="display:inline;">
                                                <button type="submit" class="btn btn-success me-1" onclick="return confirm('Confirma a aprovação deste usuário?')">
                                                    <i class="bi bi-check-lg"></i> Aprovar
                                                </button>
                                            </form>
                                            <form action="/admin/users/<?= $user['id'] ?>/reject" method="post" style="display:inline;">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Confirma a rejeição deste usuário?')">
                                                    <i class="bi bi-x-lg"></i> Rejeitar
                                                </button>
                                            </form>
                                            <a href="/admin/users/<?= $user['id'] ?>/view" class="btn btn-secondary ms-1">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
    const filterApproval = document.getElementById('filterApproval').value;
    const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
    
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const type = row.getAttribute('data-type');
        const status = row.getAttribute('data-status');
        const approval = row.getAttribute('data-approval');
        const text = row.textContent.toLowerCase();
        
        let show = true;
        
        if (filterType && type !== filterType) show = false;
        if (filterStatus && status !== filterStatus) show = false;
        if (filterApproval && approval !== filterApproval) show = false;
        if (searchTerm && !text.includes(searchTerm)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

// Filtro em tempo real na busca
document.getElementById('searchUsers').addEventListener('input', filtrarUsuarios);
document.getElementById('filterType').addEventListener('change', filtrarUsuarios);
document.getElementById('filterStatus').addEventListener('change', filtrarUsuarios);
document.getElementById('filterApproval').addEventListener('change', filtrarUsuarios);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
