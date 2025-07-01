<?php 
$title = 'Projetos - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-folder"></i>
                Projetos
            </h1>
            <p class="text-muted">Gerencie seus projetos de arquitetura</p>
        </div>
        <a href="/projects/create" class="btn btn-primary">
            <i class="bi bi-plus"></i>
            Novo Projeto
        </a>
    </div>

    <!-- Projects Grid -->
    <?php if (empty($projects)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                <h4 class="text-muted mb-3">Nenhum projeto encontrado</h4>
                <p class="text-muted mb-4">Comece criando seu primeiro projeto</p>
                <a href="/projects/create" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus"></i>
                    Criar Projeto
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <a href="/projects/<?= $project['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($project['title']) ?>
                                </a>
                            </h5>
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
                        </div>
                        
                        <p class="card-text text-muted">
                            <?= htmlspecialchars(substr($project['description'], 0, 100)) ?>
                            <?= strlen($project['description']) > 100 ? '...' : '' ?>
                        </p>
                        
                        <div class="row g-2 mb-3">
                            <?php if ($user_type === 'admin' || $user_type === 'analyst'): ?>
                            <div class="col-6">
                                <small class="text-muted d-block">Cliente</small>
                                <small><?= htmlspecialchars($project['user_name'] ?? 'N/A') ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['analyst_name'])): ?>
                            <div class="col-6">
                                <small class="text-muted d-block">Analista</small>
                                <small><?= htmlspecialchars($project['analyst_name']) ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-6">
                                <small class="text-muted d-block">Documentos</small>
                                <small>
                                    <i class="bi bi-file-earmark"></i>
                                    <?= $project['documents_count'] ?? 0 ?>
                                </small>
                            </div>
                            
                            <div class="col-6">
                                <small class="text-muted d-block">Criado em</small>
                                <small><?= date('d/m/Y', strtotime($project['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <a href="/projects/<?= $project['id'] ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-eye"></i>
                                Ver Detalhes
                            </a>
                            
                            <?php if ($user_type === 'admin' || $user_type === 'analyst'): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/status" class="d-inline">
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-play"></i>
                                                Iniciar
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="/projects/<?= $project['id'] ?>/status" class="d-inline">
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-check"></i>
                                                Concluir
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
