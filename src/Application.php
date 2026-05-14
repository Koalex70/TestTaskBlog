<?php

declare(strict_types=1);

namespace App;

use App\Http\Router;

final class Application
{
    public function run(): void
    {
        $router = new Router();
        (new CompositionRoot())->registerRoutes($router);

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $response = $router->dispatch($method, is_string($path) ? $path : '/');
        $response->send();
    }
}
