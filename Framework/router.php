<?php

use JetBrains\PhpStorm\NoReturn;

class router {
    protected array $routes = [];
    protected array $errorHandler = [];
    protected route $current;

    public function add(string $method, string $path, callable $handler): route {
        return $this->routes[] = new Route ($method, $path, $handler);
    }
    public function dispatch(){
        $paths = $this->paths();

        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = $_SERVER['REQUEST_METHOD'] ?? '/';

        $matching = $this ->match($requestMethod, $requestPath);

        if($matching){
            try {
                return $matching->dispatch();
            } catch (Throwable $e) {
                return $this->dispatchError();
            }
        }
        if(in_array($requestPath, $paths)){
            return $this->dispatchNotAllowed();
        }
        return $this->dispatchNotFound();
    }
    private function paths(): array{
        $paths = [];
        foreach ($this->routes as $route) {
            $paths[] = $route->path();
        }
        return $paths;
    }
    private function match(string $method, string $path): ?route {
        foreach ($this->routes as $route) {
            if($route->matches($method, $path)) {
                return $route;
            }
        }
        return null;
    }
    public function errorHandler(int $code, callable $handler){
        $this->errorHandler[$code] = $handler;
    }
    public function dispatchNotAllowed(){
        $this->errorHandler[400] ??= fn() => "not allowed";
        return $this->errorHandler[400]();
    }
    public function dispatchNotFound(){
        $this->errorHandler[404] ??=fn() => "not found";
        return $this->errorHandler[404]();
    }
    public function dispatchError() {
        $this->errorHandler[500] ??= fn() => "server error";
        return $this->errorHandler[500]();
    }
    #[NoReturn] public function redirect($path){
        header("Location: {$path}", $replace= true, $code = 301);
        exit;
    }
    public function current(): ?route{
        return $this->current;
    }
}