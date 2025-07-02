# Checklist de Deployment em Produção

Use este checklist para garantir que todos os passos necessários para o deployment em produção sejam realizados corretamente.

## Preparação Local

- [ ] Código em estado estável e testado
- [ ] Todas as alterações commitadas no controle de versão
- [ ] Versão do PHP compatível (PHP 8.0+)
- [ ] Dependências do Composer atualizadas
- [ ] Arquivo `config/production.php` configurado corretamente
- [ ] Validação pré-produção executada (`scripts/validate-production.sh`)
- [ ] Banco de dados exportado (`scripts/export-database.sh`)

## Configuração do Servidor

- [ ] Servidor com requisitos mínimos (PHP 8.0+, MySQL 5.7+/MariaDB 10.3+)
- [ ] Script de preparação executado (`scripts/prepare-production.sh`)
- [ ] Diretórios de armazenamento criados com permissões corretas
- [ ] Configuração do servidor web (Nginx/Apache) testada

## Deployment

- [ ] Upload dos arquivos do projeto para o servidor
- [ ] Permissões de arquivos configuradas corretamente
- [ ] Banco de dados criado e estrutura importada
- [ ] Dependências do Composer instaladas em modo de produção
- [ ] Certificado SSL instalado e configurado
- [ ] Redirecionamento HTTP para HTTPS funcionando

## Verificação Pós-Deployment

- [ ] Site acessível via HTTPS
- [ ] Processo de registro funcionando corretamente
- [ ] Sistema de aprovação de usuários funcionando
- [ ] Login de usuários aprovados funcionando
- [ ] Todas as funcionalidades principais testadas
- [ ] Verificação de logs de erro (sem erros críticos)
- [ ] Backup inicial realizado

## Otimização e Segurança

- [ ] Caching configurado (se necessário)
- [ ] Configurações de segurança do PHP aplicadas
- [ ] Headers de segurança configurados
- [ ] Sistema de backup automático configurado
- [ ] Monitoramento configurado

## Finalização

- [ ] Documentação atualizada
- [ ] Credenciais de acesso admin armazenadas com segurança
- [ ] Equipe informada sobre o deployment concluído
- [ ] Plano de manutenção estabelecido
