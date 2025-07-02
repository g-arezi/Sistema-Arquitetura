# Guia Rápido de Deployment para a Hostinger

Este é um guia personalizado para implantar o Sistema-Arquitetura no servidor da Hostinger, usando as credenciais e informações fornecidas.

## Preparação Local (Já Realizada)

- Arquivos preparados com o script `prepare-hostinger.bat`
- Configurações atualizadas com os dados corretos de banco de dados e URL

## Passos para o Deployment

### 1. Upload dos Arquivos

- Fazer upload de todos os arquivos da pasta `deployment-hostinger` para a raiz do seu site na Hostinger (`public_html`)
- Use FTP ou o Gerenciador de Arquivos da Hostinger para fazer o upload

### 2. Configuração do PHP

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

### 3. Verificação do Banco de Dados

O banco de dados já está configurado com as seguintes credenciais:
- **Nome do banco de dados**: `u367501017_sistema_arq`
- **Usuário**: `u367501017_oArezi`
- **Senha**: `2Itdigital@sA`

### 4. Importação do Banco de Dados (Se Necessário)

Se você precisar importar a estrutura do banco de dados:
1. Acesse o phpMyAdmin pelo painel da Hostinger
2. Selecione o banco de dados `u367501017_sistema_arq`
3. Vá para a aba "Importar"
4. Faça upload do arquivo SQL e clique em "Executar"

### 5. Criação de Diretórios de Armazenamento

Certifique-se de que os seguintes diretórios existem e têm permissões de escrita (755):
- `/storage/documents`
- `/storage/temp`
- `/public/uploads` (se existir)

### 6. Verificação de SSL

1. No painel da Hostinger, vá para **Website → SSL**
2. Ative o SSL para o domínio `purple-wallaby-649054.hostingersite.com`
3. Habilite o redirecionamento para HTTPS

### 7. Teste do Sistema

Após concluir os passos acima, acesse o sistema:
```
https://purple-wallaby-649054.hostingersite.com
```

Use as credenciais padrão para login:
- **Admin**: admin@sistema.com / admin123

### 8. Solução de Problemas

Se você encontrar problemas:
1. Verifique os logs de erro no painel da Hostinger em **Avançado → Logs**
2. Temporariamente, ative o modo de debug em `config/hostinger.php` alterando `'debug' => false,` para `'debug' => true,`
3. Verifique as permissões dos diretórios e arquivos

## Referências Adicionais

Para instruções mais detalhadas, consulte:
- `TUTORIAL-HOSTINGER.md` - Tutorial completo para a Hostinger
- `DEPLOY-PRODUCAO.md` - Guia geral de implantação em produção
