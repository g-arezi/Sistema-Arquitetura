# Tutorial de Implantação na Hostinger

Este guia contém instruções específicas para implantar o Sistema-Arquitetura em uma hospedagem Hostinger.

## Pré-requisitos

- Conta ativa na Hostinger (Plano de hospedagem compartilhada ou superior)
- Domínio configurado
- Acesso ao painel de controle da Hostinger
- Arquivos do projeto preparados para upload

## 1. Preparação dos Arquivos Localmente

Antes de fazer o upload, prepare os arquivos do projeto:

```bash
# No Windows, execute o script de preparação
prepare-deployment.bat
```

Este script irá criar um diretório `deployment` com todos os arquivos necessários para o upload.

## 2. Acesso ao Painel da Hostinger

1. Acesse [https://hpanel.hostinger.com/](https://hpanel.hostinger.com/)
2. Faça login com suas credenciais
3. Selecione seu plano de hospedagem

## 3. Criação do Banco de Dados MySQL

1. No painel da Hostinger, vá para **Banco de Dados → MySQL**
2. Clique em **Criar Novo Banco de Dados**
3. Preencha as informações:
   - **Nome do banco de dados**: `u367501017_sistema_arq` (já configurado)
   - **Usuário**: `u367501017_oArezi` (já configurado)
   - **Senha**: Use a senha fornecida (`2Itdigital@sA`)
4. Anote essas informações para uso posterior
5. Clique em **Criar**

## 4. Upload dos Arquivos

### 4.1 Usando o Gerenciador de Arquivos

1. No painel da Hostinger, vá para **Gerenciador de Arquivos**
2. Navegue até a pasta `public_html` ou a pasta específica do seu subdomínio
3. Clique em **Upload**
4. Selecione os arquivos da pasta `deployment` que você criou anteriormente
5. Aguarde o upload ser concluído

**Nota**: Para arquivos maiores, é recomendado usar FTP (próximo método).

### 4.2 Usando FTP (Recomendado)

1. No painel da Hostinger, vá para **Avançado → Contas FTP**
2. Crie uma nova conta FTP ou use a existente
3. Anote os detalhes de conexão:
   - **Servidor FTP**: normalmente `ftp.seudominio.com`
   - **Usuário FTP**: o usuário que você criou ou o padrão
   - **Senha FTP**: a senha configurada
   - **Porta**: 21
4. Use um cliente FTP como [FileZilla](https://filezilla-project.org/) para conectar
5. Faça upload de todos os arquivos da pasta `deployment` para a pasta `public_html` ou pasta específica do seu domínio/subdomínio

## 5. Configuração do Banco de Dados

### 5.1 Importar Estrutura do Banco

1. No painel da Hostinger, vá para **Banco de Dados → MySQL**
2. Clique no nome do banco de dados que você criou
3. Selecione a guia **phpMyAdmin**
4. Na interface do phpMyAdmin, clique em **Importar**
5. Clique em **Escolher arquivo** e selecione o arquivo SQL de exportação do seu banco de dados local
6. Clique em **Executar**

### 5.2 Configurar Acesso ao Banco

1. Acesse seu site pelo Gerenciador de Arquivos
2. Navegue até a pasta `config`
3. Edite o arquivo `production.php`
4. Atualize as seguintes configurações:

```php
'database' => [
    'host' => 'localhost', // Geralmente é localhost na Hostinger
    'name' => 'u367501017_sistema_arq', // Banco de dados na Hostinger
    'user' => 'u367501017_oArezi',      // Usuário na Hostinger
    'pass' => '2Itdigital@sA',          // Senha na Hostinger
    'charset' => 'utf8mb4',
],
```

## 6. Configuração do PHP

A Hostinger permite selecionar a versão do PHP a ser usada:

1. No painel da Hostinger, vá para **Website → PHP Configurações**
2. Selecione **PHP 8.0** ou superior
3. Verifique se as seguintes extensões estão ativadas:
   - mysqli
   - mbstring
   - xml
   - curl
   - gd
   - zip
4. Clique em **Salvar**

## 7. Configurar o Domínio e SSL

### 7.1 Configuração do Domínio

Se você ainda não configurou seu domínio:

1. No painel da Hostinger, vá para **Domínios**
2. Clique em **Gerenciar** ao lado do seu domínio
3. Em **Registros DNS**, verifique se os registros A e CNAME estão configurados corretamente

### 7.2 Ativar SSL (HTTPS)

1. No painel da Hostinger, vá para **Website → SSL**
2. Clique em **Configurar** ao lado do seu domínio
3. Selecione **Ativar SSL**
4. Aguarde alguns minutos para que o certificado seja emitido
5. Após a ativação, habilite o **Redirecionamento para HTTPS**

## 8. Configuração do Projeto

### 8.1 Atualizar Configurações do Site

No Gerenciador de Arquivos, atualize as seguintes configurações no arquivo `config/production.php`:

```php
'app' => [
    'debug' => false,
    'environment' => 'production',
    'url' => 'https://purple-wallaby-649054.hostingersite.com', // Seu domínio com HTTPS
    'timezone' => 'America/Sao_Paulo',
],
```

### 8.2 Configuração de Email (PHPMailer)

A Hostinger oferece contas de email que podem ser usadas com o PHPMailer:

```php
'mail' => [
    'host' => 'smtp.hostinger.com', // Servidor SMTP da Hostinger
    'port' => 587,
    'username' => 'seu-email@seudominio.com', // Seu email na Hostinger
    'password' => 'sua-senha-de-email', // Senha do email
    'encryption' => 'tls',
    'from_name' => 'Sistema de Arquitetura',
    'from_address' => 'seu-email@seudominio.com',
],
```

## 9. Permissões de Arquivos

A Hostinger geralmente configura as permissões automaticamente, mas você pode precisar ajustar para algumas pastas:

1. No Gerenciador de Arquivos, navegue até seu projeto
2. Clique com o botão direito nas seguintes pastas e selecione **Permissões**:
   - `storage` (se existir): Defina para 755 (drwxr-xr-x)
   - `storage/documents` (se existir): Defina para 755
   - `storage/temp` (se existir): Defina para 755
   - `public/uploads` (se existir): Defina para 755
3. Se você precisar que o sistema escreva em algum arquivo, defina suas permissões para 644 (rw-r--r--)

## 10. Verificação e Testes

Após a conclusão de todas as etapas, acesse seu site pelo navegador:

```
https://seudominio.com
```

### 10.1 Verificações Importantes

- Teste o processo de login com as credenciais de administrador
- Verifique se o sistema de aprovação de usuários está funcionando
- Teste o upload de arquivos
- Verifique se o sistema de emails está enviando notificações corretamente

### 10.2 Solução de Problemas Comuns na Hostinger

1. **Erro 500 (Internal Server Error)**:
   - Verifique o arquivo `.htaccess` para compatibilidade com a Hostinger
   - Ative o modo de debug temporariamente em `config/production.php` para ver mensagens de erro
   - Verifique os logs de erro no painel da Hostinger em **Avançado → Logs de Erro do Website**

2. **Problemas de Conexão com Banco de Dados**:
   - Verifique se as credenciais do banco de dados estão corretas
   - Confirme que o usuário do banco tem permissões suficientes

3. **Problemas com Upload de Arquivos**:
   - Verifique os limites de upload na Hostinger em **Website → PHP Configurações**
   - Aumente os valores de `upload_max_filesize` e `post_max_size` se necessário

## 11. Manutenção e Backups

### 11.1 Backups Automáticos

A Hostinger oferece backups automáticos em alguns planos:

1. No painel da Hostinger, vá para **Backups**
2. Verifique se os backups automáticos estão habilitados
3. Faça backups manuais periodicamente

### 11.2 Manutenção

1. Mantenha seu código atualizado através de FTP
2. Monitore os logs de erro regularmente
3. Verifique o desempenho do site no painel da Hostinger em **Website → Avançado → Estatísticas**

## 12. Recursos Adicionais da Hostinger

- **Cache do LiteSpeed**: Ative-o em **Website → Otimização** para melhor desempenho
- **CDN Gratuito**: Ative-o em **Website → CDN** para maior velocidade global
- **Subdomínios**: Crie ambientes de teste em **Domínios → Subdomínios**

## Conclusão

Seu Sistema-Arquitetura agora deve estar funcionando corretamente na Hostinger. Em caso de problemas ou dúvidas, consulte a [documentação oficial da Hostinger](https://support.hostinger.com.br/) ou entre em contato com o suporte deles, que é geralmente muito eficiente.

Lembre-se de manter backups regulares e monitorar o desempenho do sistema periodicamente.
