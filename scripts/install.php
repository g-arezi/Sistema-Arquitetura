<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/database.php';

try {
    // Conectar sem especificar banco de dados para criar
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Criar banco de dados
    echo "Criando banco de dados...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['database']}");
    $pdo->exec("USE {$config['database']}");
    
    // Criar tabela de usuários
    echo "Criando tabela de usuários...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            type ENUM('admin', 'analyst', 'client') DEFAULT 'client',
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Criar tabela de projetos
    echo "Criando tabela de projetos...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT NOT NULL,
            status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
            user_id INT NOT NULL,
            analyst_id INT NULL,
            deadline DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (analyst_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ");
    
    // Criar tabela de documentos
    echo "Criando tabela de documentos...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_size INT NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            uploaded_by INT NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Inserir usuários padrão
    echo "Criando usuários padrão...\n";
    
    $users = [
        [
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'type' => 'admin'
        ],
        [
            'name' => 'Analista Sistema',
            'email' => 'analista@sistema.com',
            'password' => password_hash('analista123', PASSWORD_DEFAULT),
            'type' => 'analyst'
        ],
        [
            'name' => 'Cliente Teste',
            'email' => 'cliente@sistema.com',
            'password' => password_hash('cliente123', PASSWORD_DEFAULT),
            'type' => 'client'
        ]
    ];
    
    foreach ($users as $user) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO users (name, email, password, type, active) 
            VALUES (:name, :email, :password, :type, 1)
        ");
        $stmt->execute($user);
    }
    
    // Criar projeto de exemplo
    echo "Criando projeto de exemplo...\n";
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO projects (title, description, user_id, analyst_id, status) 
        VALUES ('Projeto Residencial Example', 'Projeto arquitetônico para residência unifamiliar de 150m²', 3, 2, 'in_progress')
    ");
    $stmt->execute();
    
    echo "Instalação concluída com sucesso!\n";
    echo "\nUsuários criados:\n";
    echo "Admin: admin@sistema.com / admin123\n";
    echo "Analista: analista@sistema.com / analista123\n";
    echo "Cliente: cliente@sistema.com / cliente123\n";
    
} catch (PDOException $e) {
    echo "Erro na instalação: " . $e->getMessage() . "\n";
    exit(1);
}
