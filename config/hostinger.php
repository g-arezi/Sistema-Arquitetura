<?php

/**
 * Configurações específicas para Hostinger
 */

return [
    // Configurações de banco de dados
    'database' => [
        'host' => 'localhost',
        'name' => 'u367501017_sistema_arq', // Banco de dados na Hostinger
        'user' => 'u367501017_oArezi',      // Usuário na Hostinger
        'pass' => '2Itdigital@sA',          // Senha na Hostinger
        'charset' => 'utf8mb4',
    ],
    
    // Configurações da aplicação
    'app' => [
        'debug' => false,
        'environment' => 'production',
        'url' => 'https://purple-wallaby-649054.hostingersite.com', // Domínio na Hostinger
        'timezone' => 'America/Sao_Paulo',
    ],
    
    // Email - Configuração para Hostinger
    'mail' => [
        'host' => 'smtp.hostinger.com',
        'port' => 587,
        'username' => 'contato@seudominio.com', // Altere para seu email
        'password' => 'SuaSenhaEmail',         // Altere para senha do email
        'encryption' => 'tls',
        'from_name' => 'Sistema de Arquitetura',
        'from_address' => 'contato@seudominio.com', // Altere para seu email
    ],
    
    // Armazenamento - Caminhos para Hostinger
    'storage' => [
        'documents_path' => '/home/u367501017/domains/purple-wallaby-649054.hostingersite.com/public_html/storage/documents',
        'temp_path' => '/home/u367501017/domains/purple-wallaby-649054.hostingersite.com/public_html/storage/temp',
    ],
    
    // Limites - Ajustados para Hostinger
    'limits' => [
        'max_upload_size' => 8388608, // 8MB em bytes (limite típico na Hostinger em planos básicos)
        'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg'],
    ],
    
    // Segurança - Recomendações para Hostinger
    'security' => [
        'hash_cost' => 12,
        'session_lifetime' => 7200, // 2 horas
        'csrf_token_expiry' => 3600, // 1 hora
    ],
];
