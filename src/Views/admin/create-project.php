<?php 
$title = 'Criar Projeto - Sistema de Arquitetura';
$showSidebar = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-plus-circle"></i>
                Criar Projeto
            </h1>
            <p class="text-muted">Adicionar novo projeto ao sistema</p>
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
                    <form method="POST" action="/admin/projects">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi bi-pencil"></i>
                                    Título do Projeto *
                                </label>
                                <input type="text" class="form-control" id="title" name="title" required 
                                       placeholder="Ex: Casa Residencial Silva">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-file-text"></i>
                                Descrição *
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" required
                                      placeholder="Descreva os detalhes do projeto..."></textarea>
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
                                        <option value="<?= $client['id'] ?>">
                                            <?= htmlspecialchars($client['name']) ?> (<?= htmlspecialchars($client['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="analyst_id" class="form-label">
                                    <i class="bi bi-person-check"></i>
                                    Analista (Opcional)
                                </label>
                                <select class="form-select" id="analyst_id" name="analyst_id">
                                    <option value="">Não atribuir agora</option>
                                    <?php foreach ($analysts as $analyst): ?>
                                        <option value="<?= $analyst['id'] ?>">
                                            <?= htmlspecialchars($analyst['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="deadline" class="form-label">
                                    <i class="bi bi-calendar"></i>
                                    Prazo (Opcional)
                                </label>
                                <input type="date" class="form-control" id="deadline" name="deadline">
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Informação:</strong> O projeto será criado com status "Pendente" e poderá ser modificado posteriormente.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="/admin/projects" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i>
                                Criar Projeto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
