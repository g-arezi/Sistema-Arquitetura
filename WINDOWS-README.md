# 🪟 Sistema de Arquitetura - Windows Server

Guia completo para configuração e deploy em ambiente Windows Server com IIS.

## 📋 Índice

1. [Requisitos do Sistema](#requisitos-do-sistema)
2. [Instalação Rápida](#instalação-rápida)
3. [Configuração Manual](#configuração-manual)
4. [Scripts de Automação](#scripts-de-automação)
5. [Monitoramento](#monitoramento)
6. [Backup e Restauração](#backup-e-restauração)
7. [Troubleshooting](#troubleshooting)
8. [Segurança](#segurança)

## 🖥️ Requisitos do Sistema

### Hardware Mínimo
- **CPU**: 2 cores
- **RAM**: 4GB
- **Disco**: 20GB livres
- **Rede**: Conexão de internet para downloads

### Software
- **Sistema**: Windows Server 2016+ ou Windows 10+ (com IIS)
- **IIS**: 10.0+ com FastCGI
- **PHP**: 8.0+ (recomendado 8.2+)
- **MySQL**: 8.0+ ou MariaDB 10.5+
- **Composer**: Última versão

### Extensões PHP Obrigatórias
```
php_pdo_mysql
php_mbstring
php_curl
php_gd
php_fileinfo
php_openssl
php_zip
php_json
```

## 🚀 Instalação Rápida

### Opção 1: Script Automático (Recomendado)

#### PowerShell (Administrador)
```powershell
# Clone o repositório
git clone https://github.com/seu-usuario/sistema-arquitetura.git
cd sistema-arquitetura

# Execute o deploy
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\scripts\deploy-iis.ps1
```

#### Command Prompt (Administrador)
```cmd
# Clone o repositório
git clone https://github.com/seu-usuario/sistema-arquitetura.git
cd sistema-arquitetura

# Execute o deploy
scripts\deploy-iis.bat
```

### Opção 2: XAMPP (Desenvolvimento)
```powershell
# Para ambiente de desenvolvimento local
# 1. Instale o XAMPP
# 2. Clone o projeto em C:\xampp\htdocs\
# 3. Configure o .env
# 4. Acesse http://localhost/sistema-arquitetura/public
```

## ⚙️ Configuração Manual

### 1. Preparar Ambiente

#### Habilitar IIS e FastCGI
```powershell
# PowerShell como Administrador
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole, IIS-WebServer, IIS-CommonHttpFeatures, IIS-HttpErrors, IIS-HttpLogging, IIS-RequestFiltering, IIS-StaticContent, IIS-Security, IIS-DefaultDocument, IIS-DirectoryBrowsing, IIS-CGI -All
```

#### Instalar PHP
1. Baixar PHP para Windows: https://windows.php.net/download/
2. Extrair para `C:\PHP`
3. Configurar `php.ini`:
```ini
extension_dir = "C:\PHP\ext"
extension=pdo_mysql
extension=mbstring
extension=curl
extension=gd
extension=fileinfo
extension=openssl
extension=zip
```

#### Configurar FastCGI no IIS
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe set config -section:system.webServer/fastCgi /+[fullPath='C:\PHP\php-cgi.exe']
%WINDIR%\System32\inetsrv\appcmd.exe set config -section:system.webServer/handlers /+[name='PHP_via_FastCGI',path='*.php',verb='*',modules='FastCgiModule',scriptProcessor='C:\PHP\php-cgi.exe',resourceType='File']
```

### 2. Preparar Projeto

#### Criar Diretórios
```cmd
mkdir C:\inetpub\wwwroot\sistema-arquitetura
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage\documents
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage\temp
mkdir C:\inetpub\wwwroot\sistema-arquitetura\logs
mkdir C:\backups\sistema-arquitetura
```

#### Copiar Arquivos
```cmd
# Copiar todos os arquivos do projeto
xcopy ".\*" "C:\inetpub\wwwroot\sistema-arquitetura\" /E /I /Y
```

#### Configurar Permissões
```cmd
icacls "C:\inetpub\wwwroot\sistema-arquitetura\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\sistema-arquitetura\logs" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\sistema-arquitetura\public\uploads" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 3. Configurar IIS

#### Application Pool
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe add apppool /name:"sistema-arquitetura-pool"
%WINDIR%\System32\inetsrv\appcmd.exe set apppool "sistema-arquitetura-pool" /processModel.identityType:ApplicationPoolIdentity
%WINDIR%\System32\inetsrv\appcmd.exe set apppool "sistema-arquitetura-pool" /managedRuntimeVersion:""
```

#### Site
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe add site /name:"sistema-arquitetura" /physicalPath:"C:\inetpub\wwwroot\sistema-arquitetura\public" /bindings:"http/*:80:,https/*:443:"
%WINDIR%\System32\inetsrv\appcmd.exe set site "sistema-arquitetura" /applicationDefaults.applicationPool:"sistema-arquitetura-pool"
```

### 4. Configurar Ambiente

#### Arquivo .env
```bash
# Copiar arquivo de exemplo
copy .env.windows .env

# Editar configurações
notepad .env
```

#### Configurações Importantes
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

DB_HOST=localhost
DB_NAME=sistema_arquitetura
DB_USER=sa
DB_PASS=sua_senha_aqui

STORAGE_DOCUMENTS_PATH=C:\inetpub\wwwroot\sistema-arquitetura\storage\documents
```

## 🤖 Scripts de Automação

### Deploy
```cmd
# CMD
scripts\deploy-iis.bat

# PowerShell
scripts\deploy-iis.ps1
```

### Backup
```cmd
# CMD
scripts\backup.bat

# PowerShell
scripts\backup.ps1
```

### Health Check
```cmd
# CMD
scripts\health-check.bat

# PowerShell
scripts\health-check.ps1
```

### Agendamento (Task Scheduler)
```cmd
# Backup diário às 02:00
schtasks /create /tn "Sistema Arquitetura Backup" /tr "C:\inetpub\wwwroot\sistema-arquitetura\scripts\backup.bat" /sc daily /st 02:00

# Health check a cada hora
schtasks /create /tn "Sistema Arquitetura Health Check" /tr "C:\inetpub\wwwroot\sistema-arquitetura\scripts\health-check.bat" /sc hourly
```

## 📊 Monitoramento

### Logs do Sistema
- **IIS**: `C:\inetpub\logs\LogFiles\`
- **Aplicação**: `C:\inetpub\wwwroot\sistema-arquitetura\logs\`
- **PHP**: Configurado no `php.ini`

### Performance Counters
```powershell
# Monitorar Application Pool
Get-Counter "\Process(w3wp*)\% Processor Time"
Get-Counter "\Process(w3wp*)\Working Set"
```

### Alertas (PowerShell)
```powershell
# Script de monitoramento personalizado
$HealthCheck = .\scripts\health-check.ps1
if ($HealthCheck -gt 0) {
    # Enviar email de alerta
    Send-MailMessage -To "admin@empresa.com" -Subject "Sistema com problemas" -Body "Health check falhou"
}
```

## 💾 Backup e Restauração

### Backup Automático
```cmd
# Configurar backup diário
scripts\backup.bat
```

### Restauração
```cmd
# Parar serviços
iisreset /stop

# Restaurar arquivos
xcopy "C:\backups\sistema-arquitetura\backup_2024-01-01" "C:\inetpub\wwwroot\sistema-arquitetura\" /E /I /Y

# Restaurar banco de dados
mysql -u root -p sistema_arquitetura < backup_database.sql

# Reiniciar serviços
iisreset /start
```

## 🔧 Troubleshooting

### Erro 500 - Internal Server Error
```cmd
# Verificar logs
type C:\inetpub\wwwroot\sistema-arquitetura\logs\error.log

# Verificar configuração PHP
php -m

# Testar FastCGI
%WINDIR%\System32\inetsrv\appcmd.exe list config -section:system.webServer/fastCgi
```

### Erro 404 - Not Found
```cmd
# Verificar URL Rewrite
%WINDIR%\System32\inetsrv\appcmd.exe list config -section:system.webServer/rewrite/rules

# Verificar web.config
type C:\inetpub\wwwroot\sistema-arquitetura\web.config
```

### Problemas de Upload
```cmd
# Verificar permissões
icacls "C:\inetpub\wwwroot\sistema-arquitetura\public\uploads"

# Verificar configuração PHP
php -i | findstr upload
```

### Problema de Conectividade com Banco
```powershell
# Testar conexão
Test-NetConnection -ComputerName localhost -Port 3306

# Verificar serviço MySQL
Get-Service -Name "*mysql*"
```

## 🔒 Segurança

### SSL/TLS
```cmd
# Instalar certificado
certlm.msc

# Configurar binding HTTPS
%WINDIR%\System32\inetsrv\appcmd.exe set site "sistema-arquitetura" /+bindings.[protocol='https',bindingInformation='*:443:']
```

### Firewall
```powershell
# Abrir portas necessárias
New-NetFirewallRule -DisplayName "HTTP" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow
New-NetFirewallRule -DisplayName "HTTPS" -Direction Inbound -Protocol TCP -LocalPort 443 -Action Allow
```

### Hardening
- Desabilitar versões PHP desnecessárias
- Configurar request filtering no IIS
- Implementar rate limiting
- Configurar headers de segurança no web.config
- Monitorar logs de segurança

## 📞 Suporte

### Comandos Úteis
```cmd
# Status do IIS
iisreset /status

# Listar sites
%WINDIR%\System32\inetsrv\appcmd.exe list site

# Verificar PHP
php -v
php -m

# Logs em tempo real
powershell "Get-Content C:\inetpub\wwwroot\sistema-arquitetura\logs\error.log -Wait"
```

### Performance
```powershell
# Limpar cache
php -r "opcache_reset();"

# Otimizar Composer
composer dump-autoload --optimize

# Verificar uso de recursos
Get-Process | Where-Object {$_.ProcessName -like "*php*" -or $_.ProcessName -like "*w3wp*"}
```

---

## 📄 Documentação Adicional

- [DEPLOY-WINDOWS-IIS.md](DEPLOY-WINDOWS-IIS.md) - Instruções detalhadas de deploy
- [DEPLOY-PRODUCAO.md](DEPLOY-PRODUCAO.md) - Guia geral de produção
- [CHECKLIST-DEPLOY.md](CHECKLIST-DEPLOY.md) - Checklist de deploy

## 🆘 Em Caso de Problemas

1. Execute o health check: `scripts\health-check.ps1`
2. Verifique os logs em `C:\inetpub\wwwroot\sistema-arquitetura\logs\`
3. Consulte a documentação específica do erro
4. Entre em contato com o suporte técnico
