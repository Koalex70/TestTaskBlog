<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepository;
use App\Repository\PostViewRepository;

final class PostViewService
{
    public function __construct(
        private readonly PostViewRepository $postViewRepository,
        private readonly PostRepository $postRepository,
    ) {
    }

    public function registerUniqueView(int $postId): bool
    {
        $ip = $this->getClientIp();
        if ($ip === null) {
            return false;
        }

        if (!$this->postViewRepository->recordView($postId, $ip)) {
            return false;
        }

        $this->postRepository->incrementViews($postId);

        return true;
    }

    public function registerUniqueViewAndSyncPostRow(array &$post): bool
    {
        $postId = (int) ($post['id'] ?? 0);
        if ($postId <= 0) {
            return false;
        }

        if (!$this->registerUniqueView($postId)) {
            return false;
        }

        $post['views_count'] = (int) ($post['views_count'] ?? 0) + 1;

        return true;
    }

    private function getClientIp(): ?string
    {
        $addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $addr = is_string($addr) ? trim($addr) : '';

        return $addr !== '' ? $addr : null;
    }
}
