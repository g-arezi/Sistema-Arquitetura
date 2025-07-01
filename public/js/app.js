// Sistema de Arquitetura - JavaScript Functions

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
    initFileUpload();
    
    // Form validations
    initFormValidations();
    
    // Mobile sidebar toggle
    initMobileSidebar();
});

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
