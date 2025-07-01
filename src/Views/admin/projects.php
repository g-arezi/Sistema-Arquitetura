<?php 
$title = 'Gerenciar Projetos - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-folder"></i>
                Gerenciar Projetos
            </h1>
            <p class="text-muted">Controle de projetos do sistema</p>
        </div>
        <div>
            <a href="/admin" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Voltar ao Admin
            </a>
            <a href="/projects/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Novo Projeto
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-clock display-6 text-warning mb-2"></i>
                    <h4 class="mb-1"><?= $stats['pending'] ?></h4>
                    <p class="text-muted mb-0">Pendentes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle display-6 text-info mb-2"></i>
                    <h4 class="mb-1"><?= $stats['in_progress'] ?></h4>
                    <p class="text-muted mb-0">Em Andamento</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle display-6 text-success mb-2"></i>
                    <h4 class="mb-1"><?= $stats['completed'] ?></h4>
                    <p class="text-muted mb-0">Concluídos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-folder2-open display-6 text-primary mb-2"></i>
                    <h4 class="mb-1"><?= $stats['total'] ?></h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status:</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Todos os status</option>
                        <option value="pending">Pendente</option>
                        <option value="in_progress">Em Andamento</option>
                        <option value="completed">Concluído</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cliente:</label>
                    <select class="form-select" id="filterClient">
                        <option value="">Todos os clientes</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar:</label>
                    <input type="text" class="form-control" id="searchProjects" placeholder="Título ou descrição...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="filtrarProjetos()">
                        <i class="bi bi-search"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Projetos -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list"></i>
                Lista de Projetos
            </h5>
            <span class="badge bg-primary"><?= count($projects) ?> projetos</span>
        </div>
        <div class="card-body">
            <?php if (!empty($projects)): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="projectsTable">
                        <thead>
                            <tr>
                                <th>Projeto</th>
                                <th>Cliente</th>
                                <th>Analista</th>
                                <th>Status</th>
                                <th>Documentos</th>
                                <th>Criado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr data-status="<?= $project['status'] ?>" data-client="<?= $project['client_id'] ?>">
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($project['title']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars(substr($project['description'], 0, 50)) ?>...</small>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-person"></i>
                                        <?= htmlspecialchars($project['client_name'] ?? $project['user_name'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <?php if (isset($project['analyst_name']) && $project['analyst_name']): ?>
                                            <i class="bi bi-person-check text-success"></i>
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
                                        <i class="bi bi-file-earmark"></i>
                                        <?= $project['document_count'] ?? 0 ?>
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
                                                <li><a class="dropdown-item" href="#" onclick="atribuirAnalista(<?= $project['id'] ?>)">Atribuir Analista</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="alterarStatus(<?= $project['id'] ?>)">Alterar Status</a></li>
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
                    <p class="text-muted">Não há projetos cadastrados no sistema.</p>
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
function filtrarProjetos() {
    const filterStatus = document.getElementById('filterStatus').value;
    const filterClient = document.getElementById('filterClient').value;
    const searchTerm = document.getElementById('searchProjects').value.toLowerCase();
    
    const rows = document.querySelectorAll('#projectsTable tbody tr');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const client = row.getAttribute('data-client');
        const text = row.textContent.toLowerCase();
        
        let show = true;
        
        if (filterStatus && status !== filterStatus) show = false;
        if (filterClient && client !== filterClient) show = false;
        if (searchTerm && !text.includes(searchTerm)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function atribuirAnalista(id) {
    alert('Atribuir analista ao projeto ID: ' + id + '\nFuncionalidade será implementada em breve.');
}

function alterarStatus(id) {
    alert('Alterar status do projeto ID: ' + id + '\nFuncionalidade será implementada em breve.');
}

function confirmarExclusao(id) {
    if (confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')) {
        alert('Excluir projeto ID: ' + id + '\nFuncionalidade será implementada em breve.');
    }
}

// Filtros em tempo real
document.getElementById('searchProjects').addEventListener('input', filtrarProjetos);
document.getElementById('filterStatus').addEventListener('change', filtrarProjetos);
document.getElementById('filterClient').addEventListener('change', filtrarProjetos);
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
