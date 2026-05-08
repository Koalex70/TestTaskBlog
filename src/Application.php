<?php

declare(strict_types=1);

namespace App;

use App\Controller\DbToolsController;
use App\Controller\HomeController;
use App\Http\Router;

final class Application
{
    public function run(): void
    {
        $router = new Router();
        $homeController = new HomeController();
        $dbToolsController = new DbToolsController();

        $router->get('/', [$homeController, 'index']);
        $router->get('/db-tools', [$dbToolsController, 'index']);
        $router->post('/db-tools/migrate', [$dbToolsController, 'migrate']);
        $router->post('/db-tools/seed', [$dbToolsController, 'seed']);
        $router->post('/db-tools/clear', [$dbToolsController, 'clear']);

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $response = $router->dispatch($method, is_string($path) ? $path : '/');
        $response->send();
    }
}
