<?php
// Arquivo para verificar a estrutura dos diretórios no servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Verificação de Estrutura de Diretórios</h1>";

// Informações básicas do servidor
echo "<h2>Informações do Servidor:</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Current Script: " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li>Current Path: " . __FILE__ . "</li>";
echo "<li>Current Directory: " . __DIR__ . "</li>";
echo "</ul>";

// Verificar diretórios principais
echo "<h2>Estrutura de Diretórios:</h2>";
echo "<ul>";

// Diretório raiz
$root = $_SERVER['DOCUMENT_ROOT'];
echo "<li>Document Root ($root): " . (is_dir($root) ? "✓ Existe" : "❌ Não existe") . "</li>";

// Diretórios do projeto
$directories = [
    $root . '/vendor',
    $root . '/src',
    $root . '/config',
    $root . '/public',
    $root . '/storage',
];

foreach ($directories as $dir) {
    echo "<li>$dir: " . (is_dir($dir) ? "✓ Existe" : "❌ Não existe") . "</li>";
    
    // Verificar permissões
    if (is_dir($dir)) {
        $perms = fileperms($dir);
        $permsOctal = substr(sprintf('%o', $perms), -4);
        echo " - Permissões: $permsOctal";
    }
}

// Verificar arquivos importantes
echo "<h2>Arquivos Importantes:</h2>";
echo "<ul>";

$files = [
    $root . '/vendor/autoload.php',
    $root . '/config/production.php',
    $root . '/public/index.php',
    $root . '/.htaccess',
    $root . '/public/.htaccess',
];

foreach ($files as $file) {
    echo "<li>$file: " . (file_exists($file) ? "✓ Existe" : "❌ Não existe") . "</li>";
    
    // Verificar permissões
    if (file_exists($file)) {
        $perms = fileperms($file);
        $permsOctal = substr(sprintf('%o', $perms), -4);
        echo " - Permissões: $permsOctal";
    }
}

// Testar leitura/escrita em diretórios críticos
echo "<h2>Teste de Leitura/Escrita:</h2>";
echo "<ul>";

$testDirs = [
    $root,
    $root . '/storage',
    $root . '/storage/temp',
    $root . '/storage/documents',
    $root . '/public/uploads',
];

foreach ($testDirs as $dir) {
    if (is_dir($dir)) {
        $isWritable = is_writable($dir);
        $isReadable = is_readable($dir);
        echo "<li>$dir: " . 
             "Leitura: " . ($isReadable ? "✓" : "❌") . ", " .
             "Escrita: " . ($isWritable ? "✓" : "❌") . 
             "</li>";
    } else {
        echo "<li>$dir: ❌ Diretório não existe</li>";
    }
}

// Testar acesso ao banco de dados
echo "<h2>Teste de Banco de Dados:</h2>";
echo "<p>Tentando conectar ao banco de dados...</p>";

try {
    $configFile = $root . '/config/production.php';
    if (file_exists($configFile)) {
        $config = require_once $configFile;
        
        if (isset($config['database'])) {
            $db = $config['database'];
            echo "<p>Informações do banco configuradas:</p>";
            echo "<ul>";
            echo "<li>Host: " . $db['host'] . "</li>";
            echo "<li>Database: " . $db['name'] . "</li>";
            echo "<li>User: " . $db['user'] . "</li>";
            echo "</ul>";
            
            // Tentar conectar
            $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
            if ($conn->connect_error) {
                echo "<p>❌ Erro de conexão: " . $conn->connect_error . "</p>";
            } else {
                echo "<p>✓ Conexão bem-sucedida ao banco de dados!</p>";
                $conn->close();
            }
        } else {
            echo "<p>❌ Configurações de banco de dados não encontradas no arquivo de configuração.</p>";
        }
    } else {
        echo "<p>❌ Arquivo de configuração não encontrado.</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro ao testar conexão com banco de dados: " . $e->getMessage() . "</p>";
}
?>
