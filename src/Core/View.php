<?php

namespace App\Core;

class View {
    private static string $viewsPath = __DIR__ . '/../../src/Views/';
    private array $data = [];
    
    public function __construct(private string $view) {}
    
    public function with(string $key, $value): self {
        $this->data[$key] = $value;
        return $this;
    }
    
    public function withData(array $data): self {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    public function render(): string {
        $viewFile = self::$viewsPath . str_replace('.', '/', $this->view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View não encontrada: {$this->view}");
        }
        
        extract($this->data);
        
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }
    
    public function display(): void {
        echo $this->render();
    }
    
    public static function make(string $view): self {
        return new self($view);
    }
    
    public static function setViewsPath(string $path): void {
        self::$viewsPath = rtrim($path, '/') . '/';
    }
}
