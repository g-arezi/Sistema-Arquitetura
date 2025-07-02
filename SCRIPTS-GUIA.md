# 🎯 Scripts de Deploy - Guia Rápido

## 📝 Scripts Disponíveis

### 🖥️ **Windows**

#### Para Desenvolvimento/Teste (Sem Admin)
```bash
# Via Composer
composer run deploy-test

# Direto
powershell -ExecutionPolicy RemoteSigned -File scripts\deploy-test.ps1
```

#### Para Produção IIS (Requer Admin)
```bash
# CMD como Administrador
scripts\deploy-iis.bat

# PowerShell como Administrador  
powershell -ExecutionPolicy RemoteSigned -File scripts\deploy-iis.ps1

# Via Composer (PowerShell como Admin)
composer run deploy-windows-ps
```

### 🐧 **Linux**

#### Para Produção
```bash
# Via Composer
composer run deploy

# Direto
sudo ./scripts/deploy.sh
```

### 🔍 **Health Check**

#### Windows
```bash
# CMD
scripts\health-check.bat

# PowerShell
scripts\health-check.ps1

# Via Composer
composer run health-check-windows-ps
```

#### Linux
```bash
# Via Composer
composer run health-check

# Direto
./scripts/health-check.sh
```

### 💾 **Backup**

#### Windows
```bash
# CMD
scripts\backup.bat

# PowerShell
scripts\backup.ps1

# Via Composer
composer run backup-windows-ps
```

#### Linux
```bash
# Via Composer
composer run backup

# Direto
./scripts/backup.sh
```

## 🚀 **Início Rápido**

### Desenvolvimento Local (XAMPP/PHP Server)
```bash
# 1. Clonar projeto
git clone [repo-url]
cd sistema-arquitetura

# 2. Instalar dependências
composer install

# 3. Configurar ambiente
copy .env.windows .env
# Editar .env com suas configurações

# 4. Iniciar servidor
composer run serve
# Ou: php -S localhost:8000 -t public public/router.php
```

### Teste de Deploy (Windows, sem IIS)
```bash
# 1. Executar deploy de teste
composer run deploy-test

# 2. Testar na pasta criada
cd deploy-test
php -S localhost:8000 -t public

# 3. Acessar http://localhost:8000
```

### Produção Windows IIS
```bash
# 1. PowerShell como Administrador
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# 2. Executar deploy
.\scripts\deploy-iis.ps1

# 3. Configurar .env de produção
# 4. Acessar http://localhost ou seu domínio
```

### Produção Linux
```bash
# 1. Executar deploy
sudo ./scripts/deploy.sh

# 2. Configurar .env de produção
# 3. Configurar SSL/domínio
# 4. Acessar https://seu-dominio.com
```

## ⚠️ **Resolução de Problemas**

### Erro PowerShell: "execution policy"
```powershell
# Executar como Administrador
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Erro PowerShell: "string terminator"
- Use o script `deploy-test.ps1` ou `deploy-simple.ps1`
- Scripts foram corrigidos para evitar problemas de encoding

### Erro: "Access Denied" (Windows)
```bash
# Executar como Administrador
# Verificar permissões IIS_IUSRS
icacls pasta /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### Erro: Composer "clear-cache override"
- Isso é um aviso, não um erro
- O script continua funcionando normalmente

## 📊 **Status dos Scripts**

| Script | Windows | Linux | Status |
|--------|---------|-------|--------|
| deploy-iis.ps1 | ✅ | ❌ | Funcionando (requer Admin) |
| deploy-test.ps1 | ✅ | ❌ | Funcionando (sem Admin) |
| deploy-iis.bat | ✅ | ❌ | Funcionando (requer Admin) |
| deploy.sh | ❌ | ✅ | Funcionando |
| health-check.ps1 | ✅ | ❌ | Funcionando |
| health-check.bat | ✅ | ❌ | Funcionando |
| backup.ps1 | ✅ | ❌ | Funcionando |
| backup.bat | ✅ | ❌ | Funcionando |

## 🎯 **Recomendações**

### Para Desenvolvimento
- Use `composer run serve` (mais simples)
- Ou `composer run deploy-test` (testa deploy)

### Para Produção Windows
- Use `scripts\deploy-iis.ps1` (PowerShell como Admin)
- Configure SSL através do IIS Manager
- Configure agendamento de backup

### Para Produção Linux
- Use `scripts/deploy.sh` (como sudo)
- Configure SSL via Let's Encrypt
- Configure cron para backup automático

---

**Todos os scripts foram testados e estão funcionando corretamente! 🎉**
