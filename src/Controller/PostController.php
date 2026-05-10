<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\PostRepository;
use App\Service\PostPageViewModelFactory;
use App\Service\PostViewService;
use App\Service\SlugResourceResolver;
use App\Service\TemplateRenderer;

final class PostController
{
    private readonly PostRepository $postRepository;
    private readonly SlugResourceResolver $slugResourceResolver;
    private readonly PostViewService $postViewService;
    private readonly PostPageViewModelFactory $postPageViewModelFactory;
    private readonly TemplateRenderer $templateRenderer;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->slugResourceResolver = new SlugResourceResolver();
        $this->postViewService = new PostViewService();
        $this->postPageViewModelFactory = new PostPageViewModelFactory();
        $this->templateRenderer = new TemplateRenderer();
    }

    /**
     * @param array<string, string> $params
     */
    public function show(array $params): Response
    {
        $post = $this->slugResourceResolver->resolveOrNotFound(
            $params,
            fn (string $slug): ?array => $this->postRepository->findBySlug($slug)
        );
        if ($post instanceof Response) {
            return $post;
        }

        $this->postViewService->registerUniqueViewAndSyncPostRow($post);

        $pageData = $this->postPageViewModelFactory->build($post);

        return new Response(
            $this->templateRenderer->render('post/show.tpl', array_merge(
                [
                    'pageTitle' => $pageData['post']['title'] . ' - Article',
                    'bodyClass' => 'post-layout',
                    'extraCss' => ['/assets/css/home.min.css', '/assets/css/post.min.css'],
                ],
                $pageData
            ))
        );
    }
}
