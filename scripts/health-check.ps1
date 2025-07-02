# ===============================================
# Health Check - Sistema de Arquitetura Windows
# PowerShell Version
# ===============================================

param(
    [string]$SitePath = "C:\inetpub\wwwroot\sistema-arquitetura",
    [string]$SiteUrl = "http://localhost",
    [string]$SiteName = "sistema-arquitetura",
    [string]$AppPool = "sistema-arquitetura-pool"
)

$LogFile = Join-Path $SitePath "logs\health-check.log"
$TimeStamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

Write-Host "===============================================" -ForegroundColor Green
Write-Host "Health Check - Sistema de Arquitetura" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green

# Função para log
function Write-Log {
    param([string]$Message, [string]$Level = "INFO")
    $LogMessage = "$TimeStamp - [$Level] $Message"
    Add-Content -Path $LogFile -Value $LogMessage -ErrorAction SilentlyContinue
}

$ErrorCount = 0

try {
    Write-Log "Iniciando health check..."

    # 1. Verificar diretórios
    Write-Host "`n1. Verificando diretórios..." -ForegroundColor Yellow
    
    if (Test-Path $SitePath) {
        Write-Host "[OK] Diretório do site existe" -ForegroundColor Green
        Write-Log "Diretório do site existe"
    } else {
        Write-Host "[ERRO] Diretório do site não encontrado: $SitePath" -ForegroundColor Red
        Write-Log "Diretório do site não encontrado" "ERROR"
        $ErrorCount++
    }

    $StoragePath = Join-Path $SitePath "storage"
    if (Test-Path $StoragePath) {
        Write-Host "[OK] Diretório storage existe" -ForegroundColor Green
        Write-Log "Diretório storage existe"
    } else {
        Write-Host "[ERRO] Diretório storage não encontrado" -ForegroundColor Red
        Write-Log "Diretório storage não encontrado" "ERROR"
        $ErrorCount++
    }

    # 2. Verificar permissões
    Write-Host "`n2. Verificando permissões..." -ForegroundColor Yellow
    
    try {
        Get-ChildItem $StoragePath -ErrorAction Stop | Out-Null
        Write-Host "[OK] Permissões de leitura OK" -ForegroundColor Green
        Write-Log "Permissões de leitura OK"
    } catch {
        Write-Host "[ERRO] Problema com permissões de leitura" -ForegroundColor Red
        Write-Log "Problema com permissões de leitura" "ERROR"
        $ErrorCount++
    }

    # 3. Verificar arquivo .env
    Write-Host "`n3. Verificando arquivo .env..." -ForegroundColor Yellow
    
    $EnvFile = Join-Path $SitePath ".env"
    if (Test-Path $EnvFile) {
        Write-Host "[OK] Arquivo .env existe" -ForegroundColor Green
        Write-Log "Arquivo .env existe"
    } else {
        Write-Host "[ERRO] Arquivo .env não encontrado" -ForegroundColor Red
        Write-Log "Arquivo .env não encontrado" "ERROR"
        $ErrorCount++
    }

    # 4. Verificar IIS
    Write-Host "`n4. Verificando IIS..." -ForegroundColor Yellow
    
    Import-Module WebAdministration -ErrorAction SilentlyContinue
    
    if (Get-IISSite -Name $SiteName -ErrorAction SilentlyContinue) {
        Write-Host "[OK] Site IIS configurado" -ForegroundColor Green
        Write-Log "Site IIS configurado"
    } else {
        Write-Host "[ERRO] Site IIS não encontrado: $SiteName" -ForegroundColor Red
        Write-Log "Site IIS não encontrado" "ERROR"
        $ErrorCount++
    }

    if (Get-IISAppPool -Name $AppPool -ErrorAction SilentlyContinue) {
        $PoolState = (Get-IISAppPool -Name $AppPool).State
        Write-Host "[OK] Application Pool: $AppPool ($PoolState)" -ForegroundColor Green
        Write-Log "Application Pool: $AppPool ($PoolState)"
    } else {
        Write-Host "[ERRO] Application Pool não encontrado: $AppPool" -ForegroundColor Red
        Write-Log "Application Pool não encontrado" "ERROR"
        $ErrorCount++
    }

    # 5. Testar conectividade HTTP
    Write-Host "`n5. Testando conectividade HTTP..." -ForegroundColor Yellow
    
    try {
        $Response = Invoke-WebRequest -Uri $SiteUrl -TimeoutSec 10 -ErrorAction Stop
        $StatusCode = $Response.StatusCode
        
        if ($StatusCode -eq 200) {
            Write-Host "[OK] Site respondendo HTTP $StatusCode" -ForegroundColor Green
            Write-Log "Site respondendo HTTP $StatusCode"
        } else {
            Write-Host "[AVISO] Site respondeu com código: $StatusCode" -ForegroundColor Yellow
            Write-Log "Site respondeu código $StatusCode" "WARNING"
        }
    } catch {
        Write-Host "[ERRO] Falha na conectividade HTTP: $($_.Exception.Message)" -ForegroundColor Red
        Write-Log "Falha na conectividade HTTP: $($_.Exception.Message)" "ERROR"
        $ErrorCount++
    }

    # 6. Verificar espaço em disco
    Write-Host "`n6. Verificando espaço em disco..." -ForegroundColor Yellow
    
    $Drive = (Get-Item $SitePath).PSDrive
    $FreeSpace = [math]::Round($Drive.Free / 1GB, 2)
    $TotalSpace = [math]::Round(($Drive.Free + $Drive.Used) / 1GB, 2)
    
    Write-Host "Espaço livre: $FreeSpace GB de $TotalSpace GB" -ForegroundColor Cyan
    Write-Log "Espaço livre: $FreeSpace GB de $TotalSpace GB"
    
    if ($FreeSpace -lt 1) {
        Write-Host "[AVISO] Pouco espaço em disco disponível!" -ForegroundColor Yellow
        Write-Log "Pouco espaço em disco disponível!" "WARNING"
    }

    # 7. Verificar logs recentes
    Write-Host "`n7. Verificando logs recentes..." -ForegroundColor Yellow
    
    $ErrorLogPath = Join-Path $SitePath "logs\error.log"
    if (Test-Path $ErrorLogPath) {
        Write-Host "Últimas 5 linhas do log de erro:" -ForegroundColor Cyan
        Get-Content $ErrorLogPath -Tail 5 | ForEach-Object { Write-Host "  $_" -ForegroundColor Gray }
    } else {
        Write-Host "[OK] Nenhum log de erro encontrado" -ForegroundColor Green
    }

    # 8. Verificar processos PHP
    Write-Host "`n8. Verificando processos PHP..." -ForegroundColor Yellow
    
    $PhpProcesses = Get-Process -Name "php*" -ErrorAction SilentlyContinue
    if ($PhpProcesses) {
        Write-Host "[OK] $($PhpProcesses.Count) processo(s) PHP em execução" -ForegroundColor Green
        Write-Log "$($PhpProcesses.Count) processo(s) PHP em execução"
    } else {
        Write-Host "[AVISO] Nenhum processo PHP encontrado" -ForegroundColor Yellow
        Write-Log "Nenhum processo PHP encontrado" "WARNING"
    }

    # Resumo
    Write-Host "`n===============================================" -ForegroundColor Green
    if ($ErrorCount -eq 0) {
        Write-Host "Health Check APROVADO - Sistema funcionando corretamente!" -ForegroundColor Green
        Write-Log "Health Check APROVADO - $ErrorCount erros encontrados"
    } else {
        Write-Host "Health Check com $ErrorCount erro(s) encontrado(s)!" -ForegroundColor Red
        Write-Log "Health Check com $ErrorCount erro(s) encontrado(s)" "ERROR"
    }
    Write-Host "===============================================" -ForegroundColor Green
    Write-Host "Log salvo em: $LogFile"

    Write-Log "Health check concluído com $ErrorCount erro(s)"

} catch {
    Write-Host "[ERRO CRÍTICO] $($_.Exception.Message)" -ForegroundColor Red
    Write-Log "Erro crítico no health check: $($_.Exception.Message)" "CRITICAL"
    exit 1
}

exit $ErrorCount
