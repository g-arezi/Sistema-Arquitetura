<?php

namespace App\Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    public static function destroy(): void {
        session_destroy();
    }
    
    public static function regenerate(): void {
        session_regenerate_id(true);
    }
    
    public static function flash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }
    
    public static function getFlash(string $key): ?string {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    public static function hasFlash(string $key): bool {
        return isset($_SESSION['flash'][$key]);
    }
}
