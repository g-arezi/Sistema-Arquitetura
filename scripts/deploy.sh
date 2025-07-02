#!/bin/bash

# Script de Deploy para Produção - Sistema de Arquitetura
# Este script automatiza o processo de deploy para o servidor de produção

set -e  # Parar execução em caso de erro

echo "🚀 Iniciando deploy do Sistema de Arquitetura..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurações
PROJECT_DIR="/var/www/html"
BACKUP_DIR="/var/backups/sistema-arquitetura"
LOG_FILE="/var/log/sistema-arquitetura/deploy.log"

# Função para logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

# Função para verificar se comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

echo -e "${BLUE}1. Verificando pré-requisitos...${NC}"

# Verificar se PHP está instalado
if ! command_exists php; then
    echo -e "${RED}❌ PHP não está instalado${NC}"
    exit 1
fi

# Verificar se Composer está instalado
if ! command_exists composer; then
    echo -e "${RED}❌ Composer não está instalado${NC}"
    exit 1
fi

# Verificar se Git está instalado
if ! command_exists git; then
    echo -e "${RED}❌ Git não está instalado${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Pré-requisitos atendidos${NC}"

echo -e "${BLUE}2. Criando backup do sistema atual...${NC}"
if [ -d "$PROJECT_DIR" ]; then
    mkdir -p "$BACKUP_DIR"
    BACKUP_NAME="backup-$(date +%Y%m%d_%H%M%S)"
    tar -czf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" -C "$PROJECT_DIR" .
    log "Backup criado: $BACKUP_DIR/$BACKUP_NAME.tar.gz"
    echo -e "${GREEN}✅ Backup criado com sucesso${NC}"
else
    echo -e "${YELLOW}⚠️  Diretório do projeto não existe, pulando backup${NC}"
fi

echo -e "${BLUE}3. Baixando código do repositório...${NC}"
cd "$PROJECT_DIR"

# Se é primeira instalação
if [ ! -d ".git" ]; then
    echo "Clonando repositório..."
    git clone https://github.com/seu-usuario/sistema-arquitetura.git .
else
    echo "Atualizando código..."
    git fetch origin
    git reset --hard origin/main
fi

log "Código atualizado do repositório"
echo -e "${GREEN}✅ Código baixado/atualizado${NC}"

echo -e "${BLUE}4. Instalando dependências...${NC}"
composer install --no-dev --optimize-autoloader
log "Dependências do Composer instaladas"
echo -e "${GREEN}✅ Dependências instaladas${NC}"

echo -e "${BLUE}5. Configurando permissões...${NC}"
# Definir proprietário correto
chown -R www-data:www-data "$PROJECT_DIR"

# Permissões para diretórios
find "$PROJECT_DIR" -type d -exec chmod 755 {} \;

# Permissões para arquivos
find "$PROJECT_DIR" -type f -exec chmod 644 {} \;

# Permissões especiais para uploads e logs
mkdir -p "$PROJECT_DIR/public/uploads"
mkdir -p "$PROJECT_DIR/storage/documents"
mkdir -p "$PROJECT_DIR/storage/temp"
mkdir -p "/var/log/sistema-arquitetura"

chmod -R 755 "$PROJECT_DIR/public/uploads"
chmod -R 755 "$PROJECT_DIR/storage"
chmod -R 755 "/var/log/sistema-arquitetura"

log "Permissões configuradas"
echo -e "${GREEN}✅ Permissões configuradas${NC}"

echo -e "${BLUE}6. Verificando arquivo de configuração...${NC}"
if [ ! -f "$PROJECT_DIR/.env.production" ]; then
    echo -e "${YELLOW}⚠️  Arquivo .env.production não encontrado${NC}"
    echo "Criando arquivo de exemplo..."
    cp "$PROJECT_DIR/.env.production" "$PROJECT_DIR/.env.production.example"
    echo -e "${RED}❌ Configure o arquivo .env.production antes de continuar${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Arquivo de configuração encontrado${NC}"

echo -e "${BLUE}7. Testando conexão com banco de dados...${NC}"
php -r "
require_once '$PROJECT_DIR/vendor/autoload.php';
\$config = include '$PROJECT_DIR/config/production.php';
try {
    \$pdo = new PDO(
        'mysql:host=' . \$config['database']['host'] . ';dbname=' . \$config['database']['name'] . ';charset=' . \$config['database']['charset'],
        \$config['database']['user'],
        \$config['database']['pass']
    );
    echo 'Conexão com banco de dados: OK\n';
} catch (Exception \$e) {
    echo 'Erro na conexão com banco: ' . \$e->getMessage() . '\n';
    exit(1);
}
"
log "Conexão com banco de dados testada"
echo -e "${GREEN}✅ Conexão com banco de dados OK${NC}"

echo -e "${BLUE}8. Limpando cache e arquivos temporários...${NC}"
# Limpar cache do Composer
composer clear-cache

# Limpar arquivos temporários
rm -rf "$PROJECT_DIR/storage/temp/*"

# Limpar logs antigos (manter últimos 30 dias)
find "/var/log/sistema-arquitetura" -name "*.log" -mtime +30 -delete

log "Cache e arquivos temporários limpos"
echo -e "${GREEN}✅ Limpeza concluída${NC}"

echo -e "${BLUE}9. Configurando serviços...${NC}"

# Recarregar Apache/Nginx
if command_exists systemctl; then
    if systemctl is-active --quiet apache2; then
        systemctl reload apache2
        log "Apache recarregado"
    elif systemctl is-active --quiet nginx; then
        systemctl reload nginx
        log "Nginx recarregado"
    fi
fi

echo -e "${GREEN}✅ Serviços configurados${NC}"

echo -e "${BLUE}10. Executando testes básicos...${NC}"

# Teste de conectividade HTTP
if command_exists curl; then
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
    if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
        echo -e "${GREEN}✅ Teste HTTP: OK (Status: $HTTP_CODE)${NC}"
    else
        echo -e "${RED}❌ Teste HTTP: Falhou (Status: $HTTP_CODE)${NC}"
    fi
fi

log "Deploy concluído com sucesso"

echo ""
echo -e "${GREEN}🎉 Deploy concluído com sucesso!${NC}"
echo ""
echo -e "${YELLOW}📋 Próximos passos:${NC}"
echo "1. Verifique se o site está funcionando corretamente"
echo "2. Teste as funcionalidades principais"
echo "3. Monitore os logs para possíveis erros"
echo ""
echo -e "${BLUE}📁 Localizações importantes:${NC}"
echo "   • Projeto: $PROJECT_DIR"
echo "   • Logs: /var/log/sistema-arquitetura/"
echo "   • Backups: $BACKUP_DIR"
echo "   • Uploads: $PROJECT_DIR/public/uploads"
echo ""
echo -e "${BLUE}📊 Logs de deploy: $LOG_FILE${NC}"
echo ""
echo -e "${GREEN}Deploy finalizado às $(date)${NC}"
