# Guia de Implantação em Produção do Sistema-Arquitetura

Este guia fornece instruções detalhadas para implantar o Sistema-Arquitetura em um ambiente de produção.

## Pré-requisitos
- Servidor Linux (Ubuntu/Debian recomendado)
- PHP 8.0+
- MySQL 5.7+ ou MariaDB 10.3+
- Nginx ou Apache
- Domínio configurado para apontar para o servidor
- Acesso SSH ao servidor
- Permissões para instalar software e configurar serviços

## 1. Preparação Inicial

### 1.1 Verificar código e commit
```bash
# Certifique-se de que todas as alterações estão commitadas
git status
git add .
git commit -m "Preparação para produção"
git push
```

### 1.2 Exportar o código para implantação
```bash
# No Windows, você pode criar um ZIP do projeto
# Exclua pastas desnecessárias como .git, .vscode, etc.
```

## 2. Configuração do Servidor

### 2.1 Upload do script de preparação
Faça upload do arquivo `scripts/prepare-production.sh` para o servidor e execute-o:

```bash
# No servidor
chmod +x prepare-production.sh
sudo ./prepare-production.sh
```

Esse script irá:
- Instalar o PHP 8.0+ com extensões necessárias
- Configurar o Nginx
- Aplicar configurações de segurança do PHP
- Preparar diretórios e permissões

### 2.2 Upload dos arquivos do projeto
Faça upload dos arquivos do projeto para o diretório configurado no script (`/var/www/html/sistema-arquitetura` por padrão).

```bash
# Usando SCP (do seu computador local)
scp -r /caminho/local/Sistema-Arquitetura/* usuario@seu-servidor:/var/www/html/sistema-arquitetura/

# OU usando SFTP com ferramentas como FileZilla, WinSCP, etc.
```

### 2.3 Permissões de arquivos
No servidor, configure as permissões corretas:

```bash
cd /var/www/html/sistema-arquitetura
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage/
chown -R www-data:www-data .
```

## 3. Configuração do Banco de Dados

### 3.1 Criar banco de dados e usuário

```bash
# Acesse o MySQL/MariaDB
mysql -u root -p

# No prompt do MySQL
CREATE DATABASE sistema_arquitetura CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'db_user_prod'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON sistema_arquitetura.* TO 'db_user_prod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Importar estrutura do banco
Se você tiver um arquivo SQL com a estrutura do banco:

```bash
mysql -u db_user_prod -p sistema_arquitetura < /caminho/para/estrutura.sql
```

## 4. Configuração da Aplicação

### 4.1 Configurar arquivo de produção
Edite o arquivo `config/production.php` com as informações do seu ambiente:

```bash
cd /var/www/html/sistema-arquitetura
nano config/production.php
```

Altere as seguintes configurações:
- Credenciais do banco de dados
- URL da aplicação
- Configurações de email (SMTP)
- Caminhos de armazenamento

### 4.2 Instalar dependências do Composer

```bash
cd /var/www/html/sistema-arquitetura
composer install --no-dev --optimize-autoloader
```

## 5. Configuração do SSL (HTTPS)

### 5.1 Instalar Certbot

```bash
sudo apt-get update
sudo apt-get install certbot python3-certbot-nginx
```

### 5.2 Obter e configurar certificado SSL

```bash
sudo certbot --nginx -d sistema-arquitetura.com.br -d www.sistema-arquitetura.com.br
```

Siga as instruções do Certbot e escolha a opção para redirecionar todo o tráfego HTTP para HTTPS.

## 6. Configurações Finais

### 6.1 Verificar logs e permissões

```bash
# Verifique os logs do Nginx
sudo tail -f /var/log/nginx/error.log

# Verifique os logs do PHP
sudo tail -f /var/log/php/sistema-arquitetura-errors.log
```

### 6.2 Configurar backup automático
Crie um script de backup para o banco de dados e arquivos importantes:

```bash
# Exemplo simples de script de backup
#!/bin/bash
BACKUP_DIR="/var/backups/sistema-arquitetura"
DATE=$(date +%Y-%m-%d)
mkdir -p $BACKUP_DIR

# Backup do banco de dados
mysqldump -u db_user_prod -p'sua_senha_segura' sistema_arquitetura > $BACKUP_DIR/db-$DATE.sql

# Backup de arquivos importantes
tar -czf $BACKUP_DIR/files-$DATE.tar.gz /var/www/html/sistema-arquitetura/storage

# Manter apenas os últimos 7 dias de backups
find $BACKUP_DIR -name "db-*" -type f -mtime +7 -delete
find $BACKUP_DIR -name "files-*" -type f -mtime +7 -delete
```

Adicione este script ao crontab para execução diária:

```bash
sudo crontab -e
# Adicione a linha:
0 3 * * * /caminho/para/backup.sh > /dev/null 2>&1
```

## 7. Teste de Acesso

### 7.1 Validar funcionamento
- Acesse o site no navegador usando HTTPS
- Teste o processo de registro de usuário
- Teste o processo de aprovação de usuário
- Verifique se todas as funcionalidades estão operando corretamente

### 7.2 Monitoramento inicial
Monitore os logs do servidor nos primeiros dias para identificar possíveis problemas:

```bash
# Monitor de recursos
htop

# Logs do Nginx
tail -f /var/log/nginx/error.log

# Logs do PHP
tail -f /var/log/php/sistema-arquitetura-errors.log
```

## 8. Troubleshooting

### 8.1 Problemas comuns e soluções

#### Erro 500 - Internal Server Error
- Verifique os logs do PHP
- Verifique permissões de arquivos
- Confirme se as extensões PHP necessárias estão instaladas

#### Erro de conexão ao banco de dados
- Verifique as credenciais no arquivo `config/production.php`
- Verifique se o serviço MySQL está rodando: `systemctl status mysql`
- Teste a conexão manual: `mysql -u db_user_prod -p sistema_arquitetura`

#### Problemas de permissão
```bash
# Redefina as permissões dos arquivos
cd /var/www/html/sistema-arquitetura
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage/
chown -R www-data:www-data .
```

## 9. Otimizações Adicionais (Opcional)

### 9.1 Configurar Caching
Se o site tiver tráfego alto, considere adicionar caching:

```bash
# Instalar Redis
sudo apt-get install redis-server php-redis

# Configurar em config/production.php
```

### 9.2 Configurar CDN
Para melhorar o desempenho global, considere usar um CDN como Cloudflare.

## 10. Verificação Pós-Produção

Após uma semana em produção, faça estas verificações:
- Revisar logs em busca de erros recorrentes
- Verificar desempenho do servidor
- Confirmar que os backups estão sendo realizados corretamente
- Atualizar documentação se necessário
