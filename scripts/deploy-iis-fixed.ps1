# ===============================================
# Deploy Script para Windows/IIS - Sistema de Arquitetura
# PowerShell Version
# ===============================================

param(
    [string]$SiteName = "sistema-arquitetura",
    [string]$AppPool = "sistema-arquitetura-pool",
    [string]$SitePath = "C:\inetpub\wwwroot\sistema-arquitetura",
    [string]$BackupPath = "C:\backups\sistema-arquitetura",
    [string]$PhpPath = "C:\PHP",
    [switch]$SkipBackup
)

# Verificar se está executando como administrador
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "Este script deve ser executado como Administrador"
    Write-Host "Clique com o botão direito no PowerShell e selecione 'Executar como Administrador'"
    exit 1
}

Write-Host "===============================================" -ForegroundColor Green
Write-Host "Deploy do Sistema de Arquitetura - Windows/IIS" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green

# Importar módulo WebAdministration
Import-Module WebAdministration -ErrorAction SilentlyContinue
if (-not (Get-Module WebAdministration)) {
    Write-Warning "Módulo WebAdministration não encontrado. Instalando..."
    Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole, IIS-WebServer, IIS-CommonHttpFeatures, IIS-HttpErrors, IIS-HttpLogging, IIS-RequestFiltering, IIS-StaticContent, IIS-Security, IIS-RequestFiltering, IIS-DefaultDocument, IIS-DirectoryBrowsing -All
    Import-Module WebAdministration
}

try {
    # 1. Backup do sistema atual
    if (-not $SkipBackup -and (Test-Path $SitePath)) {
        Write-Host "`n1. Criando backup do sistema atual..." -ForegroundColor Yellow
        $BackupDir = Join-Path $BackupPath "backup_$(Get-Date -Format 'yyyy-MM-dd_HH-mm-ss')"
        if (-not (Test-Path $BackupPath)) { New-Item -ItemType Directory -Path $BackupPath -Force }
        Copy-Item -Path $SitePath -Destination $BackupDir -Recurse -Force
        Write-Host "Backup criado em: $BackupDir" -ForegroundColor Green
    } else {
        Write-Host "`n1. Pulando backup..." -ForegroundColor Gray
    }

    # 2. Criar diretórios necessários
    Write-Host "`n2. Criando diretórios necessários..." -ForegroundColor Yellow
    $Directories = @(
        $SitePath,
        "$SitePath\storage",
        "$SitePath\storage\documents",
        "$SitePath\storage\temp",
        "$SitePath\logs",
        "C:\inetpub\logs\sistema-arquitetura"
    )
    foreach ($Dir in $Directories) {
        if (-not (Test-Path $Dir)) {
            New-Item -ItemType Directory -Path $Dir -Force | Out-Null
        }
    }
    Write-Host "Diretórios criados com sucesso!" -ForegroundColor Green

    # 3. Copiar arquivos do projeto
    Write-Host "`n3. Copiando arquivos do projeto..." -ForegroundColor Yellow
    $ExcludeFile = ".\deploy-exclude.txt"
    if (Test-Path $ExcludeFile) {
        $ExcludeList = Get-Content $ExcludeFile | Where-Object { $_ -and $_ -notmatch '^#' }
        robocopy "." $SitePath /E /XD $ExcludeList /NFL /NDL /NP
    } else {
        Copy-Item -Path ".\*" -Destination $SitePath -Recurse -Force -Exclude @(".git", "node_modules", "vendor", "*.log")
    }
    Write-Host "Arquivos copiados com sucesso!" -ForegroundColor Green

    # 4. Configurar permissões
    Write-Host "`n4. Configurando permissões..." -ForegroundColor Yellow
    $StoragePaths = @("$SitePath\storage", "$SitePath\logs", "$SitePath\public\uploads")
    foreach ($Path in $StoragePaths) {
        if (Test-Path $Path) {
            icacls $Path /grant "IIS_IUSRS:(OI)(CI)F" /T /Q
        }
    }
    Write-Host "Permissões configuradas!" -ForegroundColor Green

    # 5. Instalar dependências PHP
    Write-Host "`n5. Instalando dependências PHP..." -ForegroundColor Yellow
    Set-Location $SitePath
    $ComposerPath = Join-Path $PhpPath "composer.phar"
    if (Test-Path $ComposerPath) {
        & php $ComposerPath install --no-dev --optimize-autoloader
        Write-Host "Dependências instaladas!" -ForegroundColor Green
    } else {
        Write-Warning "Composer não encontrado em $ComposerPath"
        Write-Host "Instale as dependências manualmente: composer install --no-dev --optimize-autoloader"
    }

    # 6. Configurar Application Pool
    Write-Host "`n6. Configurando Application Pool..." -ForegroundColor Yellow
    if (Get-IISAppPool -Name $AppPool -ErrorAction SilentlyContinue) {
        Write-Host "Application Pool já existe, reconfigurando..." -ForegroundColor Gray
        Remove-IISAppPool -Name $AppPool -Confirm:$false
    }
    
    New-IISAppPool -Name $AppPool -Force
    Set-IISAppPool -Name $AppPool -ProcessModel @{identityType="ApplicationPoolIdentity"}
    Set-IISAppPool -Name $AppPool -ManagedRuntimeVersion ""
    Write-Host "Application Pool configurado!" -ForegroundColor Green

    # 7. Configurar Site IIS
    Write-Host "`n7. Configurando Site IIS..." -ForegroundColor Yellow
    if (Get-IISSite -Name $SiteName -ErrorAction SilentlyContinue) {
        Write-Host "Site já existe, reconfigurando..." -ForegroundColor Gray
        Remove-IISSite -Name $SiteName -Confirm:$false
    }
    
    New-IISSite -Name $SiteName -PhysicalPath "$SitePath\public" -Protocol http -Port 80
    New-IISSiteBinding -Name $SiteName -Protocol https -Port 443
    Set-IISSite -Name $SiteName -ApplicationPool $AppPool
    Write-Host "Site IIS configurado!" -ForegroundColor Green

    # 8. Verificar configuração do ambiente
    Write-Host "`n8. Verificando configuração do ambiente..." -ForegroundColor Yellow
    $EnvFile = "$SitePath\.env"
    if (-not (Test-Path $EnvFile)) {
        $WindowsEnvFile = "$SitePath\.env.windows"
        if (Test-Path $WindowsEnvFile) {
            Copy-Item $WindowsEnvFile $EnvFile
            Write-Host "Arquivo .env criado a partir do .env.windows" -ForegroundColor Green
        } else {
            Write-Warning "Arquivo .env não encontrado!"
            Write-Host "Configure manualmente o arquivo .env antes de usar o sistema"
        }
    }

    # 9. Reiniciar serviços
    Write-Host "`n9. Reiniciando serviços..." -ForegroundColor Yellow
    Restart-IISAppPool -Name $AppPool
    iisreset /noforce | Out-Null
    Write-Host "Serviços reiniciados!" -ForegroundColor Green

    Write-Host "`n===============================================" -ForegroundColor Green
    Write-Host "Deploy concluído com sucesso!" -ForegroundColor Green
    Write-Host "===============================================" -ForegroundColor Green
    Write-Host "Site: http://localhost (ou seu domínio)"
    Write-Host "Caminho: $SitePath"
    Write-Host "Application Pool: $AppPool"
    Write-Host ""
    Write-Host "PRÓXIMOS PASSOS:" -ForegroundColor Cyan
    Write-Host "1. Configure o arquivo .env com suas credenciais"
    Write-Host "2. Configure o SSL/certificado se necessário"
    Write-Host "3. Teste o funcionamento do sistema"
    Write-Host "4. Configure backup automático"
    Write-Host "===============================================" -ForegroundColor Green

} catch {
    $ErrorMessage = $_.Exception.Message
    Write-Error "Erro durante o deploy: $ErrorMessage"
    exit 1
} finally {
    Set-Location (Split-Path $MyInvocation.MyCommand.Path)
}
