# Deploy Script para Windows/IIS - Sistema de Arquitetura
# PowerShell Version - Simplified

param(
    [string]$SiteName = "sistema-arquitetura",
    [string]$AppPool = "sistema-arquitetura-pool", 
    [string]$SitePath = "C:\inetpub\wwwroot\sistema-arquitetura",
    [string]$BackupPath = "C:\backups\sistema-arquitetura",
    [switch]$SkipBackup
)

# Check if running as administrator
$currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
$isAdmin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERRO: Este script deve ser executado como Administrador" -ForegroundColor Red
    Write-Host "Clique com o botão direito no PowerShell e selecione 'Executar como Administrador'"
    exit 1
}

Write-Host "=======================================" -ForegroundColor Green
Write-Host "Deploy Sistema de Arquitetura - IIS" -ForegroundColor Green  
Write-Host "=======================================" -ForegroundColor Green

try {
    # Check if IIS is available
    $IISPath = "$env:WINDIR\System32\inetsrv\appcmd.exe"
    if (-not (Test-Path $IISPath)) {
        Write-Host "AVISO: IIS não está instalado" -ForegroundColor Yellow
        Write-Host "Este script é para ambientes com IIS/Windows Server" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Para XAMPP, use:" -ForegroundColor Cyan
        Write-Host "  composer run deploy-xampp" -ForegroundColor Green
        Write-Host ""
        Write-Host "Para instalar IIS manualmente:" -ForegroundColor Gray
        Write-Host "  Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole, IIS-WebServer, IIS-CommonHttpFeatures -All" -ForegroundColor Gray
        exit 1
    }

    # Import WebAdministration module (optional, fallback to appcmd)
    Import-Module WebAdministration -ErrorAction SilentlyContinue
    if (-not (Get-Module WebAdministration)) {
        Write-Host "WebAdministration module não disponível, usando appcmd.exe" -ForegroundColor Yellow
        $UseAppCmd = $true
    } else {
        $UseAppCmd = $false
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
        "$SitePath\logs"
    )
    foreach ($Dir in $Directories) {
        if (-not (Test-Path $Dir)) {
            New-Item -ItemType Directory -Path $Dir -Force | Out-Null
        }
    }
    Write-Host "Diretorios criados!" -ForegroundColor Green

    # 3. Copy files
    Write-Host "3. Copiando arquivos..." -ForegroundColor Yellow
    Copy-Item -Path ".\*" -Destination $SitePath -Recurse -Force -Exclude @(".git", "node_modules", "vendor", "*.log")
    Write-Host "Arquivos copiados!" -ForegroundColor Green

    # 4. Set permissions
    Write-Host "4. Configurando permissoes..." -ForegroundColor Yellow
    $StoragePaths = @("$SitePath\storage", "$SitePath\logs", "$SitePath\public\uploads")
    foreach ($Path in $StoragePaths) {
        if (Test-Path $Path) {
            icacls $Path /grant "IIS_IUSRS:(OI)(CI)F" /T /Q | Out-Null
        }
    }
    Write-Host "Permissoes configuradas!" -ForegroundColor Green

    # 5. Configure Application Pool
    Write-Host "5. Configurando Application Pool..." -ForegroundColor Yellow
    
    if ($UseAppCmd) {
        # Using appcmd.exe
        $AppPoolExists = & $IISPath list apppool $AppPool 2>$null
        if ($AppPoolExists) {
            & $IISPath delete apppool $AppPool | Out-Null
        }
        & $IISPath add apppool /name:$AppPool | Out-Null
        & $IISPath set apppool $AppPool /processModel.identityType:ApplicationPoolIdentity | Out-Null
        & $IISPath set apppool $AppPool /managedRuntimeVersion: | Out-Null
    } else {
        # Using PowerShell cmdlets
        if (Get-IISAppPool -Name $AppPool -ErrorAction SilentlyContinue) {
            Remove-IISAppPool -Name $AppPool -Confirm:$false
        }
        New-IISAppPool -Name $AppPool -Force
        Set-IISAppPool -Name $AppPool -ProcessModel @{identityType="ApplicationPoolIdentity"}
        Set-IISAppPool -Name $AppPool -ManagedRuntimeVersion ""
    }
    Write-Host "Application Pool criado!" -ForegroundColor Green

    # 6. Configure IIS Site
    Write-Host "6. Configurando Site IIS..." -ForegroundColor Yellow
    
    if ($UseAppCmd) {
        # Using appcmd.exe
        $SiteExists = & $IISPath list site $SiteName 2>$null
        if ($SiteExists) {
            & $IISPath delete site $SiteName | Out-Null
        }
        & $IISPath add site /name:$SiteName /physicalPath:"$SitePath\public" /bindings:"http/*:80:" | Out-Null
        & $IISPath set site $SiteName /applicationDefaults.applicationPool:$AppPool | Out-Null
    } else {
        # Using PowerShell cmdlets
        if (Get-IISSite -Name $SiteName -ErrorAction SilentlyContinue) {
            Remove-IISSite -Name $SiteName -Confirm:$false
        }
        New-IISSite -Name $SiteName -PhysicalPath "$SitePath\public" -Protocol http -Port 80
        Set-IISSite -Name $SiteName -ApplicationPool $AppPool
    }
    Write-Host "Site IIS configurado!" -ForegroundColor Green

    # 7. Setup environment
    Write-Host "7. Configurando ambiente..." -ForegroundColor Yellow
    $EnvFile = "$SitePath\.env"
    if (-not (Test-Path $EnvFile)) {
        $WindowsEnvFile = "$SitePath\.env.windows"
        if (Test-Path $WindowsEnvFile) {
            Copy-Item $WindowsEnvFile $EnvFile
            Write-Host "Arquivo .env criado!" -ForegroundColor Green
        }
    }

    # 8. Restart services
    Write-Host "8. Reiniciando servicos..." -ForegroundColor Yellow
    
    if ($UseAppCmd) {
        # Using appcmd.exe
        & $IISPath stop apppool $AppPool 2>$null
        & $IISPath start apppool $AppPool | Out-Null
        iisreset /noforce | Out-Null
    } else {
        # Using PowerShell cmdlets
        Restart-IISAppPool -Name $AppPool
        iisreset /noforce | Out-Null
    }
    Write-Host "Servicos reiniciados!" -ForegroundColor Green

    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "Deploy concluido com sucesso!" -ForegroundColor Green
    Write-Host "=======================================" -ForegroundColor Green
    Write-Host "Site: http://localhost"
    Write-Host "Caminho: $SitePath"
    Write-Host "App Pool: $AppPool"

} catch {
    Write-Host "ERRO durante o deploy!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}
