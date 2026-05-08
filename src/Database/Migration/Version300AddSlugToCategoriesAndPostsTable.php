<?php

declare(strict_types=1);

namespace App\Database\Migration;

use App\Database\Migration\Base\AbstractMigration;
use PDO;

final class Version300AddSlugToCategoriesAndPostsTable extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec('ALTER TABLE categories ADD COLUMN slug VARCHAR(255) NULL');
        $pdo->exec('UPDATE categories SET slug = CONCAT("category-", id) WHERE slug IS NULL OR slug = ""');
        $pdo->exec('ALTER TABLE categories MODIFY slug VARCHAR(255) NOT NULL');
        $pdo->exec('CREATE UNIQUE INDEX idx_categories_slug ON categories (slug)');

        $pdo->exec('ALTER TABLE posts ADD COLUMN slug VARCHAR(255) NULL');
        $pdo->exec('UPDATE posts SET slug = CONCAT("post-", id) WHERE slug IS NULL OR slug = ""');
        $pdo->exec('ALTER TABLE posts MODIFY slug VARCHAR(255) NOT NULL');
        $pdo->exec('CREATE UNIQUE INDEX idx_posts_slug ON posts (slug)');
    }
}
