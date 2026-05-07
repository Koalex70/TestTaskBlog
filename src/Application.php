<?php

declare(strict_types=1);

namespace App;

use App\Controller\HomeController;
use App\Http\Router;

final class Application
{
    public function run(): void
    {
        $router = new Router();
        $homeController = new HomeController();

        $router->get('/', [$homeController, 'index']);

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $response = $router->dispatch($method, is_string($path) ? $path : '/');
        $response->send();
    }
}
