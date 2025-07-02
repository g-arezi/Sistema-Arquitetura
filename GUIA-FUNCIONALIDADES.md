# 📝 GUIA DE FUNCIONALIDADES DO SISTEMA DE ARQUITETURA

## 🌟 Visão Geral

O Sistema de Arquitetura é uma plataforma web completa para gerenciamento de projetos arquitetônicos. Este guia explica todas as funcionalidades disponíveis, organizadas por tipo de usuário.

## 👨‍💼 ADMINISTRADOR

### 📊 Dashboard Administrativo

- **Estatísticas Interativas**: Cards clicáveis mostram números totais de usuários, projetos, documentos e projetos pendentes.
- **Ações Rápidas**: Botões para gerenciar usuários, projetos, criar projetos rápidos ou acessar formulário completo.
- **Atividades Recentes**: Lista de ações recentes com botão de atualização funcional.
- **Projetos Recentes**: Tabela com os últimos projetos e ações disponíveis.

### 📁 Gerenciamento de Projetos

- **Estatísticas**: Visualização de totais por status (pendentes, em andamento, concluídos).
- **Filtros Avançados**: Busca por status, cliente ou termo específico.
- **Lista Completa**: Todos os projetos com informações detalhadas.
- **Ações por Projeto**:
  - **Visualizar**: Acesso à página de detalhes do projeto.
  - **Editar**: Modificar informações como título, descrição, prazos.
  - **Atribuir Analista**: Designar um analista através de modal interativo.
  - **Alterar Status**: Mudar o estado do projeto com feedback visual.
  - **Excluir**: Remover projetos com confirmação de segurança.

### 👥 Gerenciamento de Usuários

- **Lista de Usuários**: Todos os usuários cadastrados com informações detalhadas.
- **Ativar/Desativar**: Alternar status do usuário entre ativo e inativo.
- **Editar Usuário**: Modificar informações de perfil e tipo de acesso.
- **Visualizar Detalhes**: Ver projetos associados e estatísticas do usuário.

### 🆕 Criação de Projetos

- **Formulário Completo**: Todos os campos necessários para um novo projeto.
- **Criação Rápida**: Modal simplificado para cadastro essencial.
- **Atribuição de Analista**: Opcional durante criação ou posterior.
- **Definição de Prazos**: Calendário para selecionar datas limite.

## 👨‍🔧 ANALISTA

### 📊 Dashboard do Analista

- **Projetos Atribuídos**: Lista de projetos sob responsabilidade do analista.
- **Indicadores de Status**: Visualização rápida do estado de cada projeto.
- **Alertas de Prazo**: Destaque para projetos próximos do vencimento.

### 📋 Gerenciamento de Projetos

- **Visualização Detalhada**: Acesso a todas as informações e documentos.
- **Atualização de Status**: Botões para iniciar, concluir ou cancelar projetos.
- **Upload de Documentos**: Envio de arquivos com preview e validação.
- **Comunicação**: Interface para envio de mensagens ao cliente.

## 👨‍💻 CLIENTE

### 📊 Dashboard do Cliente

- **Meus Projetos**: Lista de projetos solicitados pelo cliente.
- **Status Atual**: Visualização do andamento de cada projeto.
- **Documentos Recentes**: Últimos arquivos enviados ou recebidos.

### 📋 Gerenciamento de Projetos

- **Solicitar Novo Projeto**: Formulário para cadastro de novos projetos.
- **Visualizar Detalhes**: Acesso a todas as informações e documentos.
- **Upload de Documentos**: Envio de arquivos com preview e validação.
- **Acompanhamento**: Visualização do histórico de atualizações.

## 📄 PROJETOS

### 📋 Página de Detalhes

- **Informações Completas**: Título, descrição, cliente, analista, status, datas.
- **Lista de Documentos**: Todos os arquivos associados ao projeto.
- **Histórico de Alterações**: Timeline com registro de atividades.
- **Ações Disponíveis**: Botões para as ações permitidas conforme perfil do usuário.

### 📤 Upload de Documentos

- **Interface Drag-and-Drop**: Arraste e solte arquivos ou clique para selecionar.
- **Preview de Arquivos**: Visualização do arquivo antes do envio.
- **Validação**: Verificação de tipo e tamanho de arquivo.
- **Feedback Visual**: Barra de progresso e mensagens de sucesso/erro.

### 🔄 Alteração de Status

- **Botões Intuitivos**: Ações disponíveis conforme status atual.
- **Confirmação**: Validação antes de alterar o estado do projeto.
- **Notificação**: Feedback visual após a mudança.

## 🛠️ FUNCIONALIDADES GLOBAIS

### 🔐 Autenticação

- **Login Seguro**: Acesso com email e senha.
- **Recuperação de Senha**: Processo para redefinir senhas esquecidas.
- **Sessões**: Gerenciamento automático de tempo de sessão.

### 👤 Perfil de Usuário

- **Visualização de Dados**: Informações pessoais e estatísticas.
- **Edição de Perfil**: Atualização de dados pessoais.
- **Alteração de Senha**: Mecanismo para trocar senha atual.

### 🔍 Pesquisa e Filtros

- **Busca Global**: Pesquisa em todo o sistema.
- **Filtros Contextuais**: Opções de filtro específicas para cada seção.
- **Ordenação**: Organização de listas por diferentes critérios.

### 📱 Responsividade

- **Layout Adaptativo**: Interface otimizada para desktop, tablet e smartphone.
- **Menu Mobile**: Navegação simplificada em dispositivos menores.
- **Interações Touch**: Suporte a gestos em dispositivos touchscreen.

## 🎨 ELEMENTOS DE INTERFACE

### 🎯 Modais Interativos

- **Criação Rápida**: Formulários em modal para ações rápidas.
- **Confirmações**: Validação de ações críticas ou irreversíveis.
- **Visualização de Detalhes**: Informações expandidas sem troca de página.

### 📊 Feedback Visual

- **Alertas**: Mensagens de sucesso, erro, aviso ou informação.
- **Animações**: Transições suaves para melhor experiência.
- **Indicadores de Loading**: Feedback durante operações assíncronas.

### 🧩 Componentes Auxiliares

- **Tooltips**: Informações adicionais ao passar o mouse.
- **Dropdowns**: Menus compactos para múltiplas opções.
- **Badges**: Indicadores visuais de status ou quantidade.
- **Timeline**: Visualização cronológica de eventos.
