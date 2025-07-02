<?php
// Arquivo index.php para a raiz (public_html)
// Este arquivo redireciona para o index.php na pasta public

// Exibir erros para debug (remova em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Verificar se a pasta public existe
if (is_dir(__DIR__ . '/public') && file_exists(__DIR__ . '/public/index.php')) {
    // Redirecionar para o index.php na pasta public
    require_once __DIR__ . '/public/index.php';
} else {
    // Modo de recuperação - exibir informações de diagnóstico
    echo "<h1>Sistema de Arquitetura - Modo de Diagnóstico</h1>";
    echo "<p>Ocorreu um problema ao carregar a aplicação.</p>";
    
    echo "<h2>Informações do Sistema:</h2>";
    echo "<ul>";
    echo "<li>PHP Version: " . phpversion() . "</li>";
    echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
    echo "<li>Current Script: " . $_SERVER['SCRIPT_NAME'] . "</li>";
    echo "<li>Request URI: " . $_SERVER['REQUEST_URI'] . "</li>";
    echo "</ul>";
    
    echo "<h2>Estrutura de Diretórios:</h2>";
    echo "<ul>";
    
    // Verificar diretórios principais
    $directories = [
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/config',
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
    ];
    
    foreach ($directories as $dir) {
        $exists = is_dir($dir);
        $readable = $exists && is_readable($dir);
        
        echo "<li>" . basename($dir) . ": " . 
             ($exists ? "✓ Existe" : "❌ Não existe") . 
             ($readable ? ", Leitura OK" : ", Sem permissão de leitura") .
             "</li>";
    }
    echo "</ul>";
    
    echo "<h2>Solução:</h2>";
    echo "<p>Verifique se:</p>";
    echo "<ol>";
    echo "<li>O diretório <code>public</code> existe na raiz do site</li>";
    echo "<li>O arquivo <code>public/index.php</code> existe e tem permissões de leitura</li>";
    echo "<li>Consulte o arquivo SOLUCAO-ERRO-403-PERSISTENTE.md para mais instruções</li>";
    echo "</ol>";
}
?>
