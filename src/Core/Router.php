<?php

namespace App\Core;

class Router {
    private array $routes = [];
    private array $middleware = [];
    
    public function get(string $path, string $handler): void {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, string $handler): void {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, string $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, string $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    public function group(array $attributes, callable $callback): void {
        $oldMiddleware = $this->middleware;
        
        if (isset($attributes['middleware'])) {
            $this->middleware[] = $attributes['middleware'];
        }
        
        // Criar uma função que aceita o router
        $callback($this);
        
        $this->middleware = $oldMiddleware;
    }
    
    private function addRoute(string $method, string $path, string $handler): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $this->middleware
        ];
    }
    
    public function run(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Debug para desenvolvimento
        if (isset($_GET['debug'])) {
            echo "Method: $requestMethod<br>";
            echo "Path: $requestPath<br>";
            echo "Routes: " . count($this->routes) . "<br>";
            foreach ($this->routes as $i => $route) {
                echo "Route $i: {$route['method']} {$route['path']}<br>";
            }
            return;
        }
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $requestMethod, $requestPath)) {
                $this->executeRoute($route, $requestPath);
                return;
            }
        }
        
        $this->notFound();
    }
    
    private function matchRoute(array $route, string $method, string $path): bool {
        if ($route['method'] !== $method) {
            return false;
        }
        
        $routePath = $route['path'];
        
        // Converter parâmetros {id} para regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $routePath);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $path);
    }
    
    private function executeRoute(array $route, string $path): void {
        // Executar middleware
        foreach ($route['middleware'] as $middleware) {
            $middlewareClass = "App\\Middleware\\" . ucfirst($middleware) . "Middleware";
            if (class_exists($middlewareClass)) {
                $middlewareInstance = new $middlewareClass();
                if (!$middlewareInstance->handle()) {
                    return;
                }
            }
        }
        
        // Extrair parâmetros da URL
        $params = $this->extractParams($route['path'], $path);
        
        // Executar controlador
        if (strpos($route['handler'], '@') !== false) {
            list($controller, $method) = explode('@', $route['handler']);
            $controllerClass = "App\\Controllers\\" . $controller;
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    call_user_func_array([$controllerInstance, $method], $params);
                } else {
                    $this->notFound();
                }
            } else {
                $this->notFound();
            }
        } else {
            // Função callable direta
            if (is_callable($route['handler'])) {
                call_user_func_array($route['handler'], $params);
            } else {
                $this->notFound();
            }
        }
    }
    
    private function extractParams(string $routePath, string $requestPath): array {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));
        
        $params = [];
        
        for ($i = 0; $i < count($routeParts); $i++) {
            if (preg_match('/\{([^}]+)\}/', $routeParts[$i])) {
                $params[] = $requestParts[$i] ?? null;
            }
        }
        
        return $params;
    }
    
    private function notFound(): void {
        http_response_code(404);
        
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Página não encontrada</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container mt-5'>
                <div class='row justify-content-center'>
                    <div class='col-md-6'>
                        <div class='card'>
                            <div class='card-body text-center'>
                                <h1 class='display-1 text-muted'>404</h1>
                                <h4>Página não encontrada</h4>
                                <p class='text-muted'>
                                    Rota solicitada: <code>$requestMethod $requestPath</code>
                                </p>
                                <a href='/' class='btn btn-primary'>Voltar ao Início</a>
                                <a href='?debug=1' class='btn btn-outline-secondary'>Debug Routes</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}
