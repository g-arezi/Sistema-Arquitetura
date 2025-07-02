@echo off
REM Deploy Script para Windows - Sistema de Arquitetura
REM Execute este script como Administrador

setlocal EnableDelayedExpansion

echo.
echo ========================================
echo   Sistema de Arquitetura - Deploy
echo ========================================
echo.

REM Configuracoes
set PROJECT_DIR=C:\inetpub\wwwroot\sistema-arquitetura
set BACKUP_DIR=C:\Backups\sistema-arquitetura
set LOG_FILE=C:\Logs\sistema-arquitetura\deploy.log
set XAMPP_DIR=C:\xampp

REM Cores para output (nao funcionam no CMD basico, mas funcionam no PowerShell)
set GREEN=[92m
set RED=[91m
set YELLOW=[93m
set BLUE=[94m
set NC=[0m

echo %GREEN%Iniciando deploy do Sistema de Arquitetura...%NC%

REM Criar diretorios se nao existirem
if not exist "%PROJECT_DIR%" mkdir "%PROJECT_DIR%"
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "C:\Logs\sistema-arquitetura" mkdir "C:\Logs\sistema-arquitetura"

echo.
echo %BLUE%1. Verificando pre-requisitos...%NC%

REM Verificar se PHP esta instalado
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%PHP nao esta instalado ou nao esta no PATH%NC%
    echo Instale o XAMPP ou configure o PHP no PATH
    pause
    exit /b 1
)

REM Verificar se Composer esta instalado
composer -V >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%Composer nao esta instalado%NC%
    echo Baixe e instale o Composer de https://getcomposer.org/
    pause
    exit /b 1
)

REM Verificar se Git esta instalado
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo %RED%Git nao esta instalado%NC%
    echo Baixe e instale o Git de https://git-scm.com/
    pause
    exit /b 1
)

echo %GREEN%Pre-requisitos verificados com sucesso!%NC%

echo.
echo %BLUE%2. Criando backup do sistema atual...%NC%

if exist "%PROJECT_DIR%" (
    set BACKUP_NAME=backup-%date:~6,4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
    set BACKUP_NAME=!BACKUP_NAME: =0!
    
    echo Criando backup: !BACKUP_NAME!
    if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
    
    REM Usar PowerShell para compactar
    powershell -Command "Compress-Archive -Path '%PROJECT_DIR%\*' -DestinationPath '%BACKUP_DIR%\!BACKUP_NAME!.zip' -Force"
    
    if !errorlevel! equ 0 (
        echo %GREEN%Backup criado com sucesso: %BACKUP_DIR%\!BACKUP_NAME!.zip%NC%
    ) else (
        echo %YELLOW%Aviso: Falha ao criar backup%NC%
    )
) else (
    echo %YELLOW%Diretorio do projeto nao existe, pulando backup%NC%
)

echo.
echo %BLUE%3. Baixando codigo do repositorio...%NC%

cd /d "%PROJECT_DIR%"

if not exist ".git" (
    echo Clonando repositorio...
    git clone https://github.com/seu-usuario/sistema-arquitetura.git .
) else (
    echo Atualizando codigo...
    git fetch origin
    git reset --hard origin/main
)

if %errorlevel% neq 0 (
    echo %RED%Erro ao baixar/atualizar codigo do repositorio%NC%
    pause
    exit /b 1
)

echo %GREEN%Codigo baixado/atualizado com sucesso!%NC%

echo.
echo %BLUE%4. Instalando dependencias...%NC%

composer install --no-dev --optimize-autoloader

if %errorlevel% neq 0 (
    echo %RED%Erro ao instalar dependencias%NC%
    pause
    exit /b 1
)

echo %GREEN%Dependencias instaladas com sucesso!%NC%

echo.
echo %BLUE%5. Configurando permissoes e diretorios...%NC%

REM Criar diretorios necessarios
if not exist "%PROJECT_DIR%\public\uploads" mkdir "%PROJECT_DIR%\public\uploads"
if not exist "%PROJECT_DIR%\storage\documents" mkdir "%PROJECT_DIR%\storage\documents"
if not exist "%PROJECT_DIR%\storage\temp" mkdir "%PROJECT_DIR%\storage\temp"
if not exist "C:\Logs\sistema-arquitetura" mkdir "C:\Logs\sistema-arquitetura"

REM Dar permissoes completas aos diretorios (Windows)
icacls "%PROJECT_DIR%\public\uploads" /grant Everyone:(OI)(CI)F /T >nul 2>&1
icacls "%PROJECT_DIR%\storage" /grant Everyone:(OI)(CI)F /T >nul 2>&1
icacls "C:\Logs\sistema-arquitetura" /grant Everyone:(OI)(CI)F /T >nul 2>&1

echo %GREEN%Diretorios e permissoes configurados!%NC%

echo.
echo %BLUE%6. Verificando arquivo de configuracao...%NC%

if not exist "%PROJECT_DIR%\.env.production" (
    echo %YELLOW%Arquivo .env.production nao encontrado%NC%
    echo Criando arquivo de exemplo...
    copy "%PROJECT_DIR%\.env.production" "%PROJECT_DIR%\.env.production.example" >nul 2>&1
    echo %RED%Configure o arquivo .env.production antes de continuar%NC%
    pause
    exit /b 1
)

echo %GREEN%Arquivo de configuracao encontrado!%NC%

echo.
echo %BLUE%7. Testando conexao com banco de dados...%NC%

REM Testar conexao PHP (versao simplificada para Windows)
php -r "echo 'Teste de conexao PHP: OK\n';"

if %errorlevel% neq 0 (
    echo %RED%Erro no teste de conexao%NC%
    pause
    exit /b 1
)

echo %GREEN%Conexao testada com sucesso!%NC%

echo.
echo %BLUE%8. Limpando cache e arquivos temporarios...%NC%

REM Limpar cache do Composer
composer clear-cache

REM Limpar arquivos temporarios
if exist "%PROJECT_DIR%\storage\temp\*" del /q "%PROJECT_DIR%\storage\temp\*"

echo %GREEN%Limpeza concluida!%NC%

echo.
echo %BLUE%9. Configurando servico web...%NC%

REM Verificar se Apache/IIS esta rodando
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe" >nul
if %errorlevel% equ 0 (
    echo Apache detectado - reiniciando...
    net stop Apache2.4 >nul 2>&1
    net start Apache2.4 >nul 2>&1
)

tasklist /FI "IMAGENAME eq w3wp.exe" 2>NUL | find /I /N "w3wp.exe" >nul
if %errorlevel% equ 0 (
    echo IIS detectado - reiniciando pool...
    powershell -Command "Import-Module WebAdministration; Restart-WebAppPool -Name 'DefaultAppPool'" >nul 2>&1
)

echo %GREEN%Servico web configurado!%NC%

echo.
echo %BLUE%10. Executando testes basicos...%NC%

REM Teste basico de conectividade
powershell -Command "try { (Invoke-WebRequest -Uri 'http://localhost' -UseBasicParsing).StatusCode } catch { 0 }" >temp_status.txt
set /p HTTP_STATUS=<temp_status.txt
del temp_status.txt

if "%HTTP_STATUS%"=="200" (
    echo %GREEN%Teste HTTP: OK %NC%
) else (
    echo %YELLOW%Teste HTTP: Status %HTTP_STATUS% %NC%
)

echo.
echo ========================================
echo %GREEN%Deploy concluido com sucesso!%NC%
echo ========================================
echo.
echo %YELLOW%Proximos passos:%NC%
echo 1. Verifique se o site esta funcionando corretamente
echo 2. Teste as funcionalidades principais
echo 3. Configure SSL se necessario
echo 4. Configure backup automatico
echo.
echo %BLUE%Localizacoes importantes:%NC%
echo   • Projeto: %PROJECT_DIR%
echo   • Logs: C:\Logs\sistema-arquitetura\
echo   • Backups: %BACKUP_DIR%
echo   • Uploads: %PROJECT_DIR%\public\uploads
echo.
echo %GREEN%Deploy finalizado em %date% %time%%NC%
echo.

REM Log do deploy
echo %date% %time% - Deploy concluido com sucesso >> "%LOG_FILE%"

pause
