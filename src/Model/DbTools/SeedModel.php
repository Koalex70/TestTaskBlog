<?php

declare(strict_types=1);

namespace App\Model\DbTools;

use App\Database\Connection;
use App\Support\Slugger;
use Faker\Factory;
use PDO;

final class SeedModel
{
    public function runSeed(int $categoriesCount = 5): array
    {
        $pdo = Connection::get();
        $faker = Factory::create('en_US');

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
                $name = ucfirst($faker->unique()->words(2, true));
                $categoryStmt->execute([
                    'name' => $name,
                    'slug' => Slugger::slugify($name) . '-' . strtolower($faker->unique()->lexify('????')),
                    'description' => $faker->sentence(12),
                ]);
                $categories[] = (int) $pdo->lastInsertId();
            }

            $postStmt = $pdo->prepare(
                'INSERT INTO posts (image, title, slug, description, content, views_count, created_at, updated_at, published_at)
                 VALUES (:image, :title, :slug, :description, :content, :views_count, NOW(), NOW(), NOW())'
            );
            $relationStmt = $pdo->prepare(
                'INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)'
            );

            foreach ($categories as $categoryId) {
                $postsForCategory = 2;
                for ($j = 0; $j < $postsForCategory; $j++) {
                    $title = ucfirst($faker->sentence(5));
                    $postStmt->execute([
                        'image' => 'https://picsum.photos/seed/' . $faker->uuid() . '/640/360',
                        'title' => $title,
                        'slug' => Slugger::slugify($title) . '-' . strtolower($faker->unique()->lexify('??????')),
                        'description' => $faker->sentence(14),
                        'content' => $faker->paragraphs(4, true),
                        'views_count' => $faker->numberBetween(0, 1000),
                    ]);

                    $postId = (int) $pdo->lastInsertId();
                    $postsInserted++;

                    $relationStmt->execute([
                        'post_id' => $postId,
                        'category_id' => $categoryId,
                    ]);
                    $relationsInserted++;
                }
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
