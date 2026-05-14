<?php

declare(strict_types=1);

namespace App\Service;

use App\Config\Config;
use App\Repository\PostRepository;
use App\Support\PostPresentation;

final class CategoryPageViewModelFactory
{
    private readonly PostRepository $postRepository;
    private readonly PostPresentation $postPresentation;

    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->postPresentation = new PostPresentation();
    }

    /**
     * @param array<string, mixed> $category
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    public function build(array $category, array $query): array
    {
        $sort = $this->normalizeSort((string) ($query['sort'] ?? 'date_desc'));
        $page = max(1, (int) ($query['page'] ?? Config::get('pagination', 'default_page', 1)));
        $perPage = (int) Config::get('pagination', 'category_posts_per_page', 10);
        $paginationWindow = (int) Config::get('pagination', 'pagination_window', 5);

        $totalItems = $this->postRepository->countByCategoryId((int) $category['id']);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page = min($page, $totalPages);

        $posts = $this->postRepository->findByCategoryIdPaginated(
            (int) $category['id'],
            $sort,
            $page,
            $perPage
        );

        $queryFor = static function (int $targetPage) use ($sort): string {
            return http_build_query([
                'sort' => $sort,
                'page' => $targetPage,
            ]);
        };

        return [
            'posts' => $this->mapPosts($posts),
            'sort' => $sort,
            'perPage' => $perPage,
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'hasPrev' => $page > 1,
            'hasNext' => $page < $totalPages,
            'prevQuery' => $queryFor($page - 1),
            'nextQuery' => $queryFor($page + 1),
            'pageNumbers' => $this->buildPageNumbers($page, $totalPages, $paginationWindow),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $posts
     *
     * @return array<int, array<string, mixed>>
     */
    private function mapPosts(array $posts): array
    {
        return array_map(function (array $post): array {
            return [
                'title' => $post['title'],
                'slug' => $post['slug'],
                'image' => $this->postPresentation->cardImageUrl((string) ($post['image'] ?? '')),
                'date' => $this->postPresentation->publishedLabel((string) ($post['published_at'] ?? '')),
                'description' => $post['description'] ?? '',
                'views_count' => (int) $post['views_count'],
            ];
        }, $posts);
    }

    private function normalizeSort(string $sort): string
    {
        return in_array($sort, ['date_desc', 'views_desc'], true) ? $sort : 'date_desc';
    }

    /**
     * @return int[]
     */
    private function buildPageNumbers(int $currentPage, int $totalPages, int $window): array
    {
        $window = max(1, $window);
        $half = (int) floor($window / 2);

        $start = max(1, $currentPage - $half);
        $end = min($totalPages, $start + $window - 1);

        if ($end - $start + 1 < $window) {
            $start = max(1, $end - $window + 1);
        }

        $pages = [];
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }

        return $pages;
    }
}
