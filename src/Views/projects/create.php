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
                Criar Novo Projeto
            </h1>
            <p class="text-muted">Preencha os dados do seu projeto</p>
        </div>
        <a href="/projects" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Voltar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="/projects">
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="bi bi-folder"></i>
                                Título do Projeto *
                            </label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" required 
                                   placeholder="Ex: Projeto Residencial João Silva">
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="bi bi-file-text"></i>
                                Descrição do Projeto *
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="5" required
                                      placeholder="Descreva detalhadamente o projeto, incluindo objetivos, especificações e requisitos..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="deadline" class="form-label">
                                    <i class="bi bi-calendar"></i>
                                    Prazo de Entrega
                                </label>
                                <input type="date" class="form-control" id="deadline" name="deadline" 
                                       min="<?= date('Y-m-d') ?>">
                                <div class="form-text">Data limite para conclusão do projeto</div>
                            </div>
                            
                            <?php if (!empty($analysts)): ?>
                            <div class="col-md-6 mb-4">
                                <label for="analyst_id" class="form-label">
                                    <i class="bi bi-person-badge"></i>
                                    Analista Responsável
                                </label>
                                <select class="form-select" id="analyst_id" name="analyst_id">
                                    <option value="">Selecionar mais tarde</option>
                                    <?php foreach ($analysts as $analyst): ?>
                                    <option value="<?= $analyst['id'] ?>">
                                        <?= htmlspecialchars($analyst['name']) ?> (<?= htmlspecialchars($analyst['email']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Profissional que irá analisar e gerenciar o projeto</div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-info-circle"></i>
                                        Informações Importantes
                                    </h6>
                                    <ul class="mb-0">
                                        <li>Após criar o projeto, você poderá fazer upload de documentos</li>
                                        <li>O analista será notificado por email sobre o novo projeto</li>
                                        <li>Você receberá atualizações sobre o progresso do projeto</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i>
                                Criar Projeto
                            </button>
                            <a href="/projects" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
