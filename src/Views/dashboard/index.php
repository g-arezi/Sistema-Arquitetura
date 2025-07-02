<?php 
$title = 'Dashboard - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-light">Dashboard</h1>
            <p class="text-muted">Bem-vindo, <?= htmlspecialchars($user_name) ?>!</p>
        </div>
        <div>
            <span class="badge bg-primary fs-6">
                <?= ucfirst($user_type) ?>
            </span>
        </div>
    </div>

    <?php if ($user_type === 'admin'): ?>
        <!-- Admin Dashboard -->
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
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-clock display-4 text-warning mb-2"></i>
                        <h3 class="mb-1"><?= $stats['projects']['pending'] ?></h3>
                        <p class="text-muted mb-0">Pendentes</p>
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
        </div>

        <!-- Recent Projects -->
        <div class="card bg-dark border-secondary">
            <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-light">
                    <i class="bi bi-folder text-primary"></i>
                    Projetos Recentes
                </h5>
                <a href="/admin/projects" class="btn btn-sm btn-outline-primary">Ver Todos</a>
            </div>
            <div class="card-body bg-dark">
                <?php if (empty($recent_projects)): ?>
                    <p class="text-muted text-center py-4">Nenhum projeto encontrado</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th class="text-light">Projeto</th>
                                    <th class="text-light">Cliente</th>
                                    <th class="text-light">Status</th>
                                    <th class="text-light">Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recent_projects, 0, 5) as $project): ?>
                                <tr class="text-light">
                                    <td>
                                        <a href="/projects/<?= $project['id'] ?>" class="text-primary text-decoration-none">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-light"><?= htmlspecialchars($project['user_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pendente',
                                            'in_progress' => 'Em Andamento',
                                            'completed' => 'Concluído',
                                            'cancelled' => 'Cancelado'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$project['status']] ?>">
                                            <?= $statusLabels[$project['status']] ?>
                                        </span>
                                    </td>
                                    <td class="text-light"><?= date('d/m/Y', strtotime($project['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- User Dashboard -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-folder display-4 text-primary mb-2"></i>
                        <h3 class="mb-1"><?= $user_stats['projects'] ?></h3>
                        <p class="text-muted mb-0">Meus Projetos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark display-4 text-success mb-2"></i>
                        <h3 class="mb-1"><?= $user_stats['documents'] ?></h3>
                        <p class="text-muted mb-0">Documentos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info bg-dark">
                    <div class="card-body text-center">
                        <i class="bi bi-plus-circle display-4 text-info mb-2"></i>
                        <h3 class="mb-1">
                            <a href="/projects/create" class="text-primary text-decoration-none">Novo</a>
                        </h3>
                        <p class="text-muted mb-0">Criar Projeto</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- My Projects -->
            <div class="col-lg-8">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-light">
                            <i class="bi bi-folder text-primary"></i>
                            Meus Projetos
                        </h5>
                        <a href="/projects" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                    </div>
                    <div class="card-body bg-dark">
                        <?php if (empty($projects)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-folder-x display-4 text-muted mb-3"></i>
                                <p class="text-muted">Nenhum projeto encontrado</p>
                                <a href="/projects/create" class="btn btn-primary">
                                    <i class="bi bi-plus"></i>
                                    Criar Primeiro Projeto
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($projects, 0, 5) as $project): ?>
                                <div class="list-group-item bg-dark border-secondary d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="/projects/<?= $project['id'] ?>" class="text-primary text-decoration-none">
                                                <?= htmlspecialchars($project['title']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= ($project['documents_count'] ?? 0) ?> documentos
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $statusColors[$project['status']] ?? 'secondary' ?>">
                                        <?= $statusLabels[$project['status']] ?? $project['status'] ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Documents -->
            <div class="col-lg-4">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-light">
                            <i class="bi bi-file-earmark text-primary"></i>
                            Documentos Recentes
                        </h5>
                    </div>
                    <div class="card-body bg-dark">
                        <?php if (empty($recent_documents)): ?>
                            <p class="text-muted text-center py-3">Nenhum documento encontrado</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_documents as $doc): ?>
                                <div class="list-group-item bg-dark border-secondary px-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark me-2 text-primary"></i>
                                        <div>
                                            <h6 class="mb-0 text-light"><?= htmlspecialchars($doc['name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($doc['project_title']) ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
