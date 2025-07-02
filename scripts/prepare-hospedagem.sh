#!/bin/bash
# Script para preparar sistema para hospedagem compartilhada

echo "🌐 Preparando sistema para hospedagem compartilhada..."

# Criar diretório temporário
rm -rf deploy-package
mkdir -p deploy-package

echo "📦 Copiando arquivos necessários..."

# Copiar arquivos essenciais
cp -r public deploy-package/
cp -r src deploy-package/
cp -r config deploy-package/
cp -r vendor deploy-package/
cp composer.json deploy-package/
cp composer.lock deploy-package/

# Copiar .htaccess
cp public/.htaccess deploy-package/public/
cp .htaccess deploy-package/ 2>/dev/null || echo "Arquivo .htaccess raiz não encontrado"

# Criar .env de exemplo
cp .env.production deploy-package/.env.example

# Criar estrutura de diretórios necessários
mkdir -p deploy-package/storage/documents
mkdir -p deploy-package/storage/temp
mkdir -p deploy-package/logs

# Criar arquivo de instruções
cat > deploy-package/INSTRUCOES-HOSPEDAGEM.md << 'EOF'
# 🌐 INSTRUÇÕES PARA HOSPEDAGEM

## 📋 PASSOS PARA COLOCAR ONLINE:

### 1. UPLOAD DOS ARQUIVOS:
- Extraia este ZIP na pasta public_html/ da sua hospedagem
- Ou em uma subpasta como public_html/sistema/

### 2. CONFIGURAR BANCO DE DADOS:
- Crie um banco MySQL no cPanel
- Anote: host, banco, usuário, senha

### 3. CONFIGURAR .ENV:
- Copie .env.example para .env
- Edite com os dados do seu banco:
  ```
  DB_HOST=localhost
  DB_NAME=seu_banco
  DB_USER=seu_usuario
  DB_PASS=sua_senha
  ```

### 4. CONFIGURAR DOMÍNIO:
- Para subpasta: https://seudominio.com/sistema/public
- Para subdomínio: Aponte para a pasta /public

### 5. CONFIGURAR PERMISSÕES:
- Pastas: 755
- Arquivos: 644
- storage/: 775
- logs/: 775

### 6. TESTAR:
- Acesse a URL configurada
- Teste login/cadastro
- Verifique se arquivos são enviados corretamente

## 🛠️ PROBLEMAS COMUNS:

### Erro 500:
- Verifique permissões
- Verifique .htaccess
- Verifique logs de erro

### Erro de Banco:
- Verifique credenciais no .env
- Teste conexão via phpMyAdmin

### CSS/JS não carrega:
- Verifique APP_URL no .env
- Verifique caminhos no código

## 📞 SUPORTE:
- Documente erros encontrados
- Verifique logs da hospedagem
- Contate suporte se necessário
EOF

# Configurar permissões (se no Linux)
if [ "$(uname)" = "Linux" ]; then
    chmod -R 755 deploy-package/
    chmod -R 644 deploy-package/**/*
    chmod -R 775 deploy-package/storage/
    chmod -R 775 deploy-package/logs/
fi

# Criar arquivo ZIP
echo "📦 Criando arquivo ZIP para upload..."
cd deploy-package
zip -r ../sistema-arquitetura-hospedagem.zip .
cd ..

# Limpar diretório temporário
rm -rf deploy-package

echo "✅ Arquivo criado: sistema-arquitetura-hospedagem.zip"
echo "📤 Este arquivo está pronto para upload na sua hospedagem!"
echo "📖 Leia o arquivo INSTRUCOES-HOSPEDAGEM.md dentro do ZIP"
echo ""
echo "🌐 Passos seguintes:"
echo "1. Faça upload do ZIP para sua hospedagem"
echo "2. Extraia na pasta public_html/"
echo "3. Configure banco de dados no cPanel"
echo "4. Edite o arquivo .env com seus dados"
echo "5. Teste o sistema online"
echo ""
echo "🚀 Bom deploy!"
