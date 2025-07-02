# Backup Script PowerShell - Sistema de Arquitetura
# Execute: PowerShell -ExecutionPolicy Bypass -File backup.ps1

param(
    [string]$ProjectDir = "C:\inetpub\wwwroot\sistema-arquitetura",
    [string]$BackupDir = "C:\Backups\sistema-arquitetura", 
    [string]$LogFile = "C:\Logs\sistema-arquitetura\backup.log",
    [int]$RetentionDays = 30
)

# Função para logging
function Write-Log {
    param([string]$Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "$timestamp - $Message"
    Write-Host $logMessage
    Add-Content -Path $LogFile -Value $logMessage -ErrorAction SilentlyContinue
}

Write-Host "=========================================" -ForegroundColor Blue
Write-Host "   Sistema de Arquitetura - Backup PS1    " -ForegroundColor Blue
Write-Host "=========================================" -ForegroundColor Blue
Write-Host ""

Write-Log "🔄 Iniciando backup do Sistema de Arquitetura..."

# Criar diretórios se não existirem
@($BackupDir, (Split-Path $LogFile)) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
        Write-Log "📁 Diretório criado: $_"
    }
}

# Nome do backup com timestamp
$backupName = "backup-$(Get-Date -Format 'yyyyMMdd_HHmmss')"
$backupPath = Join-Path $BackupDir $backupName

Write-Host "🔄 Backup: $backupName" -ForegroundColor Cyan
Write-Log "📦 Iniciando backup: $backupName"

Write-Host ""
Write-Host "1. Criando backup dos arquivos..." -ForegroundColor Cyan

try {
    # Criar diretório do backup
    New-Item -ItemType Directory -Path $backupPath -Force | Out-Null
    
    # Copiar arquivos do projeto (excluindo cache, temp, vendor)
    $excludePatterns = @(
        "storage\temp\*",
        "vendor\*", 
        ".git\*",
        "node_modules\*",
        "*.log"
    )
    
    Write-Host "Copiando arquivos do projeto..." -ForegroundColor Yellow
    
    # Copiar arquivos com exclusões
    robocopy $ProjectDir "$backupPath\files" /E /XD "storage\temp" "vendor" ".git" "node_modules" /XF "*.log" /NFL /NDL /NP | Out-Null
    
    Write-Host "✅ Backup de arquivos criado com sucesso" -ForegroundColor Green
    Write-Log "📁 Backup de arquivos criado: $backupPath\files"
}
catch {
    Write-Host "❌ Erro ao criar backup de arquivos: $($_.Exception.Message)" -ForegroundColor Red
    Write-Log "❌ Erro no backup de arquivos: $($_.Exception.Message)"
    exit 1
}

Write-Host ""
Write-Host "2. Criando backup do banco de dados..." -ForegroundColor Cyan

# Verificar se MySQL está rodando
$mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "⚠️ MySQL não está rodando, pulando backup do banco" -ForegroundColor Yellow
    Write-Log "⚠️ MySQL não encontrado"
} else {
    # Ler configurações do banco
    $envFile = Join-Path $ProjectDir ".env.production"
    if (Test-Path $envFile) {
        $envContent = Get-Content $envFile
        
        $dbHost = ($envContent | Where-Object { $_ -match "^DB_HOST=" }) -replace "DB_HOST=", ""
        $dbName = ($envContent | Where-Object { $_ -match "^DB_NAME=" }) -replace "DB_NAME=", ""
        $dbUser = ($envContent | Where-Object { $_ -match "^DB_USER=" }) -replace "DB_USER=", ""
        $dbPass = ($envContent | Where-Object { $_ -match "^DB_PASS=" }) -replace "DB_PASS=", ""
        
        if ($dbName) {
            Write-Host "Fazendo backup do banco: $dbName" -ForegroundColor Yellow
            
            try {
                # Comando mysqldump
                $mysqldumpCmd = "mysqldump"
                $arguments = @("-h$dbHost", "-u$dbUser")
                if ($dbPass) { $arguments += "-p$dbPass" }
                $arguments += $dbName
                
                $dbBackupFile = Join-Path $backupPath "database.sql"
                & $mysqldumpCmd $arguments | Out-File -FilePath $dbBackupFile -Encoding UTF8
                
                if (Test-Path $dbBackupFile) {
                    # Comprimir backup do banco
                    Compress-Archive -Path $dbBackupFile -DestinationPath "$dbBackupFile.zip" -Force
                    Remove-Item $dbBackupFile -Force
                    
                    Write-Host "✅ Backup do banco criado com sucesso" -ForegroundColor Green
                    Write-Log "🗄️ Backup do banco criado: $dbBackupFile.zip"
                } else {
                    Write-Host "⚠️ Falha no backup do banco de dados" -ForegroundColor Yellow
                    Write-Log "⚠️ Falha no backup do banco"
                }
            }
            catch {
                Write-Host "⚠️ Erro no backup do banco: $($_.Exception.Message)" -ForegroundColor Yellow
                Write-Log "⚠️ Erro no backup do banco: $($_.Exception.Message)"
            }
        } else {
            Write-Host "⚠️ Configurações do banco não encontradas" -ForegroundColor Yellow
        }
    } else {
        Write-Host "⚠️ Arquivo .env.production não encontrado" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "3. Compactando backup..." -ForegroundColor Cyan

try {
    $zipFile = "$backupPath.zip"
    Compress-Archive -Path "$backupPath\*" -DestinationPath $zipFile -Force
    
    # Remover pasta temporária
    Remove-Item $backupPath -Recurse -Force
    
    Write-Host "✅ Backup compactado com sucesso" -ForegroundColor Green
    Write-Log "📦 Backup compactado: $zipFile"
    $backupFinal = $zipFile
}
catch {
    Write-Host "⚠️ Falha na compactação, mantendo pasta: $($_.Exception.Message)" -ForegroundColor Yellow
    Write-Log "⚠️ Falha na compactação: $($_.Exception.Message)"
    $backupFinal = $backupPath
}

Write-Host ""
Write-Host "4. Verificando tamanho do backup..." -ForegroundColor Cyan

if (Test-Path $backupFinal) {
    $backupSize = (Get-Item $backupFinal).Length
    $backupSizeMB = [math]::Round($backupSize / 1MB, 2)
    $backupSizeGB = [math]::Round($backupSize / 1GB, 2)
    
    if ($backupSizeGB -ge 1) {
        Write-Host "📊 Tamanho do backup: $backupSizeGB GB" -ForegroundColor Cyan
        $sizeDisplay = "$backupSizeGB GB"
    } else {
        Write-Host "📊 Tamanho do backup: $backupSizeMB MB" -ForegroundColor Cyan
        $sizeDisplay = "$backupSizeMB MB"
    }
    
    Write-Log "📊 Tamanho do backup: $sizeDisplay"
} else {
    Write-Host "❌ Arquivo de backup não encontrado" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "5. Verificando integridade..." -ForegroundColor Cyan

if ($backupFinal.EndsWith(".zip")) {
    try {
        # Testar integridade do ZIP
        Add-Type -AssemblyName System.IO.Compression.FileSystem
        $zip = [System.IO.Compression.ZipFile]::OpenRead($backupFinal)
        $zip.Dispose()
        
        Write-Host "✅ Verificação de integridade: OK" -ForegroundColor Green
        Write-Log "✅ Integridade verificada"
    }
    catch {
        Write-Host "❌ Erro na verificação de integridade: $($_.Exception.Message)" -ForegroundColor Red
        Write-Log "❌ Erro na integridade: $($_.Exception.Message)"
    }
}

Write-Host ""
Write-Host "6. Limpando backups antigos..." -ForegroundColor Cyan

$cutoffDate = (Get-Date).AddDays(-$RetentionDays)
$oldBackups = Get-ChildItem -Path $BackupDir -Filter "backup-*" | Where-Object { $_.LastWriteTime -lt $cutoffDate }

$removedCount = 0
foreach ($oldBackup in $oldBackups) {
    try {
        Remove-Item $oldBackup.FullName -Force -Recurse
        $removedCount++
        Write-Log "🗑️ Backup antigo removido: $($oldBackup.Name)"
    }
    catch {
        Write-Host "⚠️ Falha ao remover backup antigo: $($oldBackup.Name)" -ForegroundColor Yellow
    }
}

$remainingBackups = (Get-ChildItem -Path $BackupDir -Filter "backup-*").Count
Write-Host "🗑️ Backups antigos removidos: $removedCount" -ForegroundColor Green
Write-Host "📦 Backups restantes: $remainingBackups" -ForegroundColor Cyan
Write-Log "🧹 Limpeza concluída: $removedCount removidos, $remainingBackups restantes"

Write-Host ""
Write-Host "7. Criando arquivo de informações..." -ForegroundColor Cyan

$infoFile = Join-Path $BackupDir "$backupName`_info.txt"
$infoContent = @"
Sistema de Arquitetura - Backup
================================

Data: $(Get-Date -Format 'dd/MM/yyyy HH:mm:ss')
Servidor: $env:COMPUTERNAME
Usuário: $env:USERNAME
Sistema: $(Get-WmiObject -Class Win32_OperatingSystem | Select-Object -ExpandProperty Caption)

Arquivos incluídos:
- files\: Arquivos do projeto (excluindo temp, cache, vendor)
- database.sql.zip: Backup completo do banco de dados (se disponível)

Para restaurar:
1. Extrair backup: Expand-Archive "$backupFinal" "C:\Restore"
2. Restaurar database.sql no MySQL
3. Copiar arquivos para o diretório do projeto
4. Instalar dependências: composer install
5. Configurar permissões apropriadas

Tamanho: $sizeDisplay
Integridade: Verificada
Retenção: $RetentionDays dias

PowerShell Commands para restauração:
Expand-Archive -Path "$backupFinal" -DestinationPath "C:\Restore\$backupName"
mysql -u usuario -p banco_de_dados < "C:\Restore\$backupName\database.sql"
"@

Set-Content -Path $infoFile -Value $infoContent -Encoding UTF8
Write-Log "📄 Arquivo de informações criado: $infoFile"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Blue
Write-Host "🎉 Backup concluído com sucesso!" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Blue
Write-Host ""

Write-Host "📊 Resumo do backup:" -ForegroundColor Yellow
Write-Host "   • Nome: $backupName" -ForegroundColor White
Write-Host "   • Localização: $backupFinal" -ForegroundColor White  
Write-Host "   • Tamanho: $sizeDisplay" -ForegroundColor White
Write-Host "   • Backups ativos: $remainingBackups" -ForegroundColor White
Write-Host "   • Retenção: $RetentionDays dias" -ForegroundColor White
Write-Host ""

Write-Host "📁 Para restaurar este backup:" -ForegroundColor Cyan
Write-Host "   1. Extrair: Expand-Archive '$backupFinal' 'C:\Restore'" -ForegroundColor Gray
Write-Host "   2. Restaurar DB: mysql -u USER -p DATABASE < database.sql" -ForegroundColor Gray
Write-Host "   3. Copiar arquivos para o diretório do projeto" -ForegroundColor Gray
Write-Host "   4. Executar: composer install" -ForegroundColor Gray
Write-Host ""

$finishTime = Get-Date
Write-Host "🕒 Backup finalizado em $finishTime" -ForegroundColor Green
Write-Log "🎉 Backup concluído com sucesso em $finishTime"

# Enviar notificação se webhook configurado
$webhookUrl = $env:BACKUP_WEBHOOK_URL
if ($webhookUrl) {
    try {
        $payload = @{
            text = "✅ Backup do Sistema de Arquitetura concluído"
            details = @{
                timestamp = $finishTime.ToString()
                backup_name = $backupName
                size = $sizeDisplay
                location = $backupFinal
            }
        } | ConvertTo-Json
        
        Invoke-RestMethod -Uri $webhookUrl -Method Post -Body $payload -ContentType "application/json"
        Write-Log "📨 Notificação enviada via webhook"
    }
    catch {
        Write-Log "⚠️ Falha ao enviar notificação: $($_.Exception.Message)"
    }
}

Write-Host ""
Write-Host "Pressione Enter para finalizar..." -ForegroundColor Gray
Read-Host
