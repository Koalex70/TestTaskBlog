<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use App\Database\Migration\Version000CreateCategoriesTable;
use App\Database\Migration\Version100CreatePostsTable;
use App\Database\Migration\Version200CreatePostCategoryTable;
use App\Database\Migration\Version300AddSlugToCategoriesAndPostsTable;
use App\Database\Migration\Version400CreatePostViewsTable;
use App\Database\MigrationManager;
use PDO;

final class MigrationModel
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return string[]
     */
    public function runMigrations(): array
    {
        $manager = new MigrationManager(
            $this->pdo,
            [
                new Version000CreateCategoriesTable(),
                new Version100CreatePostsTable(),
                new Version200CreatePostCategoryTable(),
                new Version300AddSlugToCategoriesAndPostsTable(),
                new Version400CreatePostViewsTable(),
            ]
        );

        return $manager->migrate();
    }
}
