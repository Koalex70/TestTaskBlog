<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\PostRepository;
use App\Service\SlugResourceResolver;

final class PostController
{
    private readonly PostRepository $postRepository;
    private readonly SlugResourceResolver $slugResourceResolver;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->slugResourceResolver = new SlugResourceResolver();
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

        $content = '<h1>Post page</h1>';
        $content .= '<p>Slug: ' . htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') . '</p>';

        return new Response($content);
    }
}
