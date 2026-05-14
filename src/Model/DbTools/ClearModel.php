<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use PDO;

final class ClearModel
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function clearData(): array
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $this->pdo->exec('TRUNCATE TABLE post_views');
            $this->pdo->exec('TRUNCATE TABLE post_category');
            $this->pdo->exec('TRUNCATE TABLE posts');
            $this->pdo->exec('TRUNCATE TABLE categories');
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
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
