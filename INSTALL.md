# Instruções de Instalação e Configuração

## Pré-requisitos

- PHP 8.0+ 
- MySQL 5.7+ ou MariaDB 10.3+
- Composer
- Servidor web (Apache/Nginx) ou usar o servidor embutido do PHP

## Instalação Passo a Passo

### 1. Configuração do Banco de Dados

1. **Inicie seu servidor MySQL/MariaDB**
2. **Crie um banco de dados** (opcional - o script pode criar):
   ```sql
   CREATE DATABASE sistema_arquitetura;
   ```
3. **Configure as credenciais** em `config/database.php`:
   ```php
   return [
       'host' => 'localhost',
       'database' => 'sistema_arquitetura',
       'username' => 'seu_usuario',
       'password' => 'sua_senha',
       'charset' => 'utf8mb4',
       'port' => 3306
   ];
   ```

### 2. Instalação das Dependências

```bash
composer install
```

### 3. Criação das Tabelas e Dados Iniciais

```bash
composer run install-db
```

Este comando irá:
- Criar o banco de dados (se não existir)
- Criar as tabelas necessárias
- Inserir usuários padrão
- Criar projeto de exemplo

### 4. Configuração de Email (Opcional)

Edite `config/mail.php` com suas configurações SMTP:

```php
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'seu-email@gmail.com',
    'password' => 'sua-senha-app',
    'from_address' => 'seu-email@gmail.com',
    'from_name' => 'Sistema de Arquitetura'
];
```

### 5. Executar o Sistema

#### Servidor PHP Embutido (Desenvolvimento)
```bash
composer run serve
```
Acesse: `http://localhost:8000`

#### Apache/Nginx (Produção)
Configure o document root para a pasta `public/`

## Usuários Padrão

Após executar `composer run install-db`:

| Tipo | Email | Senha | Funcionalidades |
|------|-------|-------|----------------|
| Admin | admin@sistema.com | admin123 | Controle total do sistema |
| Analista | analista@sistema.com | analista123 | Gerencia projetos e análises |
| Cliente | cliente@sistema.com | cliente123 | Cria projetos e faz uploads |

## Estrutura de Pastas

```
Sistema-Arquitetura/
├── config/                 # Configurações
│   ├── database.php        # Config do banco
│   └── mail.php           # Config de email
├── public/                # Arquivos públicos
│   ├── index.php          # Ponto de entrada
│   ├── css/               # Estilos CSS
│   ├── js/                # JavaScript
│   └── uploads/           # Arquivos enviados
├── scripts/               # Scripts de instalação
│   └── install.php        # Instala banco e dados
├── src/                   # Código fonte
│   ├── Controllers/       # Controladores MVC
│   ├── Core/             # Núcleo do framework
│   ├── Middleware/       # Middlewares
│   ├── Models/           # Modelos de dados
│   ├── Services/         # Serviços
│   └── Views/            # Templates
├── vendor/               # Dependências do Composer
├── composer.json         # Configuração do Composer
└── README.md            # Documentação
```

## Funcionalidades Implementadas

### ✅ Sistema de Usuários
- 3 tipos de usuários com permissões diferenciadas
- Autenticação segura com bcrypt
- Controle de acesso via middleware
- Perfil de usuário editável

### ✅ Gestão de Projetos
- Criação e visualização de projetos
- Upload seguro de documentos
- Controle de status (Pendente → Em Andamento → Concluído)
- Dashboard com estatísticas

### ✅ Interface Moderna
- Bootstrap 5 responsivo
- Design intuitivo e profissional
- Sidebar navegacional
- Feedback visual com alerts

### ✅ Segurança
- Sanitização de dados de entrada
- Prepared statements contra SQL injection
- Validação de uploads
- Headers de segurança

### ✅ Estrutura MVC
- Framework próprio com PSR-4
- Roteamento flexível
- Template engine simples
- Separação clara de responsabilidades

## Próximos Passos / Melhorias Futuras

- [ ] Sistema de notificações por email
- [ ] API REST para integrações
- [ ] Logs de auditoria
- [ ] Backup automático
- [ ] Interface de administração mais completa
- [ ] Sistema de comentários nos projetos
- [ ] Dashboard com gráficos
- [ ] Exportação de relatórios

## Suporte

Para dúvidas ou problemas:
1. Verifique os logs de erro do PHP
2. Confirme as configurações do banco de dados
3. Certifique-se de que as dependências estão instaladas
4. Verifique as permissões da pasta `public/uploads/`
