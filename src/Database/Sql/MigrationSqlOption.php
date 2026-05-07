<?php

declare(strict_types=1);

namespace App\Database\Sql;

use App\Config\Config;

final class MigrationSqlOption
{
    public static function tableOptions(): string
    {
        $databaseConfig = Config::namespace('database');
        $charset = $databaseConfig['charset'] ?? 'utf8mb4';
        $collation = $databaseConfig['collation'] ?? 'utf8mb4_unicode_ci';

        return sprintf(
            'ENGINE=InnoDB DEFAULT CHARSET=%s COLLATE=%s',
            $charset,
            $collation
        );
    }
}
