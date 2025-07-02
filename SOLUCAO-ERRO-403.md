# Solução para Erro 403 na Hostinger

Percebemos que o site está retornando um erro 403 (Forbidden). Aqui estão as etapas para solucionar este problema:

## 1. Verificar a Estrutura de Diretórios

Na Hostinger, o diretório raiz do seu site é geralmente `public_html`. Você precisa garantir que os arquivos estejam na estrutura correta.

### Solução 1: Mover arquivos para o diretório correto

1. Acesse o **Gerenciador de Arquivos** da Hostinger
2. Navegue até a pasta `public_html`
3. Verifique se os arquivos do projeto estão diretamente dentro deste diretório
4. Se os arquivos estiverem dentro de uma subpasta como `deployment-hostinger` ou outra, mova todos para o diretório `public_html`

### Solução 2: Verificar arquivo index.php

1. Confirme que o arquivo `index.php` está no diretório raiz `public_html`
2. Verifique se este arquivo tem permissão de leitura (644)

## 2. Corrigir as Permissões de Arquivos

As permissões incorretas são uma causa comum de erros 403.

1. No **Gerenciador de Arquivos** da Hostinger:
   - Selecione todos os arquivos e pastas
   - Clique com o botão direito e escolha "Permissões"
   - Configure as permissões:
     - Para diretórios: 755 (drwxr-xr-x)
     - Para arquivos: 644 (rw-r--r--)
   
2. Permissões especiais para diretórios de armazenamento:
   - Pasta `storage`: 755
   - Pasta `storage/documents`: 755
   - Pasta `storage/temp`: 755
   - Pasta `public/uploads` (se existir): 755

## 3. Corrigir o arquivo .htaccess

O arquivo `.htaccess` pode estar causando o problema. Vamos corrigi-lo:

1. Acesse o diretório `public_html/public`
2. Edite o arquivo `.htaccess` (ou crie um novo se não existir)
3. Substitua o conteúdo por:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Remova qualquer redirecionamento HTTPS por enquanto para debugging
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Handle client-side routing
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Manda todas as requisições para index.php
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Configurações básicas de segurança
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

## 4. Verificar o Arquivo index.php no Diretório Public

Verifique se o arquivo index.php na pasta public está correto. Ele deve conter:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar configurações
$config = require_once __DIR__ . '/../config/production.php';

// Iniciar a aplicação
// O restante do seu código...
```

## 5. Ativar Modo de Debug

Vamos ativar temporariamente o modo de debug para identificar problemas:

1. Acesse o arquivo `config/production.php`
2. Altere `'debug' => false,` para `'debug' => true,`
3. Adicione esta linha no início do arquivo index.php (logo após a tag de abertura <?php):

```php
// Exibir todos os erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## 6. Criar um Arquivo de Teste Simples

Para verificar se o problema é com as permissões ou com a configuração:

1. Crie um arquivo `teste.php` no diretório `public_html` com o seguinte conteúdo:

```php
<?php
echo "Teste funcionando!";
phpinfo();
?>
```

2. Acesse: `https://purple-wallaby-649054.hostingersite.com/teste.php`
3. Se este arquivo funcionar, o problema está na configuração da aplicação, não nas permissões

## 7. Verificar os Logs de Erro

Verifique os logs de erro para ter mais detalhes:

1. Acesse o painel da Hostinger
2. Vá para **Avançado → Logs de Erro do Website**
3. Analise os erros recentes para identificar o problema específico

## 8. Verificar Configuração do PHP

1. No painel da Hostinger, vá para **Website → PHP Configurações**
2. Confirme que a versão do PHP é 8.0 ou superior
3. Verifique se todas as extensões necessárias estão ativadas

## 9. Contatar o Suporte da Hostinger

Se todas as soluções acima não resolverem o problema:

1. Tire screenshots dos logs de erro e da estrutura de arquivos
2. Contate o suporte da Hostinger explicando:
   - Que você está enfrentando um erro 403
   - Que você já verificou permissões e estrutura de arquivos
   - Quais medidas você já tentou para resolver o problema

O suporte da Hostinger geralmente é muito eficiente e pode ajudar com problemas específicos da plataforma.

## Após a Correção

Depois que o site estiver funcionando:

1. Desative o modo de debug (altere `'debug' => true,` para `'debug' => false,` no arquivo `config/production.php`)
2. Remova as linhas de exibição de erro do index.php
3. Reative o redirecionamento para HTTPS no .htaccess se necessário
