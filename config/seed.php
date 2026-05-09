<?php

declare(strict_types=1);

return [
    'locale' => 'en_US',
    'categories_count' => 5,
    /** Total posts created = categories_count * posts_per_category; each post is linked to one random category. */
    'posts_per_category' => 20,
    'category_name_words' => 2,
    'category_slug_suffix_mask' => '????',
    'category_description_words' => 12,
    'post_title_words' => 5,
    'post_slug_suffix_mask' => '??????',
    'post_description_words' => 14,
    'post_content_paragraphs' => 4,
    'post_views_min' => 0,
    'post_views_max' => 1000,
    'post_published_at_since' => '-2 years',
    'post_published_at_until' => 'now',
    'post_image_base_url' => 'https://picsum.photos/seed/',
    'post_image_width' => 640,
    'post_image_height' => 360,
];
