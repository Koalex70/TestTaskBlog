<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use App\Database\Connection;
use App\Database\Migration\Version000CreateCategoriesTable;
use App\Database\Migration\Version100CreatePostsTable;
use App\Database\Migration\Version200CreatePostCategoryTable;
use App\Database\MigrationManager;

final class MigrationModel
{
    /**
     * @return string[]
     */
    public function runMigrations(): array
    {
        $manager = new MigrationManager(
            Connection::get(),
            [
                new Version000CreateCategoriesTable(),
                new Version100CreatePostsTable(),
                new Version200CreatePostCategoryTable(),
            ]
        );

        return $manager->migrate();
    }
}
