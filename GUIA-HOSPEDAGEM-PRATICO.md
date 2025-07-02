# 🌐 **GUIA PRÁTICO: COMO COLOCAR O SISTEMA NO HOST**

## 🎯 **PASSO A PASSO SIMPLIFICADO**

### **1. PREPARAR O SISTEMA PARA HOSPEDAGEM**

Execute no seu computador:

```bash
# Otimizar para produção
composer run prod-install

# Criar pacote para hospedagem
composer run deploy-hospedagem
```

Isso criará um arquivo ZIP otimizado para upload.

---

### **2. HOSPEDAGEM COMPARTILHADA (Mais Comum)**

**A. Comprar hospedagem com:**
- PHP 8.0+
- MySQL 5.7+
- mod_rewrite habilitado
- Composer (ou suporte a vendor/)

**B. Fazer upload:**
1. Acesse cPanel/Painel da hospedagem
2. Vá em "Gerenciador de Arquivos"
3. Entre na pasta `public_html/`
4. Crie uma pasta `sistema/` (ou o nome que quiser)
5. Extraia o ZIP dentro desta pasta
6. Configure permissões: 755 para pastas, 644 para arquivos

**C. Configurar banco de dados:**
1. No cPanel, vá em "Bancos de dados MySQL"
2. Crie um banco: `seu_usuario_sistema`
3. Crie usuário e dê permissões totais
4. Anote: host, banco, usuário, senha

**D. Configurar .env:**
Crie arquivo `.env` na raiz do projeto no host:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br/sistema

DB_HOST=localhost
DB_NAME=seu_usuario_sistema
DB_USER=seu_usuario_db
DB_PASS=senha_do_banco

STORAGE_DOCUMENTS_PATH=../storage/documents
STORAGE_TEMP_PATH=../storage/temp
ERROR_LOG_PATH=../logs/error.log
```

**E. Testar:**
- Acesse: `https://seudominio.com.br/sistema/public`
- Ou configure subdomínio apontando para `/public`

---

### **3. VPS/CLOUD (AWS, DigitalOcean, etc.)**

**A. Conectar ao servidor:**
```bash
ssh usuario@ip-do-servidor
```

**B. Instalar dependências:**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 php php-mysql php-mbstring php-curl php-gd php-zip git composer mysql-server -y

# Habilitar mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**C. Fazer deploy:**
```bash
cd /var/www/html
sudo git clone https://github.com/seu-usuario/sistema-arquitetura.git
cd sistema-arquitetura

# Executar deploy
sudo ./scripts/deploy.sh
```

**D. Configurar Virtual Host:**
```bash
sudo nano /etc/apache2/sites-available/sistema.conf
```

Conteúdo:
```apache
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
```

```bash
sudo a2ensite sistema.conf
sudo systemctl reload apache2
```

**E. Configurar SSL (opcional mas recomendado):**
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d seudominio.com.br
```

---

### **4. HOSPEDAGENS ESPECÍFICAS**

#### **HOSTINGER:**
1. Upload via File Manager
2. Extrair ZIP em `public_html/`
3. Banco: MySQL via hPanel
4. SSL: Grátis Let's Encrypt automático
5. Subdomínio: Configurar DNS para pasta `/public`

#### **HOSTGATOR:**
1. Upload via cPanel File Manager
2. Configurar subdomínio se necessário
3. MySQL via cPanel
4. SSL: Disponível (grátis ou pago)

#### **GODADDY:**
1. Upload via FTP ou File Manager
2. Banco via MySQL Databases
3. Pode precisar ajustar .htaccess
4. SSL: Pago ou Let's Encrypt

---

### **5. PROBLEMAS COMUNS**

#### **Erro 500 - Internal Server Error:**
```
Solução:
- chmod 755 nas pastas
- chmod 644 nos arquivos
- Verificar se mod_rewrite está ativo
- Verificar logs de erro
```

#### **Erro de Banco de Dados:**
```
Solução:
- Verificar credenciais no .env
- Testar conexão via phpMyAdmin
- Verificar se extensão pdo_mysql está instalada
```

#### **CSS/JS não carrega:**
```
Solução:
- Verificar APP_URL no .env
- Ajustar caminhos no HTML
- Verificar se .htaccess está correto
```

---

### **6. CHECKLIST DE DEPLOY**

**✅ Antes do Upload:**
- [ ] Executar `composer run prod-install`
- [ ] Testar sistema localmente
- [ ] Preparar arquivo .env de produção
- [ ] Criar backup do projeto atual

**✅ Durante o Upload:**
- [ ] Upload dos arquivos corretos
- [ ] Configurar permissões adequadas
- [ ] Criar banco de dados
- [ ] Configurar .env com dados reais

**✅ Após o Upload:**
- [ ] Testar URL principal
- [ ] Testar login/cadastro
- [ ] Testar upload de arquivos
- [ ] Verificar logs de erro
- [ ] Configurar SSL

---

### **7. COMANDOS ÚTEIS**

```bash
# Preparar para hospedagem compartilhada
composer run deploy-hospedagem

# Testar deploy localmente
composer run deploy-test

# Fazer backup antes do deploy
composer run backup

# Deploy automático (detecta ambiente)
composer run deploy-auto
```

---

## 📞 **SUPORTE**

Se encontrar problemas:
1. Verifique logs de erro no cPanel
2. Teste conexão de banco via phpMyAdmin
3. Verifique permissões dos arquivos
4. Confira .htaccess e mod_rewrite
5. Verifique extensões PHP instaladas

**O sistema está pronto para qualquer tipo de hospedagem! 🚀**
