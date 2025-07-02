# ✅ Checklist de Deploy - Sistema de Arquitetura

Use este checklist para garantir que todos os passos sejam seguidos durante o deploy.

## 🔧 Pré-Deploy (Servidor)

### Requisitos do Sistema
- [ ] Servidor com Linux Ubuntu 20.04+ ou similar
- [ ] Mínimo 2GB RAM, recomendado 4GB+
- [ ] Mínimo 10GB espaço livre em disco
- [ ] Acesso SSH como root ou sudo
- [ ] Domínio configurado apontando para o servidor

### Instalação de Software
- [ ] Apache 2.4+ ou Nginx 1.18+ instalado
- [ ] PHP 7.4+ ou 8.0+ instalado
- [ ] MySQL 8.0+ ou MariaDB 10.5+ instalado
- [ ] Composer instalado globalmente
- [ ] Git instalado
- [ ] Certbot instalado (para SSL)

### Extensões PHP
- [ ] php-mysql
- [ ] php-pdo
- [ ] php-mbstring
- [ ] php-json
- [ ] php-curl
- [ ] php-gd
- [ ] php-zip
- [ ] php-xml
- [ ] php-intl
- [ ] php-fileinfo
- [ ] php-openssl

## 🗄️ Configuração do Banco de Dados

- [ ] MySQL/MariaDB rodando e configurado
- [ ] Banco de dados criado: `sistema_arquitetura`
- [ ] Usuário específico criado para a aplicação
- [ ] Permissões adequadas concedidas ao usuário
- [ ] Charset UTF8MB4 configurado
- [ ] Estrutura do banco importada (se aplicável)
- [ ] Dados iniciais inseridos (se aplicável)

## 📁 Deploy da Aplicação

### Preparação
- [ ] Diretório `/var/www/html` criado
- [ ] Proprietário correto configurado (`www-data`)
- [ ] Repositório clonado ou código copiado
- [ ] Branch correto (main/master) baixado

### Configuração
- [ ] Arquivo `.env.production` criado e configurado
- [ ] Configurações de banco de dados definidas
- [ ] Configurações de email definidas
- [ ] URL da aplicação configurada
- [ ] Modo de produção ativado (`APP_DEBUG=false`)
- [ ] Configurações de segurança definidas

### Dependências e Permissões
- [ ] `composer install --no-dev --optimize-autoloader` executado
- [ ] Permissões de arquivos configuradas (644)
- [ ] Permissões de diretórios configuradas (755)
- [ ] Diretório `public/uploads` criado com permissões 755
- [ ] Diretório `storage/documents` criado com permissões 755
- [ ] Diretório `storage/temp` criado com permissões 755
- [ ] Diretório de logs criado: `/var/log/sistema-arquitetura`

## 🌐 Configuração do Servidor Web

### Apache
- [ ] Módulo `mod_rewrite` ativado
- [ ] Módulo `mod_headers` ativado
- [ ] Módulo `mod_ssl` ativado
- [ ] VirtualHost criado e configurado
- [ ] Document root apontando para `/var/www/html/public`
- [ ] Site ativado (`a2ensite`)
- [ ] Site padrão desativado (`a2dissite 000-default`)
- [ ] Arquivo `.htaccess` funcionando

### Nginx (se aplicável)
- [ ] Configuração do site criada
- [ ] Document root apontando para `/var/www/html/public`
- [ ] PHP-FPM configurado e rodando
- [ ] Site ativado (`ln -s` em sites-enabled)
- [ ] Configuração de rewrite funcionando

### Teste de Conectividade
- [ ] Servidor web reiniciado sem erros
- [ ] Site responde via HTTP
- [ ] Rewrite rules funcionando (URLs amigáveis)
- [ ] Arquivos estáticos sendo servidos (CSS, JS, imagens)

## 🔒 Configuração SSL

- [ ] Certbot instalado
- [ ] Certificado SSL obtido via Let's Encrypt
- [ ] Redirect HTTP → HTTPS configurado
- [ ] Certificado válido e funcionando
- [ ] Renovação automática configurada no cron

## 🛡️ Segurança

### Firewall
- [ ] UFW ativado
- [ ] Porta 22 (SSH) liberada
- [ ] Porta 80 (HTTP) liberada
- [ ] Porta 443 (HTTPS) liberada
- [ ] Outras portas desnecessárias bloqueadas

### Hardening
- [ ] Fail2Ban instalado e configurado
- [ ] Headers de segurança configurados
- [ ] Diretórios sensíveis protegidos
- [ ] Arquivos de configuração protegidos
- [ ] PHP configurado para produção
- [ ] Server tokens desabilitados

### Arquivos Sensíveis
- [ ] `.env.production` não acessível via web
- [ ] Diretório `config/` protegido
- [ ] Diretório `vendor/` protegido
- [ ] Diretório `src/` protegido
- [ ] Diretório `.git/` protegido (se existir)

## 📊 Logs e Monitoramento

- [ ] Logs de erro do PHP configurados
- [ ] Logs de acesso do servidor web configurados
- [ ] Logs da aplicação configurados
- [ ] Logrotate configurado
- [ ] Logs sendo escritos nos diretórios corretos

## 🔄 Backup e Manutenção

- [ ] Script de backup criado e testado
- [ ] Backup automático configurado no cron
- [ ] Diretório de backups criado: `/var/backups/sistema-arquitetura`
- [ ] Retenção de backups configurada (30 dias)
- [ ] Teste de restauração realizado

## ✅ Testes Funcionais

### Conectividade
- [ ] Página inicial carrega corretamente
- [ ] CSS e JavaScript carregando
- [ ] Imagens carregando
- [ ] URLs amigáveis funcionando

### Funcionalidades Core
- [ ] Formulário de login funciona
- [ ] Formulário de registro funciona
- [ ] Dashboard carrega após login
- [ ] Sidebar navegação funciona
- [ ] Logout funciona

### Recursos da Aplicação
- [ ] Upload de arquivos funciona
- [ ] Criação de projetos funciona
- [ ] Listagem de projetos funciona
- [ ] Perfil de usuário funciona
- [ ] Funcionalidades de admin funcionam (se aplicável)

### Comunicação
- [ ] Envio de emails funciona
- [ ] Notificações funcionam
- [ ] Formulários de contato funcionam (se aplicável)

## 🔍 Testes de Performance

- [ ] Tempo de carregamento da página inicial < 3 segundos
- [ ] Tempo de login < 2 segundos
- [ ] Upload de arquivos funciona dentro do limite esperado
- [ ] Consultas ao banco otimizadas
- [ ] Cache configurado adequadamente

## 📱 Testes de Compatibilidade

- [ ] Site funciona no Chrome
- [ ] Site funciona no Firefox
- [ ] Site funciona no Safari
- [ ] Site funciona no Edge
- [ ] Site responsivo funciona em dispositivos móveis

## 🚨 Testes de Segurança

- [ ] Teste de injeção SQL não funciona
- [ ] Teste de XSS não funciona
- [ ] Upload de arquivos maliciosos é bloqueado
- [ ] Acesso direto a arquivos sensíveis é negado
- [ ] Headers de segurança estão presentes

## 📋 Pós-Deploy

### Documentação
- [ ] Credenciais de produção documentadas e guardadas em local seguro
- [ ] URLs importantes documentadas
- [ ] Procedimentos de manutenção documentados
- [ ] Contatos de emergência atualizados

### Monitoramento
- [ ] Alertas de espaço em disco configurados
- [ ] Monitoramento de uptime configurado
- [ ] Monitoramento de logs configurado
- [ ] Alertas de erro configurados

### Treinamento
- [ ] Usuários finais treinados
- [ ] Administradores treinados
- [ ] Documentação de usuário disponível
- [ ] Suporte técnico informado

## 🎉 Go Live!

- [ ] Todos os itens acima verificados
- [ ] Teste final completo realizado
- [ ] Stakeholders notificados
- [ ] DNS propagado (se mudança de domínio)
- [ ] Sistema oficialmente em produção
- [ ] Monitoramento ativo nas primeiras 24h

---

## 📞 Contatos de Emergência

**Desenvolvedor:**
- Nome: _____________
- Telefone: _____________
- Email: _____________

**Administrador do Servidor:**
- Nome: _____________
- Telefone: _____________
- Email: _____________

**Provedor de Hospedagem:**
- Empresa: _____________
- Suporte: _____________
- Portal: _____________

## 📅 Informações do Deploy

- **Data do Deploy:** ___/___/______
- **Responsável:** _____________
- **Versão:** _____________
- **Environment:** Production
- **Status:** [ ] Sucesso [ ] Falha [ ] Parcial

**Observações:**
_________________________________________________
_________________________________________________
_________________________________________________

---

**Assinatura do Responsável:** ___________________________ **Data:** ___/___/______
