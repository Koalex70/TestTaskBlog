<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\TemplateRenderer;

final class HomeController
{
    private readonly TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $this->templateRenderer = new TemplateRenderer();
    }

    public function index(): Response
    {
        $sections = $this->buildPlaceholderSections();

        return new Response(
            $this->templateRenderer->render('home/index.tpl', [
                'pageTitle' => 'Blog Home',
                'bodyClass' => 'home-layout',
                'extraCss' => ['/assets/css/home.min.css'],
                'sections' => $sections,
            ])
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildPlaceholderSections(): array
    {
        $posts = [
            [
                'title' => 'Lorem ipsum dolor sit amet',
                'date' => 'July 16, 2026',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quis est tempus dolor imperdiet at. Ac suspendisse sit amet facilisis lectus neque sollicitudin.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'title' => 'Lorem ipsum dolor sit amet',
                'date' => 'July 15, 2026',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quis est tempus dolor imperdiet at. Ac suspendisse sit amet facilisis lectus neque sollicitudin.',
                'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'title' => 'Lorem ipsum dolor sit amet',
                'date' => 'July 14, 2026',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quis est tempus dolor imperdiet at. Ac suspendisse sit amet facilisis lectus neque sollicitudin.',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
            ],
        ];

        return [
            ['title' => 'Category 1', 'posts' => $posts],
            ['title' => 'Category 2', 'posts' => $posts],
            ['title' => 'Category 3', 'posts' => $posts],
            ['title' => 'Category 4', 'posts' => $posts],
        ];
    }
}
