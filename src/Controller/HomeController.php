<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\HomePageSectionsFactory;
use App\Service\TemplateRenderer;
use App\Support\PostPresentation;

final class HomeController
{
    private readonly TemplateRenderer $templateRenderer;
    private readonly HomePageSectionsFactory $homePageSectionsFactory;

    public function __construct()
    {
        $this->templateRenderer = new TemplateRenderer();
        $postPresentation = new PostPresentation();
        $this->homePageSectionsFactory = new HomePageSectionsFactory(
            new CategoryRepository(),
            new PostRepository(),
            $postPresentation
        );
    }

    public function index(): Response
    {
        $sections = $this->homePageSectionsFactory->build();

        return new Response(
            $this->templateRenderer->render('home/index.tpl', [
                'pageTitle' => 'Blog Home',
                'bodyClass' => 'home-layout',
                'extraCss' => ['/assets/css/home.min.css'],
                'sections' => $sections,
            ])
        );
    }
}
