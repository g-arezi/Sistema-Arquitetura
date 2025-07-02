    <?php if (isset($showSidebar) && $showSidebar): ?>
            </main>
        </div>
    </div>
    <?php else: ?>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.querySelector('.btn-close')) {
                        alert.querySelector('.btn-close').click();
                    }
                });
            }, 5000);
            
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Inicializar popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Adicionar classe aos forms para loading
            document.querySelectorAll('form[data-loading]').forEach(form => {
                form.addEventListener('submit', function() {
                    const button = this.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        const originalText = button.innerHTML;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
                        
                        // Restaurar estado original se o form não submeter após 10s (timeout)
                        setTimeout(() => {
                            if (button.disabled) {
                                button.disabled = false;
                                button.innerHTML = originalText;
                            }
                        }, 10000);
                    }
                });
            });

            // Animar elementos com fade-in
            document.querySelectorAll('.fade-in').forEach(el => {
                el.classList.add('animate');
            });

            // Inicialização do upload de arquivos
            enhanceFileUpload();
        });
        
        // Melhoria na funcionalidade de upload de arquivos
        function enhanceFileUpload() {
            const uploadAreas = document.querySelectorAll('.upload-area');
            if (uploadAreas.length === 0) return;
            
            uploadAreas.forEach(area => {
                area.addEventListener('click', function() {
                    const fileInput = this.querySelector('input[type="file"]');
                    if (fileInput) fileInput.click();
                });
                
                // Drag and drop
                area.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('border-primary');
                });
                
                area.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-primary');
                });
                
                area.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('border-primary');
                    
                    const fileInput = this.querySelector('input[type="file"]');
                    if (fileInput && e.dataTransfer.files.length > 0) {
                        fileInput.files = e.dataTransfer.files;
                        
                        // Trigger change event
                        const event = new Event('change', { bubbles: true });
                        fileInput.dispatchEvent(event);
                    }
                });
            });
            
            // File input change handler
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    const fileInfo = this.closest('.upload-area')?.querySelector('.file-info');
                    if (fileInfo && this.files.length > 0) {
                        const file = this.files[0];
                        const fileSize = formatFileSize(file.size);
                        
                        fileInfo.innerHTML = `
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark text-primary me-2"></i>
                                    <div>
                                        <div class="fw-bold">${file.name}</div>
                                        <small class="text-muted">${fileSize}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
            });
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</body>
</html>
