<?php
// Este é um arquivo index simplificado para diagnóstico do erro 403
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Sistema de Arquitetura</h1>";
echo "<p>Teste de acesso ao arquivo index.php</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Diretório atual: " . __DIR__ . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Verificar acesso ao autoloader
echo "<h2>Teste de autoloader:</h2>";
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "<p>Arquivo autoload.php encontrado!</p>";
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        echo "<p>Autoloader carregado com sucesso!</p>";
    } catch (Exception $e) {
        echo "<p>Erro ao carregar autoloader: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ERRO: Arquivo autoload.php não encontrado.</p>";
    echo "<p>Caminhos:</p>";
    echo "<p>__DIR__: " . __DIR__ . "</p>";
    echo "<p>__DIR__ . '/../vendor/autoload.php': " . __DIR__ . '/../vendor/autoload.php' . "</p>";
}

// Teste de acesso ao diretório
echo "<h2>Teste de diretórios:</h2>";
$directories = [
    __DIR__,
    __DIR__ . '/..',
    __DIR__ . '/../vendor',
    __DIR__ . '/../config',
    __DIR__ . '/../src',
];

foreach ($directories as $dir) {
    echo "<p>Diretório: $dir - " . (is_dir($dir) ? "Existe" : "Não existe") . "</p>";
}
?>
