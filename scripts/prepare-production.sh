#!/bin/bash
# Script de preparação para produção
# Execute este script no servidor de produção

# Configurações
SITE_DIR="/var/www/html/sistema-arquitetura"
PHP_VERSION="8.0"
WEB_USER="www-data"
DOMAIN="sistema-arquitetura.com.br"

echo "=== Preparando Sistema de Arquitetura para produção ==="
echo "Diretório de instalação: $SITE_DIR"
echo "Versão PHP: $PHP_VERSION"
echo "Domínio: $DOMAIN"
echo "----------------------------------------------------"

# Criar diretório do site
echo "Criando diretório do site..."
mkdir -p $SITE_DIR

# Instalar dependências do PHP
echo "Instalando dependências PHP..."
apt-get update
apt-get install -y php$PHP_VERSION-fpm php$PHP_VERSION-mysql php$PHP_VERSION-xml \
                  php$PHP_VERSION-curl php$PHP_VERSION-mbstring php$PHP_VERSION-zip \
                  php$PHP_VERSION-gd php$PHP_VERSION-intl

# Instalar e configurar Nginx
echo "Instalando e configurando Nginx..."
apt-get install -y nginx
cat > /etc/nginx/sites-available/$DOMAIN << EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;

    root $SITE_DIR/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }

    # Configurações de segurança
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
}
EOF

# Ativar o site
ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/

# Configurações de segurança do PHP
echo "Configurando PHP para produção..."
cat > /etc/php/$PHP_VERSION/fpm/conf.d/99-sistema-arquitetura.ini << EOF
; Configurações de produção
display_errors = Off
log_errors = On
error_log = /var/log/php/sistema-arquitetura-errors.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
max_execution_time = 60
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
session.cookie_secure = On
session.cookie_httponly = On
EOF

# Diretório de logs PHP
mkdir -p /var/log/php
chown $WEB_USER:$WEB_USER /var/log/php

# Reiniciar serviços
echo "Reiniciando serviços..."
systemctl restart php$PHP_VERSION-fpm
systemctl restart nginx

echo "----------------------------------------------------"
echo "Configuração de servidor concluída!"
echo "Agora faça o upload dos arquivos da aplicação para $SITE_DIR"
echo "Não se esqueça de configurar o SSL com Certbot/Let's Encrypt!"
echo "----------------------------------------------------"
