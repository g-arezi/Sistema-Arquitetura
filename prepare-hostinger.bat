@echo off
REM Script de preparação para deployment na Hostinger
REM Este script cria um pacote otimizado para upload na Hostinger

echo ===== Preparação para Deployment na Hostinger =====
echo.

set DEPLOY_DIR=deployment-hostinger
set CONFIG_FILE=config\hostinger.php

REM Verificar se o PHP está instalado
php -v > nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: PHP não encontrado. Certifique-se de que o PHP está no PATH.
    goto :end
)

REM Verificar se o Composer está instalado
composer -V > nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: Composer não encontrado. Certifique-se de que o Composer está no PATH.
    goto :end
)

REM Criar diretório de deployment
echo Criando diretório de deployment...
if exist %DEPLOY_DIR% (
    echo Limpando diretório existente...
    rmdir /S /Q %DEPLOY_DIR%
)
mkdir %DEPLOY_DIR%

REM Criar arquivo de configuração para Hostinger
echo Criando arquivo de configuração para Hostinger...
if not exist config\hostinger.php (
    echo ^<?php > %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo /**>> %CONFIG_FILE%
    echo  * Configurações específicas para Hostinger>> %CONFIG_FILE%
    echo  */>> %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo return [>> %CONFIG_FILE%
    echo     // Configurações de banco de dados>> %CONFIG_FILE%
    echo     'database' => [>> %CONFIG_FILE%
    echo         'host' => 'localhost',>> %CONFIG_FILE%
    echo         'name' => 'u367501017_sistema_arq', // Banco de dados na Hostinger>> %CONFIG_FILE%
    echo         'user' => 'u367501017_oArezi',      // Usuário na Hostinger>> %CONFIG_FILE%
    echo         'pass' => '2Itdigital@sA',          // Senha na Hostinger>> %CONFIG_FILE%
    echo         'charset' => 'utf8mb4',>> %CONFIG_FILE%
    echo     ],>> %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo     // Configurações da aplicação>> %CONFIG_FILE%
    echo     'app' => [>> %CONFIG_FILE%
    echo         'debug' => false,>> %CONFIG_FILE%
    echo         'environment' => 'production',>> %CONFIG_FILE%
    echo         'url' => 'https://purple-wallaby-649054.hostingersite.com', // Domínio na Hostinger>> %CONFIG_FILE%
    echo         'timezone' => 'America/Sao_Paulo',>> %CONFIG_FILE%
    echo     ],>> %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo     // Email - Configuração para Hostinger>> %CONFIG_FILE%
    echo     'mail' => [>> %CONFIG_FILE%
    echo         'host' => 'smtp.hostinger.com',>> %CONFIG_FILE%
    echo         'port' => 587,>> %CONFIG_FILE%
    echo         'username' => 'contato@seudominio.com', // Altere para seu email>> %CONFIG_FILE%
    echo         'password' => 'SuaSenhaEmail',         // Altere para senha do email>> %CONFIG_FILE%
    echo         'encryption' => 'tls',>> %CONFIG_FILE%
    echo         'from_name' => 'Sistema de Arquitetura',>> %CONFIG_FILE%
    echo         'from_address' => 'contato@seudominio.com', // Altere para seu email>> %CONFIG_FILE%
    echo     ],>> %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo     // Armazenamento - Caminhos para Hostinger>> %CONFIG_FILE%
    echo     'storage' => [>> %CONFIG_FILE%
    echo         'documents_path' => '/home/u367501017/domains/purple-wallaby-649054.hostingersite.com/public_html/storage/documents',>> %CONFIG_FILE%
    echo         'temp_path' => '/home/u367501017/domains/purple-wallaby-649054.hostingersite.com/public_html/storage/temp',>> %CONFIG_FILE%
    echo     ],>> %CONFIG_FILE%
    echo. >> %CONFIG_FILE%
    echo     // Limites - Ajustados para Hostinger>> %CONFIG_FILE%
    echo     'limits' => [>> %CONFIG_FILE%
    echo         'max_upload_size' => 8388608, // 8MB em bytes (limite típico na Hostinger em planos básicos)>> %CONFIG_FILE%
    echo         'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg'],>> %CONFIG_FILE%
    echo     ],>> %CONFIG_FILE%
    echo ];>> %CONFIG_FILE%
    echo.>> %CONFIG_FILE%
    
    echo Arquivo de configuração criado: %CONFIG_FILE%
    echo IMPORTANTE: Edite este arquivo com suas configurações específicas da Hostinger.
) else (
    echo Arquivo de configuração para Hostinger já existe. Usando arquivo existente.
)

REM Criar arquivo .htaccess especial para Hostinger
echo Criando arquivo .htaccess otimizado para Hostinger...
set HTACCESS_FILE=%DEPLOY_DIR%\public\.htaccess

mkdir %DEPLOY_DIR%\public

echo # Otimizado para Hostinger> %HTACCESS_FILE%
echo RewriteEngine On>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Forçar HTTPS>> %HTACCESS_FILE%
echo RewriteCond %%{HTTPS} off>> %HTACCESS_FILE%
echo RewriteRule ^(.*)$ https://%%{HTTP_HOST}%%{REQUEST_URI} [L,R=301]>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Handle client-side routing>> %HTACCESS_FILE%
echo RewriteCond %%{REQUEST_FILENAME} !-f>> %HTACCESS_FILE%
echo RewriteCond %%{REQUEST_FILENAME} !-d>> %HTACCESS_FILE%
echo RewriteCond %%{REQUEST_URI} !^/index\.php>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Send all requests to index.php>> %HTACCESS_FILE%
echo RewriteRule ^(.*)$ /index.php [QSA,L]>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Segurança reforçada>> %HTACCESS_FILE%
echo ^<IfModule mod_headers.c^>>> %HTACCESS_FILE%
echo     Header always set X-Content-Type-Options nosniff>> %HTACCESS_FILE%
echo     Header always set X-Frame-Options DENY>> %HTACCESS_FILE%
echo     Header always set X-XSS-Protection "1; mode=block">> %HTACCESS_FILE%
echo     Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains">> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Otimização de cache>> %HTACCESS_FILE%
echo ^<IfModule mod_expires.c^>>> %HTACCESS_FILE%
echo     ExpiresActive On>> %HTACCESS_FILE%
echo     ExpiresByType image/jpg "access plus 1 year">> %HTACCESS_FILE%
echo     ExpiresByType image/jpeg "access plus 1 year">> %HTACCESS_FILE%
echo     ExpiresByType image/gif "access plus 1 year">> %HTACCESS_FILE%
echo     ExpiresByType image/png "access plus 1 year">> %HTACCESS_FILE%
echo     ExpiresByType image/svg+xml "access plus 1 year">> %HTACCESS_FILE%
echo     ExpiresByType text/css "access plus 1 month">> %HTACCESS_FILE%
echo     ExpiresByType text/javascript "access plus 1 month">> %HTACCESS_FILE%
echo     ExpiresByType application/javascript "access plus 1 month">> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Compressão Gzip>> %HTACCESS_FILE%
echo ^<IfModule mod_deflate.c^>>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE text/plain>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE text/html>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE text/xml>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE text/css>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE text/javascript>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/xml>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/xhtml+xml>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/rss+xml>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/javascript>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/x-javascript>> %HTACCESS_FILE%
echo     AddOutputFilterByType DEFLATE application/json>> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Prevenir acesso a arquivos sensíveis>> %HTACCESS_FILE%
echo ^<FilesMatch "^\.env|composer\.json|composer\.lock"^>>> %HTACCESS_FILE%
echo     Order allow,deny>> %HTACCESS_FILE%
echo     Deny from all>> %HTACCESS_FILE%
echo ^</FilesMatch^>>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # PHP configurações>> %HTACCESS_FILE%
echo ^<IfModule mod_php8.c^>>> %HTACCESS_FILE%
echo     php_value upload_max_filesize 8M>> %HTACCESS_FILE%
echo     php_value post_max_size 8M>> %HTACCESS_FILE%
echo     php_value memory_limit 128M>> %HTACCESS_FILE%
echo     php_value max_execution_time 60>> %HTACCESS_FILE%
echo     php_flag display_errors off>> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%

echo Arquivo .htaccess criado com configurações otimizadas para Hostinger.

REM Instalar dependências do Composer (otimizadas para produção)
echo Instalando dependências do Composer para produção...
composer install --no-dev --optimize-autoloader

REM Copiar arquivos para o diretório de deployment
echo Copiando arquivos para o diretório de deployment...
xcopy "public\*" "%DEPLOY_DIR%\public\*" /E /I /Y
xcopy "src\*" "%DEPLOY_DIR%\src\*" /E /I /Y
xcopy "config\*" "%DEPLOY_DIR%\config\*" /E /I /Y
xcopy "vendor\*" "%DEPLOY_DIR%\vendor\*" /E /I /Y
mkdir "%DEPLOY_DIR%\storage"
mkdir "%DEPLOY_DIR%\storage\documents"
mkdir "%DEPLOY_DIR%\storage\temp"

copy "composer.json" "%DEPLOY_DIR%\"
copy "composer.lock" "%DEPLOY_DIR%\"
copy "TUTORIAL-HOSTINGER.md" "%DEPLOY_DIR%\"

REM Criar arquivo index.php com configuração para Hostinger
echo Ajustando arquivo index.php para ambiente Hostinger...
set INDEX_FILE=%DEPLOY_DIR%\public\index.php
copy "public\index.php" "%INDEX_FILE%"

echo.
echo Preparação concluída! Os arquivos estão prontos para upload na Hostinger.
echo.
echo Pasta de deployment: %DEPLOY_DIR%
echo.
echo PRÓXIMOS PASSOS:
echo 1. Edite o arquivo config\hostinger.php com suas configurações específicas
echo 2. Faça upload de todo o conteúdo da pasta %DEPLOY_DIR% para sua hospedagem
echo 3. Siga as instruções no arquivo TUTORIAL-HOSTINGER.md
echo.

:end
pause
