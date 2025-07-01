<?php 
$title = $project['title'] . ' - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 

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

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-folder"></i>
                <?= htmlspecialchars($project['title']) ?>
            </h1>
            <p class="text-muted">
                Criado em <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                <?php if ($project['deadline']): ?>
                    • Prazo: <?= date('d/m/Y', strtotime($project['deadline'])) ?>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <span class="badge bg-<?= $statusColors[$project['status']] ?> fs-6 me-2">
                <?= $statusLabels[$project['status']] ?>
            </span>
            <a href="/projects" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Project Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text"></i>
                        Detalhes do Projeto
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Cliente:</strong><br>
                            <?= htmlspecialchars($project['user_name'] ?? 'N/A') ?><br>
                            <small class="text-muted"><?= htmlspecialchars($project['user_email'] ?? '') ?></small>
                        </div>
                        
                        <?php if ($project['analyst_name']): ?>
                        <div class="col-md-6">
                            <strong>Analista:</strong><br>
                            <?= htmlspecialchars($project['analyst_name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($project['analyst_email'] ?? '') ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Descrição:</strong><br>
                        <p class="mt-2"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                    </div>
                    
                    <?php if ($user_type === 'admin' || $user_type === 'analyst'): ?>
                    <div class="border-top pt-3">
                        <strong>Ações:</strong>
                        <div class="btn-group ms-2" role="group">
                            <?php if ($project['status'] === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-info" onclick="updateStatus('in_progress')">
                                    <i class="bi bi-play"></i> Iniciar
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($project['status'] === 'in_progress'): ?>
                                <button type="button" class="btn btn-sm btn-success" onclick="updateStatus('completed')">
                                    <i class="bi bi-check"></i> Concluir
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($project['status'] !== 'cancelled'): ?>
                                <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus('cancelled')">
                                    <i class="bi bi-x"></i> Cancelar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Documents -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark"></i>
                        Documentos (<?= count($documents) ?>)
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus"></i>
                        Upload
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-x display-4 text-muted mb-3"></i>
                            <p class="text-muted">Nenhum documento encontrado</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="bi bi-upload"></i>
                                Fazer Upload
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($documents as $doc): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($doc['name']) ?></h6>
                                        <small class="text-muted">
                                            Enviado em <?= date('d/m/Y H:i', strtotime($doc['uploaded_at'])) ?>
                                            <?php if ($doc['uploaded_by_name']): ?>
                                                por <?= htmlspecialchars($doc['uploaded_by_name']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle"></i>
                        Informações
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-<?= $statusColors[$project['status']] ?> ms-1">
                                <?= $statusLabels[$project['status']] ?>
                            </span>
                        </li>
                        <li class="mb-2">
                            <strong>Criado:</strong> <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                        </li>
                        <?php if ($project['deadline']): ?>
                        <li class="mb-2">
                            <strong>Prazo:</strong> <?= date('d/m/Y', strtotime($project['deadline'])) ?>
                        </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <strong>Documentos:</strong> <?= count($documents) ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-clock-history"></i>
                        Histórico
                    </h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="bi bi-plus-circle text-success"></i>
                            <div>
                                <small class="text-muted">Projeto criado</small><br>
                                <small><?= date('d/m/Y H:i', strtotime($project['created_at'])) ?></small>
                            </div>
                        </div>
                        
                        <?php if ($project['status'] !== 'pending'): ?>
                        <div class="timeline-item">
                            <i class="bi bi-play-circle text-info"></i>
                            <div>
                                <small class="text-muted">Status: <?= $statusLabels[$project['status']] ?></small><br>
                                <small><?= date('d/m/Y H:i', strtotime($project['updated_at'])) ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/projects/<?= $project['id'] ?>/upload" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i>
                        Upload de Documento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="upload-area mb-3">
                        <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                        <p class="mb-2">Clique para selecionar ou arraste o arquivo aqui</p>
                        <small class="text-muted">PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR (máx. 10MB)</small>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar,.jpg,.jpeg,.png,.gif" required hidden>
                        <div class="file-info mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i>
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    if (confirm('Tem certeza que deseja alterar o status do projeto?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/projects/<?= $project['id'] ?>/status`;
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'status';
        input.value = status;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.timeline-item i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
