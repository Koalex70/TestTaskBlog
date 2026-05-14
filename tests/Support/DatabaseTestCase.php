<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Application;
use App\Database\Connection;
use App\Model\DbTools\MigrationModel;
use PDO;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new MigrationModel(Connection::get()))->runMigrations();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $pdo = $this->pdo();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE post_views');
        $pdo->exec('TRUNCATE TABLE post_category');
        $pdo->exec('TRUNCATE TABLE posts');
        $pdo->exec('TRUNCATE TABLE categories');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function pdo(): PDO
    {
        return Connection::get();
    }

    protected function insertCategory(string $name, string $slug): int
    {
        $stmt = $this->pdo()->prepare(
            'INSERT INTO categories (name, slug, description, created_at, updated_at)
             VALUES (:name, :slug, :description, NOW(), NOW())'
        );
        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'description' => $name . ' description',
        ]);

        return (int) $this->pdo()->lastInsertId();
    }

    protected function insertPost(
        string $title,
        string $slug,
        int $viewsCount,
        string $publishedAt = '2026-01-01 10:00:00'
    ): int {
        $stmt = $this->pdo()->prepare(
            'INSERT INTO posts (image, title, slug, description, content, views_count, created_at, updated_at, published_at)
             VALUES (:image, :title, :slug, :description, :content, :views_count, NOW(), NOW(), :published_at)'
        );
        $stmt->execute([
            'image' => 'https://picsum.photos/seed/' . $slug . '/640/360',
            'title' => $title,
            'slug' => $slug,
            'description' => $title . ' description',
            'content' => $title . ' content',
            'views_count' => $viewsCount,
            'published_at' => $publishedAt,
        ]);

        return (int) $this->pdo()->lastInsertId();
    }

    protected function linkPostToCategory(int $postId, int $categoryId): void
    {
        $stmt = $this->pdo()->prepare(
            'INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)'
        );
        $stmt->execute([
            'post_id' => $postId,
            'category_id' => $categoryId,
        ]);
    }

    /**
     * @return array{status:int,body:string}
     */
    protected function dispatchGet(string $uri): array
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = $uri;

        parse_str((string) parse_url($uri, PHP_URL_QUERY), $_GET);

        http_response_code(200);
        ob_start();
        (new Application())->run();
        $body = (string) ob_get_clean();

        return [
            'status' => (int) http_response_code(),
            'body' => $body,
        ];
    }
}
