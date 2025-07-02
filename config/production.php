<?php
/**
 * Configurações de Produção
 * Este arquivo deve ser usado no servidor de produção
 * As configurações são lidas das variáveis de ambiente
 */

// Carregar variáveis de ambiente se existir arquivo .env
if (file_exists(__DIR__ . '/../.env.production')) {
    $lines = file(__DIR__ . '/../.env.production', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

return [
    // Configurações de banco de dados
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'sistema_arquitetura',
        'user' => $_ENV['DB_USER'] ?? 'root',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],
    
    // Configurações da aplicação
    'app' => [
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'environment' => $_ENV['APP_ENV'] ?? 'production',
        'url' => $_ENV['APP_URL'] ?? 'https://sistema-arquitetura.com.br',
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo',
    ],
    
    // Segurança
    'security' => [
        'hash_cost' => intval($_ENV['HASH_COST'] ?? 12),
        'session_lifetime' => intval($_ENV['SESSION_LIFETIME'] ?? 7200),
        'csrf_token_expiry' => intval($_ENV['CSRF_TOKEN_EXPIRY'] ?? 3600),
        'session_secure' => filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN),
    ],
    
    // Email
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => intval($_ENV['MAIL_PORT'] ?? 587),
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Sistema de Arquitetura',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@sistema-arquitetura.com.br',
    ],
    
    // Armazenamento
    'storage' => [
        'documents_path' => $_ENV['STORAGE_DOCUMENTS_PATH'] ?? '/var/www/html/storage/documents',
        'temp_path' => $_ENV['STORAGE_TEMP_PATH'] ?? '/var/www/html/storage/temp',
    ],
    
    // Limites
    'limits' => [
        'max_upload_size' => intval($_ENV['MAX_UPLOAD_SIZE'] ?? 15728640), // 15MB em bytes
        'allowed_file_types' => explode(',', $_ENV['ALLOWED_FILE_TYPES'] ?? 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,dwg'),
    ],
    
    // Logs
    'logs' => [
        'error_log' => $_ENV['ERROR_LOG_PATH'] ?? '/var/log/sistema-arquitetura/error.log',
        'access_log' => $_ENV['ACCESS_LOG_PATH'] ?? '/var/log/sistema-arquitetura/access.log',
    ],
    
    // Cache
    'cache' => [
        'enabled' => filter_var($_ENV['CACHE_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'ttl' => intval($_ENV['CACHE_TTL'] ?? 3600),
    ],
    
    // Backup
    'backup' => [
        'enabled' => filter_var($_ENV['BACKUP_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'path' => $_ENV['BACKUP_PATH'] ?? '/var/backups/sistema-arquitetura',
        'retention_days' => intval($_ENV['BACKUP_RETENTION_DAYS'] ?? 30),
    ],
];
