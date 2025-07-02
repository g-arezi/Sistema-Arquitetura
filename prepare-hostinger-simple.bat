@echo off
REM Script simplificado para preparação da Hostinger
REM Este script cria um pacote otimizado para upload na Hostinger

echo ===== Preparação para Deployment na Hostinger =====
echo.

set DEPLOY_DIR=deployment-hostinger

REM Criar diretório de deployment
echo Criando diretório de deployment...
if exist %DEPLOY_DIR% (
    echo Limpando diretório existente...
    rmdir /S /Q %DEPLOY_DIR%
)
mkdir %DEPLOY_DIR%

REM Criar estrutura de diretórios
mkdir %DEPLOY_DIR%\public
mkdir %DEPLOY_DIR%\src
mkdir %DEPLOY_DIR%\config
mkdir %DEPLOY_DIR%\vendor
mkdir %DEPLOY_DIR%\storage
mkdir %DEPLOY_DIR%\storage\documents
mkdir %DEPLOY_DIR%\storage\temp

REM Copiar arquivos para o diretório de deployment
echo Copiando arquivos para o diretório de deployment...
xcopy "public\*" "%DEPLOY_DIR%\public\*" /E /I /Y
xcopy "src\*" "%DEPLOY_DIR%\src\*" /E /I /Y
xcopy "config\*" "%DEPLOY_DIR%\config\*" /E /I /Y
xcopy "vendor\*" "%DEPLOY_DIR%\vendor\*" /E /I /Y

copy "composer.json" "%DEPLOY_DIR%\"
copy "composer.lock" "%DEPLOY_DIR%\"
copy "TUTORIAL-HOSTINGER.md" "%DEPLOY_DIR%\"
copy "GUIA-RAPIDO-DEPLOY.md" "%DEPLOY_DIR%\"

REM Criar arquivo .htaccess especial para Hostinger
echo Criando arquivo .htaccess otimizado para Hostinger...
set HTACCESS_FILE=%DEPLOY_DIR%\public\.htaccess

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

REM Copiar arquivo de configuração para produção
echo Copiando arquivo de configuração para produção...
copy "config\hostinger.php" "%DEPLOY_DIR%\config\production.php"

echo.
echo Preparação concluída! Os arquivos estão prontos para upload na Hostinger.
echo.
echo Pasta de deployment: %DEPLOY_DIR%
echo.
echo PRÓXIMOS PASSOS:
echo 1. Faça upload de todo o conteúdo da pasta %DEPLOY_DIR% para sua hospedagem
echo 2. Siga as instruções no arquivo GUIA-RAPIDO-DEPLOY.md
echo.

pause
