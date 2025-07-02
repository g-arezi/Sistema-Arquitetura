#!/bin/bash
# Script para validar a configuração antes do deploy em produção

echo "=== Validando configuração para produção ==="
echo "----------------------------------------------------"

# Verificar arquivos essenciais
echo "Verificando arquivos essenciais..."
ESSENTIAL_FILES=(
    "public/index.php"
    "config/production.php"
    "src/Controllers/AuthController.php"
    "src/Controllers/AdminController.php"
    "src/Models/UserFile.php"
)

MISSING_FILES=0
for FILE in "${ESSENTIAL_FILES[@]}"; do
    if [ ! -f "$FILE" ]; then
        echo "❌ Arquivo não encontrado: $FILE"
        MISSING_FILES=$((MISSING_FILES+1))
    else
        echo "✅ Arquivo encontrado: $FILE"
    fi
done

if [ $MISSING_FILES -gt 0 ]; then
    echo "AVISO: $MISSING_FILES arquivo(s) essencial(is) não encontrado(s)!"
else
    echo "Todos os arquivos essenciais encontrados."
fi
echo "----------------------------------------------------"

# Verificar configuração de produção
echo "Verificando arquivo de configuração de produção..."
PROD_CONFIG="config/production.php"

if [ -f "$PROD_CONFIG" ]; then
    # Verificar valores de configuração críticos
    if grep -q "SENHA_SEGURA_AQUI" "$PROD_CONFIG"; then
        echo "❌ AVISO: Senha padrão encontrada no arquivo de configuração!"
        echo "   Por favor, altere 'SENHA_SEGURA_AQUI' para uma senha segura."
    else
        echo "✅ Senha personalizada configurada."
    fi
    
    if grep -q "sistema-arquitetura.com.br" "$PROD_CONFIG"; then
        echo "❌ AVISO: URL padrão encontrada no arquivo de configuração!"
        echo "   Por favor, atualize com seu domínio real."
    else
        echo "✅ URL personalizada configurada."
    fi
    
    # Verificar modo de depuração
    if grep -q "'debug' => true" "$PROD_CONFIG"; then
        echo "❌ AVISO: Modo de depuração ativado! Desative para produção."
    else
        echo "✅ Modo de depuração está desativado."
    fi
else
    echo "❌ Arquivo de configuração de produção não encontrado!"
fi
echo "----------------------------------------------------"

# Verificar composer.json e dependências
echo "Verificando dependências..."
if [ -f "composer.json" ]; then
    echo "✅ composer.json encontrado."
    
    # Verificar se o PHPMailer está incluído
    if grep -q "phpmailer/phpmailer" "composer.json"; then
        echo "✅ PHPMailer está incluído nas dependências."
    else
        echo "❌ AVISO: PHPMailer não encontrado nas dependências!"
    fi
    
    # Verificar outras dependências críticas
    # Adicione mais verificações conforme necessário
else
    echo "❌ composer.json não encontrado!"
fi
echo "----------------------------------------------------"

# Verificar se os vendors estão instalados
if [ -d "vendor" ]; then
    echo "✅ Diretório vendor encontrado."
else
    echo "❌ AVISO: Diretório vendor não encontrado! Execute 'composer install'."
fi
echo "----------------------------------------------------"

# Verificar se há arquivos sensíveis versionados
echo "Verificando arquivos sensíveis..."
SENSITIVE_FILES=(
    ".env"
    "config/local.php"
    "config/dev.php"
)

for FILE in "${SENSITIVE_FILES[@]}"; do
    if [ -f "$FILE" ]; then
        echo "❌ AVISO: Arquivo sensível encontrado: $FILE"
        echo "   Considere adicionar este arquivo ao .gitignore"
    fi
done
echo "----------------------------------------------------"

echo "Verificação concluída."
echo "Revise os avisos acima antes de prosseguir com o deploy em produção."
echo "----------------------------------------------------"
