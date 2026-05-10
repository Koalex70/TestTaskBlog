<?php

declare(strict_types=1);

namespace App\Controller;

use App\Config\Config;
use App\Http\Response;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\TemplateRenderer;

final class HomeController
{
    private readonly TemplateRenderer $templateRenderer;
    private readonly CategoryRepository $categoryRepository;
    private readonly PostRepository $postRepository;

    public function __construct()
    {
        $this->templateRenderer = new TemplateRenderer();
        $this->categoryRepository = new CategoryRepository();
        $this->postRepository = new PostRepository();
    }

    public function index(): Response
    {
        $sections = $this->buildSectionsFromDatabase();

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
    private function buildSectionsFromDatabase(): array
    {
        $categories = $this->categoryRepository->findAllWithPosts();
        $latestLimit = (int) Config::get('blog', 'home_latest_posts_per_category', 3);

        $sections = [];
        foreach ($categories as $category) {
            $posts = $this->postRepository->findLatestByCategoryId((int) $category['id'], $latestLimit);
            if ($posts === []) {
                continue;
            }

            $mappedPosts = array_map(function (array $post): array {
                return [
                    'title' => $post['title'],
                    'slug' => $post['slug'],
                    'date' => date('F j, Y', strtotime((string) $post['published_at'])),
                    'excerpt' => $post['description'] ?? '',
                    'image' => $post['image'] ?: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
                ];
            }, $posts);

            $sections[] = [
                'title' => $category['name'],
                'slug' => $category['slug'],
                'posts' => $mappedPosts,
            ];
        }

        return $sections;
    }
}
