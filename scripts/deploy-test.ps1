# Deploy Script para Windows/IIS - Sistema de Arquitetura
# PowerShell Version - Development Mode (No Admin Required)

param(
    [string]$SiteName = "sistema-arquitetura",
    [string]$SitePath = ".\deploy-test",
    [switch]$TestMode = $true
)

Write-Host "=======================================" -ForegroundColor Green
Write-Host "Deploy Sistema de Arquitetura - TESTE" -ForegroundColor Green  
Write-Host "=======================================" -ForegroundColor Green

if ($TestMode) {
    Write-Host "MODO DE TESTE - Sem configuracao IIS" -ForegroundColor Yellow
}

try {
    # 1. Create test directories
    Write-Host "1. Criando diretorios de teste..." -ForegroundColor Yellow
    $Directories = @(
        $SitePath,
        "$SitePath\storage", 
        "$SitePath\storage\documents",
        "$SitePath\storage\temp",
        "$SitePath\logs",
        "$SitePath\public",
        "$SitePath\public\uploads"
    )
    foreach ($Dir in $Directories) {
        if (-not (Test-Path $Dir)) {
            New-Item -ItemType Directory -Path $Dir -Force | Out-Null
        }
    }
    Write-Host "Diretorios criados!" -ForegroundColor Green

    # 2. Copy files for testing
    Write-Host "2. Copiando arquivos..." -ForegroundColor Yellow
    
    # Copy main files
    $FilesToCopy = @(
        "public\*",
        "src\*", 
        "config\*",
        "composer.json",
        ".env*"
    )
    
    foreach ($Pattern in $FilesToCopy) {
        if (Test-Path $Pattern) {
            $DestPath = Join-Path $SitePath (Split-Path $Pattern -Parent)
            if (-not (Test-Path $DestPath)) {
                New-Item -ItemType Directory -Path $DestPath -Force | Out-Null
            }
            Copy-Item -Path $Pattern -Destination $DestPath -Recurse -Force -ErrorAction SilentlyContinue
        }
    }
    Write-Host "Arquivos copiados!" -ForegroundColor Green

    # 3. Setup environment
    Write-Host "3. Configurando ambiente..." -ForegroundColor Yellow
    $EnvFile = "$SitePath\.env"
    if (-not (Test-Path $EnvFile)) {
        $WindowsEnvFile = ".\.env.windows"
        if (Test-Path $WindowsEnvFile) {
            Copy-Item $WindowsEnvFile $EnvFile
            Write-Host "Arquivo .env criado!" -ForegroundColor Green
        } else {
            # Create basic .env for testing
            $EnvContent = @"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=localhost
DB_NAME=sistema_arquitetura
DB_USER=root
DB_PASS=

STORAGE_DOCUMENTS_PATH=storage/documents
STORAGE_TEMP_PATH=storage/temp
"@
            Set-Content -Path $EnvFile -Value $EnvContent
            Write-Host "Arquivo .env basico criado!" -ForegroundColor Green
        }
    }

    # 4. Test PHP server (if in test mode)
    if ($TestMode) {
        Write-Host "4. Testando servidor PHP..." -ForegroundColor Yellow
        $PhpVersion = php -v 2>$null
        if ($PhpVersion) {
            Write-Host "PHP encontrado!" -ForegroundColor Green
            Write-Host "Para testar, execute:" -ForegroundColor Cyan
            Write-Host "  cd $SitePath" -ForegroundColor Gray
            Write-Host "  php -S localhost:8000 -t public" -ForegroundColor Gray
        } else {
            Write-Host "PHP nao encontrado no PATH" -ForegroundColor Yellow
        }
    }

    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "Deploy de teste concluido!" -ForegroundColor Green
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "Caminho: $SitePath"
    Write-Host ""
    Write-Host "PROXIMOS PASSOS:" -ForegroundColor Cyan
    Write-Host "1. Para producao, execute como Administrador"
    Write-Host "2. Para teste local: cd $SitePath && php -S localhost:8000 -t public"

} catch {
    Write-Host "ERRO durante o deploy!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}
