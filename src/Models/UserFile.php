<?php

namespace App\Models;

class UserFile {
    private static array $users = [
        1 => [
            'id' => 1,
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => '$2y$10$8kj3wWDBvOfAOGCfJkB4deQkgH6aYpwKwk.RA04.B1E6jd8/7lgSa', // admin123
            'type' => 'admin',
            'active' => 1,
            'approved' => 1,
            'created_at' => '2025-07-01 10:00:00'
        ],
        2 => [
            'id' => 2,
            'name' => 'Analista Sistema',
            'email' => 'analyst@sistema.com',
            'password' => '$2y$10$qLRi5X1QL2Vw7.Krme5qA.8ESTM5N/0SR0bG6zxDppXUY7VXY.S8a', // analyst123
            'type' => 'analyst',
            'active' => 1,
            'approved' => 1,
            'created_at' => '2025-07-01 10:00:00'
        ],
        3 => [
            'id' => 3,
            'name' => 'Cliente Teste',
            'email' => 'client@sistema.com',
            'password' => '$2y$10$3WswNYETeoI0NH8weR8dQ.9NyZC6omPIzBanrtMfT0k.tUCMKlKeW', // client123
            'type' => 'client',
            'active' => 1,
            'approved' => 1,
            'created_at' => '2025-07-01 10:00:00'
        ],
        4 => [
            'id' => 4,
            'name' => 'Cliente Pendente',
            'email' => 'pending@sistema.com',
            'password' => '$2y$10$3WswNYETeoI0NH8weR8dQ.9NyZC6omPIzBanrtMfT0k.tUCMKlKeW', // client123
            'type' => 'client',
            'active' => 1,
            'approved' => 0, // Aguardando aprovação
            'created_at' => '2025-07-01 14:30:00'
        ]
    ];
    
    public static function findByEmail(string $email): ?array {
        foreach (self::$users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    
    public static function findById(int $id): ?array {
        return self::$users[$id] ?? null;
    }
    
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public static function findAll(): array {
        return array_values(self::$users);
    }
    
    public static function getUserStats(int $userId): array {
        return [
            'projects' => 2,
            'documents' => 5
        ];
    }
    
    public static function update(int $id, array $data): bool {
        if (!isset(self::$users[$id])) {
            return false;
        }
        
        // Simula atualização (em uma implementação real, salvaria em arquivo ou banco)
        foreach ($data as $key => $value) {
            if (isset(self::$users[$id][$key])) {
                self::$users[$id][$key] = $value;
            }
        }
        
        return true;
    }
    
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public static function toggleStatus(int $id): bool {
        if (!isset(self::$users[$id])) {
            return false;
        }
        
        // Simula toggle do status
        self::$users[$id]['active'] = self::$users[$id]['active'] ? 0 : 1;
        return true;
    }
    
    public static function create(array $userData): int {
        // Simula criação de um novo usuário (em implementação real, salvaria em arquivo ou banco)
        $id = count(self::$users) + 1;
        
        self::$users[$id] = [
            'id' => $id,
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => self::hashPassword($userData['password']),
            'type' => $userData['type'] ?? 'client', // Tipo padrão é cliente
            'active' => 1,
            'approved' => 0, // Por padrão, aguardando aprovação
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $id;
    }
    
    public static function toggleApproval(int $id): bool {
        if (!isset(self::$users[$id])) {
            return false;
        }
        
        // Simula toggle da aprovação
        self::$users[$id]['approved'] = self::$users[$id]['approved'] ? 0 : 1;
        return true;
    }
    
    public static function approve(int $id): bool {
        if (!isset(self::$users[$id])) {
            return false;
        }
        
        // Aprova o usuário
        self::$users[$id]['approved'] = 1;
        return true;
    }
    
    public static function reject(int $id): bool {
        if (!isset(self::$users[$id])) {
            return false;
        }
        
        // Rejeita o usuário (no caso, apenas desativa)
        self::$users[$id]['active'] = 0;
        return true;
    }
    
    public static function getPendingApproval(): array {
        return array_values(array_filter(self::$users, function($user) {
            return $user['approved'] == 0 && $user['active'] == 1;
        }));
    }
}
