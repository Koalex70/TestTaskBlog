<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\CategoryRepository;
use App\Service\CategoryPageViewModelFactory;
use App\Service\SlugResourceResolver;
use App\Service\TemplateRenderer;

final class CategoryController
{
    private readonly CategoryRepository $categoryRepository;
    private readonly CategoryPageViewModelFactory $viewModelFactory;
    private readonly SlugResourceResolver $slugResourceResolver;
    private readonly TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->viewModelFactory = new CategoryPageViewModelFactory();
        $this->slugResourceResolver = new SlugResourceResolver();
        $this->templateRenderer = new TemplateRenderer();
    }

    /**
     * @param array<string, string> $params
     */
    public function show(array $params): Response
    {
        $category = $this->slugResourceResolver->resolveOrNotFound(
            $params,
            fn (string $slug): ?array => $this->categoryRepository->findBySlug($slug)
        );
        if ($category instanceof Response) {
            return $category;
        }

        $viewModel = $this->viewModelFactory->build($category, $_GET);

        return new Response(
            $this->templateRenderer->render('category/show.tpl', array_merge(
                [
                    'pageTitle' => $category['name'] . ' - Category',
                    'bodyClass' => 'category-layout',
                    'extraCss' => ['/assets/css/home.min.css', '/assets/css/category.min.css'],
                    'extraJs' => ['/assets/js/category.js'],
                    'category' => $category,
                    'breadcrumbs' => [
                        ['label' => 'Blog', 'url' => '/'],
                        ['label' => $category['name'], 'url' => null],
                    ],
                ],
                $viewModel
            ))
        );
    }

    /**
     * @param array<string, string> $params
     */
    public function data(array $params): Response
    {
        $category = $this->slugResourceResolver->resolveOrNotFound(
            $params,
            fn (string $slug): ?array => $this->categoryRepository->findBySlug($slug),
            fn (): Response => Response::json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 404)
        );
        if ($category instanceof Response) {
            return $category;
        }

        $viewModel = $this->viewModelFactory->build($category, $_GET);

        return Response::json([
            'status' => 'success',
            'category' => [
                'name' => $category['name'],
                'slug' => $category['slug'],
            ],
            'data' => $viewModel,
        ]);
    }
}
