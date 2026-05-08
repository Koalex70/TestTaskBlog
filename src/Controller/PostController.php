<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Repository\PostRepository;

final class PostController
{
    private readonly PostRepository $postRepository;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
    }

    /**
     * @param array<string, string> $params
     */
    public function show(array $params): Response
    {
        $slug = $params['slug'] ?? '';
        $post = $this->postRepository->findBySlug($slug);

        if ($post === null) {
            return (new NotFoundController())->show();
        }

        $content = '<h1>Post page</h1>';
        $content .= '<p>Slug: ' . htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') . '</p>';

        return new Response($content);
    }
}
