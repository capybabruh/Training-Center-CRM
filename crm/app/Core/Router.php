<?php
// app/Core/Router.php

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri, array $container): void
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            [$class, $action] = $this->routes[$method][$path];
            $controller = $container[$class] ?? new $class();
            $controller->$action();
            return;
        }

        // Path ton tai nhung sai method -> 405
        foreach ($this->routes as $routes) {
            if (isset($routes[$path])) {
                http_response_code(405);
                render('errors/405', ['title' => '405 Method Not Allowed']);
                return;
            }
        }

        // Khong co route nao khop -> 404
        http_response_code(404);
        render('errors/404', ['title' => '404 Not Found']);
    }
}
