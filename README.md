# Sistema de Arquitetura

Um sistema completo de gestão de documentos e projetos de arquitetura desenvolvido em PHP 8+ com Composer.

## Funcionalidades

### 🏗️ Sistema de Usuários
- **3 tipos de usuários**: Administrador, Analista e Cliente
- **Autenticação**: Login e cadastro seguros
- **Controle de acesso**: Permissões diferenciadas por tipo

### 📋 Dashboard Interativo
- **Upload de documentos**: Interface moderna para envio de arquivos
- **Gestão de projetos**: Acompanhamento de status
- **Painel administrativo**: Controle completo para admins

### 📧 Sistema de Notificações
- **Email automático**: Notificações para pendências
- **Alertas**: Atualizações de projeto
- **Lembretes**: Documentos em atraso

## 🛠️ Tecnologias

- **PHP 8+** com Composer
- **Framework modular próprio**
- **Bootstrap 5** para UI responsiva
- **MySQL** para banco de dados
- **PHPMailer** para emails
- **Upload seguro** de arquivos

## ✅ Sistema Funcionando!

O sistema está **rodando e funcionando** em `http://localhost:8000`

### 🚀 Como Testar

1. **Acesse**: `http://localhost:8000`
2. **Faça login** com uma das contas:
   - **Admin**: `admin@sistema.com` / `admin123`
   - **Analista**: `analista@sistema.com` / `analista123`
   - **Cliente**: `cliente@sistema.com` / `cliente123`
3. **Explore** o dashboard e funcionalidades

### 📋 Status das Funcionalidades

- ✅ **Sistema de rotas** funcionando
- ✅ **Login/logout** operacional
- ✅ **Dashboard** com dados de demonstração
- ✅ **Interface Bootstrap 5** responsiva
- ✅ **Controle de acesso** por tipo de usuário
- ⚠️ **Banco de dados** (requer MySQL/MariaDB rodando)

### 🔧 Para Usar com Banco de Dados

Se você tem MySQL/MariaDB instalado:

1. **Inicie o MySQL**
2. **Execute**: `composer run install-db`
3. **Reinicie** o servidor

Caso contrário, o sistema funciona com dados de demonstração em arquivo.

## Instalação

1. **Clone o repositório**
   ```bash
   git clone <repository-url>
   cd Sistema-Arquitetura
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Configure o banco de dados**
   - Inicie o MySQL/MariaDB
   - Edite `config/database.php` com suas credenciais
   - Execute: `composer run install-db`

4. **Configure o email (opcional)**
   - Edite `config/mail.php` com suas configurações SMTP

5. **Inicie o servidor**
   ```bash
   composer run serve
   ```

6. **Acesse o sistema**
   - URL: `http://localhost:8000`

## Estrutura do Projeto

```
src/
├── Controllers/     # Controladores MVC
├── Models/         # Modelos de dados
├── Views/          # Templates de visualização
├── Core/           # Núcleo do framework
├── Services/       # Serviços (Email, Upload, etc.)
└── Middleware/     # Middlewares de autenticação

public/             # Arquivos públicos
├── index.php       # Ponto de entrada
├── css/           # Estilos CSS
├── js/            # JavaScript
└── uploads/       # Arquivos enviados

config/            # Configurações
scripts/           # Scripts de instalação
```

## Uso

Acesse `http://localhost:8000` após iniciar o servidor.

### Usuários Padrão (após executar install-db)

- **Admin**: admin@sistema.com / admin123
- **Analista**: analista@sistema.com / analista123  
- **Cliente**: cliente@sistema.com / cliente123

### Funcionalidades Principais

#### 🏗️ Sistema de Usuários
- **3 tipos**: Admin (controle total), Analista (gerencia projetos), Cliente (cria projetos)
- **Autenticação**: Login/logout seguro com validação
- **Permissões**: Controle de acesso baseado em roles

#### 📋 Dashboard Interativo
- **Visão geral**: Estatísticas personalizadas por tipo de usuário
- **Gestão**: Acompanhamento de projetos e documentos
- **Interface**: Design responsivo com Bootstrap 5

#### 📁 Gestão de Projetos
- **Criação**: Clientes podem criar novos projetos
- **Atribuição**: Admins podem designar analistas
- **Status**: Acompanhamento do progresso (Pendente → Em Andamento → Concluído)
- **Upload**: Sistema seguro de envio de documentos

#### 📧 Sistema de Notificações (configurável)
- **Email automático**: Notificações de status
- **Alertas**: Atualizações em tempo real
- **PHPMailer**: Integração completa

### Segurança
- **Sanitização**: Todos os dados são sanitizados
- **Prepared Statements**: Proteção contra SQL injection
- **Upload seguro**: Validação de tipos e tamanhos de arquivo
- **Middleware**: Controle de acesso em todas as rotas protegidas
