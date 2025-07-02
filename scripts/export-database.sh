#!/bin/bash
# Script para exportar o banco de dados para produção

# Configurações - ajuste conforme necessário
DB_USER="seu_usuario_db"
DB_PASS="sua_senha_db"
DB_NAME="sistema_arquitetura"
BACKUP_FILE="sistema_arquitetura_export.sql"

echo "=== Exportando banco de dados para produção ==="
echo "Banco: $DB_NAME"
echo "Arquivo de saída: $BACKUP_FILE"
echo "----------------------------------------------------"

# Exportar o banco de dados
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "Exportação concluída com sucesso!"
    echo "Arquivo gerado: $BACKUP_FILE"
    echo "Tamanho do arquivo: $(du -h $BACKUP_FILE | cut -f1)"
else
    echo "ERRO: Falha ao exportar o banco de dados."
    echo "Verifique as credenciais e tente novamente."
fi

echo "----------------------------------------------------"
echo "Para importar este arquivo no servidor de produção, use:"
echo "mysql -u db_user_prod -p sistema_arquitetura < $BACKUP_FILE"
echo "----------------------------------------------------"
