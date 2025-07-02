# Passos para Resolver o Erro 403 na Hostinger

Esse guia contém os passos exatos para resolver o erro 403 (Forbidden) que está ocorrendo no site `purple-wallaby-649054.hostingersite.com`.

## Passo 1: Verificar a Estrutura de Diretórios

O primeiro problema pode ser a estrutura de diretórios. Na Hostinger, os arquivos públicos devem estar no diretório `public_html`.

### O que fazer:

1. Acesse o **Gerenciador de Arquivos** da Hostinger
2. Verifique se a estrutura está assim:
   ```
   public_html/
   ├── public/         (contém index.php e outros arquivos públicos)
   ├── src/            (contém código fonte)
   ├── config/         (contém configurações)
   ├── vendor/         (contém dependências)
   └── storage/        (contém arquivos de armazenamento)
   ```

3. **Correção necessária**: Os arquivos da aplicação devem estar diretamente em `public_html`, não dentro de uma subpasta como `deployment-hostinger` ou outra.

## Passo 2: Corrigir o Arquivo .htaccess

O arquivo .htaccess pode estar bloqueando o acesso ou configurado incorretamente.

### O que fazer:

1. Acesse o diretório `public_html/public`
2. Edite o arquivo `.htaccess` (ou crie um novo se não existir)
3. Substitua o conteúdo pelo seguinte:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle client-side routing
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Envia todas as requisições para index.php
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Configurações básicas de segurança
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

## Passo 3: Corrigir Permissões de Arquivos

As permissões incorretas são causa comum de erros 403.

### O que fazer:

1. No **Gerenciador de Arquivos** da Hostinger:
   - Selecione todos os arquivos e pastas em `public_html`
   - Clique com o botão direito e escolha "Permissões"
   - Configure as permissões:
     - Diretórios: 755 (drwxr-xr-x)
     - Arquivos: 644 (rw-r--r--)
   
2. Dê atenção especial a estas pastas:
   - `public_html/storage`: 755
   - `public_html/storage/documents`: 755
   - `public_html/storage/temp`: 755
   - `public_html/public/uploads` (se existir): 755
   - `public_html/public/index.php`: 644

## Passo 4: Testar com um Arquivo Simples

Para isolar o problema, vamos testar com um arquivo simples.

### O que fazer:

1. Faça upload do arquivo `teste.php` (da pasta `fix-403-hostinger`) para o diretório raiz `public_html`
2. Acesse: `https://purple-wallaby-649054.hostingersite.com/teste.php`
3. Se funcionar, o problema não é nas permissões gerais, mas na configuração da aplicação
4. Se continuar com erro 403, o problema está nas permissões ou nas configurações do servidor

## Passo 5: Ativar Modo de Debug

Para identificar o problema exato, ative o modo de debug.

### O que fazer:

1. Edite o arquivo `public_html/config/production.php`
2. Altere a linha:
   ```php
   'debug' => false,
   ```
   Para:
   ```php
   'debug' => true,
   ```

3. No início do arquivo `public_html/public/index.php`, adicione estas linhas (logo após a tag de abertura <?php):
   ```php
   // Exibir todos os erros para debug
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   ```

## Passo 6: Verificar Configuração do PHP

A versão do PHP ou extensões faltantes podem causar problemas.

### O que fazer:

1. No painel da Hostinger, vá para **Website → PHP Configurações**
2. Certifique-se de que:
   - Versão do PHP: 8.0 ou superior
   - Extensões ativadas: mysqli, mbstring, xml, curl, gd, zip

## Passo 7: Verificar os Logs de Erro

Os logs de erro podem revelar o problema exato.

### O que fazer:

1. No painel da Hostinger, vá para **Avançado → Logs de Erro do Website**
2. Examine os erros recentes
3. Procure por mensagens relacionadas ao acesso negado, arquivos não encontrados, ou permissões

## Passo 8: Testar com o Index de Debug

Para isolar problemas de autoloader ou configuração.

### O que fazer:

1. Faça upload do arquivo `index-debug.php` (da pasta `fix-403-hostinger`) para o diretório raiz `public_html`
2. Acesse: `https://purple-wallaby-649054.hostingersite.com/index-debug.php`
3. Analise as mensagens de erro ou sucesso para identificar qual componente está falhando

## Passo 9: Contatar o Suporte da Hostinger

Se os passos anteriores não resolverem, contate o suporte.

### O que fazer:

1. Acesse o painel da Hostinger e clique em "Suporte" ou "Ajuda"
2. Descreva o problema detalhadamente:
   - Mencione que está enfrentando um erro 403 (Forbidden)
   - Informe que já verificou permissões e estrutura de arquivos
   - Liste os passos que já tentou
   - Forneça o URL do site: `purple-wallaby-649054.hostingersite.com`

## Conclusão

Após a resolução do problema:

1. Remova os arquivos de teste (teste.php e index-debug.php)
2. Desative o modo de debug no arquivo `config/production.php`
3. Remova as linhas de exibição de erro do arquivo `public/index.php`
4. Reative o redirecionamento para HTTPS no .htaccess se necessário
