@echo off
REM Script para criar pacote com solução para erro 404/403 na Hostinger

echo Criando pacote de solução para erro 404/403 na Hostinger...

REM Criar pasta de destino se não existir
if not exist "hostinger-solution-404" mkdir hostinger-solution-404

REM Copiar arquivos para a pasta de destino
copy "hostinger-solution\index.php" "hostinger-solution-404\index.php"
copy "hostinger-solution\.htaccess" "hostinger-solution-404\.htaccess"
copy "hostinger-solution\diagnose.php" "hostinger-solution-404\diagnose.php"
copy "hostinger-solution\test-basic.php" "hostinger-solution-404\test-basic.php"
copy "hostinger-solution\README.md" "hostinger-solution-404\README.md"
copy "hostinger-solution\GUIA-PASSO-A-PASSO.md" "hostinger-solution-404\GUIA-PASSO-A-PASSO.md"

echo Criando arquivo ZIP...

REM Comprimir a pasta em um arquivo ZIP
powershell -Command "Compress-Archive -Path 'hostinger-solution-404\*' -DestinationPath 'solucao-erro-404-hostinger.zip' -Force"

echo Pacote criado com sucesso: solucao-erro-404-hostinger.zip
echo.
echo Este pacote contém:
echo  - index.php modificado para a raiz
echo  - .htaccess otimizado
echo  - diagnose.php (ferramenta de diagnóstico)
echo  - test-basic.php (teste básico)
echo  - Documentação detalhada

pause
