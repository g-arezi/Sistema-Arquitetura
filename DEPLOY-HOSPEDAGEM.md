# 🌐 **Guia de Deploy para Hospedagem (Host)**

## 🎯 **Tipos de Hospedagem Suportados**

### 1. **🏢 Hospedagem Compartilhada** (cPanel, Hostinger, etc.)
### 2. **☁️ Cloud/VPS** (AWS, DigitalOcean, Vultr, etc.)
### 3. **🪟 Windows Server** (Hospedagem Windows)
### 4. **🐧 Linux Server** (Ubuntu, CentOS)

---

## 🏢 **HOSPEDAGEM COMPARTILHADA (Mais Comum)**

### **📋 Pré-requisitos da Hospedagem:**
- ✅ PHP 8.0+ 
- ✅ MySQL 5.7+
- ✅ Composer (ou possibilidade de upload vendor/)
- ✅ mod_rewrite habilitado
- ✅ Extensões: pdo_mysql, mbstring, curl, gd, fileinfo

### **📤 Processo de Upload:**

#### **Passo 1: Preparar arquivos para upload**
```bash
# No seu computador, execute:
composer run prod-install
composer run deploy-test

# Isto criará uma versão otimizada
```

#### **Passo 2: Compactar projeto**
```bash
# Criar arquivo ZIP com apenas os arquivos necessários
# Incluir: public/, src/, config/, vendor/, composer.json, .htaccess
# EXCLUIR: .git/, node_modules/, scripts/, *.md, .env (criar novo no host)
```

#### **Passo 3: Upload via cPanel/FTP**
```
1. Acesse cPanel da sua hospedagem
2. Vá em "Gerenciador de Arquivos" 
3. Entre na pasta public_html/ (ou www/)
4. Extraia o ZIP dentro de uma subpasta (ex: public_html/sistema/)
5. Configure permissões 755 para pastas e 644 para arquivos
```

#### **Passo 4: Configurar .env de produção**
```bash
# Criar arquivo .env na raiz do projeto no host
# Copiar base do .env.production e ajustar:

APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br/sistema

# Banco de dados (fornecido pela hospedagem)
DB_HOST=localhost
DB_NAME=seu_usuario_dbname
DB_USER=seu_usuario_db
DB_PASS=senha_fornecida_host

# Ajustar caminhos para hospedagem compartilhada
STORAGE_DOCUMENTS_PATH=../storage/documents
STORAGE_TEMP_PATH=../storage/temp
ERROR_LOG_PATH=../logs/error.log
```

#### **Passo 5: Configurar banco de dados**
```
1. No cPanel, vá em "Bancos de dados MySQL"
2. Crie um banco: seu_usuario_sistema
3. Crie usuário e dê permissões totais
4. Importe estrutura SQL se houver
5. Anote credenciais para o .env
```

#### **Passo 6: Configurar domínio/subdomínio**
```
Opção A - Subpasta:
https://seudominio.com.br/sistema/public

Opção B - Subdomínio:
sistema.seudominio.com.br -> apontar para /public_html/sistema/public
```

---

## ☁️ **CLOUD/VPS (AWS, DigitalOcean, etc.)**

### **🚀 Deploy Automatizado:**

#### **Passo 1: Conectar ao servidor**
```bash
ssh usuario@ip-do-servidor
```

#### **Passo 2: Instalar dependências**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 php php-mysql php-mbstring php-curl php-gd php-zip git composer mysql-server -y

# CentOS/RHEL
sudo yum install httpd php php-mysql php-mbstring php-curl php-gd php-zip git mysql-server -y
```

#### **Passo 3: Clonar e configurar projeto**
```bash
cd /var/www/html
sudo git clone https://github.com/seu-usuario/sistema-arquitetura.git
cd sistema-arquitetura

# Executar deploy para Linux
sudo ./scripts/deploy.sh

# Ou usar o script já criado:
sudo composer run deploy
```

#### **Passo 4: Configurar Apache Virtual Host**
```bash
# Criar arquivo de configuração
sudo nano /etc/apache2/sites-available/sistema-arquitetura.conf

# Conteúdo:
<VirtualHost *:80>
    ServerName seudominio.com.br
    DocumentRoot /var/www/html/sistema-arquitetura/public
    
    <Directory /var/www/html/sistema-arquitetura/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sistema-error.log
    CustomLog ${APACHE_LOG_DIR}/sistema-access.log combined
</VirtualHost>

# Habilitar site
sudo a2ensite sistema-arquitetura.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### **Passo 5: Configurar SSL (Let's Encrypt)**
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d seudominio.com.br
```

---

## 🏗️ **SCRIPTS DE DEPLOY PARA HOST**

Vou criar scripts específicos para hospedagem:

### **Script para Hospedagem Compartilhada:**
```bash
# deploy-shared-hosting.sh
#!/bin/bash
echo "Preparando arquivos para hospedagem compartilhada..."

# Criar diretório temporário
mkdir -p deploy-package
cd deploy-package

# Copiar arquivos necessários
cp -r ../public ./
cp -r ../src ./
cp -r ../config ./
cp -r ../vendor ./
cp ../composer.json ./
cp ../.htaccess ./

# Criar .env de exemplo
cp ../.env.production .env.example

# Criar estrutura de diretórios
mkdir -p storage/documents
mkdir -p storage/temp
mkdir -p logs

# Criar arquivo ZIP
zip -r ../sistema-arquitetura-host.zip ./*

cd ..
rm -rf deploy-package

echo "Arquivo criado: sistema-arquitetura-host.zip"
echo "Upload este arquivo para sua hospedagem!"
```

### **Script para VPS/Cloud:**
```bash
# deploy-cloud.sh
#!/bin/bash
echo "Deploy para VPS/Cloud..."

# Atualizar sistema
apt update && apt upgrade -y

# Instalar dependências
apt install apache2 php php-mysql php-mbstring php-curl php-gd php-zip php-xml composer git mysql-server -y

# Configurar diretórios
mkdir -p /var/www/html/sistema-arquitetura
cd /var/www/html/sistema-arquitetura

# Clonar projeto (substitua pela sua URL)
git clone https://github.com/seu-usuario/sistema-arquitetura.git .

# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Configurar permissões
chown -R www-data:www-data /var/www/html/sistema-arquitetura
chmod -R 755 /var/www/html/sistema-arquitetura
chmod -R 775 storage logs

echo "Deploy concluído! Configure o .env e Virtual Host"
```

---

## 📋 **CHECKLIST DE DEPLOY PARA HOST**

### **✅ Antes do Upload:**
- [ ] Executar `composer run prod-install`
- [ ] Testar sistema localmente
- [ ] Preparar arquivo .env de produção
- [ ] Criar backup do projeto atual
- [ ] Verificar extensões PHP da hospedagem

### **✅ Durante o Upload:**
- [ ] Upload dos arquivos corretos (sem .git, scripts, etc.)
- [ ] Configurar permissões (755 para pastas, 644 para arquivos)
- [ ] Criar banco de dados na hospedagem
- [ ] Configurar .env com dados reais

### **✅ Após o Upload:**
- [ ] Testar URL principal
- [ ] Testar login/cadastro
- [ ] Testar upload de arquivos
- [ ] Verificar logs de erro
- [ ] Configurar SSL se disponível

---

## 🛠️ **HOSPEDAGENS ESPECÍFICAS**

### **Hostinger:**
```
1. Upload via File Manager
2. Extrair ZIP em public_html/
3. Banco: MySQL via hPanel
4. SSL: Grátis Let's Encrypt
5. .htaccess: Automaticamente reconhecido
```

### **HostGator:**
```
1. Upload via cPanel File Manager
2. Configurar subdomínio se necessário
3. MySQL via cPanel
4. Certificado SSL disponível
```

### **GoDaddy:**
```
1. Upload via FTP ou File Manager
2. Banco via MySQL Databases
3. Pode precisar ajustar .htaccess
4. SSL pago ou Let's Encrypt
```

### **AWS/DigitalOcean:**
```
1. Usar scripts de deploy automatizado
2. Configurar Load Balancer se necessário
3. RDS para banco de dados
4. CloudFlare para CDN/SSL
```

---

## 🔧 **PROBLEMAS COMUNS E SOLUÇÕES**

### **Erro 500 - Internal Server Error:**
```
Causa: Permissões ou .htaccess
Solução: 
- chmod 755 nas pastas
- chmod 644 nos arquivos
- Verificar se mod_rewrite está ativo
```

### **Erro de Banco de Dados:**
```
Causa: Credenciais incorretas no .env
Solução:
- Verificar host, user, password, database
- Testar conexão via phpMyAdmin
```

### **Arquivos não carregam:**
```
Causa: Caminhos incorretos
Solução:
- Ajustar BASE_URL no .env
- Verificar caminhos relativos
```

### **CSS/JS não carrega:**
```
Causa: Problema de caminho ou .htaccess
Solução:
- Verificar APP_URL no .env
- Ajustar caminhos no HTML
```

---

Quer que eu crie os scripts específicos para sua hospedagem ou precisa de ajuda com alguma hospedagem específica?
