// Sistema de Arquitetura - JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Apply dark theme classes globally
    applyDarkTheme();
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        });
    }, 5000);

    // File upload enhancements
    initFileUpload();
    
    // Form validations
    initFormValidations();
    
    // Mobile sidebar toggle
    initMobileSidebar();
    
    // Initialize dark theme components
    initDarkThemeComponents();
});

// Apply Dark Theme
function applyDarkTheme() {
    // Apply dark theme to body if not already applied
    if (!document.body.classList.contains('bg-dark')) {
        document.body.classList.add('bg-dark', 'text-light');
    }
    
    // Apply dark theme to existing cards
    const cards = document.querySelectorAll('.card:not(.bg-dark)');
    cards.forEach(card => {
        card.classList.add('bg-dark', 'border-secondary');
        card.classList.remove('bg-white', 'border-light');
        
        // Update card headers and bodies
        const headers = card.querySelectorAll('.card-header');
        headers.forEach(header => {
            header.classList.add('bg-dark', 'border-secondary', 'text-light');
        });
        
        const bodies = card.querySelectorAll('.card-body');  
        bodies.forEach(body => {
            body.classList.add('bg-dark', 'text-light');
        });
        
        const footers = card.querySelectorAll('.card-footer');
        footers.forEach(footer => {
            footer.classList.add('bg-dark', 'border-secondary', 'text-light');
        });
    });
    
    // Apply dark theme to tables
    const tables = document.querySelectorAll('.table:not(.table-dark)');
    tables.forEach(table => {
        table.classList.add('table-dark');
    });
    
    // Apply dark theme to modals
    const modals = document.querySelectorAll('.modal-content:not(.bg-dark)');
    modals.forEach(modal => {
        modal.classList.add('bg-dark', 'text-light', 'border-secondary');
        
        const headers = modal.querySelectorAll('.modal-header');
        headers.forEach(header => {
            header.classList.add('bg-dark', 'border-secondary', 'text-light');
        });
        
        const bodies = modal.querySelectorAll('.modal-body');
        bodies.forEach(body => {
            body.classList.add('bg-dark', 'text-light');
        });
        
        const footers = modal.querySelectorAll('.modal-footer');
        footers.forEach(footer => {
            footer.classList.add('bg-dark', 'border-secondary', 'text-light');
        });
    });
    
    // Apply dark theme to list groups
    const listGroups = document.querySelectorAll('.list-group-item:not(.bg-dark)');
    listGroups.forEach(item => {
        item.classList.add('bg-dark', 'border-secondary', 'text-light');
    });
    
    // Apply dark theme to dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-menu:not(.bg-dark)');
    dropdowns.forEach(dropdown => {
        dropdown.classList.add('bg-dark', 'border-secondary');
        
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(item => {
            item.classList.add('text-light');
        });
    });
    
    // Update text colors
    const textElements = document.querySelectorAll('.text-dark:not(.text-light)');
    textElements.forEach(element => {
        element.classList.remove('text-dark');
        element.classList.add('text-light');
    });
}

// Initialize Dark Theme Components
function initDarkThemeComponents() {
    // Add hover effects to clickable cards
    const clickableCards = document.querySelectorAll('.card[onclick], .card[style*="cursor"]');
    clickableCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
        });
    });
    
    // Enhance form controls appearance
    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.style.borderColor = '#0d6efd';
            this.style.boxShadow = '0 0 0 0.2rem rgba(13,110,253,0.25)';
        });
        
        control.addEventListener('blur', function() {
            this.style.borderColor = '#404040';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add loading spinner to buttons when clicked
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-clockwise spinner me-2"></i>' + 'Carregando...';
                this.disabled = true;
                
                // Re-enable after form submission (fallback)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 5000);
            }
        });
    });
}

// File Upload Functionality
function initFileUpload() {
    const uploadAreas = document.querySelectorAll('.upload-area');
    
    uploadAreas.forEach(function(area) {
        const fileInput = area.querySelector('input[type="file"]');
        
        if (!fileInput) return;
        
        // Click to upload
        area.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Drag and drop
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            area.classList.add('dragover');
        });
        
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            area.classList.remove('dragover');
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            area.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        });
        
        // File selection
        fileInput.addEventListener('change', function() {
            handleFileSelect(this);
        });
    });
}

function handleFileSelect(input) {
    const files = input.files;
    const uploadArea = input.closest('.upload-area');
    const fileInfo = uploadArea.querySelector('.file-info');
    
    if (files.length > 0) {
        const file = files[0];
        const fileName = file.name;
        const fileSize = formatFileSize(file.size);
        
        if (fileInfo) {
            fileInfo.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark text-primary me-2"></i>
                    <div>
                        <div class="fw-bold">${fileName}</div>
                        <small class="text-muted">${fileSize}</small>
                    </div>
                </div>
            `;
        }
        
        // Validate file
        if (!validateFile(file)) {
            showAlert('Tipo de arquivo não permitido ou muito grande.', 'danger');
            input.value = '';
            if (fileInfo) fileInfo.innerHTML = '';
        }
    }
}

function validateFile(file) {
    const allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain'
    ];
    
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    return allowedTypes.includes(file.type) && file.size <= maxSize;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form Validations
function initFormValidations() {
    // Password confirmation
    const passwordConfirm = document.getElementById('confirm_password');
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Senhas não coincidem');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    }
    
    // Form submission with loading
    const forms = document.querySelectorAll('form[data-loading]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
            }
        });
    });
}

// Mobile Sidebar
function initMobileSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
}

// Utility Functions
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer') || document.body;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        const closeBtn = alertDiv.querySelector('.btn-close');
        if (closeBtn) closeBtn.click();
    }, 5000);
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// AJAX Helper
function makeRequest(url, options = {}) {
    const defaults = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = Object.assign(defaults, options);
    
    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showAlert('Erro na requisição. Tente novamente.', 'danger');
        });
}

// Project status update
function updateProjectStatus(projectId, status) {
    const formData = new FormData();
    formData.append('status', status);
    
    showAlert('Atualizando status do projeto...', 'info');
    
    makeRequest(`/projects/${projectId}/status`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(data => {
        if (data.success) {
            showAlert('Status do projeto atualizado com sucesso!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert('Erro ao atualizar status do projeto.', 'danger');
        }
    });
}

// Admin functions
function navigateToUsers() {
    window.location.href = '/admin/users';
}

function navigateToProjects() {
    window.location.href = '/admin/projects';
}

function showDocumentsInfo() {
    const modal = new bootstrap.Modal(document.getElementById('documentsInfoModal') || createDocumentsInfoModal());
    modal.show();
}

function createDocumentsInfoModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'documentsInfoModal';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informações de Documentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center mb-3">
                        <i class="bi bi-file-earmark-text display-1 text-info"></i>
                    </div>
                    <h4 class="text-center">Estatísticas de Documentos</h4>
                    <div class="list-group mt-3">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Total de Documentos:</span>
                            <span class="badge bg-primary">${document.getElementById('documentCount')?.textContent || '0'}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Documentos por Projeto:</span>
                            <span class="badge bg-success">Média: 4</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Tipos mais comuns:</span>
                            <span>PDF, JPG, DWG</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

function showPendingProjects() {
    window.location.href = '/admin/projects?status=pending';
}

function openQuickCreateModal() {
    const modal = new bootstrap.Modal(document.getElementById('quickCreateModal') || createQuickCreateModal());
    modal.show();
}

function createQuickCreateModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'quickCreateModal';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Criação Rápida de Projeto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/projects" method="POST" data-loading>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="client_id" class="form-label">Cliente</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Selecione...</option>
                                <option value="3">Cliente Teste</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="analyst_id" class="form-label">Analista</label>
                            <select class="form-select" id="analyst_id" name="analyst_id">
                                <option value="">Selecione...</option>
                                <option value="2">Analista Sistema</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Prazo</label>
                            <input type="date" class="form-control" id="deadline" name="deadline">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i>
                            Criar Projeto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

function refreshActivities() {
    const activitiesList = document.getElementById('activitiesList');
    if (activitiesList) {
        activitiesList.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Atualizando atividades...</p>
            </div>
        `;
        
        setTimeout(() => {
            activitiesList.innerHTML = `
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <i class="bi bi-person-plus text-success"></i>
                            Novo usuário cadastrado
                        </div>
                        <small class="text-muted">Cliente Exemplo</small>
                    </div>
                    <small class="text-muted">Agora</small>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <i class="bi bi-pencil text-info"></i>
                            Projeto atualizado
                        </div>
                        <small class="text-muted">Casa Residencial</small>
                    </div>
                    <small class="text-muted">2min atrás</small>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <i class="bi bi-file-earmark text-primary"></i>
                            Documento enviado
                        </div>
                        <small class="text-muted">Planta baixa.pdf</small>
                    </div>
                    <small class="text-muted">5min atrás</small>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <i class="bi bi-check-circle text-success"></i>
                            Status alterado
                        </div>
                        <small class="text-muted">Projeto concluído</small>
                    </div>
                    <small class="text-muted">10min atrás</small>
                </div>
            `;
            
            showAlert('Atividades atualizadas com sucesso!', 'success');
        }, 1500);
    }
}

// Funções para gerenciamento de projetos
function assignAnalyst(projectId) {
    const modal = new bootstrap.Modal(document.getElementById('assignAnalystModal') || createAssignAnalystModal(projectId));
    modal.show();
}

function createAssignAnalystModal(projectId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'assignAnalystModal';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atribuir Analista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/projects/assign-analyst" method="POST" data-loading>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="${projectId}">
                        <div class="mb-3">
                            <label for="analyst_id" class="form-label">Selecione o Analista</label>
                            <select class="form-select" id="analyst_id" name="analyst_id" required>
                                <option value="">Selecione...</option>
                                <option value="2">Analista Sistema</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-check"></i>
                            Atribuir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

function changeProjectStatus(projectId) {
    const modal = new bootstrap.Modal(document.getElementById('changeStatusModal') || createChangeStatusModal(projectId));
    modal.show();
}

function createChangeStatusModal(projectId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'changeStatusModal';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Status do Projeto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="/admin/projects/change-status" method="POST" data-loading>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" value="${projectId}">
                        <div class="mb-3">
                            <label for="status" class="form-label">Selecione o Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Selecione...</option>
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluído</option>
                                <option value="cancelled">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise"></i>
                            Atualizar Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

function confirmDeleteProject(projectId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteProjectModal') || createDeleteProjectModal(projectId));
    modal.show();
}

function createDeleteProjectModal(projectId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'deleteProjectModal';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i>
                        Confirmar Exclusão
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                    </div>
                    <p>Tem certeza que deseja excluir este projeto e todos os seus documentos?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="/admin/projects/${projectId}/delete" method="POST" style="display:inline">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                            Excluir Permanentemente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

// Melhoria na funcionalidade de upload de arquivos
function enhanceFileUpload() {
    const uploadAreas = document.querySelectorAll('.upload-area');
    if (uploadAreas.length === 0) return;
    
    uploadAreas.forEach(area => {
        area.addEventListener('click', function() {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput) fileInput.click();
        });
    });
    
    document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length === 0) {
                e.preventDefault();
                showAlert('Por favor, selecione um arquivo para upload.', 'warning');
            } else {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
                }
            }
        });
    });
}

// Inicializar melhorias no carregamento do DOM
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        });
    }, 5000);

    // File upload enhancements
    enhanceFileUpload();
    
    // Form validations
    initFormValidations();
    
    // Mobile sidebar toggle
    initMobileSidebar();
});

// Funções utilitárias adicionais

// Exibe um alerta flutuante temporário
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
        delay: 5000,
        animation: true
    });
    
    bsToast.show();
    
    // Remover do DOM após esconder
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Melhoria da função de confirmação para usar modal em vez de confirm nativo
function confirmActionModal(title, message, callback, type = 'warning') {
    const modalId = 'confirmActionModal';
    let modal = document.getElementById(modalId);
    
    // Se já existir um modal, removê-lo
    if (modal) {
        document.body.removeChild(modal);
    }
    
    // Criar novo modal
    modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'modal fade';
    modal.tabIndex = -1;
    
    const iconClass = {
        'warning': 'bi-exclamation-triangle text-warning',
        'danger': 'bi-exclamation-circle text-danger',
        'info': 'bi-info-circle text-info',
        'success': 'bi-check-circle text-success'
    }[type] || 'bi-question-circle text-info';
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="${iconClass} me-2"></i>
                        ${title}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-${type}" id="confirmActionBtn">Confirmar</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
    
    // Configurar o botão de confirmação
    document.getElementById('confirmActionBtn').addEventListener('click', function() {
        modalInstance.hide();
        callback();
    });
    
    // Remover o modal quando fechado
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

// Salvar formulário via AJAX
function submitFormAjax(formElement, successCallback, errorCallback) {
    const form = formElement instanceof HTMLFormElement ? formElement : document.querySelector(formElement);
    
    if (!form) {
        console.error('Formulário não encontrado');
        return;
    }
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        }
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            
            if (data.success) {
                if (typeof successCallback === 'function') {
                    successCallback(data);
                } else {
                    showToast(data.message || 'Operação realizada com sucesso!', 'success');
                }
            } else {
                if (typeof errorCallback === 'function') {
                    errorCallback(data);
                } else {
                    showToast(data.message || 'Erro ao processar solicitação.', 'danger');
                }
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            
            if (typeof errorCallback === 'function') {
                errorCallback({ success: false, message: 'Erro na comunicação com o servidor.' });
            } else {
                showToast('Erro na comunicação com o servidor.', 'danger');
            }
        });
    });
}
