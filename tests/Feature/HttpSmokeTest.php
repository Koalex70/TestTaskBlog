<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;

final class HttpSmokeTest extends DatabaseTestCase
{
    public function testCategoryApiRouteReturnsSuccessJson(): void
    {
        $categoryId = $this->insertCategory('Tech', 'tech');
        $postId = $this->insertPost('Hello Tech', 'hello-tech', 3);
        $this->linkPostToCategory($postId, $categoryId);

        $response = $this->dispatchGet('/api/category/tech?sort=date_desc&page=1');
        self::assertSame(200, $response['status']);

        $decoded = json_decode($response['body'], true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('success', $decoded['status'] ?? null);
        self::assertSame('tech', $decoded['category']['slug'] ?? null);
        self::assertIsArray($decoded['data']['posts'] ?? null);
    }

    public function testPostRouteReturnsHtmlPage(): void
    {
        $categoryId = $this->insertCategory('Space', 'space');
        $postId = $this->insertPost('Mars Log', 'mars-log', 1);
        $this->linkPostToCategory($postId, $categoryId);

        $response = $this->dispatchGet('/post/mars-log?from_category=space');
        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Mars Log', $response['body']);
        self::assertStringContainsString('Related articles', $response['body']);
    }
}
