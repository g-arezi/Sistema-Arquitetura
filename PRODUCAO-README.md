# 🏭 Sistema de Arquitetura - Configuração de Produção

Este documento contém informações essenciais para a configuração e manutenção do Sistema de Arquitetura em ambiente de produção.

## 📁 Estrutura de Arquivos de Produção

```
/var/www/html/                          # Diretório principal
├── public/                             # Document root do servidor web
│   ├── .htaccess                      # Configurações Apache (com segurança)
│   ├── index.php                     # Entry point (detecta ambiente)
│   ├── css/style.css                 # Tema escuro aplicado
│   ├── js/app.js                     # JavaScript com funções de produção
│   └── uploads/                      # Uploads de usuários (755)
├── config/
│   ├── production.php                # Configuração principal de produção
│   └── nginx-site.conf               # Configuração Nginx (opcional)
├── scripts/
│   ├── deploy.sh                     # Script de deploy automatizado
│   ├── backup.sh                     # Script de backup automatizado
│   └── health-check.sh               # Script de verificação de saúde
├── storage/
│   ├── documents/                    # Documentos do sistema (755)
│   └── temp/                         # Arquivos temporários (755)
├── .env.production                   # Variáveis de ambiente (configure!)
└── DEPLOY-PRODUCAO.md               # Guia completo de deploy
```

## ⚙️ Configuração Rápida

### 1. Clone e Configure
```bash
# Clonar repositório
git clone https://github.com/seu-usuario/sistema-arquitetura.git /var/www/html
cd /var/www/html

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Configurar ambiente
cp .env.production .env.production.local
nano .env.production.local  # Configure suas credenciais
```

### 2. Deploy Automatizado
```bash
# Executar script de deploy
chmod +x scripts/deploy.sh
sudo ./scripts/deploy.sh
```

### 3. Verificação de Saúde
```bash
# Executar health check
chmod +x scripts/health-check.sh
./scripts/health-check.sh
```

## 🔧 Comandos Úteis de Produção

### Composer Scripts
```bash
composer run prod-install        # Instalar dependências de produção
composer run deploy             # Executar deploy
composer run backup             # Executar backup
composer run clear-cache        # Limpar cache
composer run security-check     # Verificar segurança
```

### Manutenção
```bash
# Atualizar sistema
git pull origin main
composer run prod-update
sudo systemctl restart apache2

# Backup manual
./scripts/backup.sh

# Verificar logs
tail -f /var/log/sistema-arquitetura/php_errors.log
tail -f /var/log/apache2/sistema-arquitetura-error.log
```

## 🛡️ Recursos de Segurança Implementados

### Headers HTTP
- ✅ **X-Frame-Options**: DENY
- ✅ **X-Content-Type-Options**: nosniff  
- ✅ **X-XSS-Protection**: 1; mode=block
- ✅ **Strict-Transport-Security**: HSTS habilitado
- ✅ **Content-Security-Policy**: CSP configurado
- ✅ **Referrer-Policy**: strict-origin-when-cross-origin

### Proteção de Arquivos
- ✅ Diretórios sensíveis protegidos (config/, vendor/, src/)
- ✅ Arquivos de configuração inacessíveis via web
- ✅ Upload de PHP bloqueado no diretório uploads/
- ✅ Arquivos de ambiente (.env) protegidos

### Configurações PHP
- ✅ `display_errors = Off`
- ✅ `expose_php = Off`
- ✅ `session.cookie_httponly = On`
- ✅ `session.cookie_secure = On`
- ✅ Logs de erro configurados

## 📊 Monitoramento

### Health Check Automático
```bash
# Adicionar ao cron para verificação a cada 30 minutos
*/30 * * * * /var/www/html/scripts/health-check.sh >> /var/log/sistema-arquitetura/cron.log 2>&1
```

### Backup Automático
```bash
# Backup diário às 2:00 AM
0 2 * * * /var/www/html/scripts/backup.sh >> /var/log/sistema-arquitetura/backup.log 2>&1
```

### Logs Importantes
```bash
# Logs da aplicação
/var/log/sistema-arquitetura/php_errors.log
/var/log/sistema-arquitetura/access.log
/var/log/sistema-arquitetura/backup.log

# Logs do servidor web
/var/log/apache2/sistema-arquitetura-error.log
/var/log/apache2/sistema-arquitetura-access.log
```

## 🔄 Processo de Deploy

### Deploy Manual
1. **Backup**: `./scripts/backup.sh`
2. **Update**: `git pull origin main`
3. **Dependencies**: `composer run prod-install`
4. **Permissions**: Verificar permissões
5. **Restart**: `sudo systemctl restart apache2`
6. **Test**: `./scripts/health-check.sh`

### Deploy Automatizado
```bash
# Script completo com backup, atualização e testes
sudo ./scripts/deploy.sh
```

## 🚨 Troubleshooting

### Problemas Comuns

#### Site não carrega (Error 500)
```bash
# Verificar logs
tail -f /var/log/apache2/sistema-arquitetura-error.log
tail -f /var/log/sistema-arquitetura/php_errors.log

# Verificar permissões
ls -la /var/www/html/public/
sudo chown -R www-data:www-data /var/www/html/
```

#### Erro de conexão com banco
```bash
# Testar conexão
mysql -u usuario -p banco_de_dados

# Verificar configurações
cat /var/www/html/.env.production.local
```

#### Upload não funciona
```bash
# Verificar diretório uploads
ls -la /var/www/html/public/uploads/
sudo chmod 755 /var/www/html/public/uploads/

# Verificar configurações PHP
php -ini | grep upload
```

### Comandos de Diagnóstico
```bash
# Status dos serviços
systemctl status apache2
systemctl status mysql
systemctl status fail2ban

# Espaço em disco
df -h

# Uso de memória
free -m

# Health check completo
./scripts/health-check.sh
```

## 🔐 Variáveis de Ambiente Críticas

Configure estas variáveis no arquivo `.env.production.local`:

```bash
# CRÍTICO: Configure antes do primeiro deploy!
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com.br

# Banco de dados
DB_HOST=localhost
DB_NAME=sistema_arquitetura
DB_USER=seu_usuario
DB_PASS=senha_super_segura

# Email
MAIL_HOST=smtp.seu-provedor.com
MAIL_USERNAME=noreply@seu-dominio.com.br
MAIL_PASSWORD=senha_email_segura

# Segurança
SESSION_SECURE=true
HASH_COST=12
```

## 📞 Contatos de Emergência

**Sistema em Produção:**
- 🌐 URL: https://seu-dominio.com.br
- 📧 Email: suporte@seu-dominio.com.br
- 📱 WhatsApp: +55 (11) 99999-9999

**Equipe Técnica:**
- 👨‍💻 Desenvolvedor: dev@empresa.com
- 🔧 DevOps: devops@empresa.com
- 🏢 Suporte: suporte@empresa.com

## 📚 Documentação Adicional

- 📖 **[DEPLOY-PRODUCAO.md](DEPLOY-PRODUCAO.md)**: Guia completo de deploy
- ✅ **[CHECKLIST-DEPLOY.md](CHECKLIST-DEPLOY.md)**: Checklist de deploy
- 🏥 **Health Check**: `./scripts/health-check.sh`
- 💾 **Backup**: `./scripts/backup.sh`

---

**⚠️ IMPORTANTE**: Sempre faça backup antes de qualquer alteração em produção!

**🔒 SEGURANÇA**: Nunca commite arquivos `.env.production.local` com dados reais!

**📊 MONITORAMENTO**: Execute health check regularmente para garantir a saúde do sistema!
