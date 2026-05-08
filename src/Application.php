<?php

declare(strict_types=1);

namespace App;

use App\Controller\CategoryController;
use App\Controller\DbToolsController;
use App\Controller\HomeController;
use App\Controller\NotFoundController;
use App\Controller\PostController;
use App\Http\Router;

final class Application
{
    public function run(): void
    {
        $router = new Router();
        $homeController = new HomeController();
        $dbToolsController = new DbToolsController();
        $categoryController = new CategoryController();
        $postController = new PostController();
        $notFoundController = new NotFoundController();

        $router->get('/', [$homeController, 'index']);
        $router->get('/category/{slug}', [$categoryController, 'show']);
        $router->get('/post/{slug}', [$postController, 'show']);
        $router->get('/db-tools', [$dbToolsController, 'index']);
        $router->post('/db-tools/migrate', [$dbToolsController, 'migrate']);
        $router->post('/db-tools/seed', [$dbToolsController, 'seed']);
        $router->post('/db-tools/clear', [$dbToolsController, 'clear']);
        $router->setNotFoundHandler([$notFoundController, 'show']);

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $response = $router->dispatch($method, is_string($path) ? $path : '/');
        $response->send();
    }
}
