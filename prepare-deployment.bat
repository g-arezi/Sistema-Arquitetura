@echo off
REM Script para preparação de deployment em ambiente Windows
REM Este script auxilia na preparação dos arquivos para upload no servidor de produção

echo ===== Preparação de Deployment do Sistema-Arquitetura =====
echo.

REM Verificar se o PHP está instalado e acessível
php -v > nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: PHP não encontrado no PATH. Certifique-se de que o PHP está instalado e acessível.
    goto :end
)

REM Verificar se o Composer está instalado e acessível
composer -V > nul 2>&1
if %errorlevel% neq 0 (
    echo ERRO: Composer não encontrado no PATH. Certifique-se de que o Composer está instalado e acessível.
    goto :end
)

echo Verificando dependências do Composer...
composer install --no-dev --optimize-autoloader
if %errorlevel% neq 0 (
    echo ERRO: Falha ao instalar dependências do Composer.
    goto :end
)

echo.
echo Validando configuração...
echo.

REM Verificar arquivos essenciais
set MISSING=0
if not exist "public\index.php" (
    echo ERRO: Arquivo public\index.php não encontrado.
    set /a MISSING+=1
)
if not exist "config\production.php" (
    echo ERRO: Arquivo config\production.php não encontrado.
    set /a MISSING+=1
)
if not exist "src\Controllers\AuthController.php" (
    echo ERRO: Arquivo src\Controllers\AuthController.php não encontrado.
    set /a MISSING+=1
)
if not exist "src\Controllers\AdminController.php" (
    echo ERRO: Arquivo src\Controllers\AdminController.php não encontrado.
    set /a MISSING+=1
)
if not exist "src\Models\UserFile.php" (
    echo ERRO: Arquivo src\Models\UserFile.php não encontrado.
    set /a MISSING+=1
)

if %MISSING% gtr 0 (
    echo.
    echo AVISO: %MISSING% arquivo(s) essencial(is) não encontrado(s)!
    echo.
) else (
    echo Todos os arquivos essenciais encontrados.
)

echo.
echo Verificando arquivo de configuração de produção...
echo.

findstr /C:"SENHA_SEGURA_AQUI" "config\production.php" > nul
if %errorlevel% equ 0 (
    echo AVISO: Senha padrão encontrada no arquivo de configuração!
    echo        Por favor, altere 'SENHA_SEGURA_AQUI' para uma senha segura.
) else (
    echo Senha personalizada configurada.
)

findstr /C:"sistema-arquitetura.com.br" "config\production.php" > nul
if %errorlevel% equ 0 (
    echo AVISO: URL padrão encontrada no arquivo de configuração!
    echo        Por favor, atualize com seu domínio real.
) else (
    echo URL personalizada configurada.
)

findstr /C:"'debug' => true" "config\production.php" > nul
if %errorlevel% equ 0 (
    echo AVISO: Modo de depuração ativado! Desative para produção.
) else (
    echo Modo de depuração está desativado.
)

echo.
echo Preparando arquivos para deployment...
echo.

REM Criar diretório para o pacote de deployment
if not exist "deployment" mkdir deployment

REM Cópia dos arquivos necessários (sem .git, .vscode, etc.)
xcopy "public\*" "deployment\public\*" /E /I /Y
xcopy "src\*" "deployment\src\*" /E /I /Y
xcopy "config\*" "deployment\config\*" /E /I /Y
xcopy "vendor\*" "deployment\vendor\*" /E /I /Y
xcopy "scripts\*" "deployment\scripts\*" /E /I /Y
copy "composer.json" "deployment\"
copy "composer.lock" "deployment\"
copy "*.md" "deployment\"
copy "public\.htaccess" "deployment\public\"

echo.
echo Arquivos preparados no diretório 'deployment'
echo.
echo Próximos passos:
echo 1. Revise o arquivo config\production.php e faça as alterações necessárias
echo 2. Faça upload do conteúdo do diretório 'deployment' para o servidor de produção
echo 3. Siga as instruções em DEPLOY-PRODUCAO.md para configurar o servidor
echo.

:end
pause
