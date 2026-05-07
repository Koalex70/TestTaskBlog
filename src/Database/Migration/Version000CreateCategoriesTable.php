<?php

declare(strict_types=1);

namespace App\Database\Migration;

use App\Database\Migration\Base\AbstractMigration;
use App\Database\Sql\MigrationSqlOption;
use PDO;

final class Version000CreateCategoriesTable extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $this->execute(
            $pdo,
            'CREATE TABLE IF NOT EXISTS categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            ) ' . MigrationSqlOption::tableOptions()
        );
    }
}
