# Script para preparar deploy em Hospedagem Compartilhada
# PowerShell Version

param(
    [string]$DeployDir = "deploy-hospedagem",
    [string]$ZipFile = "sistema-arquitetura-hospedagem.zip",
    [string]$DomainExample = "seudominio.com.br"
)

Write-Host "===============================================" -ForegroundColor Green
Write-Host "Preparando deploy para Hospedagem Compartilhada" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green

try {
    # Limpar deploy anterior
    if (Test-Path $DeployDir) {
        Remove-Item $DeployDir -Recurse -Force
    }
    if (Test-Path $ZipFile) {
        Remove-Item $ZipFile -Force
    }

    Write-Host "`n1. Criando estrutura de deploy..." -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $DeployDir -Force | Out-Null
    New-Item -ItemType Directory -Path "$DeployDir\storage\documents" -Force | Out-Null
    New-Item -ItemType Directory -Path "$DeployDir\storage\temp" -Force | Out-Null
    New-Item -ItemType Directory -Path "$DeployDir\logs" -Force | Out-Null

    Write-Host "2. Copiando arquivos essenciais..." -ForegroundColor Yellow
    
    # Copiar diretórios principais
    $DirsToQopy = @("public", "src", "config", "vendor")
    foreach ($Dir in $DirsToQopy) {
        if (Test-Path $Dir) {
            Copy-Item -Path $Dir -Destination "$DeployDir\$Dir" -Recurse -Force
        }
    }
    
    # Copiar arquivos específicos
    $FilesToCopy = @("composer.json", "composer.lock")
    foreach ($File in $FilesToCopy) {
        if (Test-Path $File) {
            Copy-Item -Path $File -Destination $DeployDir -Force
        }
    }

    Write-Host "3. Criando .env de exemplo para hospedagem..." -ForegroundColor Yellow
    $EnvContent = @"
# Configuração para Hospedagem Compartilhada
# Renomeie para .env e configure com dados reais

APP_ENV=production
APP_DEBUG=false
APP_URL=https://$DomainExample/sistema

# Banco de dados (fornecido pela hospedagem)
DB_HOST=localhost
DB_NAME=seu_usuario_dbname
DB_USER=seu_usuario_db
DB_PASS=senha_fornecida_host
DB_CHARSET=utf8mb4

# Email (configure com dados reais)
MAIL_HOST=smtp.$DomainExample
MAIL_PORT=587
MAIL_USERNAME=noreply@$DomainExample
MAIL_PASSWORD=sua_senha_email
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME="Sistema de Arquitetura"
MAIL_FROM_ADDRESS=noreply@$DomainExample

# Segurança
HASH_COST=12
SESSION_LIFETIME=7200
CSRF_TOKEN_EXPIRY=3600
SESSION_SECURE=true

# Armazenamento (ajustado para hospedagem compartilhada)
STORAGE_DOCUMENTS_PATH=storage/documents
STORAGE_TEMP_PATH=storage/temp

# Logs
ERROR_LOG_PATH=logs/error.log
ACCESS_LOG_PATH=logs/access.log

# Cache
CACHE_ENABLED=true
CACHE_TTL=3600

# Backup
BACKUP_ENABLED=false

# Upload limits (verificar com hospedagem)
MAX_UPLOAD_SIZE=15728640
ALLOWED_FILE_TYPES=pdf,doc,docx,xls,xlsx,jpg,jpeg,png,dwg
"@
    Set-Content -Path "$DeployDir\.env.hospedagem" -Value $EnvContent -Encoding UTF8

    Write-Host "4. Criando .htaccess otimizado..." -ForegroundColor Yellow
    $HtaccessContent = @'
# Sistema de Arquitetura - Hospedagem Compartilhada

RewriteEngine On

# Force HTTPS (se disponível)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Handle Angular and Vue.js HTML5 mode
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Prevent access to sensitive files
<Files ~ "^\..*">
    Order allow,deny
    Deny from all
</Files>

<FilesMatch "\.(env|ini|conf|sql|log|md)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect configuration directories
<IfModule mod_rewrite.c>
    RewriteRule ^(config|src|storage|logs|vendor)/ - [F,L]
</IfModule>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Security - disable server signature
ServerSignature Off

# PHP settings (se permitido pela hospedagem)
<IfModule mod_php.c>
    php_value upload_max_filesize 16M
    php_value post_max_size 16M
    php_value max_execution_time 300
    php_value memory_limit 256M
</IfModule>
'@
    Set-Content -Path "$DeployDir\public\.htaccess" -Value $HtaccessContent -Encoding UTF8

    Write-Host "5. Criando instruções de instalação..." -ForegroundColor Yellow
    $InstructionsContent = @"
# 📋 INSTRUÇÕES DE INSTALAÇÃO NA HOSPEDAGEM

## 🎯 Passo 1: Upload dos arquivos
1. Acesse o painel da sua hospedagem (cPanel, hPanel, etc.)
2. Vá para o Gerenciador de Arquivos
3. Entre na pasta public_html/ (ou www/)
4. Crie uma subpasta 'sistema' (ou nome desejado)
5. Faça upload de todos os arquivos para esta pasta
6. Extraia se necessário

## 🗄️ Passo 2: Configurar banco de dados
1. No painel de controle, vá para 'Bancos de dados MySQL'
2. Crie um novo banco: exemplo_sistema
3. Crie um usuário: exemplo_user
4. Associe o usuário ao banco com todas as permissões
5. Anote: nome do banco, usuário, senha, host

## ⚙️ Passo 3: Configurar ambiente
1. Renomeie o arquivo '.env.hospedagem' para '.env'
2. Edite o arquivo .env com os dados reais:
   - Substitua dados do banco de dados
   - Configure email se necessário
   - Ajuste APP_URL para seu domínio

## 🔐 Passo 4: Configurar permissões
1. Selecione todas as pastas
2. Altere permissões para 755
3. Selecione todos os arquivos
4. Altere permissões para 644
5. Para storage/ e logs/: permissão 775

## 🌐 Passo 5: Configurar domínio

### Opção A: Subpasta
- URL: https://$DomainExample/sistema/public
- Nenhuma configuração adicional necessária

### Opção B: Subdomínio (recomendado)
- Crie subdomínio: sistema.$DomainExample
- Aponte para a pasta: /public_html/sistema/public
- URL final: https://sistema.$DomainExample

### Opção C: Domínio principal
- Configure Document Root para: /public_html/sistema/public
- URL: https://$DomainExample

## ✅ Passo 6: Testar instalação
1. Acesse a URL configurada
2. Você deve ver a página inicial do sistema
3. Teste cadastro de usuário
4. Teste login
5. Teste upload de arquivos
6. Verifique se não há erros 404

## 🔧 Solução de problemas comuns

### Erro 500 - Internal Server Error
- Verifique permissões dos arquivos e pastas
- Verifique se mod_rewrite está ativo
- Consulte logs de erro da hospedagem

### Erro de banco de dados
- Verifique credenciais no .env
- Teste conexão via phpMyAdmin
- Verifique se o banco foi criado

### CSS/JS não carregam
- Verifique APP_URL no .env
- Verifique se .htaccess está funcionando
- Teste sem força HTTPS temporariamente

### Upload não funciona
- Verifique permissões da pasta storage/
- Verifique limites de upload da hospedagem
- Consulte documentação da hospedagem

## 📞 Suporte
Se precisar de ajuda:
1. Verifique logs em: logs/error.log
2. Consulte documentação da sua hospedagem
3. Verifique se todos os requisitos PHP estão atendidos

## 🎉 Pronto!
Após seguir todos os passos, seu sistema estará funcionando na hospedagem.
URL de acesso: conforme configurado no Passo 5.
"@
    Set-Content -Path "$DeployDir\INSTALACAO-HOSPEDAGEM.md" -Value $InstructionsContent -Encoding UTF8

    Write-Host "6. Criando arquivo de verificação..." -ForegroundColor Yellow
    $CheckContent = @"
<?php
// Arquivo de verificação do sistema
echo "<h1>Sistema de Arquitetura - Verificação</h1>";
echo "<h2>Status do PHP:</h2>";
echo "Versão PHP: " . phpversion() . "<br>";
echo "Data/Hora: " . date('Y-m-d H:i:s') . "<br>";

echo "<h2>Extensões necessárias:</h2>";
$required = ['pdo_mysql', 'mbstring', 'curl', 'gd', 'fileinfo', 'openssl', 'zip'];
foreach ($required as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "$status $ext<br>";
}

echo "<h2>Configurações PHP:</h2>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

echo "<h2>Permissões de diretórios:</h2>";
$dirs = ['storage', 'storage/documents', 'storage/temp', 'logs'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? '✅' : '❌';
        echo "$writable $dir (permissão: $perms)<br>";
    } else {
        echo "❌ $dir (não existe)<br>";
    }
}

if (file_exists('.env')) {
    echo "<h2>Arquivo .env:</h2>";
    echo "✅ Arquivo .env encontrado<br>";
} else {
    echo "<h2>Arquivo .env:</h2>";
    echo "❌ Arquivo .env não encontrado!<br>";
    echo "Renomeie .env.hospedagem para .env<br>";
}
?>
"@
    Set-Content -Path "$DeployDir\public\verificar.php" -Value $CheckContent -Encoding UTF8

    Write-Host "7. Compactando arquivos..." -ForegroundColor Yellow
    Compress-Archive -Path "$DeployDir\*" -DestinationPath $ZipFile -Force

    Write-Host "8. Limpando arquivos temporários..." -ForegroundColor Yellow
    Remove-Item $DeployDir -Recurse -Force

    $FileSize = [math]::Round((Get-Item $ZipFile).Length / 1MB, 2)

    Write-Host "`n===============================================" -ForegroundColor Green
    Write-Host "Deploy para hospedagem preparado com sucesso!" -ForegroundColor Green
    Write-Host "===============================================" -ForegroundColor Green
    Write-Host "Arquivo criado: $ZipFile ($FileSize MB)" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "PRÓXIMOS PASSOS:" -ForegroundColor Yellow
    Write-Host "1. Faça upload do arquivo ZIP para sua hospedagem" -ForegroundColor White
    Write-Host "2. Extraia na pasta pública (public_html)" -ForegroundColor White
    Write-Host "3. Siga as instruções em INSTALACAO-HOSPEDAGEM.md" -ForegroundColor White
    Write-Host "4. Configure o arquivo .env com dados reais" -ForegroundColor White
    Write-Host "5. Teste com: https://seudominio.com/sistema/public/verificar.php" -ForegroundColor White
    Write-Host "===============================================" -ForegroundColor Green

} catch {
    Write-Host "ERRO durante a preparação!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}
