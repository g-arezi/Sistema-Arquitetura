<?php 
$title = 'Detalhes do Usuário - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-person"></i>
                Detalhes do Usuário
            </h1>
            <p class="text-muted">Visualizando informações do usuário</p>
        </div>
        <div>
            <a href="/admin/users" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
            <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-primary">
                <i class="bi bi-pencil"></i>
                Editar
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Informações do Usuário -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-vcard"></i>
                        Informações Pessoais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-primary text-white mb-3">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h5><?= htmlspecialchars($user['name']) ?></h5>
                        <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>ID:</strong> <?= $user['id'] ?>
                    </div>
                    <div class="mb-3">
                        <strong>Tipo:</strong>
                        <?php
                        $typeClass = match($user['type']) {
                            'admin' => 'danger',
                            'analyst' => 'info',
                            'client' => 'success',
                            default => 'secondary'
                        };
                        
                        $typeText = match($user['type']) {
                            'admin' => 'Administrador',
                            'analyst' => 'Analista',
                            'client' => 'Cliente',
                            default => 'Desconhecido'
                        };
                        ?>
                        <span class="badge bg-<?= $typeClass ?>"><?= $typeText ?></span>
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <?php if ($user['active'] ?? true): ?>
                            <span class="badge bg-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inativo</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Aprovação:</strong>
                        <?php if (isset($user['approved']) && $user['approved']): ?>
                            <span class="badge bg-success">Aprovado</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Pendente</span>
                            
                            <?php if ($user['active']): ?>
                                <div class="mt-2">
                                    <form action="/admin/users/<?= $user['id'] ?>/approve" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirma a aprovação deste usuário?')">
                                            <i class="bi bi-check-lg"></i> Aprovar
                                        </button>
                                    </form>
                                    <form action="/admin/users/<?= $user['id'] ?>/reject" method="post" class="d-inline ms-1">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Confirma a rejeição deste usuário?')">
                                            <i class="bi bi-x-lg"></i> Rejeitar
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Data de Cadastro:</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                    </div>
                    <?php if (isset($user['last_login']) && $user['last_login']): ?>
                    <div>
                        <strong>Último Login:</strong> <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-<?= ($user['active'] ?? true) ? 'danger' : 'success' ?>"
                                onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>', <?= ($user['active'] ?? true) ? 'true' : 'false' ?>)">
                            <?php if ($user['active'] ?? true): ?>
                                <i class="bi bi-person-x"></i> Desativar
                            <?php else: ?>
                                <i class="bi bi-person-check"></i> Ativar
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart"></i>
                        Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>Projetos Totais:</div>
                        <strong><?= $stats['total_projects'] ?? 0 ?></strong>
                    </div>
                    <?php if ($user['type'] === 'client'): ?>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Projetos Pendentes:</div>
                        <strong><?= $stats['pending_projects'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Projetos Concluídos:</div>
                        <strong><?= $stats['completed_projects'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Documentos Enviados:</div>
                        <strong><?= $stats['documents_count'] ?? 0 ?></strong>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($user['type'] === 'analyst'): ?>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Em Análise:</div>
                        <strong><?= $stats['in_progress_projects'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>Concluídos:</div>
                        <strong><?= $stats['completed_projects'] ?? 0 ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Taxa de Conclusão:</div>
                        <strong><?= $stats['completion_rate'] ?? '0%' ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Projetos do Usuário -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i>
                        Projetos do Usuário
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($projects)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($project['title']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars(substr($project['description'], 0, 50)) . (strlen($project['description']) > 50 ? '...' : '') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = match($project['status']) {
                                                    'pending' => 'warning',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    default => 'secondary'
                                                };
                                                
                                                $statusText = match($project['status']) {
                                                    'pending' => 'Pendente',
                                                    'in_progress' => 'Em Andamento',
                                                    'completed' => 'Concluído',
                                                    default => 'Desconhecido'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/projects/<?= $project['id'] ?>" class="btn btn-outline-primary" title="Ver Detalhes">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="/admin/projects/<?= $project['id'] ?>/edit" class="btn btn-outline-warning" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                            <h5>Nenhum Projeto Encontrado</h5>
                            <p class="text-muted">Este usuário não possui projetos no sistema.</p>
                            
                            <?php if ($user['type'] === 'client'): ?>
                                <a href="/admin/projects/create" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle"></i>
                                    Criar Projeto
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto;
}
</style>

<script>
function toggleUserStatus(userId, userName, isActive) {
    const action = isActive ? 'desativar' : 'ativar';
    if (confirm(`Deseja realmente ${action} o usuário ${userName}?`)) {
        // Enviar formulário de toggle
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}/toggle`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
