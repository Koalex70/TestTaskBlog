<?php

declare(strict_types=1);

namespace App\Database\Migration;

use App\Database\Migration\Base\AbstractMigration;
use App\Database\Sql\MigrationSqlOption;
use PDO;

final class Version100CreatePostsTable extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $this->execute(
            $pdo,
            'CREATE TABLE IF NOT EXISTS posts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                image VARCHAR(1024) NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                content MEDIUMTEXT NOT NULL,
                views_count INT UNSIGNED NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                published_at DATETIME NOT NULL,
                INDEX idx_posts_views_count (views_count),
                INDEX idx_posts_published_at (published_at)
            ) ' . MigrationSqlOption::tableOptions()
        );
    }
}
