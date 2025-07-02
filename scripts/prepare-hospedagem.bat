@echo off
echo 🌐 Preparando sistema para hospedagem compartilhada...

REM Limpar diretório temporário
if exist deploy-package rmdir /s /q deploy-package
mkdir deploy-package

echo 📦 Copiando arquivos necessários...

REM Copiar arquivos essenciais
xcopy /e /i public deploy-package\public
xcopy /e /i src deploy-package\src
xcopy /e /i config deploy-package\config
xcopy /e /i vendor deploy-package\vendor
copy composer.json deploy-package\
copy composer.lock deploy-package\

REM Copiar .htaccess
copy public\.htaccess deploy-package\public\
copy .htaccess deploy-package\ 2>nul

REM Criar .env de exemplo
copy .env.production deploy-package\.env.example

REM Criar estrutura de diretórios necessários
mkdir deploy-package\storage\documents
mkdir deploy-package\storage\temp
mkdir deploy-package\logs

REM Criar arquivo de instruções
echo # 🌐 INSTRUÇÕES PARA HOSPEDAGEM > deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ## 📋 PASSOS PARA COLOCAR ONLINE: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 1. UPLOAD DOS ARQUIVOS: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Extraia este ZIP na pasta public_html/ da sua hospedagem >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Ou em uma subpasta como public_html/sistema/ >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 2. CONFIGURAR BANCO DE DADOS: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Crie um banco MySQL no cPanel >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Anote: host, banco, usuário, senha >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 3. CONFIGURAR .ENV: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Copie .env.example para .env >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Edite com os dados do seu banco: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo   DB_HOST=localhost >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo   DB_NAME=seu_banco >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo   DB_USER=seu_usuario >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo   DB_PASS=sua_senha >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 4. CONFIGURAR DOMÍNIO: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Para subpasta: https://seudominio.com/sistema/public >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Para subdomínio: Aponte para a pasta /public >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 5. CONFIGURAR PERMISSÕES: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Pastas: 755 >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Arquivos: 644 >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - storage/: 775 >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - logs/: 775 >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo. >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo ### 6. TESTAR: >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Acesse a URL configurada >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Teste login/cadastro >> deploy-package\INSTRUCOES-HOSPEDAGEM.md
echo - Verifique se arquivos são enviados corretamente >> deploy-package\INSTRUCOES-HOSPEDAGEM.md

REM Criar arquivo ZIP usando PowerShell
echo 📦 Criando arquivo ZIP para upload...
powershell -command "Compress-Archive -Path 'deploy-package\*' -DestinationPath 'sistema-arquitetura-hospedagem.zip' -Force"

REM Limpar diretório temporário
rmdir /s /q deploy-package

echo ✅ Arquivo criado: sistema-arquitetura-hospedagem.zip
echo 📤 Este arquivo está pronto para upload na sua hospedagem!
echo 📖 Leia o arquivo INSTRUCOES-HOSPEDAGEM.md dentro do ZIP
echo.
echo 🌐 Passos seguintes:
echo 1. Faça upload do ZIP para sua hospedagem
echo 2. Extraia na pasta public_html/
echo 3. Configure banco de dados no cPanel
echo 4. Edite o arquivo .env com seus dados
echo 5. Teste o sistema online
echo.
echo 🚀 Bom deploy!
pause
