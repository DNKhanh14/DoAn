<?php

namespace App\Core;

class AdminRouter
{
    private array $routes = [];

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function any(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => trim($path, '/'),
            'handler' => $handler,
        ];
    }

    public function dispatch(?string $uri = null): void
    {
        $uri = $uri ?? $this->resolveUri();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($route['path'] === $uri) {
                $this->invoke($route['handler']);
                return;
            }
        }

        http_response_code(404);
        echo '404 - Admin page not found';
    }

    private function resolveUri(): string
    {
        if (!empty($_GET['route'])) {
            return trim((string) $_GET['route'], '/');
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !empty($_POST['route'])) {
            return trim((string) $_POST['route'], '/');
        }

        return 'dashboard';
    }

    private function invoke(string $handler): void
    {
        [$class, $method] = array_pad(explode('@', $handler), 2, 'index');
        $class = 'App\\Controllers\\Admin\\' . $class;

        if (!class_exists($class)) {
            http_response_code(500);
            echo 'Admin controller not found: ' . htmlspecialchars($class);
            return;
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo 'Admin action not found: ' . htmlspecialchars($method);
            return;
        }

        $controller->{$method}();
    }
}
