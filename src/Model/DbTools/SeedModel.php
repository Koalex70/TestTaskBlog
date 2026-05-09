<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use App\Config\Config;
use App\Database\Connection;
use App\Support\Slugger;
use Faker\Factory;
use PDO;

final class SeedModel
{
    public function runSeed(?int $categoriesCount = null): array
    {
        $seedConfig = Config::namespace('seed');
        $categoriesCount ??= (int) ($seedConfig['categories_count'] ?? 5);

        $pdo = Connection::get();
        $faker = Factory::create((string) ($seedConfig['locale'] ?? 'en_US'));

        $categories = [];
        $postsInserted = 0;
        $relationsInserted = 0;

        $pdo->beginTransaction();
        try {
            $categoryStmt = $pdo->prepare(
                'INSERT INTO categories (name, slug, description, created_at, updated_at)
                 VALUES (:name, :slug, :description, NOW(), NOW())'
            );

            for ($i = 0; $i < $categoriesCount; $i++) {
                $name = ucfirst($faker->unique()->words((int) ($seedConfig['category_name_words'] ?? 2), true));
                $categoryStmt->execute([
                    'name' => $name,
                    'slug' => Slugger::slugify($name) . '-' . strtolower(
                        $faker->unique()->lexify((string) ($seedConfig['category_slug_suffix_mask'] ?? '????'))
                    ),
                    'description' => $faker->sentence((int) ($seedConfig['category_description_words'] ?? 12)),
                ]);
                $categories[] = (int) $pdo->lastInsertId();
            }

            $postStmt = $pdo->prepare(
                'INSERT INTO posts (image, title, slug, description, content, views_count, created_at, updated_at, published_at)
                 VALUES (:image, :title, :slug, :description, :content, :views_count, NOW(), NOW(), :published_at)'
            );
            $relationStmt = $pdo->prepare(
                'INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)'
            );

            $postsPerCategory = (int) ($seedConfig['posts_per_category'] ?? 2);
            $totalPosts = $categoriesCount * $postsPerCategory;
            for ($p = 0; $p < $totalPosts; $p++) {
                $title = ucfirst($faker->sentence((int) ($seedConfig['post_title_words'] ?? 5)));
                $publishedAt = $faker->dateTimeBetween(
                    (string) ($seedConfig['post_published_at_since'] ?? '-2 years'),
                    (string) ($seedConfig['post_published_at_until'] ?? 'now')
                )->format('Y-m-d H:i:s');
                $postStmt->execute([
                    'image' => (string) ($seedConfig['post_image_base_url'] ?? 'https://picsum.photos/seed/')
                        . $faker->uuid()
                        . '/'
                        . (int) ($seedConfig['post_image_width'] ?? 640)
                        . '/'
                        . (int) ($seedConfig['post_image_height'] ?? 360),
                    'title' => $title,
                    'slug' => Slugger::slugify($title) . '-' . strtolower(
                        $faker->unique()->lexify((string) ($seedConfig['post_slug_suffix_mask'] ?? '??????'))
                    ),
                    'description' => $faker->sentence((int) ($seedConfig['post_description_words'] ?? 14)),
                    'content' => $faker->paragraphs((int) ($seedConfig['post_content_paragraphs'] ?? 4), true),
                    'views_count' => $faker->numberBetween(
                        (int) ($seedConfig['post_views_min'] ?? 0),
                        (int) ($seedConfig['post_views_max'] ?? 1000)
                    ),
                    'published_at' => $publishedAt,
                ]);

                $postId = (int) $pdo->lastInsertId();
                $postsInserted++;

                $categoryId = $faker->randomElement($categories);
                $relationStmt->execute([
                    'post_id' => $postId,
                    'category_id' => $categoryId,
                ]);
                $relationsInserted++;
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        return [
            'categories' => count($categories),
            'posts' => $postsInserted,
            'relations' => $relationsInserted,
        ];
    }
}
