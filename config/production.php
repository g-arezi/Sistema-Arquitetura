<?php
/**
 * Configurações de Produção
 * Este arquivo deve ser copiado para o servidor de produção
 */

return [
    // Configurações de banco de dados
    'database' => [
        'host' => 'localhost',
        'name' => 'sistema_arquitetura',
        'user' => 'db_user_prod',
        'pass' => 'SENHA_SEGURA_AQUI', // Altere para uma senha segura!
        'charset' => 'utf8mb4',
    ],
    
    // Configurações da aplicação
    'app' => [
        'debug' => false,
        'environment' => 'production',
        'url' => 'https://sistema-arquitetura.com.br',
        'timezone' => 'America/Sao_Paulo',
    ],
    
    // Segurança
    'security' => [
        'hash_cost' => 12,
        'session_lifetime' => 7200, // 2 horas
        'csrf_token_expiry' => 3600, // 1 hora
    ],
    
    // Email
    'mail' => [
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'noreply@sistema-arquitetura.com.br',
        'password' => 'SENHA_EMAIL_AQUI', // Altere para a senha do email
        'encryption' => 'tls',
        'from_name' => 'Sistema de Arquitetura',
        'from_address' => 'noreply@sistema-arquitetura.com.br',
    ],
    
    // Armazenamento
    'storage' => [
        'documents_path' => '/var/www/sistema-arquitetura/storage/documents',
        'temp_path' => '/var/www/sistema-arquitetura/storage/temp',
    ],
    
    // Limites
    'limits' => [
        'max_upload_size' => 15728640, // 15MB em bytes
        'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg'],
    ],
];
