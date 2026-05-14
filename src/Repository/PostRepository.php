<?php

declare(strict_types=1);

namespace App\Repository;

use App\Config\Config;
use PDO;

final class PostRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findLatestByCategoryId(int $categoryId, int $limit = 0): array
    {
        if ($limit <= 0) {
            $limit = (int) Config::get('blog', 'home_latest_posts_per_category', 3);
        }

        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.title, p.slug, p.image, p.description, p.views_count, p.published_at
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY p.published_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countByCategoryId(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) AS total
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id'
        );
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByCategoryIdPaginated(
        int $categoryId,
        string $sort = 'date_desc',
        int $page = 0,
        int $perPage = 0
    ): array {
        if ($page <= 0) {
            $page = (int) Config::get('pagination', 'default_page', 1);
        }

        if ($perPage <= 0) {
            $perPage = (int) Config::get('pagination', 'category_posts_per_page', 10);
        }
        $maxPerPage = (int) Config::get('pagination', 'category_posts_per_page_max', 50);
        $perPage = min($perPage, $maxPerPage);

        $orderBy = match ($sort) {
            'views_desc' => 'p.views_count DESC, p.published_at DESC',
            default => 'p.published_at DESC, p.id DESC',
        };

        $offset = max(0, ($page - 1) * $perPage);

        $sql = sprintf(
            'SELECT p.id, p.title, p.slug, p.image, p.description, p.views_count, p.published_at
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY %s
             LIMIT :limit OFFSET :offset',
            $orderBy
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, image, description, content, views_count, published_at
             FROM posts
             WHERE slug = :slug
             LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);

        $post = $stmt->fetch();

        return is_array($post) ? $post : null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findCategoriesByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             WHERE pc.post_id = :post_id
             ORDER BY c.name ASC'
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll();
    }

    public function incrementViews(int $postId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE posts
             SET views_count = views_count + 1
             WHERE id = :id'
        );
        $stmt->execute(['id' => $postId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findRelatedByPostId(int $postId, int $limit = 0): array
    {
        if ($limit <= 0) {
            $limit = (int) Config::get('blog', 'related_posts_limit', 3);
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                p.id,
                p.title,
                p.slug,
                p.image,
                p.description,
                p.views_count,
                p.published_at,
                COUNT(*) AS shared_categories_count
             FROM post_category current_pc
             INNER JOIN post_category related_pc
                 ON related_pc.category_id = current_pc.category_id
                 AND related_pc.post_id != current_pc.post_id
             INNER JOIN posts p ON p.id = related_pc.post_id
             WHERE current_pc.post_id = :post_id
             GROUP BY p.id, p.title, p.slug, p.image, p.description, p.views_count, p.published_at
             ORDER BY shared_categories_count DESC, p.views_count DESC, p.published_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('post_id', $postId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
