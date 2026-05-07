<?php

declare(strict_types=1);

namespace App\Database\Migration;

use PDO;

abstract class AbstractMigration implements MigrationInterface
{
    abstract public function up(PDO $pdo): void;

    public function getVersion(): string
    {
        $parts = explode('\\', static::class);

        return end($parts) ?: static::class;
    }

    protected function execute(PDO $pdo, string $sql): void
    {
        $pdo->exec($sql);
    }
}
