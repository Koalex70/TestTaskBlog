<?php

declare(strict_types=1);

namespace App\Database\Migration;

use App\Database\Migration\Base\AbstractMigration;
use App\Database\Sql\MigrationSqlOption;
use PDO;

final class Version400CreatePostViewsTable extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $this->execute(
            $pdo,
            'CREATE TABLE IF NOT EXISTS post_views (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id INT UNSIGNED NOT NULL,
                viewer_ip VARCHAR(45) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_post_views_post_ip (post_id, viewer_ip),
                CONSTRAINT fk_post_views_post
                    FOREIGN KEY (post_id) REFERENCES posts (id)
                    ON DELETE CASCADE,
                INDEX idx_post_views_post_id (post_id)
            ) ' . MigrationSqlOption::tableOptions()
        );
    }
}
