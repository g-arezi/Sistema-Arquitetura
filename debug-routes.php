<?php
// Arquivo debug-routes.php para diagnóstico
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Debug de Rotas - Sistema de Arquitetura</h1>";

// Tentar carregar o autoloader
$autoloader_paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/public/../vendor/autoload.php'
];

$autoloaded = false;
foreach ($autoloader_paths as $path) {
    if (file_exists($path)) {
        echo "<p>Tentando carregar autoloader: $path</p>";
        try {
            require_once $path;
            echo "<p>✅ Autoloader carregado com sucesso!</p>";
            $autoloaded = true;
            break;
        } catch (Exception $e) {
            echo "<p>❌ Erro ao carregar autoloader: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>❌ Não encontrado: $path</p>";
    }
}

if (!$autoloaded) {
    echo "<p>❌ Não foi possível carregar o autoloader. Continuando sem ele.</p>";
}

// Informações do ambiente
echo "<h2>Informações do Ambiente</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li>Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "</li>";
echo "<li>Request URI: " . $_SERVER['REQUEST_URI'] . "</li>";
echo "<li>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li>Current Directory: " . __DIR__ . "</li>";
echo "</ul>";

// Testar carregamento de um controlador simples
echo "<h2>Teste de Carregamento de Controlador</h2>";
if ($autoloaded) {
    try {
        // Tentar carregar um controlador para testar
        if (class_exists('App\Controllers\HelperController')) {
            echo "<p>✅ HelperController encontrado!</p>";
            $controller = new App\Controllers\HelperController();
            echo "<p>✅ HelperController instanciado com sucesso!</p>";
        } else {
            echo "<p>❌ HelperController não encontrado.</p>";
            echo "<p>Namespaces disponíveis:</p>";
            $declaredClasses = get_declared_classes();
            $appClasses = array_filter($declaredClasses, function($className) {
                return strpos($className, 'App\\') === 0;
            });
            echo "<pre>" . print_r($appClasses, true) . "</pre>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Erro ao carregar controlador: " . $e->getMessage() . "</p>";
    }
}

// Testar roteador
echo "<h2>Teste do Roteador</h2>";
if ($autoloaded && class_exists('App\Core\Router')) {
    try {
        echo "<p>✅ Classe Router encontrada!</p>";
        $router = new App\Core\Router();
        echo "<p>✅ Router instanciado com sucesso!</p>";
        
        // Registrar uma rota de teste
        $router->get('/test', function() {
            return "Teste de rota funcionando!";
        });
        
        echo "<p>✅ Rota de teste registrada!</p>";
    } catch (Exception $e) {
        echo "<p>❌ Erro ao testar roteador: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Classe Router não encontrada ou autoloader não carregado.</p>";
}

// Listar diretórios principais
echo "<h2>Estrutura de Diretórios</h2>";
$directories = [
    __DIR__,
    __DIR__ . '/src',
    __DIR__ . '/vendor',
    __DIR__ . '/config',
    __DIR__ . '/public',
    __DIR__ . '/../src',
    __DIR__ . '/../vendor',
    __DIR__ . '/../config',
    __DIR__ . '/../public',
];

foreach ($directories as $dir) {
    $exists = is_dir($dir);
    $readable = $exists && is_readable($dir);
    echo "<p>" . $dir . ": " . 
         ($exists ? "✅ Existe" : "❌ Não existe") . 
         ($readable ? ", Leitura OK" : ($exists ? ", ❌ Sem permissão de leitura" : "")) .
         "</p>";
    
    // Listar alguns arquivos do diretório se ele existir
    if ($exists && $readable) {
        $files = scandir($dir);
        if (count($files) > 2) { // Ignorar . e ..
            echo "<ul>";
            $count = 0;
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    echo "<li>" . $file . "</li>";
                    $count++;
                    if ($count >= 5) {
                        echo "<li>...</li>";
                        break;
                    }
                }
            }
            echo "</ul>";
        } else {
            echo "<p>Diretório vazio</p>";
        }
    }
}

// Próximos passos
echo "<h2>Próximos Passos</h2>";
echo "<p>Se você está vendo esta página, o arquivo debug-routes.php está sendo executado corretamente.</p>";
echo "<p>Para resolver problemas com o roteamento:</p>";
echo "<ol>";
echo "<li>Verifique se o arquivo .htaccess está configurado corretamente</li>";
echo "<li>Confirme que a estrutura de diretórios corresponde à esperada pelo sistema</li>";
echo "<li>Verifique se todas as classes necessárias estão acessíveis</li>";
echo "<li>Teste com o arquivo index.php simplificado fornecido</li>";
echo "</ol>";
?>
