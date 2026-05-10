<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepository;

final class PostPageViewModelFactory
{
    private readonly PostRepository $postRepository;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
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
            'image' => $this->postImageUrl((string) ($post['image'] ?? '')),
            'views_count' => (int) ($post['views_count'] ?? 0),
            'date' => date('F j, Y', strtotime((string) ($post['published_at'] ?? 'now'))),
        ];
    }
    
    private function mapRelatedPostCard(array $row): array
    {
        return [
            'title' => $row['title'],
            'slug' => $row['slug'],
            'description' => (string) ($row['description'] ?? ''),
            'image' => $this->postImageUrl((string) ($row['image'] ?? '')),
            'date' => date('F j, Y', strtotime((string) ($row['published_at'] ?? 'now'))),
            'views_count' => (int) ($row['views_count'] ?? 0),
        ];
    }

    private function postImageUrl(string $image): string
    {
        if ($image !== '') {
            return $image;
        }

        return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80';
    }
}
