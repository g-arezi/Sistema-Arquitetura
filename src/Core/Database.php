<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $connection = null;
    
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            try {
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
                self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                throw new \Exception("Erro de conexão com o banco de dados: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
    
    public static function query(string $sql, array $params = []): \PDOStatement {
        $connection = self::getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public static function find(string $table, int $id): ?array {
        $stmt = self::query("SELECT * FROM {$table} WHERE id = ?", [$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function findBy(string $table, array $conditions): ?array {
        $where = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(' AND ', $where);
        $stmt = self::query("SELECT * FROM {$table} WHERE {$whereClause}", $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public static function findAll(string $table, array $conditions = []): array {
        $sql = "SELECT * FROM {$table}";
        $params = [];
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public static function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($data);
        
        return self::getConnection()->lastInsertId();
    }
    
    public static function update(string $table, int $id, array $data): bool {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $set);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute($data);
    }
    
    public static function delete(string $table, int $id): bool {
        $stmt = self::query("DELETE FROM {$table} WHERE id = ?", [$id]);
        return $stmt->rowCount() > 0;
    }
}
