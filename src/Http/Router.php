<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes[$this->routeKey('GET', $path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes[$this->routeKey('POST', $path)] = $handler;
    }

    public function dispatch(string $method, string $path): Response
    {
        $key = $this->routeKey($method, $path);
        $handler = $this->routes[$key] ?? null;

        if (!is_callable($handler)) {
            return new Response('Not Found', 404);
        }

        $response = $handler();
        if ($response instanceof Response) {
            return $response;
        }

        return new Response((string) $response);
    }

    private function routeKey(string $method, string $path): string
    {
        $normalizedPath = rtrim($path, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        return strtoupper($method) . ' ' . $normalizedPath;
    }
}
