# Deploy em Windows Server/IIS

## Requisitos

### Servidor Windows
- Windows Server 2016+ ou Windows 10+ (com IIS)
- IIS com PHP configurado
- PHP 8.0+ com extensões necessárias
- Composer instalado
- Acesso de administrador

### Extensões PHP Necessárias
```
php_pdo
php_pdo_mysql
php_curl
php_gd
php_mbstring
php_openssl
php_zip
php_fileinfo
```

## Preparação do Ambiente

### 1. Instalar IIS
```powershell
# PowerShell como Administrador
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole, IIS-WebServer, IIS-CommonHttpFeatures, IIS-HttpErrors, IIS-HttpLogging, IIS-RequestFiltering, IIS-StaticContent, IIS-Security, IIS-DefaultDocument, IIS-DirectoryBrowsing -All
```

### 2. Instalar PHP
1. Baixar PHP para Windows: https://windows.php.net/download/
2. Extrair para `C:\PHP`
3. Configurar no IIS via FastCGI

### 3. Instalar Composer
1. Baixar: https://getcomposer.org/download/
2. Instalar globalmente ou colocar em `C:\PHP\composer.phar`

## Deploy Automático

### Opção 1: Script CMD (deploy-iis.bat)
```cmd
# Como Administrador
scripts\deploy-iis.bat
```

### Opção 2: Script PowerShell (deploy-iis.ps1)
```powershell
# PowerShell como Administrador
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\scripts\deploy-iis.ps1
```

### Opção 3: Deploy Customizado
```powershell
# Com parâmetros personalizados
.\scripts\deploy-iis.ps1 -SiteName "meu-site" -SitePath "C:\sites\meu-site" -SkipBackup
```

## Deploy Manual

### 1. Preparar Diretórios
```cmd
mkdir C:\inetpub\wwwroot\sistema-arquitetura
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage\documents
mkdir C:\inetpub\wwwroot\sistema-arquitetura\storage\temp
mkdir C:\inetpub\wwwroot\sistema-arquitetura\logs
mkdir C:\inetpub\logs\sistema-arquitetura
mkdir C:\backups\sistema-arquitetura
```

### 2. Copiar Arquivos
- Copiar todos os arquivos do projeto para `C:\inetpub\wwwroot\sistema-arquitetura`
- Excluir: `.git`, `node_modules`, `vendor`, arquivos `.md`

### 3. Configurar Permissões
```cmd
icacls "C:\inetpub\wwwroot\sistema-arquitetura\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\sistema-arquitetura\logs" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\sistema-arquitetura\public\uploads" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 4. Instalar Dependências
```cmd
cd C:\inetpub\wwwroot\sistema-arquitetura
composer install --no-dev --optimize-autoloader
```

### 5. Configurar IIS

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

### 6. Configurar Ambiente
- Copiar `.env.windows` para `.env`
- Ajustar configurações de banco de dados e email
- Verificar caminhos dos diretórios

## Configuração SSL

### Certificate Store (Certificado Instalado)
1. Instalar certificado no Windows Certificate Store
2. Configurar binding HTTPS no IIS:
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe set site "sistema-arquitetura" /bindings:"https/*:443:"
```

### Let's Encrypt (Certificado Gratuito)
1. Instalar win-acme: https://www.win-acme.com/
2. Executar para obter certificado
3. Configurar renovação automática

## Monitoramento

### Logs do IIS
- Logs do site: `C:\inetpub\logs\LogFiles\`
- Logs de erro: `C:\inetpub\logs\sistema-arquitetura\`
- Logs da aplicação: `C:\inetpub\wwwroot\sistema-arquitetura\logs\`

### Performance Counters
- Monitorar Application Pool
- Monitorar uso de CPU/Memória
- Configurar alertas se necessário

## Backup Automático

### Script PowerShell (Task Scheduler)
```powershell
# Criar task no Windows Task Scheduler
schtasks /create /tn "Sistema Arquitetura Backup" /tr "powershell.exe -File C:\scripts\backup.ps1" /sc daily /st 02:00
```

### Backup Manual
```cmd
scripts\backup.bat
```

## Troubleshooting

### Problema: Erro 500
- Verificar logs em `C:\inetpub\wwwroot\sistema-arquitetura\logs\`
- Verificar configuração PHP
- Verificar permissões de arquivos

### Problema: Arquivo não encontrado
- Verificar web.config
- Verificar URL Rewrite module
- Verificar documento padrão (index.php)

### Problema: Upload não funciona
- Verificar permissões da pasta uploads
- Verificar configuração PHP (upload_max_filesize)
- Verificar configuração IIS (request limits)

### Problema: Erro de banco de dados
- Verificar configuração em .env
- Verificar conectividade com MySQL
- Verificar credenciais de banco

## URLs Importantes
- Site: `http://localhost` ou seu domínio
- Admin: `http://localhost/admin`
- Logs de erro: `C:\inetpub\logs\sistema-arquitetura\error.log`

## Comandos Úteis

### Reiniciar serviços
```cmd
iisreset /noforce
%WINDIR%\System32\inetsrv\appcmd.exe stop apppool "sistema-arquitetura-pool"
%WINDIR%\System32\inetsrv\appcmd.exe start apppool "sistema-arquitetura-pool"
```

### Listar sites
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe list site
%WINDIR%\System32\inetsrv\appcmd.exe list apppool
```

### Verificar configuração
```cmd
%WINDIR%\System32\inetsrv\appcmd.exe list config
```
