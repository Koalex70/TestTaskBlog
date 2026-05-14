<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\Repository\PostRepository;
use Tests\Support\DatabaseTestCase;

final class PostRepositoryRelatedTest extends DatabaseTestCase
{
    public function testFindRelatedByPostIdSortsBySharedCategoriesThenViewsThenDate(): void
    {
        $cat1 = $this->insertCategory('Cat One', 'cat-one');
        $cat2 = $this->insertCategory('Cat Two', 'cat-two');
        $cat3 = $this->insertCategory('Cat Three', 'cat-three');

        $current = $this->insertPost('Current', 'current-post', 0, '2026-01-05 10:00:00');
        $this->linkPostToCategory($current, $cat1);
        $this->linkPostToCategory($current, $cat2);

        $best = $this->insertPost('Best Match', 'best-match', 5, '2026-01-01 10:00:00');
        $this->linkPostToCategory($best, $cat1);
        $this->linkPostToCategory($best, $cat2);

        $highViews = $this->insertPost('High Views', 'high-views', 100, '2025-12-31 10:00:00');
        $this->linkPostToCategory($highViews, $cat1);

        $midViews = $this->insertPost('Mid Views', 'mid-views', 50, '2026-01-10 10:00:00');
        $this->linkPostToCategory($midViews, $cat2);

        $noise = $this->insertPost('Noise', 'noise-post', 999, '2026-02-01 10:00:00');
        $this->linkPostToCategory($noise, $cat3);

        $rows = (new PostRepository($this->pdo()))->findRelatedByPostId($current, 3);
        $slugs = array_map(static fn (array $r): string => (string) $r['slug'], $rows);

        self::assertSame(['best-match', 'high-views', 'mid-views'], $slugs);
    }
}
