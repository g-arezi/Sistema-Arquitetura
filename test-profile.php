<?php
// Teste específico do perfil

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Session;
use App\Models\UserFile as User;

Session::start();

echo "🔧 Teste do Perfil\n\n";

// Simular login como admin
$user = User::findByEmail('admin@sistema.com');
if ($user) {
    Session::set('user_id', $user['id']);
    Session::set('user_name', $user['name']);
    Session::set('user_email', $user['email']);
    Session::set('user_type', $user['type']);
    
    echo "✅ Login simulado: {$user['name']}\n";
    
    // Testar busca por ID
    $profileUser = User::findById(Session::get('user_id'));
    if ($profileUser) {
        echo "✅ Usuário encontrado para perfil:\n";
        echo "   Nome: {$profileUser['name']}\n";
        echo "   Email: {$profileUser['email']}\n";
        echo "   Tipo: {$profileUser['type']}\n";
    } else {
        echo "❌ Usuário não encontrado!\n";
    }
    
} else {
    echo "❌ Erro no login!\n";
}

echo "\n🌐 Agora acesse: http://localhost:8000/profile\n";
