<?php

declare(strict_types=1);

namespace App\Database\Migration;

use App\Database\Migration\Base\AbstractMigration;
use App\Database\Sql\MigrationSqlOption;
use PDO;

final class Version200CreatePostCategoryTable extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $this->execute(
            $pdo,
            'CREATE TABLE IF NOT EXISTS post_category (
                post_id INT UNSIGNED NOT NULL,
                category_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (post_id, category_id),
                CONSTRAINT fk_post_category_post
                    FOREIGN KEY (post_id) REFERENCES posts(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_post_category_category
                    FOREIGN KEY (category_id) REFERENCES categories(id)
                    ON DELETE CASCADE,
                INDEX idx_post_category_category_id (category_id)
            ) ' . MigrationSqlOption::tableOptions()
        );
    }
}
