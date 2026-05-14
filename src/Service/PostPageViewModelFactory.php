<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepository;
use App\Support\PostPresentation;

final class PostPageViewModelFactory
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly PostPresentation $postPresentation,
    ) {
    }

    public function build(array $post): array
    {
        $postId = (int) $post['id'];

        return [
            'post' => $this->mapMainPost($post),
            'categories' => $this->postRepository->findCategoriesByPostId($postId),
            'relatedPosts' => array_map(
                fn (array $row): array => $this->mapRelatedPostCard($row),
                $this->postRepository->findRelatedByPostId($postId)
            ),
        ];
    }

    private function mapMainPost(array $post): array
    {
        $content = (string) ($post['content'] ?? '');

        return [
            'title' => $post['title'],
            'slug' => $post['slug'],
            'description' => (string) ($post['description'] ?? ''),
            'content_html' => nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
            'image' => $this->postPresentation->cardImageUrl((string) ($post['image'] ?? '')),
            'views_count' => (int) ($post['views_count'] ?? 0),
            'date' => $this->postPresentation->publishedLabel((string) ($post['published_at'] ?? '')),
        ];
    }

    private function mapRelatedPostCard(array $row): array
    {
        return [
            'title' => $row['title'],
            'slug' => $row['slug'],
            'description' => (string) ($row['description'] ?? ''),
            'image' => $this->postPresentation->cardImageUrl((string) ($row['image'] ?? '')),
            'date' => $this->postPresentation->publishedLabel((string) ($row['published_at'] ?? '')),
            'views_count' => (int) ($row['views_count'] ?? 0),
        ];
    }
}
