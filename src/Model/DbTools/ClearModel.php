<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use App\Database\Connection;

final class ClearModel
{
    public function clearData(): array
    {
        $pdo = Connection::get();

        $pdo->beginTransaction();
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('TRUNCATE TABLE post_views');
            $pdo->exec('TRUNCATE TABLE post_category');
            $pdo->exec('TRUNCATE TABLE posts');
            $pdo->exec('TRUNCATE TABLE categories');
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        return [
            'categories' => 0,
            'posts' => 0,
            'relations' => 0,
        ];
    }
}
