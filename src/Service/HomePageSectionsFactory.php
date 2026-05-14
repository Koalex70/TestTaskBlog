<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\Config;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Support\PostPresentation;

final class HomePageSectionsFactory
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly PostRepository $postRepository,
        private readonly PostPresentation $postPresentation,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function build(): array
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
                    'date' => $this->postPresentation->publishedLabel((string) $post['published_at']),
                    'excerpt' => $post['description'] ?? '',
                    'image' => $this->postPresentation->cardImageUrl((string) ($post['image'] ?? '')),
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
