<?php 
$title = 'Editar Projeto - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-pencil"></i>
                Editar Projeto
            </h1>
            <p class="text-muted">Modificar informações do projeto</p>
        </div>
        <div>
            <a href="/admin/projects" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Voltar aos Projetos
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="/admin/projects/<?= $project['id'] ?>">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi bi-pencil"></i>
                                    Título do Projeto *
                                </label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       value="<?= htmlspecialchars($project['title']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-file-text"></i>
                                Descrição *
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($project['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">
                                    <i class="bi bi-person"></i>
                                    Cliente *
                                </label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" <?= $client['id'] == $project['client_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($client['name']) ?> (<?= htmlspecialchars($client['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="analyst_id" class="form-label">
                                    <i class="bi bi-person-check"></i>
                                    Analista
                                </label>
                                <select class="form-select" id="analyst_id" name="analyst_id">
                                    <option value="">Não atribuído</option>
                                    <?php foreach ($analysts as $analyst): ?>
                                        <option value="<?= $analyst['id'] ?>" <?= $analyst['id'] == $project['analyst_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($analyst['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="deadline" class="form-label">
                                    <i class="bi bi-calendar"></i>
                                    Prazo
                                </label>
                                <input type="date" class="form-control" id="deadline" name="deadline" 
                                       value="<?= htmlspecialchars($project['deadline']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-flag"></i>
                                    Status *
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?= $project['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="in_progress" <?= $project['status'] === 'in_progress' ? 'selected' : '' ?>>Em Andamento</option>
                                    <option value="completed" <?= $project['status'] === 'completed' ? 'selected' : '' ?>>Concluído</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Atenção:</strong> As alterações afetarão o projeto imediatamente. 
                            <?php if ($project['status'] === 'completed'): ?>
                                Este projeto está marcado como concluído.
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/projects" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Informações do Projeto
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <?= $project['id'] ?></p>
                            <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($project['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Última atualização:</strong> <?= date('d/m/Y H:i', strtotime($project['updated_at'])) ?></p>
                            <p><strong>Documentos:</strong> <?= $project['document_count'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
