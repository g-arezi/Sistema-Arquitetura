# Deploy Script PowerShell - Sistema de Arquitetura
# Execute como Administrador: PowerShell -ExecutionPolicy Bypass -File deploy.ps1

param(
    [string]$ProjectDir = "C:\inetpub\wwwroot\sistema-arquitetura",
    [string]$BackupDir = "C:\Backups\sistema-arquitetura",
    [string]$LogFile = "C:\Logs\sistema-arquitetura\deploy.log"
)

# Função para logging
function Write-Log {
    param([string]$Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "$timestamp - $Message"
    Write-Host $logMessage
    Add-Content -Path $LogFile -Value $logMessage -ErrorAction SilentlyContinue
}

# Função para verificar se comando existe
function Test-Command {
    param([string]$Command)
    try {
        Get-Command $Command -ErrorAction Stop
        return $true
    }
    catch {
        return $false
    }
}

Write-Host "==========================================" -ForegroundColor Blue
Write-Host "   Sistema de Arquitetura - Deploy PS1    " -ForegroundColor Blue
Write-Host "==========================================" -ForegroundColor Blue
Write-Host ""

Write-Log "🚀 Iniciando deploy do Sistema de Arquitetura..."

# Verificar se está executando como administrador
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "❌ Este script deve ser executado como Administrador" -ForegroundColor Red
    Write-Host "Clique com o botão direito no PowerShell e selecione 'Executar como administrador'" -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "1. Verificando pré-requisitos..." -ForegroundColor Cyan

# Criar diretórios se não existirem
@($ProjectDir, $BackupDir, (Split-Path $LogFile)) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
        Write-Log "📁 Diretório criado: $_"
    }
}

# Verificar PHP
if (-not (Test-Command "php")) {
    Write-Host "❌ PHP não está instalado ou não está no PATH" -ForegroundColor Red
    Write-Host "Instale o XAMPP ou adicione o PHP ao PATH do sistema" -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

# Verificar Composer
if (-not (Test-Command "composer")) {
    Write-Host "❌ Composer não está instalado" -ForegroundColor Red
    Write-Host "Baixe e instale de: https://getcomposer.org/" -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

# Verificar Git
if (-not (Test-Command "git")) {
    Write-Host "❌ Git não está instalado" -ForegroundColor Red
    Write-Host "Baixe e instale de: https://git-scm.com/" -ForegroundColor Yellow
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "✅ Pré-requisitos verificados com sucesso!" -ForegroundColor Green
Write-Log "✅ Pré-requisitos atendidos"

Write-Host ""
Write-Host "2. Criando backup do sistema atual..." -ForegroundColor Cyan

if (Test-Path $ProjectDir) {
    $backupName = "backup-$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    $backupPath = Join-Path $BackupDir "$backupName.zip"
    
    try {
        Compress-Archive -Path "$ProjectDir\*" -DestinationPath $backupPath -Force
        Write-Host "✅ Backup criado: $backupPath" -ForegroundColor Green
        Write-Log "💾 Backup criado: $backupPath"
    }
    catch {
        Write-Host "⚠️ Aviso: Falha ao criar backup: $($_.Exception.Message)" -ForegroundColor Yellow
        Write-Log "⚠️ Falha no backup: $($_.Exception.Message)"
    }
} else {
    Write-Host "⚠️ Diretório do projeto não existe, pulando backup" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "3. Baixando código do repositório..." -ForegroundColor Cyan

Set-Location $ProjectDir

if (-not (Test-Path ".git")) {
    Write-Host "Clonando repositório..." -ForegroundColor Yellow
    git clone https://github.com/seu-usuario/sistema-arquitetura.git .
} else {
    Write-Host "Atualizando código..." -ForegroundColor Yellow
    git fetch origin
    git reset --hard origin/main
}

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro ao baixar/atualizar código" -ForegroundColor Red
    Write-Log "❌ Erro no git: $LASTEXITCODE"
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "✅ Código baixado/atualizado com sucesso!" -ForegroundColor Green
Write-Log "📥 Código atualizado do repositório"

Write-Host ""
Write-Host "4. Instalando dependências..." -ForegroundColor Cyan

composer install --no-dev --optimize-autoloader

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Erro ao instalar dependências" -ForegroundColor Red
    Write-Log "❌ Erro no composer: $LASTEXITCODE"
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "✅ Dependências instaladas com sucesso!" -ForegroundColor Green
Write-Log "📦 Dependências do Composer instaladas"

Write-Host ""
Write-Host "5. Configurando permissões e diretórios..." -ForegroundColor Cyan

# Criar diretórios necessários
$directories = @(
    "$ProjectDir\public\uploads",
    "$ProjectDir\storage\documents", 
    "$ProjectDir\storage\temp",
    "C:\Logs\sistema-arquitetura"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Log "📁 Diretório criado: $dir"
    }
    
    # Configurar permissões no Windows
    try {
        $acl = Get-Acl $dir
        $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("Everyone", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
        $acl.SetAccessRule($accessRule)
        Set-Acl $dir $acl
    }
    catch {
        Write-Host "⚠️ Aviso: Falha ao configurar permissões em $dir" -ForegroundColor Yellow
    }
}

Write-Host "✅ Diretórios e permissões configurados!" -ForegroundColor Green
Write-Log "🔧 Permissões configuradas"

Write-Host ""
Write-Host "6. Verificando arquivo de configuração..." -ForegroundColor Cyan

if (-not (Test-Path "$ProjectDir\.env.production")) {
    Write-Host "⚠️ Arquivo .env.production não encontrado" -ForegroundColor Yellow
    Write-Host "❌ Configure o arquivo .env.production antes de continuar" -ForegroundColor Red
    Write-Log "❌ Arquivo .env.production não encontrado"
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host "✅ Arquivo de configuração encontrado!" -ForegroundColor Green

Write-Host ""
Write-Host "7. Testando conectividade..." -ForegroundColor Cyan

# Testar PHP básico
try {
    $phpTest = php -r "echo 'OK';"
    if ($phpTest -eq "OK") {
        Write-Host "✅ Teste PHP: OK" -ForegroundColor Green
        Write-Log "✅ Teste PHP passou"
    }
}
catch {
    Write-Host "❌ Erro no teste PHP" -ForegroundColor Red
    Write-Log "❌ Erro no teste PHP"
}

Write-Host ""
Write-Host "8. Limpando cache e arquivos temporários..." -ForegroundColor Cyan

# Limpar cache do Composer
composer clear-cache

# Limpar arquivos temporários
try {
    Remove-Item "$ProjectDir\storage\temp\*" -Force -Recurse -ErrorAction SilentlyContinue
    Write-Log "🧹 Cache e arquivos temporários limpos"
}
catch {
    Write-Host "⚠️ Aviso: Alguns arquivos temporários não puderam ser removidos" -ForegroundColor Yellow
}

Write-Host "✅ Limpeza concluída!" -ForegroundColor Green

Write-Host ""
Write-Host "9. Configurando serviço web..." -ForegroundColor Cyan

# Reiniciar Apache (XAMPP)
$apacheService = Get-Service -Name "Apache*" -ErrorAction SilentlyContinue
if ($apacheService) {
    try {
        Restart-Service $apacheService.Name -Force
        Write-Host "✅ Apache reiniciado" -ForegroundColor Green
        Write-Log "🔄 Apache reiniciado"
    }
    catch {
        Write-Host "⚠️ Aviso: Falha ao reiniciar Apache" -ForegroundColor Yellow
    }
}

# Reiniciar IIS se disponível
if (Get-WindowsFeature -Name IIS-WebServer -ErrorAction SilentlyContinue) {
    try {
        Import-Module WebAdministration -ErrorAction SilentlyContinue
        Restart-WebAppPool -Name "DefaultAppPool" -ErrorAction SilentlyContinue
        Write-Host "✅ IIS reiniciado" -ForegroundColor Green
        Write-Log "🔄 IIS reiniciado"
    }
    catch {
        Write-Host "⚠️ Aviso: Falha ao reiniciar IIS" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "10. Executando testes básicos..." -ForegroundColor Cyan

# Teste de conectividade HTTP
try {
    $response = Invoke-WebRequest -Uri "http://localhost" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Teste HTTP: OK (Status: $($response.StatusCode))" -ForegroundColor Green
        Write-Log "✅ Teste HTTP passou: $($response.StatusCode)"
    } else {
        Write-Host "⚠️ Teste HTTP: Status $($response.StatusCode)" -ForegroundColor Yellow
        Write-Log "⚠️ Teste HTTP: Status $($response.StatusCode)"
    }
}
catch {
    Write-Host "⚠️ Teste HTTP: Falha na conectividade" -ForegroundColor Yellow
    Write-Log "⚠️ Teste HTTP falhou: $($_.Exception.Message)"
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Blue
Write-Host "🎉 Deploy concluído com sucesso!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Blue
Write-Host ""

Write-Host "📋 Próximos passos:" -ForegroundColor Yellow
Write-Host "1. Verifique se o site está funcionando: http://localhost"
Write-Host "2. Configure SSL se necessário"
Write-Host "3. Teste as funcionalidades principais"
Write-Host "4. Configure backup automático"
Write-Host ""

Write-Host "📁 Localizações importantes:" -ForegroundColor Cyan
Write-Host "   • Projeto: $ProjectDir"
Write-Host "   • Logs: C:\Logs\sistema-arquitetura\"
Write-Host "   • Backups: $BackupDir"
Write-Host "   • Uploads: $ProjectDir\public\uploads"
Write-Host ""

$finishTime = Get-Date
Write-Host "🕒 Deploy finalizado em $finishTime" -ForegroundColor Green
Write-Log "🎉 Deploy concluído com sucesso em $finishTime"

Write-Host ""
Write-Host "Pressione Enter para finalizar..." -ForegroundColor Gray
Read-Host
