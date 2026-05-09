<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PostRepository;
use App\Repository\PostViewRepository;

final class PostViewService
{
    private readonly PostViewRepository $postViewRepository;
    private readonly PostRepository $postRepository;

    public function __construct()
    {
        $this->postViewRepository = new PostViewRepository();
        $this->postRepository = new PostRepository();
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

    private function getClientIp(): ?string
    {
        $addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $addr = is_string($addr) ? trim($addr) : '';

        return $addr !== '' ? $addr : null;
    }
}
