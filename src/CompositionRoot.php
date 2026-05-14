<?php

declare(strict_types=1);

namespace App;

use App\Controller\CategoryController;
use App\Controller\DbToolsController;
use App\Controller\HomeController;
use App\Controller\NotFoundController;
use App\Controller\PostController;
use App\Database\Connection;
use App\Http\Router;
use App\Model\DbTools\ClearModel;
use App\Model\DbTools\MigrationModel;
use App\Model\DbTools\SeedModel;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\PostViewRepository;
use App\Security\CsrfTokenManager;
use App\Service\CategoryPageViewModelFactory;
use App\Service\EnvironmentService;
use App\Service\HomePageSectionsFactory;
use App\Service\PostPageViewModelFactory;
use App\Service\PostViewService;
use App\Service\SlugResourceResolver;
use App\Service\TemplateRenderer;
use App\Support\PostPresentation;

final class CompositionRoot
{
    public function registerRoutes(Router $router): void
    {
        $pdo = Connection::get();
        $templateRenderer = new TemplateRenderer();
        $postPresentation = new PostPresentation();
        $categoryRepository = new CategoryRepository($pdo);
        $postRepository = new PostRepository($pdo);

        $notFoundController = new NotFoundController($templateRenderer);
        $slugResourceResolver = new SlugResourceResolver(
            fn () => $notFoundController->show()
        );

        $homePageSectionsFactory = new HomePageSectionsFactory(
            $categoryRepository,
            $postRepository,
            $postPresentation
        );
        $homeController = new HomeController($templateRenderer, $homePageSectionsFactory);

        $categoryPageViewModelFactory = new CategoryPageViewModelFactory(
            $postRepository,
            $postPresentation
        );
        $categoryController = new CategoryController(
            $categoryRepository,
            $categoryPageViewModelFactory,
            $slugResourceResolver,
            $templateRenderer
        );

        $postPageViewModelFactory = new PostPageViewModelFactory(
            $postRepository,
            $postPresentation
        );
        $postViewService = new PostViewService(
            new PostViewRepository($pdo),
            $postRepository
        );
        $postController = new PostController(
            $postRepository,
            $slugResourceResolver,
            $postViewService,
            $postPageViewModelFactory,
            $templateRenderer
        );

        $dbToolsController = new DbToolsController(
            new CsrfTokenManager(),
            new EnvironmentService(),
            $templateRenderer,
            new MigrationModel(),
            new SeedModel(),
            new ClearModel()
        );

        $router->get('/', [$homeController, 'index']);
        $router->get('/category/{slug}', [$categoryController, 'show']);
        $router->get('/api/category/{slug}', [$categoryController, 'data']);
        $router->get('/post/{slug}', [$postController, 'show']);
        $router->get('/db-tools', [$dbToolsController, 'index']);
        $router->post('/db-tools/migrate', [$dbToolsController, 'migrate']);
        $router->post('/db-tools/seed', [$dbToolsController, 'seed']);
        $router->post('/db-tools/clear', [$dbToolsController, 'clear']);
        $router->setNotFoundHandler([$notFoundController, 'show']);
    }
}
