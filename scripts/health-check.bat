@echo off
REM ===============================================
REM Health Check - Sistema de Arquitetura Windows
REM ===============================================

echo ===============================================
echo Health Check - Sistema de Arquitetura
echo ===============================================

set SITE_PATH=C:\inetpub\wwwroot\sistema-arquitetura
set SITE_URL=http://localhost
set LOG_FILE=%SITE_PATH%\logs\health-check.log

echo %date% %time% - Iniciando health check... >> "%LOG_FILE%"

echo.
echo 1. Verificando diretórios...
if exist "%SITE_PATH%" (
    echo [OK] Diretório do site existe
    echo %date% %time% - [OK] Diretório do site existe >> "%LOG_FILE%"
) else (
    echo [ERRO] Diretório do site não encontrado: %SITE_PATH%
    echo %date% %time% - [ERRO] Diretório do site não encontrado >> "%LOG_FILE%"
    goto :error
)

if exist "%SITE_PATH%\storage" (
    echo [OK] Diretório storage existe
) else (
    echo [ERRO] Diretório storage não encontrado
    echo %date% %time% - [ERRO] Diretório storage não encontrado >> "%LOG_FILE%"
    goto :error
)

echo.
echo 2. Verificando permissões...
dir "%SITE_PATH%\storage" >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Permissões de leitura OK
) else (
    echo [ERRO] Problema com permissões de leitura
    echo %date% %time% - [ERRO] Problema com permissões >> "%LOG_FILE%"
)

echo.
echo 3. Verificando arquivo .env...
if exist "%SITE_PATH%\.env" (
    echo [OK] Arquivo .env existe
    echo %date% %time% - [OK] Arquivo .env existe >> "%LOG_FILE%"
) else (
    echo [ERRO] Arquivo .env não encontrado
    echo %date% %time% - [ERRO] Arquivo .env não encontrado >> "%LOG_FILE%"
    goto :error
)

echo.
echo 4. Verificando IIS...
%WINDIR%\System32\inetsrv\appcmd.exe list site "sistema-arquitetura" >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Site IIS configurado
    echo %date% %time% - [OK] Site IIS OK >> "%LOG_FILE%"
) else (
    echo [ERRO] Site IIS não encontrado
    echo %date% %time% - [ERRO] Site IIS não encontrado >> "%LOG_FILE%"
)

%WINDIR%\System32\inetsrv\appcmd.exe list apppool "sistema-arquitetura-pool" >nul 2>&1
if %errorLevel% equ 0 (
    echo [OK] Application Pool configurado
    echo %date% %time% - [OK] Application Pool OK >> "%LOG_FILE%"
) else (
    echo [ERRO] Application Pool não encontrado
    echo %date% %time% - [ERRO] Application Pool não encontrado >> "%LOG_FILE%"
)

echo.
echo 5. Testando conectividade HTTP...
curl -s -o nul -w "%%{http_code}" %SITE_URL% > temp_response.txt 2>&1
set /p HTTP_CODE=<temp_response.txt
del temp_response.txt

if "%HTTP_CODE%"=="200" (
    echo [OK] Site respondendo HTTP 200
    echo %date% %time% - [OK] Site respondendo HTTP 200 >> "%LOG_FILE%"
) else (
    echo [AVISO] Site respondeu com código: %HTTP_CODE%
    echo %date% %time% - [AVISO] Site respondeu código %HTTP_CODE% >> "%LOG_FILE%"
)

echo.
echo 6. Verificando espaço em disco...
for /f "tokens=3" %%a in ('dir /-c "%SITE_PATH%" ^| find "bytes free"') do set FREE_SPACE=%%a
echo Espaço livre: %FREE_SPACE% bytes
echo %date% %time% - Espaço livre: %FREE_SPACE% bytes >> "%LOG_FILE%"

echo.
echo 7. Verificando logs recentes...
if exist "%SITE_PATH%\logs\error.log" (
    echo Últimas 5 linhas do log de erro:
    powershell "Get-Content '%SITE_PATH%\logs\error.log' -Tail 5"
) else (
    echo [OK] Nenhum erro recente encontrado
)

echo.
echo ===============================================
echo Health Check Concluído
echo Log salvo em: %LOG_FILE%
echo ===============================================
echo %date% %time% - Health check concluído >> "%LOG_FILE%"

goto :end

:error
echo.
echo ===============================================
echo [ERRO] Health Check encontrou problemas!
echo Verifique o log: %LOG_FILE%
echo ===============================================
echo %date% %time% - Health check com erros >> "%LOG_FILE%"
exit /b 1

:end
exit /b 0
