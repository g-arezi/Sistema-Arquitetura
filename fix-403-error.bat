@echo off
REM Script para corrigir o erro 403 na Hostinger
REM Este script atualiza os arquivos do deployment para resolver problemas comuns de acesso

echo ===== Correção para Erro 403 na Hostinger =====
echo.

set DEPLOY_DIR=fix-403-hostinger

REM Criar diretório para a correção
echo Criando diretório para correção...
if exist %DEPLOY_DIR% (
    echo Limpando diretório existente...
    rmdir /S /Q %DEPLOY_DIR%
)
mkdir %DEPLOY_DIR%

REM Criar estrutura simplificada
mkdir %DEPLOY_DIR%\public
mkdir %DEPLOY_DIR%\public_html

REM Criar arquivo .htaccess corrigido
echo Criando arquivo .htaccess simplificado...
set HTACCESS_FILE=%DEPLOY_DIR%\public\.htaccess

echo ^<IfModule mod_rewrite.c^>> %HTACCESS_FILE%
echo     RewriteEngine On>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo     # Remova qualquer redirecionamento HTTPS por enquanto para debugging>> %HTACCESS_FILE%
echo     # RewriteCond %%{HTTPS} off>> %HTACCESS_FILE%
echo     # RewriteRule ^(.*)$ https://%%{HTTP_HOST}%%{REQUEST_URI} [L,R=301]>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo     # Handle client-side routing>> %HTACCESS_FILE%
echo     RewriteCond %%{REQUEST_FILENAME} !-f>> %HTACCESS_FILE%
echo     RewriteCond %%{REQUEST_FILENAME} !-d>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo     # Manda todas as requisições para index.php>> %HTACCESS_FILE%
echo     RewriteRule ^(.*)$ index.php [QSA,L]>> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%
echo.>> %HTACCESS_FILE%
echo # Configurações básicas de segurança>> %HTACCESS_FILE%
echo ^<IfModule mod_headers.c^>>> %HTACCESS_FILE%
echo     Header set X-Content-Type-Options "nosniff">> %HTACCESS_FILE%
echo     Header set X-XSS-Protection "1; mode=block">> %HTACCESS_FILE%
echo ^</IfModule^>>> %HTACCESS_FILE%

REM Criar um arquivo de teste simples
echo Criando arquivo de teste...
set TEST_FILE=%DEPLOY_DIR%\teste.php

echo ^<?php> %TEST_FILE%
echo // Arquivo de teste para verificar acesso>> %TEST_FILE%
echo echo "Teste funcionando!";>> %TEST_FILE%
echo echo "\n\nInformações do servidor:";>> %TEST_FILE%
echo echo "\n\nDiretório atual: " . __DIR__;>> %TEST_FILE%
echo echo "\n\nPHP Version: " . phpversion();>> %TEST_FILE%
echo phpinfo();>> %TEST_FILE%
echo ?^>> %TEST_FILE%

REM Criar um arquivo de debug para o index.php
echo Criando arquivo index.php com debug...
set INDEX_DEBUG=%DEPLOY_DIR%\index-debug.php

echo ^<?php> %INDEX_DEBUG%
echo // Exibir todos os erros para debug>> %INDEX_DEBUG%
echo ini_set('display_errors', 1);>> %INDEX_DEBUG%
echo ini_set('display_startup_errors', 1);>> %INDEX_DEBUG%
echo error_reporting(E_ALL);>> %INDEX_DEBUG%
echo.>> %INDEX_DEBUG%
echo // Teste de carregamento básico>> %INDEX_DEBUG%
echo echo "Index de debug carregado com sucesso!";>> %INDEX_DEBUG%
echo.>> %INDEX_DEBUG%
echo // Tentar carregar o autoloader>> %INDEX_DEBUG%
echo try {>> %INDEX_DEBUG%
echo     if (file_exists(__DIR__ . '/vendor/autoload.php')) {>> %INDEX_DEBUG%
echo         require_once __DIR__ . '/vendor/autoload.php';>> %INDEX_DEBUG%
echo         echo "\n\nAutoloader carregado com sucesso.";>> %INDEX_DEBUG%
echo     } else {>> %INDEX_DEBUG%
echo         echo "\n\nERRO: Arquivo vendor/autoload.php não encontrado.";>> %INDEX_DEBUG%
echo     }>> %INDEX_DEBUG%
echo } catch (Exception $e) {>> %INDEX_DEBUG%
echo     echo "\n\nErro ao carregar autoloader: " . $e->getMessage();>> %INDEX_DEBUG%
echo }>> %INDEX_DEBUG%
echo.>> %INDEX_DEBUG%
echo // Tentar carregar configurações>> %INDEX_DEBUG%
echo try {>> %INDEX_DEBUG%
echo     if (file_exists(__DIR__ . '/config/production.php')) {>> %INDEX_DEBUG%
echo         $config = require_once __DIR__ . '/config/production.php';>> %INDEX_DEBUG%
echo         echo "\n\nConfigurações carregadas com sucesso.";>> %INDEX_DEBUG%
echo         echo "\n\nConfigurações: ";>> %INDEX_DEBUG%
echo         print_r($config);>> %INDEX_DEBUG%
echo     } else {>> %INDEX_DEBUG%
echo         echo "\n\nERRO: Arquivo config/production.php não encontrado.";>> %INDEX_DEBUG%
echo     }>> %INDEX_DEBUG%
echo } catch (Exception $e) {>> %INDEX_DEBUG%
echo     echo "\n\nErro ao carregar configurações: " . $e->getMessage();>> %INDEX_DEBUG%
echo }>> %INDEX_DEBUG%
echo ?^>> %INDEX_DEBUG%

REM Instruções para atualizar a configuração
echo Criando instruções para atualização da configuração...
set CONFIG_UPDATE=%DEPLOY_DIR%\atualizar-config.txt

echo Para atualizar a configuração e habilitar o modo de debug:> %CONFIG_UPDATE%
echo.>> %CONFIG_UPDATE%
echo 1. Acesse o arquivo config/production.php>> %CONFIG_UPDATE%
echo 2. Altere a linha 'debug' => false para 'debug' => true>> %CONFIG_UPDATE%
echo 3. Salve o arquivo>> %CONFIG_UPDATE%
echo.>> %CONFIG_UPDATE%
echo Isso permitirá ver mensagens de erro detalhadas para identificar o problema.>> %CONFIG_UPDATE%

REM Copiar arquivo SOLUCAO-ERRO-403.md
copy "SOLUCAO-ERRO-403.md" "%DEPLOY_DIR%\"

echo.
echo Arquivos de correção criados com sucesso!
echo.
echo Pasta: %DEPLOY_DIR%
echo.
echo Como usar:
echo 1. Faça upload dos arquivos desta pasta para o servidor
echo 2. O arquivo teste.php deve ser colocado no diretório raiz (public_html)
echo 3. O arquivo .htaccess deve substituir o existente na pasta public
echo 4. Siga as instruções em SOLUCAO-ERRO-403.md para resolver o problema
echo.

pause
