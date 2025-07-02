@echo off
REM ===============================================
REM Preparar arquivos para Hospedagem Compartilhada
REM ===============================================

echo ===============================================
echo Preparando deploy para Hospedagem Compartilhada
echo ===============================================

set DEPLOY_DIR=deploy-hospedagem
set ZIP_FILE=sistema-arquitetura-hospedagem.zip

REM Limpar deploy anterior
if exist "%DEPLOY_DIR%" rmdir /s /q "%DEPLOY_DIR%"
if exist "%ZIP_FILE%" del "%ZIP_FILE%"

echo.
echo 1. Criando estrutura de deploy...
mkdir "%DEPLOY_DIR%"
mkdir "%DEPLOY_DIR%\storage"
mkdir "%DEPLOY_DIR%\storage\documents"
mkdir "%DEPLOY_DIR%\storage\temp"
mkdir "%DEPLOY_DIR%\logs"

echo.
echo 2. Copiando arquivos essenciais...
xcopy "public" "%DEPLOY_DIR%\public\" /E /I /Y
xcopy "src" "%DEPLOY_DIR%\src\" /E /I /Y
xcopy "config" "%DEPLOY_DIR%\config\" /E /I /Y
xcopy "vendor" "%DEPLOY_DIR%\vendor\" /E /I /Y
copy "composer.json" "%DEPLOY_DIR%\"

echo.
echo 3. Criando .env de exemplo para hospedagem...
(
echo # Configuração para Hospedagem Compartilhada
echo # Renomeie para .env e configure com dados reais
echo.
echo APP_ENV=production
echo APP_DEBUG=false
echo APP_URL=https://seudominio.com.br/sistema
echo.
echo # Banco de dados ^(fornecido pela hospedagem^)
echo DB_HOST=localhost
echo DB_NAME=seu_usuario_dbname
echo DB_USER=seu_usuario_db
echo DB_PASS=senha_fornecida_host
echo DB_CHARSET=utf8mb4
echo.
echo # Email ^(configure com dados reais^)
echo MAIL_HOST=smtp.seudominio.com.br
echo MAIL_PORT=587
echo MAIL_USERNAME=noreply@seudominio.com.br
echo MAIL_PASSWORD=sua_senha_email
echo MAIL_ENCRYPTION=tls
echo MAIL_FROM_NAME="Sistema de Arquitetura"
echo MAIL_FROM_ADDRESS=noreply@seudominio.com.br
echo.
echo # Segurança
echo HASH_COST=12
echo SESSION_LIFETIME=7200
echo CSRF_TOKEN_EXPIRY=3600
echo SESSION_SECURE=true
echo.
echo # Armazenamento ^(ajustado para hospedagem compartilhada^)
echo STORAGE_DOCUMENTS_PATH=storage/documents
echo STORAGE_TEMP_PATH=storage/temp
echo.
echo # Logs
echo ERROR_LOG_PATH=logs/error.log
echo ACCESS_LOG_PATH=logs/access.log
echo.
echo # Cache
echo CACHE_ENABLED=true
echo CACHE_TTL=3600
) > "%DEPLOY_DIR%\.env.hospedagem"

echo.
echo 4. Criando .htaccess otimizado...
(
echo # Sistema de Arquitetura - Hospedagem Compartilhada
echo.
echo RewriteEngine On
echo.
echo # Force HTTPS ^(se disponível^)
echo RewriteCond %%{HTTPS} off
echo RewriteRule ^^(.*)$ https://%%{HTTP_HOST}%%{REQUEST_URI} [L,R=301]
echo.
echo # Handle Angular and Vue.js HTML5 mode
echo RewriteCond %%{REQUEST_FILENAME} !-f
echo RewriteCond %%{REQUEST_FILENAME} !-d
echo RewriteRule ^^(.*)$ index.php [QSA,L]
echo.
echo # Security headers
echo ^<IfModule mod_headers.c^>
echo     Header always set X-Frame-Options DENY
echo     Header always set X-Content-Type-Options nosniff
echo     Header always set X-XSS-Protection "1; mode=block"
echo     Header always set Referrer-Policy "strict-origin-when-cross-origin"
echo ^</IfModule^>
echo.
echo # Prevent access to sensitive files
echo ^<Files ~ "^^\..*"^>
echo     Order allow,deny
echo     Deny from all
echo ^</Files^>
echo.
echo ^<FilesMatch "\.(env|ini|conf|sql|log|md)$"^>
echo     Order allow,deny
echo     Deny from all
echo ^</FilesMatch^>
echo.
echo # Gzip compression
echo ^<IfModule mod_deflate.c^>
echo     AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
echo ^</IfModule^>
echo.
echo # Browser caching
echo ^<IfModule mod_expires.c^>
echo     ExpiresActive On
echo     ExpiresByType text/css "access plus 1 year"
echo     ExpiresByType application/javascript "access plus 1 year"
echo     ExpiresByType image/png "access plus 1 year"
echo     ExpiresByType image/jpg "access plus 1 year"
echo     ExpiresByType image/jpeg "access plus 1 year"
echo     ExpiresByType image/gif "access plus 1 year"
echo ^</IfModule^>
) > "%DEPLOY_DIR%\public\.htaccess"

echo.
echo 5. Criando instruções de instalação...
(
echo # INSTRUÇÕES DE INSTALAÇÃO NA HOSPEDAGEM
echo.
echo ## Passo 1: Upload dos arquivos
echo 1. Faça upload de todos os arquivos para public_html/sistema/ ^(ou sua pasta desejada^)
echo 2. Extraia se necessário
echo.
echo ## Passo 2: Configurar banco de dados
echo 1. No cPanel, crie um banco MySQL
echo 2. Anote: nome do banco, usuário, senha, host
echo.
echo ## Passo 3: Configurar ambiente
echo 1. Renomeie .env.hospedagem para .env
echo 2. Edite o .env com os dados reais do banco
echo 3. Configure email se necessário
echo.
echo ## Passo 4: Configurar permissões
echo 1. Pastas: chmod 755
echo 2. Arquivos: chmod 644
echo 3. storage/ e logs/: chmod 775
echo.
echo ## Passo 5: Configurar domínio
echo Opção A: Subpasta - https://seudominio.com/sistema/public
echo Opção B: Subdomínio - sistema.seudominio.com ^(apontar para /public^)
echo.
echo ## Passo 6: Testar
echo 1. Acesse a URL configurada
echo 2. Teste login/cadastro
echo 3. Teste upload de arquivos
echo.
echo ## Em caso de problemas:
echo - Verifique logs em logs/error.log
echo - Verifique se mod_rewrite está ativo
echo - Verifique permissões das pastas
) > "%DEPLOY_DIR%\INSTALACAO-HOSPEDAGEM.txt"

echo.
echo 6. Compactando arquivos...
powershell "Compress-Archive -Path '%DEPLOY_DIR%\*' -DestinationPath '%ZIP_FILE%' -Force"

echo.
echo 7. Limpando arquivos temporários...
rmdir /s /q "%DEPLOY_DIR%"

echo.
echo ===============================================
echo Deploy para hospedagem preparado!
echo ===============================================
echo Arquivo criado: %ZIP_FILE%
echo.
echo PRÓXIMOS PASSOS:
echo 1. Faça upload do arquivo ZIP para sua hospedagem
echo 2. Extraia na pasta pública (public_html)
echo 3. Siga as instruções em INSTALACAO-HOSPEDAGEM.txt
echo 4. Configure o arquivo .env com dados reais
echo ===============================================

pause
