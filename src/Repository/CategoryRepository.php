<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use PDO;

final class CategoryRepository
{
    private readonly PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllWithPosts(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.id, c.name, c.slug, c.description, COUNT(pc.post_id) AS posts_count
             FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             GROUP BY c.id, c.name, c.slug, c.description
             HAVING COUNT(pc.post_id) > 0
             ORDER BY c.name ASC'
        );

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, slug, description
             FROM categories
             WHERE slug = :slug
             LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);

        $category = $stmt->fetch();

        return is_array($category) ? $category : null;
    }
}
