<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<int, array{method:string,path:string,handler:callable}> */
    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function setNotFoundHandler(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function dispatch(string $method, string $path): Response
    {
        $normalizedPath = $this->normalizePath($path);
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['path'], $normalizedPath);
            if ($params === null) {
                continue;
            }

            return $this->invokeHandler($route['handler'], $params);
        }

        if (!is_callable($this->notFoundHandler)) {
            return new Response('Not Found', 404);
        }

        return $this->invokeHandler($this->notFoundHandler, []);
    }

    /**
     * @param array<string, string> $params
     */
    private function invokeHandler(callable $handler, array $params): Response
    {
        $response = $params === [] ? $handler() : $handler($params);
        if ($response instanceof Response) {
            return $response;
        }

        return new Response((string) $response);
    }

    private function normalizePath(string $path): string
    {
        $normalizedPath = rtrim($path, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        return $normalizedPath;
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!is_string($pattern) || !preg_match($pattern, $requestPath, $matches)) {
            return null;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
