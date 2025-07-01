<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\ProjectFile as Project;
use App\Models\UserFile as User;

echo "=== TESTE DAS FUNCIONALIDADES ADMIN ===\n\n";

// Teste 1: Verificar projetos existentes
echo "1. Projetos disponíveis:\n";
$projects = Project::findAll();
foreach ($projects as $project) {
    echo "   ID: {$project['id']} - {$project['title']} - Status: {$project['status']}\n";
}
echo "\n";

// Teste 2: Teste de atribuição de analista
echo "2. Testando atribuição de analista:\n";
if (!empty($projects)) {
    $projectId = $projects[0]['id'];
    $analystId = 2; // Analista Sistema
    
    echo "   Atribuindo analista ID $analystId ao projeto ID $projectId...\n";
    $result = Project::assignAnalyst($projectId, $analystId);
    echo "   Resultado: " . ($result ? "SUCESSO" : "FALHA") . "\n";
    
    // Verificar se foi atribuído
    $updatedProject = Project::findById($projectId);
    echo "   Analista atual: " . ($updatedProject['analyst_name'] ?? 'Nenhum') . "\n";
}
echo "\n";

// Teste 3: Teste de alteração de status
echo "3. Testando alteração de status:\n";
if (!empty($projects)) {
    $projectId = $projects[0]['id'];
    $newStatus = 'in_progress';
    
    echo "   Alterando status do projeto ID $projectId para '$newStatus'...\n";
    $result = Project::changeStatus($projectId, $newStatus);
    echo "   Resultado: " . ($result ? "SUCESSO" : "FALHA") . "\n";
    
    // Verificar se foi alterado
    $updatedProject = Project::findById($projectId);
    echo "   Status atual: " . ($updatedProject['status'] ?? 'Desconhecido') . "\n";
}
echo "\n";

// Teste 4: Teste de criação de projeto
echo "4. Testando criação de projeto:\n";
$newProjectData = [
    'title' => 'Projeto Teste Admin',
    'description' => 'Projeto criado para testar funcionalidades administrativas',
    'client_id' => 3,
    'analyst_id' => 2,
    'status' => 'pending'
];

echo "   Criando novo projeto...\n";
$newProjectId = Project::create($newProjectData);
echo "   Novo projeto criado com ID: $newProjectId\n";

// Verificar se foi criado
$createdProject = Project::findById($newProjectId);
if ($createdProject) {
    echo "   Projeto criado: {$createdProject['title']}\n";
} else {
    echo "   ERRO: Projeto não foi encontrado após criação\n";
}
echo "\n";

// Teste 5: Teste de exclusão
echo "5. Testando exclusão de projeto:\n";
if ($newProjectId) {
    echo "   Excluindo projeto ID $newProjectId...\n";
    $result = Project::delete($newProjectId);
    echo "   Resultado: " . ($result ? "SUCESSO" : "FALHA") . "\n";
    
    // Verificar se foi excluído
    $deletedProject = Project::findById($newProjectId);
    if (!$deletedProject) {
        echo "   Projeto excluído com sucesso\n";
    } else {
        echo "   ERRO: Projeto ainda existe após exclusão\n";
    }
}
echo "\n";

// Teste 6: Verificar usuários para formulários
echo "6. Usuários disponíveis para formulários:\n";
$users = User::findAll();
$clients = array_filter($users, fn($u) => $u['type'] === 'client');
$analysts = array_filter($users, fn($u) => $u['type'] === 'analyst');

echo "   Clientes:\n";
foreach ($clients as $client) {
    echo "     ID: {$client['id']} - {$client['name']} ({$client['email']})\n";
}

echo "   Analistas:\n";
foreach ($analysts as $analyst) {
    echo "     ID: {$analyst['id']} - {$analyst['name']} ({$analyst['email']})\n";
}
echo "\n";

echo "=== TESTES CONCLUÍDOS ===\n";
