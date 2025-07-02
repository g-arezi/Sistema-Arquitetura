@echo off
REM Script de Backup para Windows - Sistema de Arquitetura
REM Execute este script para fazer backup automatizado

setlocal EnableDelayedExpansion

echo.
echo ========================================
echo   Sistema de Arquitetura - Backup
echo ========================================
echo.

REM Configuracoes
set PROJECT_DIR=C:\inetpub\wwwroot\sistema-arquitetura
set BACKUP_DIR=C:\Backups\sistema-arquitetura
set LOG_FILE=C:\Logs\sistema-arquitetura\backup.log
set RETENTION_DAYS=30

REM Cores
set GREEN=[92m
set RED=[91m
set YELLOW=[93m
set BLUE=[94m
set NC=[0m

echo %GREEN%Iniciando backup do Sistema de Arquitetura...%NC%

REM Criar diretorios se nao existirem
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"
if not exist "C:\Logs\sistema-arquitetura" mkdir "C:\Logs\sistema-arquitetura"

REM Nome do backup com timestamp
set BACKUP_NAME=backup-%date:~6,4%%date:~3,2%%date:~0,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set BACKUP_NAME=!BACKUP_NAME: =0!
set BACKUP_PATH=%BACKUP_DIR%\!BACKUP_NAME!

echo %BLUE%1. Criando backup dos arquivos...%NC%
echo Backup: !BACKUP_NAME!

REM Criar diretorio do backup
mkdir "%BACKUP_PATH%"

echo Copiando arquivos do projeto...
xcopy "%PROJECT_DIR%" "%BACKUP_PATH%\files" /E /I /Q /H /Y /EXCLUDE:backup_exclude.txt >nul 2>&1

if !errorlevel! equ 0 (
    echo %GREEN%Backup de arquivos criado com sucesso%NC%
) else (
    echo %RED%Erro ao criar backup de arquivos%NC%
    goto :error
)

echo.
echo %BLUE%2. Criando backup do banco de dados...%NC%

REM Verificar se MySQL está rodando
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe" >nul
if %errorlevel% neq 0 (
    echo %YELLOW%MySQL nao esta rodando, pulando backup do banco%NC%
    goto :skip_db
)

REM Ler configuracoes do banco (versao simplificada)
if exist "%PROJECT_DIR%\.env.production" (
    for /f "tokens=2 delims==" %%a in ('findstr "DB_HOST" "%PROJECT_DIR%\.env.production"') do set DB_HOST=%%a
    for /f "tokens=2 delims==" %%a in ('findstr "DB_NAME" "%PROJECT_DIR%\.env.production"') do set DB_NAME=%%a
    for /f "tokens=2 delims==" %%a in ('findstr "DB_USER" "%PROJECT_DIR%\.env.production"') do set DB_USER=%%a
    for /f "tokens=2 delims==" %%a in ('findstr "DB_PASS" "%PROJECT_DIR%\.env.production"') do set DB_PASS=%%a
    
    REM Remover espacos
    set DB_HOST=!DB_HOST: =!
    set DB_NAME=!DB_NAME: =!
    set DB_USER=!DB_USER: =!
    set DB_PASS=!DB_PASS: =!
    
    echo Fazendo backup do banco: !DB_NAME!
    
    REM Backup do MySQL (assumindo que mysqldump está no PATH)
    mysqldump -h!DB_HOST! -u!DB_USER! -p!DB_PASS! !DB_NAME! > "%BACKUP_PATH%\database.sql" 2>nul
    
    if !errorlevel! equ 0 (
        echo %GREEN%Backup do banco criado com sucesso%NC%
    ) else (
        echo %YELLOW%Aviso: Falha no backup do banco de dados%NC%
    )
) else (
    echo %YELLOW%Arquivo de configuracao nao encontrado, pulando backup do banco%NC%
)

:skip_db

echo.
echo %BLUE%3. Compactando backup...%NC%

REM Usar PowerShell para compactar
powershell -Command "Compress-Archive -Path '%BACKUP_PATH%\*' -DestinationPath '%BACKUP_PATH%.zip' -Force"

if !errorlevel! equ 0 (
    echo %GREEN%Backup compactado com sucesso%NC%
    
    REM Remover pasta temporaria
    rmdir /s /q "%BACKUP_PATH%" 2>nul
    
    set BACKUP_FINAL=%BACKUP_PATH%.zip
) else (
    echo %YELLOW%Aviso: Falha na compactacao, mantendo pasta%NC%
    set BACKUP_FINAL=%BACKUP_PATH%
)

echo.
echo %BLUE%4. Verificando tamanho do backup...%NC%

for %%A in ("!BACKUP_FINAL!") do (
    set BACKUP_SIZE=%%~zA
    set /a BACKUP_SIZE_MB=!BACKUP_SIZE!/1024/1024
)

echo Tamanho do backup: !BACKUP_SIZE_MB! MB

echo.
echo %BLUE%5. Limpando backups antigos...%NC%

REM Listar backups antigos (simplificado - remove backups de mais de X dias)
forfiles /p "%BACKUP_DIR%" /s /m backup-*.* /d -%RETENTION_DAYS% /c "cmd /c del @path" 2>nul

echo Backups antigos removidos (mais de %RETENTION_DAYS% dias)

echo.
echo %BLUE%6. Criando arquivo de informacoes...%NC%

REM Criar arquivo de informacoes
(
echo Sistema de Arquitetura - Backup
echo ================================
echo.
echo Data: %date% %time%
echo Servidor: %COMPUTERNAME%
echo Usuario: %USERNAME%
echo.
echo Arquivos incluidos:
echo - files\: Arquivos do projeto
echo - database.sql: Backup do banco de dados ^(se disponivel^)
echo.
echo Para restaurar:
echo 1. Extrair arquivos no diretorio do projeto
echo 2. Restaurar database.sql no MySQL
echo 3. Instalar dependencias ^(composer install^)
echo 4. Configurar permissoes apropriadas
echo.
echo Tamanho: !BACKUP_SIZE_MB! MB
) > "%BACKUP_DIR%\!BACKUP_NAME!_info.txt"

REM Log do backup
echo %date% %time% - Backup criado: !BACKUP_NAME! ^(!BACKUP_SIZE_MB! MB^) >> "%LOG_FILE%"

echo.
echo ========================================
echo %GREEN%Backup concluido com sucesso!%NC%
echo ========================================
echo.
echo %YELLOW%Resumo do backup:%NC%
echo   • Nome: !BACKUP_NAME!
echo   • Localizacao: !BACKUP_FINAL!
echo   • Tamanho: !BACKUP_SIZE_MB! MB
echo   • Retencao: %RETENTION_DAYS% dias
echo.
echo %BLUE%Para restaurar este backup:%NC%
echo   1. Extrair: PowerShell Expand-Archive "!BACKUP_FINAL!" "C:\Restore"
echo   2. Restaurar DB: mysql -u usuario -p banco ^< database.sql
echo   3. Copiar arquivos para o diretorio do projeto
echo   4. Executar: composer install
echo.
echo %GREEN%Backup finalizado em %date% %time%%NC%
echo.
goto :end

:error
echo %RED%Erro durante o backup!%NC%
echo Verifique os logs em: %LOG_FILE%
echo %date% %time% - ERRO no backup >> "%LOG_FILE%"

:end
pause
