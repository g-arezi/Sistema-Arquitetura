@echo off
REM ===============================================
REM Deploy Script para Windows/IIS - Sistema de Arquitetura
REM ===============================================

echo ===============================================
echo Deploy do Sistema de Arquitetura - Windows/IIS
echo ===============================================

REM Configuração
set SITE_NAME=sistema-arquitetura
set APP_POOL=sistema-arquitetura-pool
set SITE_PATH=C:\inetpub\wwwroot\sistema-arquitetura
set BACKUP_PATH=C:\backups\sistema-arquitetura
set PHP_PATH=C:\PHP
set COMPOSER_PATH=%PHP_PATH%\composer.phar

REM Verificar se está executando como administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERRO: Este script deve ser executado como Administrador
    pause
    exit /b 1
)

echo.
echo 1. Criando backup do sistema atual...
if exist "%SITE_PATH%" (
    if not exist "%BACKUP_PATH%" mkdir "%BACKUP_PATH%"
    xcopy "%SITE_PATH%" "%BACKUP_PATH%\backup_%date:~10,4%-%date:~4,2%-%date:~7,2%_%time:~0,2%-%time:~3,2%-%time:~6,2%" /E /I /Y
    echo Backup criado com sucesso!
) else (
    echo Diretório do site não existe, pulando backup...
)

echo.
echo 2. Criando diretórios necessários...
if not exist "%SITE_PATH%" mkdir "%SITE_PATH%"
if not exist "%SITE_PATH%\storage" mkdir "%SITE_PATH%\storage"
if not exist "%SITE_PATH%\storage\documents" mkdir "%SITE_PATH%\storage\documents"
if not exist "%SITE_PATH%\storage\temp" mkdir "%SITE_PATH%\storage\temp"
if not exist "%SITE_PATH%\logs" mkdir "%SITE_PATH%\logs"
if not exist "C:\inetpub\logs\sistema-arquitetura" mkdir "C:\inetpub\logs\sistema-arquitetura"

echo.
echo 3. Copiando arquivos do projeto...
xcopy ".\*" "%SITE_PATH%\" /E /I /Y /EXCLUDE:deploy-exclude.txt
echo Arquivos copiados com sucesso!

echo.
echo 4. Configurando permissões...
icacls "%SITE_PATH%\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "%SITE_PATH%\logs" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "%SITE_PATH%\public\uploads" /grant "IIS_IUSRS:(OI)(CI)F" /T
echo Permissões configuradas!

echo.
echo 5. Instalando dependências PHP...
cd /d "%SITE_PATH%"
if exist "%COMPOSER_PATH%" (
    php "%COMPOSER_PATH%" install --no-dev --optimize-autoloader
    echo Dependências instaladas!
) else (
    echo AVISO: Composer não encontrado em %COMPOSER_PATH%
    echo Instale as dependências manualmente: composer install --no-dev --optimize-autoloader
)

echo.
echo 6. Configurando Application Pool...
%WINDIR%\System32\inetsrv\appcmd.exe list apppool "%APP_POOL%" >nul 2>&1
if %errorLevel% neq 0 (
    %WINDIR%\System32\inetsrv\appcmd.exe add apppool /name:"%APP_POOL%"
    %WINDIR%\System32\inetsrv\appcmd.exe set apppool "%APP_POOL%" /processModel.identityType:ApplicationPoolIdentity
    %WINDIR%\System32\inetsrv\appcmd.exe set apppool "%APP_POOL%" /managedRuntimeVersion:""
    echo Application Pool criado!
) else (
    echo Application Pool já existe, reconfigurando...
    %WINDIR%\System32\inetsrv\appcmd.exe set apppool "%APP_POOL%" /processModel.identityType:ApplicationPoolIdentity
)

echo.
echo 7. Configurando Site IIS...
%WINDIR%\System32\inetsrv\appcmd.exe list site "%SITE_NAME%" >nul 2>&1
if %errorLevel% neq 0 (
    %WINDIR%\System32\inetsrv\appcmd.exe add site /name:"%SITE_NAME%" /physicalPath:"%SITE_PATH%\public" /bindings:"http/*:80:,https/*:443:"
    %WINDIR%\System32\inetsrv\appcmd.exe set site "%SITE_NAME%" /applicationDefaults.applicationPool:"%APP_POOL%"
    echo Site IIS criado!
) else (
    echo Site já existe, reconfigurando...
    %WINDIR%\System32\inetsrv\appcmd.exe set site "%SITE_NAME%" /physicalPath:"%SITE_PATH%\public"
    %WINDIR%\System32\inetsrv\appcmd.exe set site "%SITE_NAME%" /applicationDefaults.applicationPool:"%APP_POOL%"
)

echo.
echo 8. Verificando configuração do ambiente...
if not exist "%SITE_PATH%\.env" (
    if exist "%SITE_PATH%\.env.windows" (
        copy "%SITE_PATH%\.env.windows" "%SITE_PATH%\.env"
        echo Arquivo .env criado a partir do .env.windows
    ) else (
        echo AVISO: Arquivo .env não encontrado!
        echo Configure manualmente o arquivo .env antes de usar o sistema
    )
)

echo.
echo 9. Reiniciando serviços...
%WINDIR%\System32\inetsrv\appcmd.exe stop apppool "%APP_POOL%"
%WINDIR%\System32\inetsrv\appcmd.exe start apppool "%APP_POOL%"
iisreset /noforce

echo.
echo ===============================================
echo Deploy concluído com sucesso!
echo ===============================================
echo Site: http://localhost (ou seu domínio)
echo Caminho: %SITE_PATH%
echo Application Pool: %APP_POOL%
echo.
echo PRÓXIMOS PASSOS:
echo 1. Configure o arquivo .env com suas credenciais
echo 2. Configure o SSL/certificado se necessário
echo 3. Teste o funcionamento do sistema
echo 4. Configure backup automático
echo ===============================================

pause
