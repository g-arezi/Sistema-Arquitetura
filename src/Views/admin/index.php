<?php 
$title = 'Administração - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-gear"></i>
                Administração
            </h1>
            <p class="text-muted">Painel de controle do sistema</p>
        </div>
        <div>
            <span class="badge bg-danger fs-6">
                <i class="bi bi-shield-check"></i>
                Admin
            </span>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-primary mb-2"></i>
                    <h3 class="mb-1"><?= $stats['users'] ?></h3>
                    <p class="text-muted mb-0">Usuários</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-folder display-4 text-success mb-2"></i>
                    <h3 class="mb-1"><?= $stats['projects']['total'] ?></h3>
                    <p class="text-muted mb-0">Projetos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark display-4 text-info mb-2"></i>
                    <h3 class="mb-1"><?= $stats['documents']['total'] ?></h3>
                    <p class="text-muted mb-0">Documentos</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-clock display-4 text-warning mb-2"></i>
                    <h3 class="mb-1"><?= $stats['projects']['pending'] ?></h3>
                    <p class="text-muted mb-0">Pendentes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i>
                        Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/admin/users" class="btn btn-outline-primary">
                            <i class="bi bi-people"></i>
                            Gerenciar Usuários
                        </a>
                        <a href="/admin/projects" class="btn btn-outline-success">
                            <i class="bi bi-folder"></i>
                            Gerenciar Projetos
                        </a>
                        <a href="/projects/create" class="btn btn-outline-info">
                            <i class="bi bi-plus-circle"></i>
                            Novo Projeto
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up"></i>
                        Atividades Recentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Novo usuário cadastrado</div>
                                <small class="text-muted">Cliente Teste</small>
                            </div>
                            <small class="text-muted">2h atrás</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Projeto atualizado</div>
                                <small class="text-muted">Casa Residencial</small>
                            </div>
                            <small class="text-muted">4h atrás</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Documento enviado</div>
                                <small class="text-muted">Planta baixa.pdf</small>
                            </div>
                            <small class="text-muted">6h atrás</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projetos Recentes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-folder2-open"></i>
                Projetos Recentes
            </h5>
            <a href="/admin/projects" class="btn btn-sm btn-outline-primary">
                Ver Todos
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($recent_projects)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Projeto</th>
                                <th>Cliente</th>
                                <th>Analista</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_projects as $project): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($project['title']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($project['description']) ?></small>
                                    </td>
                                    <td>
                                        <i class="bi bi-person"></i>
                                        <?= htmlspecialchars($project['client_name'] ?? $project['user_name'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <?php if (isset($project['analyst_name']) && $project['analyst_name']): ?>
                                            <i class="bi bi-person-check"></i>
                                            <?= htmlspecialchars($project['analyst_name']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="bi bi-person-dash"></i>
                                                Não atribuído
                                            </span>
                                        <?php endif; ?>
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
                                        <span class="badge bg-<?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="/projects/<?= $project['id'] ?>" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="/projects/<?= $project['id'] ?>">Ver Detalhes</a></li>
                                                <li><a class="dropdown-item" href="/projects/<?= $project['id'] ?>/edit">Editar</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao(<?= $project['id'] ?>)">Excluir</a></li>
                                            </ul>
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
                    <h5 class="text-muted">Nenhum projeto encontrado</h5>
                    <p class="text-muted">Quando houver projetos, eles aparecerão aqui.</p>
                    <a href="/projects/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Criar Primeiro Projeto
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')) {
        // Implementar exclusão
        alert('Funcionalidade de exclusão será implementada em breve.');
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
