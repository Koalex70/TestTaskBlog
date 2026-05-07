<?php

declare(strict_types=1);

namespace App\Database\Migration;

use PDO;

interface MigrationInterface
{
    public function getVersion(): string;

    public function up(PDO $pdo): void;
}
