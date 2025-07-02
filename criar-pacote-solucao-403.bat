@echo off
REM Script para criar um pacote de solução para o erro 403 na Hostinger
REM Este script cria um conjunto de arquivos para resolver o erro 403

echo ===== Criando Pacote de Solução para Erro 403 na Hostinger =====
echo.

set SOLUTION_DIR=solucao-403-hostinger

REM Criar diretório para a solução
echo Criando diretório para a solução...
if exist %SOLUTION_DIR% (
    echo Limpando diretório existente...
    rmdir /S /Q %SOLUTION_DIR%
)
mkdir %SOLUTION_DIR%

REM Copiar arquivos de solução
echo Copiando arquivos de solução...
copy "test-basic.php" "%SOLUTION_DIR%\"
copy "directory-check.php" "%SOLUTION_DIR%\"
copy "root-index.php" "%SOLUTION_DIR%\index.php"
copy "root-htaccess.txt" "%SOLUTION_DIR%\.htaccess"
copy "public\index-simplified.php" "%SOLUTION_DIR%\index-simplified.php"
copy "public\htaccess-fix.txt" "%SOLUTION_DIR%\public-htaccess.txt"
copy "SOLUCAO-ERRO-403-PERSISTENTE.md" "%SOLUTION_DIR%\"

REM Criar um arquivo README para orientações
echo Criando arquivo README...
set README_FILE=%SOLUTION_DIR%\README.txt

echo Pacote de Solução para Erro 403 na Hostinger> %README_FILE%
echo ============================================>> %README_FILE%
echo.>> %README_FILE%
echo Este pacote contém arquivos para resolver o erro 403 persistente na Hostinger.>> %README_FILE%
echo.>> %README_FILE%
echo Passos para resolução:>> %README_FILE%
echo.>> %README_FILE%
echo 1. Faça upload do arquivo 'index.php' para a RAIZ do seu site (public_html)>> %README_FILE%
echo 2. Faça upload do arquivo '.htaccess' para a RAIZ do seu site (public_html)>> %README_FILE%
echo 3. Faça upload do arquivo 'directory-check.php' para a RAIZ do site>> %README_FILE%
echo 4. Acesse: https://purple-wallaby-649054.hostingersite.com/directory-check.php>> %README_FILE%
echo    - Este arquivo mostrará informações sobre a estrutura de diretórios>> %README_FILE%
echo.>> %README_FILE%
echo 5. Se o erro persistir, tente estas soluções adicionais:>> %README_FILE%
echo    - Renomeie o arquivo 'public-htaccess.txt' para '.htaccess' e faça upload>> %README_FILE%
echo      para a pasta 'public' no servidor>> %README_FILE%
echo    - Renomeie 'index-simplified.php' para 'index.php' e faça upload para>> %README_FILE%
echo      substituir o arquivo existente na pasta 'public' no servidor>> %README_FILE%
echo.>> %README_FILE%
echo 6. Leia o arquivo 'SOLUCAO-ERRO-403-PERSISTENTE.md' para soluções detalhadas>> %README_FILE%
echo.>> %README_FILE%
echo Arquivos incluídos:>> %README_FILE%
echo - index.php: Arquivo para a raiz do site que redireciona para a pasta public>> %README_FILE%
echo - .htaccess: Arquivo de configuração para a raiz do site>> %README_FILE%
echo - directory-check.php: Ferramenta de diagnóstico para verificar a estrutura>> %README_FILE%
echo - public-htaccess.txt: Arquivo .htaccess alternativo para a pasta public>> %README_FILE%
echo - index-simplified.php: Versão simplificada do index.php para diagnóstico>> %README_FILE%
echo - test-basic.php: Arquivo de teste muito básico para verificação de acesso>> %README_FILE%
echo - SOLUCAO-ERRO-403-PERSISTENTE.md: Guia detalhado de soluções>> %README_FILE%

echo.
echo Pacote de solução criado com sucesso!
echo.
echo Pasta: %SOLUTION_DIR%
echo.
echo Para resolver o erro 403:
echo 1. Faça upload dos arquivos desta pasta para o servidor da Hostinger
echo 2. Siga as instruções no arquivo README.txt
echo.

pause
