# Auto Deploy Script - Detecta ambiente e executa deploy apropriado
# PowerShell Version

param(
    [switch]$Force,
    [string]$Environment = "auto"
)

Write-Host "============================================" -ForegroundColor Green
Write-Host "Auto Deploy - Sistema de Arquitetura" -ForegroundColor Green  
Write-Host "============================================" -ForegroundColor Green

# Detectar ambiente
$HasXAMPP = $false
$HasIIS = $false
$HasApache = $false

# Verificar XAMPP
$XamppPaths = @(
    "C:\xampp",
    "E:\ferramentas\XAMPP",
    "D:\xampp",
    "$env:ProgramFiles\xampp"
)

foreach ($XamppPath in $XamppPaths) {
    if (Test-Path $XamppPath) {
        Write-Host "XAMPP encontrado em: $XamppPath" -ForegroundColor Green
        $HasXAMPP = $true
        $XamppDetected = $XamppPath
        break
    }
}

# Verificar IIS
$IISPath = "$env:WINDIR\System32\inetsrv\appcmd.exe"
if (Test-Path $IISPath) {
    Write-Host "IIS encontrado" -ForegroundColor Green
    $HasIIS = $true
}

# Verificar Apache standalone
$ApacheService = Get-Service -Name "*apache*" -ErrorAction SilentlyContinue
if ($ApacheService) {
    Write-Host "Apache standalone encontrado" -ForegroundColor Green
    $HasApache = $true
}

Write-Host ""
Write-Host "Ambiente detectado:" -ForegroundColor Yellow

if ($HasXAMPP) {
    Write-Host "✅ XAMPP: $XamppDetected" -ForegroundColor Green
}
if ($HasIIS) {
    Write-Host "✅ IIS: Disponível" -ForegroundColor Green
}
if ($HasApache -and -not $HasXAMPP) {
    Write-Host "✅ Apache: Standalone" -ForegroundColor Green
}

if (-not $HasXAMPP -and -not $HasIIS -and -not $HasApache) {
    Write-Host "❌ Nenhum servidor web detectado" -ForegroundColor Red
}

Write-Host ""

# Escolher deploy baseado no ambiente
if ($Environment -eq "auto") {
    if ($HasXAMPP) {
        Write-Host "🎯 Executando deploy para XAMPP..." -ForegroundColor Cyan
        $ChosenDeploy = "xampp"
        $XamppSitePath = "$XamppDetected\htdocs\sistema-arquitetura"
    } elseif ($HasIIS) {
        Write-Host "🎯 Executando deploy para IIS..." -ForegroundColor Cyan
        $ChosenDeploy = "iis"
    } else {
        Write-Host "🎯 Executando deploy de teste..." -ForegroundColor Cyan
        $ChosenDeploy = "test"
    }
} else {
    $ChosenDeploy = $Environment.ToLower()
    Write-Host "🎯 Deploy forçado para: $ChosenDeploy" -ForegroundColor Cyan
}

Write-Host ""

try {
    switch ($ChosenDeploy) {
        "xampp" {
            if ($HasXAMPP) {
                Write-Host "Executando deploy XAMPP..." -ForegroundColor Yellow
                & powershell -ExecutionPolicy RemoteSigned -File "scripts\deploy-xampp.ps1" -XamppPath $XamppDetected -SitePath $XamppSitePath
                
                if ($LASTEXITCODE -eq 0) {
                    Write-Host ""
                    Write-Host "🎉 Deploy XAMPP concluído com sucesso!" -ForegroundColor Green
                    Write-Host "URL: http://localhost/sistema-arquitetura/public" -ForegroundColor Cyan
                }
            } else {
                Write-Host "ERRO: XAMPP não encontrado para deploy" -ForegroundColor Red
                exit 1
            }
        }
        
        "iis" {
            if ($HasIIS) {
                Write-Host "Executando deploy IIS..." -ForegroundColor Yellow
                & powershell -ExecutionPolicy RemoteSigned -File "scripts\deploy-iis.ps1"
                
                if ($LASTEXITCODE -eq 0) {
                    Write-Host ""
                    Write-Host "🎉 Deploy IIS concluído com sucesso!" -ForegroundColor Green
                    Write-Host "URL: http://localhost" -ForegroundColor Cyan
                }
            } else {
                Write-Host "ERRO: IIS não encontrado para deploy" -ForegroundColor Red
                Write-Host "Para instalar IIS:" -ForegroundColor Yellow
                Write-Host "Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole -All" -ForegroundColor Gray
                exit 1
            }
        }
        
        "test" {
            Write-Host "Executando deploy de teste..." -ForegroundColor Yellow
            & powershell -ExecutionPolicy RemoteSigned -File "scripts\deploy-test.ps1"
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host ""
                Write-Host "🎉 Deploy de teste concluído!" -ForegroundColor Green
                Write-Host "Para testar: cd deploy-test && php -S localhost:8000 -t public" -ForegroundColor Cyan
            }
        }
        
        default {
            Write-Host "ERRO: Ambiente '$ChosenDeploy' não reconhecido" -ForegroundColor Red
            Write-Host "Ambientes disponíveis: xampp, iis, test" -ForegroundColor Yellow
            exit 1
        }
    }

} catch {
    Write-Host "ERRO durante o deploy!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "Auto Deploy concluído!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
