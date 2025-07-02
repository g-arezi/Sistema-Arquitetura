<?php 
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função helper para formato de data relativa
function formatTimeAgo($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $interval = $now->diff($past);
    
    if ($interval->y > 0) {
        return $interval->y . ' ano' . ($interval->y > 1 ? 's' : '') . ' atrás';
    }
    if ($interval->m > 0) {
        return $interval->m . ' mês' . ($interval->m > 1 ? 'es' : '') . ' atrás';
    }
    if ($interval->d > 0) {
        return $interval->d . ' dia' . ($interval->d > 1 ? 's' : '') . ' atrás';
    }
    if ($interval->h > 0) {
        return $interval->h . 'h atrás';
    }
    if ($interval->i > 0) {
        return $interval->i . 'min atrás';
    }
    return 'agora';
}

// Ensure we have activities even if the controller didn't provide them
if (!isset($activities)) {
    $activities = [
        [
            'id' => 1,
            'type' => 'user_created',
            'description' => 'Novo usuário cadastrado',
            'subject' => 'Cliente Teste',
            'icon' => 'bi-person-plus',
            'icon_color' => 'success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
        ],
        [
            'id' => 2,
            'type' => 'project_updated',
            'description' => 'Projeto atualizado',
            'subject' => 'Casa Residencial',
            'icon' => 'bi-pencil',
            'icon_color' => 'info',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours'))
        ],
        [
            'id' => 3,
            'type' => 'document_uploaded',
            'description' => 'Documento enviado',
            'subject' => 'Planta baixa.pdf',
            'icon' => 'bi-file-earmark',
            'icon_color' => 'primary',
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
        ],
        [
            'id' => 4,
            'type' => 'status_changed',
            'description' => 'Status alterado',
            'subject' => 'Projeto concluído',
            'icon' => 'bi-check-circle',
            'icon_color' => 'success',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 5,
            'type' => 'analyst_assigned',
            'description' => 'Analista atribuído',
            'subject' => 'Reforma Comercial Santos',
            'icon' => 'bi-person-check',
            'icon_color' => 'info',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ]
    ];
}

// Set values needed for header
$title = 'Atividades Recentes - Sistema de Arquitetura';
$showSidebar = true;
$user_type = 'admin'; // ensure admin menu is shown
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="bi bi-clock-history"></i>
                Histórico de Atividades
            </h1>
            <p class="text-muted">Registro de todas as atividades do sistema</p>
        </div>
        <div>
            <a href="/admin" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Voltar ao Admin
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="refreshActivityList()">
                <i class="bi bi-arrow-clockwise"></i>
                Atualizar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo:</label>
                    <select class="form-select" id="filterType">
                        <option value="">Todos os tipos</option>
                        <option value="user">Usuários</option>
                        <option value="project">Projetos</option>
                        <option value="document">Documentos</option>
                        <option value="status">Status</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Período:</label>
                    <select class="form-select" id="filterPeriod">
                        <option value="">Todo o histórico</option>
                        <option value="today">Hoje</option>
                        <option value="week">Última semana</option>
                        <option value="month">Último mês</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar:</label>
                    <input type="text" class="form-control" id="searchActivities" placeholder="Buscar atividades...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="filterActivities()">
                        <i class="bi bi-search"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Atividades -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-check"></i>
                Atividades Registradas
            </h5>
            <span class="badge bg-primary"><?= count($activities) ?> atividades</span>
        </div>
        <div class="card-body">
            <?php if (!empty($activities)): ?>
                <div class="list-group list-group-flush" id="activityList">
                    <?php foreach ($activities as $activity): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="me-auto">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light p-2 rounded-circle me-3">
                                        <i class="bi <?= $activity['icon'] ?> text-<?= $activity['icon_color'] ?>" style="font-size: 1.2rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($activity['description']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($activity['subject']) ?></small>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted ms-3">
                                <?= formatTimeAgo($activity['created_at']) ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginação -->
                <nav aria-label="Navegação de página" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Próximo</a>
                        </li>
                    </ul>
                </nav>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma atividade encontrada</h5>
                    <p class="text-muted">Não há registros de atividades no sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function filterActivities() {
    const filterType = document.getElementById('filterType').value;
    const filterPeriod = document.getElementById('filterPeriod').value;
    const searchTerm = document.getElementById('searchActivities').value.toLowerCase();
    
    const activities = document.querySelectorAll('#activityList .list-group-item');
    
    activities.forEach(activity => {
        const text = activity.textContent.toLowerCase();
        
        let show = true;
        
        // Simulação de filtros - em um sistema real, isso seria feito no servidor
        if (filterType) {
            if (filterType === 'user' && !text.includes('usuário')) show = false;
            if (filterType === 'project' && !text.includes('projeto')) show = false;
            if (filterType === 'document' && !text.includes('documento')) show = false;
            if (filterType === 'status' && !text.includes('status')) show = false;
        }
        
        if (searchTerm && !text.includes(searchTerm)) show = false;
        
        activity.style.display = show ? '' : 'none';
    });
}

function refreshActivityList() {
    // Adicionar efeito de loading
    const activityList = document.getElementById('activityList');
    if (activityList) {
        const originalContent = activityList.innerHTML;
        activityList.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-3">Atualizando atividades...</p>
            </div>
        `;
        
        // Simulação de atualização - em um sistema real, isso seria uma chamada AJAX
        setTimeout(() => {
            activityList.innerHTML = originalContent;
            showToast('Atividades atualizadas com sucesso!', 'success');
        }, 1500);
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    `;
    
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        container.appendChild(toast);
    } else {
        toastContainer.appendChild(toast);
    }
    
    const bsToast = new bootstrap.Toast(toast, {
        delay: 3000
    });
    
    bsToast.show();
}

// Event listeners
document.getElementById('searchActivities').addEventListener('input', filterActivities);
document.getElementById('filterType').addEventListener('change', filterActivities);
document.getElementById('filterPeriod').addEventListener('change', filterActivities);
</script>

<style>
/* Estilo para o ícone das atividades */
.list-group-item .bg-light {
    transition: all 0.3s ease;
}

.list-group-item:hover .bg-light {
    transform: scale(1.1);
}

/* Animação para o refresh */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?php 
// The formatTimeAgo function is now defined at the top of the file
?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
