<?php

declare(strict_types=1);

namespace Tests\Service;

use App\Repository\PostRepository;
use App\Repository\PostViewRepository;
use App\Service\PostViewService;
use Tests\Support\DatabaseTestCase;

final class PostViewServiceTest extends DatabaseTestCase
{
    public function testRegisterUniqueViewCountsOnlyFirstViewPerIp(): void
    {
        $postId = $this->insertPost('Post A', 'post-a', 0);
        $_SERVER['REMOTE_ADDR'] = '127.0.0.10';

        $service = new PostViewService(new PostViewRepository(), new PostRepository());
        self::assertTrue($service->registerUniqueView($postId));
        self::assertFalse($service->registerUniqueView($postId));

        $views = (int) $this->pdo()->query("SELECT views_count FROM posts WHERE id = {$postId}")->fetchColumn();
        self::assertSame(1, $views);

        $repo = new PostViewRepository();
        self::assertTrue($repo->hasView($postId, '127.0.0.10'));
    }

    public function testRegisterUniqueViewReturnsFalseWhenRemoteAddrMissing(): void
    {
        $postId = $this->insertPost('Post B', 'post-b', 7);
        unset($_SERVER['REMOTE_ADDR']);

        $service = new PostViewService(new PostViewRepository(), new PostRepository());
        self::assertFalse($service->registerUniqueView($postId));

        $views = (int) $this->pdo()->query("SELECT views_count FROM posts WHERE id = {$postId}")->fetchColumn();
        self::assertSame(7, $views);
    }
}
