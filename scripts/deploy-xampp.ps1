# Deploy Script para XAMPP - Sistema de Arquitetura
# PowerShell Version for XAMPP Environment

param(
    [string]$SiteName = "sistema-arquitetura",
    [string]$SitePath = "C:\xampp\htdocs\sistema-arquitetura",
    [string]$BackupPath = "C:\xampp\backups\sistema-arquitetura",
    [string]$XamppPath = "C:\xampp",
    [switch]$SkipBackup
)

Write-Host "==========================================" -ForegroundColor Green
Write-Host "Deploy Sistema de Arquitetura - XAMPP" -ForegroundColor Green  
Write-Host "==========================================" -ForegroundColor Green

try {
    # Check if XAMPP exists
    if (-not (Test-Path $XamppPath)) {
        Write-Host "ERRO: XAMPP não encontrado em $XamppPath" -ForegroundColor Red
        Write-Host "Verifique se o XAMPP está instalado" -ForegroundColor Yellow
        exit 1
    }

    # 1. Create backup
    if (-not $SkipBackup -and (Test-Path $SitePath)) {
        Write-Host "1. Criando backup..." -ForegroundColor Yellow
        $BackupDir = Join-Path $BackupPath "backup_$(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss')"
        if (-not (Test-Path $BackupPath)) { 
            New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
        }
        Copy-Item -Path $SitePath -Destination $BackupDir -Recurse -Force
        Write-Host "Backup criado: $BackupDir" -ForegroundColor Green
    } else {
        Write-Host "1. Pulando backup..." -ForegroundColor Gray
    }

    # 2. Create directories
    Write-Host "2. Criando diretorios..." -ForegroundColor Yellow
    $Directories = @(
        $SitePath,
        "$SitePath\storage", 
        "$SitePath\storage\documents",
        "$SitePath\storage\temp",
        "$SitePath\logs",
        "$SitePath\public\uploads"
    )
    foreach ($Dir in $Directories) {
        if (-not (Test-Path $Dir)) {
            New-Item -ItemType Directory -Path $Dir -Force | Out-Null
        }
    }
    Write-Host "Diretorios criados!" -ForegroundColor Green

    # 3. Copy files
    Write-Host "3. Copiando arquivos..." -ForegroundColor Yellow
    
    # Get current directory (where the script is running from)
    $SourcePath = Get-Location
    
    # Files and folders to copy
    $ItemsToCopy = @(
        "public",
        "src", 
        "config",
        "vendor",
        "composer.json",
        ".env*",
        "*.php"
    )
    
    foreach ($Item in $ItemsToCopy) {
        $SourceItem = Join-Path $SourcePath $Item
        if (Test-Path $SourceItem) {
            Copy-Item -Path $SourceItem -Destination $SitePath -Recurse -Force -ErrorAction SilentlyContinue
        }
    }
    Write-Host "Arquivos copiados!" -ForegroundColor Green

    # 4. Set permissions (for Windows)
    Write-Host "4. Configurando permissoes..." -ForegroundColor Yellow
    $StoragePaths = @("$SitePath\storage", "$SitePath\logs", "$SitePath\public\uploads")
    foreach ($Path in $StoragePaths) {
        if (Test-Path $Path) {
            # Give full permissions to current user and system
            icacls $Path /grant "$env:USERNAME:(OI)(CI)F" /T /Q 2>$null | Out-Null
            icacls $Path /grant "SYSTEM:(OI)(CI)F" /T /Q 2>$null | Out-Null
        }
    }
    Write-Host "Permissoes configuradas!" -ForegroundColor Green

    # 5. Install dependencies (if composer exists)
    Write-Host "5. Verificando dependencias PHP..." -ForegroundColor Yellow
    Set-Location $SitePath
    
    # Check if composer exists
    $ComposerPath = "$XamppPath\php\composer.phar"
    $GlobalComposer = Get-Command composer -ErrorAction SilentlyContinue
    
    if (Test-Path $ComposerPath) {
        Write-Host "Instalando dependencias com Composer local..." -ForegroundColor Cyan
        & "$XamppPath\php\php.exe" $ComposerPath install --no-dev --optimize-autoloader
    } elseif ($GlobalComposer) {
        Write-Host "Instalando dependencias com Composer global..." -ForegroundColor Cyan
        composer install --no-dev --optimize-autoloader
    } else {
        Write-Host "Composer nao encontrado, pulando instalacao de dependencias" -ForegroundColor Yellow
        Write-Host "Execute manualmente: composer install --no-dev --optimize-autoloader" -ForegroundColor Gray
    }

    # 6. Setup environment
    Write-Host "6. Configurando ambiente..." -ForegroundColor Yellow
    $EnvFile = "$SitePath\.env"
    if (-not (Test-Path $EnvFile)) {
        $WindowsEnvFile = "$SitePath\.env.windows"
        if (Test-Path $WindowsEnvFile) {
            Copy-Item $WindowsEnvFile $EnvFile
            Write-Host "Arquivo .env criado a partir do .env.windows!" -ForegroundColor Green
        } else {
            # Create basic .env for XAMPP
            $EnvContent = @"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/sistema-arquitetura/public

DB_HOST=localhost
DB_NAME=sistema_arquitetura
DB_USER=root
DB_PASS=

STORAGE_DOCUMENTS_PATH=storage/documents
STORAGE_TEMP_PATH=storage/temp

# XAMPP MySQL default settings
DB_PORT=3306
"@
            Set-Content -Path $EnvFile -Value $EnvContent -Encoding UTF8
            Write-Host "Arquivo .env criado para XAMPP!" -ForegroundColor Green
        }
    }

    # 7. Create .htaccess for pretty URLs (if not exists)
    Write-Host "7. Configurando .htaccess..." -ForegroundColor Yellow
    $HtaccessFile = "$SitePath\public\.htaccess"
    if (-not (Test-Path $HtaccessFile)) {
        $HtaccessContent = @"
# Sistema de Arquitetura - .htaccess

RewriteEngine On

# Handle Angular and Vue.js HTML5 mode
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Prevent access to sensitive files
<Files ~ "^\.">
    Order allow,deny
    Deny from all
</Files>

<FilesMatch "\.(env|ini|conf|sql|log)$">
    Order allow,deny
    Deny from all
</FilesMatch>
"@
        Set-Content -Path $HtaccessFile -Value $HtaccessContent -Encoding UTF8
        Write-Host ".htaccess criado!" -ForegroundColor Green
    }

    # 8. Check XAMPP services
    Write-Host "8. Verificando servicos XAMPP..." -ForegroundColor Yellow
    
    $ApacheService = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    $MySQLService = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    
    if ($ApacheService) {
        Write-Host "Apache: RODANDO" -ForegroundColor Green
    } else {
        Write-Host "Apache: PARADO" -ForegroundColor Red
        Write-Host "Inicie o Apache pelo XAMPP Control Panel" -ForegroundColor Yellow
    }
    
    if ($MySQLService) {
        Write-Host "MySQL: RODANDO" -ForegroundColor Green
    } else {
        Write-Host "MySQL: PARADO" -ForegroundColor Red
        Write-Host "Inicie o MySQL pelo XAMPP Control Panel" -ForegroundColor Yellow
    }

    Write-Host "==========================================" -ForegroundColor Green
    Write-Host "Deploy concluido com sucesso!" -ForegroundColor Green
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host "Caminho: $SitePath"
    Write-Host "URL: http://localhost/sistema-arquitetura/public"
    Write-Host ""
    Write-Host "PROXIMOS PASSOS:" -ForegroundColor Cyan
    Write-Host "1. Inicie Apache e MySQL no XAMPP Control Panel"
    Write-Host "2. Configure o banco de dados no .env"
    Write-Host "3. Acesse: http://localhost/sistema-arquitetura/public"
    Write-Host "4. Ou via index.php: http://localhost/sistema-arquitetura/public/index.php"

} catch {
    Write-Host "ERRO durante o deploy!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
} finally {
    # Return to original directory
    Set-Location $PSScriptRoot
}
