# 🚀 Guia de Deploy para Produção - Sistema de Arquitetura

Este documento contém todas as instruções necessárias para colocar o Sistema de Arquitetura em produção.

## 🎯 Escolha da Plataforma

### 🐧 Linux (Ubuntu/CentOS)
- **Recomendado para**: Servidores dedicados, VPS, cloud
- **Servidor Web**: Apache/Nginx
- **Documentação**: Veja seções abaixo

### 🪟 Windows Server/IIS
- **Recomendado para**: Ambientes Windows corporativos
- **Servidor Web**: IIS com FastCGI
- **Documentação**: [DEPLOY-WINDOWS-IIS.md](DEPLOY-WINDOWS-IIS.md)

### � Docker
- **Recomendado para**: Containers, microserviços
- **Plataforma**: Docker Compose
- **Documentação**: Em desenvolvimento

---

## �📋 Pré-requisitos Linux

### Servidor
- **Sistema Operacional**: Linux (Ubuntu 20.04+ recomendado)  
- **Servidor Web**: Apache 2.4+ ou Nginx 1.18+
- **PHP**: 7.4+ ou 8.0+ (recomendado)
- **Banco de dados**: MySQL 8.0+ ou MariaDB 10.5+
- **Memória RAM**: Mínimo 2GB, recomendado 4GB+
- **Espaço em disco**: Mínimo 10GB livres

### Extensões PHP Necessárias
```bash
php-mysql php-pdo php-mbstring php-json php-curl php-gd 
php-zip php-xml php-intl php-fileinfo php-openssl
```

### Ferramentas
- Git
- Composer
- Certbot (para SSL Let's Encrypt)

## 🔧 Preparação do Servidor Linux

### 1. Atualizar sistema
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Instalar Apache e PHP
```bash
sudo apt install apache2 php libapache2-mod-php php-mysql php-pdo php-mbstring php-json php-curl php-gd php-zip php-xml php-intl php-fileinfo php-openssl -y
```

### 3. Instalar MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 4. Instalar Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 5. Instalar Git
```bash
sudo apt install git -y
```

## 🗄️ Configuração do Banco de Dados

### 1. Criar banco e usuário
```sql
-- Conectar como root
sudo mysql -u root -p

-- Criar banco
CREATE DATABASE sistema_arquitetura CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário
CREATE USER 'sistema_user'@'localhost' IDENTIFIED BY 'SENHA_SUPER_SEGURA_AQUI';

-- Conceder permissões
GRANT ALL PRIVILEGES ON sistema_arquitetura.* TO 'sistema_user'@'localhost';

-- Aplicar mudanças
FLUSH PRIVILEGES;

-- Sair
EXIT;
```

### 2. Importar estrutura do banco
```bash
# Se você tem um dump SQL
mysql -u sistema_user -p sistema_arquitetura < database_schema.sql
```

## 📁 Deploy da Aplicação

### 1. Preparar diretório
```bash
sudo mkdir -p /var/www/html
sudo chown -R $USER:www-data /var/www/html
```

### 2. Clonar repositório
```bash
cd /var/www/html
git clone https://github.com/seu-usuario/sistema-arquitetura.git .
```

### 3. Instalar dependências
```bash
composer install --no-dev --optimize-autoloader
```

### 4. Configurar ambiente
```bash
# Copiar arquivo de configuração
cp .env.production .env.production.local

# Editar configurações (usar seu editor preferido)
nano .env.production.local
```

**Configurações importantes no .env.production.local:**
```bash
# Ambiente
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com.br

# Banco de Dados
DB_HOST=localhost
DB_NAME=sistema_arquitetura
DB_USER=sistema_user
DB_PASS=SENHA_SUPER_SEGURA_AQUI

# Email (configure com seus dados reais)
MAIL_HOST=smtp.seu-provedor.com
MAIL_USERNAME=noreply@seu-dominio.com.br
MAIL_PASSWORD=sua_senha_email

# Segurança
SESSION_SECURE=true
```

### 5. Configurar permissões
```bash
# Executar script de deploy
chmod +x scripts/deploy.sh
sudo ./scripts/deploy.sh
```

**Ou configurar manualmente:**
```bash
# Proprietário
sudo chown -R www-data:www-data /var/www/html

# Permissões de diretórios
sudo find /var/www/html -type d -exec chmod 755 {} \;

# Permissões de arquivos
sudo find /var/www/html -type f -exec chmod 644 {} \;

# Diretórios especiais
sudo mkdir -p /var/www/html/public/uploads
sudo mkdir -p /var/www/html/storage/{documents,temp}
sudo chmod -R 755 /var/www/html/public/uploads
sudo chmod -R 755 /var/www/html/storage

# Logs
sudo mkdir -p /var/log/sistema-arquitetura
sudo chmod -R 755 /var/log/sistema-arquitetura
sudo chown -R www-data:www-data /var/log/sistema-arquitetura
```

## 🌐 Configuração do Apache

### 1. Criar VirtualHost
```bash
sudo nano /etc/apache2/sites-available/sistema-arquitetura.conf
```

**Conteúdo do arquivo:**
```apache
<VirtualHost *:80>
    ServerName seu-dominio.com.br
    ServerAlias www.seu-dominio.com.br
    DocumentRoot /var/www/html/public
    
    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog /var/log/apache2/sistema-arquitetura-error.log
    CustomLog /var/log/apache2/sistema-arquitetura-access.log combined
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

### 2. Ativar site e módulos
```bash
# Ativar módulos necessários
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod ssl

# Ativar site
sudo a2ensite sistema-arquitetura.conf

# Desativar site padrão
sudo a2dissite 000-default.conf

# Reiniciar Apache
sudo systemctl restart apache2
```

## 🔒 Configuração SSL (Let's Encrypt)

### 1. Instalar Certbot
```bash
sudo apt install certbot python3-certbot-apache -y
```

### 2. Obter certificado
```bash
sudo certbot --apache -d seu-dominio.com.br -d www.seu-dominio.com.br
```

### 3. Renovação automática
```bash
# Testar renovação
sudo certbot renew --dry-run

# Adicionar ao cron
sudo crontab -e

# Adicionar linha:
0 12 * * * /usr/bin/certbot renew --quiet
```

## 📊 Monitoramento e Logs

### 1. Configurar logrotate
```bash
sudo nano /etc/logrotate.d/sistema-arquitetura
```

**Conteúdo:**
```
/var/log/sistema-arquitetura/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    copytruncate
}
```

### 2. Configurar backup automático
```bash
# Dar permissão ao script
chmod +x scripts/backup.sh

# Adicionar ao cron
sudo crontab -e

# Backup diário às 2:00 AM
0 2 * * * /var/www/html/scripts/backup.sh
```

## 🛡️ Segurança Adicional

### 1. Firewall
```bash
# Ativar UFW
sudo ufw enable

# Permitir SSH
sudo ufw allow 22

# Permitir HTTP e HTTPS
sudo ufw allow 80
sudo ufw allow 443

# Verificar status
sudo ufw status
```

### 2. Fail2Ban (proteção contra ataques)
```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Configurar PHP para produção
```bash
sudo nano /etc/php/8.0/apache2/php.ini
```

**Configurações importantes:**
```ini
display_errors = Off
log_errors = On
error_log = /var/log/sistema-arquitetura/php_errors.log
max_execution_time = 60
max_input_time = 60
memory_limit = 256M
post_max_size = 16M
upload_max_filesize = 15M
session.cookie_httponly = On
session.cookie_secure = On
expose_php = Off
```

## ✅ Verificação Final

### 1. Testar conectividade
```bash
curl -I http://seu-dominio.com.br
curl -I https://seu-dominio.com.br
```

### 2. Verificar logs
```bash
tail -f /var/log/apache2/sistema-arquitetura-error.log
tail -f /var/log/sistema-arquitetura/php_errors.log
```

### 3. Testar funcionalidades
- [ ] Página inicial carrega
- [ ] Login funciona
- [ ] Upload de arquivos funciona
- [ ] Envio de emails funciona
- [ ] Dashboard carrega corretamente

## 🔄 Manutenção Contínua

### Atualizações
```bash
# Entrar no diretório do projeto
cd /var/www/html

# Backup antes da atualização
./scripts/backup.sh

# Atualizar código
git pull origin main

# Instalar/atualizar dependências
composer install --no-dev --optimize-autoloader

# Limpar cache
composer clear-cache

# Reiniciar Apache
sudo systemctl restart apache2
```

### Monitoramento
- Configurar alertas para espaço em disco
- Monitorar logs de erro
- Verificar performance do banco de dados
- Monitorar tempo de resposta do site

## 🆘 Troubleshooting

### Problemas Comuns

**Erro 500:**
- Verificar logs do Apache: `/var/log/apache2/sistema-arquitetura-error.log`
- Verificar logs do PHP: `/var/log/sistema-arquitetura/php_errors.log`
- Verificar permissões dos arquivos

**Erro de banco de dados:**
- Verificar credenciais em `.env.production.local`
- Testar conexão: `mysql -u usuario -p banco`
- Verificar se o serviço MySQL está rodando: `sudo systemctl status mysql`

**Upload não funciona:**
- Verificar permissões do diretório `public/uploads`
- Verificar configurações PHP: `upload_max_filesize`, `post_max_size`
- Verificar espaço em disco

**Emails não são enviados:**
- Verificar configurações SMTP em `.env.production.local`
- Testar conectividade SMTP
- Verificar logs de email

## 📞 Suporte

Em caso de problemas durante o deploy:

1. Verificar logs detalhados
2. Consultar documentação do servidor web
3. Verificar configurações de PHP e MySQL
4. Contatar administrador do sistema

---

**Importante**: Sempre faça backup antes de qualquer alteração em produção!

## 📚 Links Úteis

- [Documentação do Apache](https://httpd.apache.org/docs/)
- [Documentação do PHP](https://www.php.net/docs.php)
- [Let's Encrypt](https://letsencrypt.org/)
- [Composer](https://getcomposer.org/)
- [MySQL](https://dev.mysql.com/doc/)
