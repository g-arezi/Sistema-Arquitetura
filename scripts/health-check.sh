#!/bin/bash

# Health Check Script - Sistema de Arquitetura
# Script para verificar se todos os componentes estão funcionando corretamente

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

PROJECT_DIR="/var/www/html"
CONFIG_FILE="$PROJECT_DIR/config/production.php"
LOG_FILE="/var/log/sistema-arquitetura/health-check.log"

echo -e "${BLUE}🔍 Sistema de Arquitetura - Health Check${NC}"
echo "$(date)"
echo "========================================"

# Função para logging
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" >> "$LOG_FILE" 2>/dev/null || true
}

# Contadores
PASSED=0
FAILED=0

# Função para teste
test_component() {
    local name="$1"
    local command="$2"
    
    echo -n "Testando $name... "
    
    if eval "$command" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ OK${NC}"
        log "✅ $name: OK"
        ((PASSED++))
    else
        echo -e "${RED}❌ FALHA${NC}"
        log "❌ $name: FALHOU"
        ((FAILED++))
    fi
}

echo -e "${BLUE}1. Verificando Servidor Web${NC}"
test_component "Apache/Nginx" "curl -s -o /dev/null -w '%{http_code}' http://localhost | grep -q '200\|302'"
test_component "HTTPS Redirect" "curl -s -I http://localhost | grep -q 'Location.*https'"
test_component "SSL Certificate" "curl -s -I https://localhost >/dev/null 2>&1"

echo -e "${BLUE}2. Verificando PHP${NC}"
test_component "PHP Version" "php -v | grep -q 'PHP [78]'"
test_component "PHP Extensions" "php -m | grep -q 'mysqli\|pdo_mysql\|curl\|gd\|mbstring'"
test_component "PHP Error Logs" "test -w /var/log/sistema-arquitetura/php_errors.log || touch /var/log/sistema-arquitetura/php_errors.log"

echo -e "${BLUE}3. Verificando Banco de Dados${NC}"
if [ -f "$CONFIG_FILE" ]; then
    DB_CONFIG=$(php -r "
    \$config = include '$CONFIG_FILE';
    echo \$config['database']['host'] . '|' . 
         \$config['database']['name'] . '|' . 
         \$config['database']['user'] . '|' . 
         \$config['database']['pass'];
    " 2>/dev/null || echo "|||")
    
    IFS='|' read -r DB_HOST DB_NAME DB_USER DB_PASS <<< "$DB_CONFIG"
    
    test_component "MySQL Service" "systemctl is-active mysql"
    test_component "Database Connection" "mysql -h'$DB_HOST' -u'$DB_USER' -p'$DB_PASS' -e 'SELECT 1' '$DB_NAME'"
    test_component "Database Tables" "mysql -h'$DB_HOST' -u'$DB_USER' -p'$DB_PASS' -e 'SHOW TABLES' '$DB_NAME' | grep -q 'users\|projects'"
else
    echo -e "${RED}❌ Arquivo de configuração não encontrado${NC}"
    ((FAILED++))
fi

echo -e "${BLUE}4. Verificando Arquivos e Permissões${NC}"
test_component "Document Root" "test -d $PROJECT_DIR/public"
test_component "Autoloader" "test -f $PROJECT_DIR/vendor/autoload.php"
test_component "Uploads Directory" "test -d $PROJECT_DIR/public/uploads && test -w $PROJECT_DIR/public/uploads"
test_component "Storage Directory" "test -d $PROJECT_DIR/storage && test -w $PROJECT_DIR/storage"
test_component "Logs Directory" "test -d /var/log/sistema-arquitetura && test -w /var/log/sistema-arquitetura"

echo -e "${BLUE}5. Verificando Recursos do Sistema${NC}"
test_component "Espaço em Disco (>1GB)" "df -BG $PROJECT_DIR | awk 'NR==2 {print \$4}' | sed 's/G//' | awk '\$1 > 1'"
test_component "Memória RAM (>500MB)" "free -m | awk 'NR==2{printf \"%.0f\", \$7}' | awk '\$1 > 500'"
test_component "Load Average (<2.0)" "uptime | awk -F'load average:' '{print \$2}' | awk -F, '{print \$1}' | awk '\$1 < 2.0'"

echo -e "${BLUE}6. Verificando Segurança${NC}"
test_component "Firewall Ativo" "ufw status | grep -q 'Status: active'"
test_component "SSL Headers" "curl -s -I https://localhost | grep -q 'Strict-Transport-Security'"
test_component "Security Headers" "curl -s -I https://localhost | grep -q 'X-Frame-Options\|X-Content-Type-Options'"
test_component "Config Files Protected" "curl -s -o /dev/null -w '%{http_code}' https://localhost/config/production.php | grep -q '403\|404'"

echo -e "${BLUE}7. Verificando Funcionalidades${NC}"
test_component "CSS Loading" "curl -s -o /dev/null -w '%{http_code}' https://localhost/css/style.css | grep -q '200'"
test_component "JS Loading" "curl -s -o /dev/null -w '%{http_code}' https://localhost/js/app.js | grep -q '200'"
test_component "Login Page" "curl -s https://localhost/login | grep -q 'Entrar no Sistema'"

echo -e "${BLUE}8. Verificando Logs${NC}"
test_component "PHP Error Log" "test -f /var/log/sistema-arquitetura/php_errors.log"
test_component "Apache/Nginx Logs" "test -f /var/log/apache2/sistema-arquitetura-error.log || test -f /var/log/nginx/sistema-arquitetura-error.log"
test_component "Log Rotation" "test -f /etc/logrotate.d/sistema-arquitetura"

echo -e "${BLUE}9. Verificando Backup${NC}"
test_component "Backup Script" "test -x $PROJECT_DIR/scripts/backup.sh"
test_component "Backup Directory" "test -d /var/backups/sistema-arquitetura"
test_component "Recent Backup" "find /var/backups/sistema-arquitetura -name 'backup-*' -mtime -7 | grep -q ."

echo -e "${BLUE}10. Verificando Performance${NC}"
RESPONSE_TIME=$(curl -s -o /dev/null -w '%{time_total}' https://localhost 2>/dev/null || echo "99")
test_component "Response Time (<3s)" "echo '$RESPONSE_TIME < 3.0' | bc -l | grep -q 1"

# Verificar se há erros recentes nos logs
RECENT_ERRORS=$(grep -c "$(date +%Y-%m-%d)" /var/log/sistema-arquitetura/php_errors.log 2>/dev/null || echo "0")
test_component "No Recent PHP Errors" "test $RECENT_ERRORS -lt 10"

echo ""
echo "========================================"
echo -e "${BLUE}📊 Resumo do Health Check${NC}"
echo "Testes passaram: $GREEN$PASSED$NC"
echo "Testes falharam: $RED$FAILED$NC"
echo "Total de testes: $((PASSED + FAILED))"

if [ $FAILED -eq 0 ]; then
    echo ""
    echo -e "${GREEN}🎉 Todos os testes passaram! Sistema saudável.${NC}"
    log "✅ Health check completado: $PASSED/$((PASSED + FAILED)) testes passaram"
    exit 0
else
    echo ""
    echo -e "${RED}⚠️  $FAILED teste(s) falharam. Verifique os componentes acima.${NC}"
    log "❌ Health check completado: $FAILED/$((PASSED + FAILED)) testes falharam"
    
    echo ""
    echo -e "${YELLOW}💡 Dicas para resolução:${NC}"
    echo "• Verifique os logs: tail -f /var/log/sistema-arquitetura/*.log"
    echo "• Verifique serviços: systemctl status apache2 mysql"
    echo "• Verifique configurações: cat $CONFIG_FILE"
    echo "• Verifique permissões: ls -la $PROJECT_DIR"
    
    exit 1
fi
