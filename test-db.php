<?php
// Teste de conexão com banco de dados

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'port' => 3306
];

try {
    echo "Testando conexão com MySQL...\n";
    $dsn = "mysql:host={$config['host']};port={$config['port']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "✅ Conexão com MySQL estabelecida com sucesso!\n";
    
    // Verificar se banco existe
    $stmt = $pdo->query("SHOW DATABASES LIKE 'sistema_arquitetura'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Banco 'sistema_arquitetura' encontrado!\n";
        
        // Verificar tabelas
        $pdo->exec("USE sistema_arquitetura");
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "✅ Tabelas encontradas: " . implode(', ', $tables) . "\n";
        } else {
            echo "⚠️ Banco existe mas não há tabelas. Execute: composer run install-db\n";
        }
    } else {
        echo "⚠️ Banco 'sistema_arquitetura' não encontrado. Execute: composer run install-db\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    echo "\nInstruções:\n";
    echo "1. Certifique-se de que o MySQL/MariaDB está rodando\n";
    echo "2. Verifique as credenciais em config/database.php\n";
    echo "3. Se necessário, altere a senha no arquivo de configuração\n";
}
