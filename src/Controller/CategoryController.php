<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\CategoryRepository;

final class CategoryController
{
    private readonly CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * @param array<string, string> $params
     */
    public function show(array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category === null) {
            return (new NotFoundController())->show();
        }

        $content = '<h1>Category page</h1>';
        $content .= '<p>Slug: ' . htmlspecialchars($category['slug'], ENT_QUOTES, 'UTF-8') . '</p>';

        return new Response($content);
    }
}
