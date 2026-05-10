<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use PDO;

final class PostViewRepository
{
    private readonly PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function hasView(int $postId, string $viewerIp): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM post_views
             WHERE post_id = :post_id AND viewer_ip = :viewer_ip
             LIMIT 1'
        );
        $stmt->execute([
            'post_id' => $postId,
            'viewer_ip' => $viewerIp,
        ]);

        return (bool) $stmt->fetchColumn();
    }
    
    public function recordView(int $postId, string $viewerIp): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO post_views (post_id, viewer_ip) VALUES (:post_id, :viewer_ip)'
        );
        $stmt->execute([
            'post_id' => $postId,
            'viewer_ip' => $viewerIp,
        ]);

        return $stmt->rowCount() === 1;
    }
}
