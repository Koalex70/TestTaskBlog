<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\HomePageSectionsFactory;
use App\Service\TemplateRenderer;

final class HomeController
{
    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
        private readonly HomePageSectionsFactory $homePageSectionsFactory,
    ) {
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
