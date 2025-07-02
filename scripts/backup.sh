#!/bin/bash

# Script de Backup Automatizado - Sistema de Arquitetura
# Execute este script via cron para backups automáticos

set -e

# Configurações
PROJECT_DIR="/var/www/html"
BACKUP_DIR="/var/backups/sistema-arquitetura"
LOG_FILE="/var/log/sistema-arquitetura/backup.log"
RETENTION_DAYS=30

# Carregar configurações do banco
CONFIG_FILE="$PROJECT_DIR/config/production.php"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Função para logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOG_FILE"
}

echo -e "${BLUE}🔄 Iniciando backup do Sistema de Arquitetura...${NC}"

# Criar diretório de backup se não existir
mkdir -p "$BACKUP_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

# Nome do backup com timestamp
BACKUP_NAME="backup-$(date +%Y%m%d_%H%M%S)"
BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"

log "Iniciando backup: $BACKUP_NAME"

echo -e "${BLUE}1. Criando backup dos arquivos...${NC}"
mkdir -p "$BACKUP_PATH"

# Backup dos arquivos do projeto (excluindo cache e temp)
tar -czf "$BACKUP_PATH/files.tar.gz" \
    --exclude="storage/temp/*" \
    --exclude="vendor" \
    --exclude=".git" \
    --exclude="node_modules" \
    --exclude="*.log" \
    -C "$PROJECT_DIR" .

log "Backup de arquivos criado: $BACKUP_PATH/files.tar.gz"
echo -e "${GREEN}✅ Backup de arquivos concluído${NC}"

echo -e "${BLUE}2. Criando backup do banco de dados...${NC}"

# Extrair configurações do banco de dados
DB_CONFIG=$(php -r "
\$config = include '$CONFIG_FILE';
echo \$config['database']['host'] . '|' . 
     \$config['database']['name'] . '|' . 
     \$config['database']['user'] . '|' . 
     \$config['database']['pass'];
")

IFS='|' read -r DB_HOST DB_NAME DB_USER DB_PASS <<< "$DB_CONFIG"

# Criar backup do banco
mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_PATH/database.sql"

# Comprimir backup do banco
gzip "$BACKUP_PATH/database.sql"

log "Backup do banco de dados criado: $BACKUP_PATH/database.sql.gz"
echo -e "${GREEN}✅ Backup do banco de dados concluído${NC}"

echo -e "${BLUE}3. Criando arquivo de informações...${NC}"

# Criar arquivo com informações do backup
cat > "$BACKUP_PATH/info.txt" << EOF
Sistema de Arquitetura - Backup
================================

Data: $(date)
Servidor: $(hostname)
PHP Version: $(php -v | head -n 1)
Sistema: $(uname -a)

Arquivos incluídos:
- files.tar.gz: Arquivos do projeto (excluindo temp, cache, vendor)
- database.sql.gz: Backup completo do banco de dados
- info.txt: Este arquivo com informações do backup

Para restaurar:
1. Extrair files.tar.gz no diretório do projeto
2. Restaurar database.sql.gz no MySQL
3. Configurar permissões apropriadas
4. Instalar dependências (composer install)

Tamanho dos arquivos:
$(ls -lh "$BACKUP_PATH")
EOF

log "Arquivo de informações criado"
echo -e "${GREEN}✅ Arquivo de informações criado${NC}"

echo -e "${BLUE}4. Calculando tamanhos e verificando integridade...${NC}"

# Calcular tamanhos
FILES_SIZE=$(du -h "$BACKUP_PATH/files.tar.gz" | cut -f1)
DB_SIZE=$(du -h "$BACKUP_PATH/database.sql.gz" | cut -f1)
TOTAL_SIZE=$(du -sh "$BACKUP_PATH" | cut -f1)

# Verificar integridade dos arquivos comprimidos
if gzip -t "$BACKUP_PATH/files.tar.gz" && gzip -t "$BACKUP_PATH/database.sql.gz"; then
    echo -e "${GREEN}✅ Verificação de integridade: OK${NC}"
    log "Verificação de integridade passou - Arquivos: $FILES_SIZE, Banco: $DB_SIZE, Total: $TOTAL_SIZE"
else
    echo -e "${RED}❌ Erro na verificação de integridade${NC}"
    log "ERRO: Falha na verificação de integridade"
    exit 1
fi

echo -e "${BLUE}5. Limpando backups antigos...${NC}"

# Remover backups mais antigos que o período de retenção
find "$BACKUP_DIR" -name "backup-*" -type d -mtime +$RETENTION_DAYS -exec rm -rf {} \;

REMAINING_BACKUPS=$(find "$BACKUP_DIR" -name "backup-*" -type d | wc -l)
log "Limpeza concluída. Backups restantes: $REMAINING_BACKUPS"
echo -e "${GREEN}✅ Limpeza de backups antigos concluída${NC}"

echo -e "${BLUE}6. Enviando notificação (opcional)...${NC}"

# Se curl estiver disponível, pode enviar notificação para webhook
if command -v curl >/dev/null 2>&1 && [ ! -z "${WEBHOOK_URL:-}" ]; then
    curl -X POST "$WEBHOOK_URL" \
        -H "Content-Type: application/json" \
        -d "{
            \"text\": \"✅ Backup do Sistema de Arquitetura concluído\",
            \"details\": {
                \"timestamp\": \"$(date)\",
                \"backup_name\": \"$BACKUP_NAME\",
                \"files_size\": \"$FILES_SIZE\",
                \"database_size\": \"$DB_SIZE\",
                \"total_size\": \"$TOTAL_SIZE\"
            }
        }" > /dev/null 2>&1
    log "Notificação enviada via webhook"
fi

log "Backup concluído com sucesso: $BACKUP_PATH"

echo ""
echo -e "${GREEN}🎉 Backup concluído com sucesso!${NC}"
echo ""
echo -e "${YELLOW}📊 Resumo do backup:${NC}"
echo "   • Nome: $BACKUP_NAME"
echo "   • Localização: $BACKUP_PATH"
echo "   • Arquivos: $FILES_SIZE"
echo "   • Banco de dados: $DB_SIZE"
echo "   • Total: $TOTAL_SIZE"
echo "   • Backups ativos: $REMAINING_BACKUPS"
echo ""
echo -e "${BLUE}📁 Para restaurar este backup:${NC}"
echo "   1. Extrair: tar -xzf $BACKUP_PATH/files.tar.gz"
echo "   2. Restaurar DB: gunzip -c $BACKUP_PATH/database.sql.gz | mysql -u USER -p DATABASE"
echo "   3. Instalar dependências: composer install"
echo "   4. Configurar permissões apropriadas"
echo ""
echo -e "${GREEN}Backup finalizado às $(date)${NC}"
