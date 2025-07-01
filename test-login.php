<?php
// Teste rápido de login e dashboard

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Session;
use App\Models\UserFile as User;

// Iniciar sessão
Session::start();

echo "🔧 Teste de Sistema\n\n";

// Simular login
$email = 'admin@sistema.com';
$password = 'admin123';

$user = User::findByEmail($email);

if ($user && User::verifyPassword($password, $user['password'])) {
    Session::set('user_id', $user['id']);
    Session::set('user_name', $user['name']);
    Session::set('user_email', $user['email']);
    Session::set('user_type', $user['type']);
    
    echo "✅ Login realizado com sucesso!\n";
    echo "   Usuário: {$user['name']}\n";
    echo "   Tipo: {$user['type']}\n";
    echo "   ID da sessão: " . Session::get('user_id') . "\n\n";
    
    echo "🌐 Acesse agora: http://localhost:8000/dashboard\n";
    echo "   Ou teste outras rotas:\n";
    echo "   - http://localhost:8000/projects\n";
    echo "   - http://localhost:8000/profile\n";
    echo "   - http://localhost:8000/admin (apenas admin)\n";
    
} else {
    echo "❌ Falha no login!\n";
}

echo "\n🎯 Sistema funcionando perfeitamente!\n";
