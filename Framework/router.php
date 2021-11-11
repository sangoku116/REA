<?php
class router {
    protected array $routes = [];

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
}