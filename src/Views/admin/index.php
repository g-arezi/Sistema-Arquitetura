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
            <div class="card border-primary h-100" onclick="navigateToUsers()" style="cursor: pointer;" 
                 data-bs-toggle="tooltip" data-bs-placement="top" title="Clique para gerenciar usuários">
                <div class="card-body text-center">
                    <i class="bi bi-people display-4 text-primary mb-2"></i>
                    <h3 class="mb-1" id="userCount"><?= $stats['users'] ?></h3>
                    <p class="text-muted mb-0">Usuários</p>
                    <small class="text-primary">
                        <i class="bi bi-arrow-right"></i>
                        Gerenciar
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-success h-100" onclick="navigateToProjects()" style="cursor: pointer;"
                 data-bs-toggle="tooltip" data-bs-placement="top" title="Clique para gerenciar projetos">
                <div class="card-body text-center">
                    <i class="bi bi-folder display-4 text-success mb-2"></i>
                    <h3 class="mb-1" id="projectCount"><?= $stats['projects']['total'] ?></h3>
                    <p class="text-muted mb-0">Projetos</p>
                    <small class="text-success">
                        <i class="bi bi-arrow-right"></i>
                        Gerenciar
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-info h-100" onclick="showDocumentsInfo()" style="cursor: pointer;"
                 data-bs-toggle="tooltip" data-bs-placement="top" title="Clique para ver informações dos documentos">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark display-4 text-info mb-2"></i>
                    <h3 class="mb-1" id="documentCount"><?= $stats['documents']['total'] ?></h3>
                    <p class="text-muted mb-0">Documentos</p>
                    <small class="text-info">
                        <i class="bi bi-info-circle"></i>
                        Detalhes
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-warning h-100" onclick="showPendingProjects()" style="cursor: pointer;"
                 data-bs-toggle="tooltip" data-bs-placement="top" title="Clique para ver projetos pendentes">
                <div class="card-body text-center">
                    <i class="bi bi-clock display-4 text-warning mb-2"></i>
                    <h3 class="mb-1" id="pendingCount"><?= $stats['projects']['pending'] ?></h3>
                    <p class="text-muted mb-0">Pendentes</p>
                    <small class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Atenção
                    </small>
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
                            <span class="badge bg-primary ms-1"><?= $stats['users'] ?></span>
                        </a>
                        <a href="/admin/projects" class="btn btn-outline-success">
                            <i class="bi bi-folder"></i>
                            Gerenciar Projetos
                            <span class="badge bg-success ms-1"><?= $stats['projects']['total'] ?></span>
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="openQuickCreateModal()">
                            <i class="bi bi-plus-circle"></i>
                            Criação Rápida
                        </button>
                        <a href="/admin/projects/create" class="btn btn-outline-warning">
                            <i class="bi bi-plus-square"></i>
                            Novo Projeto Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up"></i>
                        Atividades Recentes
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshActivities()">
                        <i class="bi bi-arrow-clockwise"></i>
                        Atualizar
                    </button>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="activitiesList">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <i class="bi bi-person-plus text-success"></i>
                                    Novo usuário cadastrado
                                </div>
                                <small class="text-muted">Cliente Teste</small>
                            </div>
                            <small class="text-muted">2h atrás</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <i class="bi bi-pencil text-info"></i>
                                    Projeto atualizado
                                </div>
                                <small class="text-muted">Casa Residencial</small>
                            </div>
                            <small class="text-muted">4h atrás</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <i class="bi bi-file-earmark text-primary"></i>
                                    Documento enviado
                                </div>
                                <small class="text-muted">Planta baixa.pdf</small>
                            </div>
                            <small class="text-muted">6h atrás</small>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">
                                    <i class="bi bi-check-circle text-success"></i>
                                    Status alterado
                                </div>
                                <small class="text-muted">Projeto concluído</small>
                            </div>
                            <small class="text-muted">1d atrás</small>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="/admin/activities" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-clock-history"></i>
                            Ver Histórico Completo
                        </a>
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
                                            <a href="/projects/<?= $project['id'] ?>" class="btn btn-outline-primary" title="Ver Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/admin/projects/<?= $project['id'] ?>/edit" class="btn btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="dropdown" title="Mais Ações">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="/projects/<?= $project['id'] ?>">
                                                    <i class="bi bi-eye"></i> Ver Detalhes
                                                </a></li>
                                                <li><a class="dropdown-item" href="/admin/projects/<?= $project['id'] ?>/edit">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="openAssignAnalystModal(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title']) ?>')">
                                                    <i class="bi bi-person-plus"></i> Atribuir Analista
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="openChangeStatusModal(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title']) ?>', '<?= $project['status'] ?>')">
                                                    <i class="bi bi-flag"></i> Alterar Status
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="openDeleteModal(<?= $project['id'] ?>, '<?= htmlspecialchars($project['title']) ?>')">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </a></li>
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

<!-- Modal para Criação Rápida -->
<div class="modal fade" id="quickCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-lightning-charge"></i>
                    Criação Rápida de Projeto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickCreateForm" method="POST" action="/admin/projects">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quickTitle" class="form-label">
                                <i class="bi bi-pencil"></i>
                                Título do Projeto *
                            </label>
                            <input type="text" class="form-control" id="quickTitle" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quickClient" class="form-label">
                                <i class="bi bi-person"></i>
                                Cliente *
                            </label>
                            <select class="form-select" id="quickClient" name="client_id" required>
                                <option value="">Selecione...</option>
                                <option value="3">Cliente Teste</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="quickDescription" class="form-label">
                            <i class="bi bi-file-text"></i>
                            Descrição *
                        </label>
                        <textarea class="form-control" id="quickDescription" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quickAnalyst" class="form-label">
                                <i class="bi bi-person-check"></i>
                                Analista
                            </label>
                            <select class="form-select" id="quickAnalyst" name="analyst_id">
                                <option value="">Atribuir depois</option>
                                <option value="1">Administrador</option>
                                <option value="2">Analista Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quickDeadline" class="form-label">
                                <i class="bi bi-calendar"></i>
                                Prazo
                            </label>
                            <input type="date" class="form-control" id="quickDeadline" name="deadline">
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Criação Rápida:</strong> O projeto será criado com status "Pendente" e poderá ser editado posteriormente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-rocket"></i>
                        Criar Projeto Rapidamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Atribuir Analista -->
<div class="modal fade" id="assignAnalystModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus"></i>
                    Atribuir Analista
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignAnalystForm" method="POST" action="/admin/projects/assign-analyst">
                <div class="modal-body">
                    <p>Atribuir analista para o projeto: <strong id="assignProjectTitle"></strong></p>
                    
                    <input type="hidden" id="assignProjectId" name="project_id">
                    
                    <div class="mb-3">
                        <label for="analystSelect" class="form-label">Selecionar Analista:</label>
                        <select class="form-select" id="analystSelect" name="analyst_id" required>
                            <option value="">Selecione um analista...</option>
                            <option value="1">Administrador</option>
                            <option value="2">Analista Sistema</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Atribuir Analista
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Alterar Status -->
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-flag"></i>
                    Alterar Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeStatusForm" method="POST" action="/admin/projects/change-status">
                <div class="modal-body">
                    <p>Alterar status do projeto: <strong id="statusProjectTitle"></strong></p>
                    
                    <input type="hidden" id="statusProjectId" name="project_id">
                    
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Novo Status:</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="pending">🟡 Pendente</option>
                            <option value="in_progress">🔵 Em Andamento</option>
                            <option value="completed">🟢 Concluído</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Alterar Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Confirmar Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                    </div>
                    
                    <p>Tem certeza que deseja excluir o projeto: <strong id="deleteProjectTitle"></strong>?</p>
                    
                    <p class="text-muted">Todos os documentos e dados relacionados ao projeto serão perdidos permanentemente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Sim, Excluir Projeto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Função para abrir modal de criação rápida
function openQuickCreateModal() {
    const modal = new bootstrap.Modal(document.getElementById('quickCreateModal'));
    modal.show();
}

// Funções de navegação para cards de estatísticas
function navigateToUsers() {
    window.location.href = '/admin/users';
}

function navigateToProjects() {
    window.location.href = '/admin/projects';
}

function showDocumentsInfo() {
    alert('Funcionalidade de documentos será implementada em breve!\n\nTotal de documentos: <?= $stats['documents']['total'] ?>');
}

function showPendingProjects() {
    if (<?= $stats['projects']['pending'] ?> > 0) {
        if (confirm('Existem <?= $stats['projects']['pending'] ?> projeto(s) pendente(s).\n\nDeseja visualizá-los agora?')) {
            window.location.href = '/admin/projects?filter=pending';
        }
    } else {
        showSuccessMessage('🎉 Parabéns! Não há projetos pendentes no momento.');
    }
}

// Função para atualizar atividades
function refreshActivities() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    
    // Mostrar loading
    icon.className = 'bi bi-arrow-clockwise spin';
    btn.disabled = true;
    
    // Simular atualização (em uma implementação real, faria uma chamada AJAX)
    setTimeout(() => {
        // Restaurar botão
        icon.className = 'bi bi-arrow-clockwise';
        btn.disabled = false;
        
        // Mostrar feedback
        showSuccessMessage('Atividades atualizadas!');
    }, 1000);
}

// Função para abrir modal de atribuir analista
function openAssignAnalystModal(projectId, projectTitle) {
    document.getElementById('assignProjectId').value = projectId;
    document.getElementById('assignProjectTitle').textContent = projectTitle;
    
    const modal = new bootstrap.Modal(document.getElementById('assignAnalystModal'));
    modal.show();
}

// Função para abrir modal de alterar status
function openChangeStatusModal(projectId, projectTitle, currentStatus) {
    document.getElementById('statusProjectId').value = projectId;
    document.getElementById('statusProjectTitle').textContent = projectTitle;
    document.getElementById('statusSelect').value = currentStatus;
    
    const modal = new bootstrap.Modal(document.getElementById('changeStatusModal'));
    modal.show();
}

// Função para abrir modal de exclusão
function openDeleteModal(projectId, projectTitle) {
    document.getElementById('deleteProjectTitle').textContent = projectTitle;
    document.getElementById('deleteForm').action = `/admin/projects/${projectId}/delete`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Feedback visual para formulários
document.getElementById('quickCreateForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Criando...';
    btn.disabled = true;
});

document.getElementById('assignAnalystForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Atribuindo...';
    btn.disabled = true;
});

document.getElementById('changeStatusForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Alterando...';
    btn.disabled = true;
});

document.getElementById('deleteForm').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Excluindo...';
    btn.disabled = true;
});

// Mensagens de confirmação mais amigáveis
function showSuccessMessage(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alert.style.top = '20px';
    alert.style.right = '20px';
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        <i class="bi bi-check-circle"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Auto-refresh da página após operações (opcional)
if (window.location.search.includes('success=1')) {
    showSuccessMessage('Operação realizada com sucesso!');
    
    // Limpar URL
    const url = new URL(window.location);
    url.searchParams.delete('success');
    window.history.replaceState({}, '', url);
}

// Funcionalidades antigas mantidas para compatibilidade (removidas posteriormente)
function confirmarExclusao(id) {
    openDeleteModal(id, 'este projeto');
}

function atribuirAnalista(projectId) {
    openAssignAnalystModal(projectId, 'Projeto ID: ' + projectId);
}

function alterarStatus(projectId) {
    openChangeStatusModal(projectId, 'Projeto ID: ' + projectId, 'pending');
}

// Inicialização quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Adicionar efeitos de hover aos cards de estatísticas
    const statCards = document.querySelectorAll('.card[onclick]');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.2s ease-in-out';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Adicionar contador animado nos números
    animateCounters();
});

// Função para animar contadores
function animateCounters() {
    const counters = [
        { element: document.getElementById('userCount'), target: <?= $stats['users'] ?> },
        { element: document.getElementById('projectCount'), target: <?= $stats['projects']['total'] ?> },
        { element: document.getElementById('documentCount'), target: <?= $stats['documents']['total'] ?> },
        { element: document.getElementById('pendingCount'), target: <?= $stats['projects']['pending'] ?> }
    ];
    
    counters.forEach(counter => {
        if (counter.element) {
            animateValue(counter.element, 0, counter.target, 1000);
        }
    });
}

// Função para animar um valor específico
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        element.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Inicialização global
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contadores animados
    const animateCounters = () => {
        document.querySelectorAll('.card-body h3, .card-body h4').forEach(counter => {
            const finalValue = parseInt(counter.textContent);
            if (!isNaN(finalValue)) {
                // Reset para zero
                counter.textContent = '0';
                
                // Animar até o valor final
                let currentValue = 0;
                const duration = 1500; // ms
                const increment = Math.ceil(finalValue / (duration / 50));
                
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= finalValue) {
                        counter.textContent = finalValue;
                        clearInterval(timer);
                    } else {
                        counter.textContent = currentValue;
                    }
                }, 50);
            }
        });
    };
    
    // Executar animação após carregamento
    setTimeout(animateCounters, 300);
    
    // Ativar todos os tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.card {
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.btn {
    transition: all 0.15s ease-in-out;
}

.list-group-item {
    transition: background-color 0.15s ease-in-out;
}

.list-group-item:hover {
    background-color: var(--bs-light);
}

.modal-content {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
