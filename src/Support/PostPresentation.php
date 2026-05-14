<?php

declare(strict_types=1);

namespace App\Support;

use App\Config\Config;

final class PostPresentation
{
    public function cardImageUrl(string $stored): string
    {
        if ($stored !== '') {
            return $stored;
        }

        return (string) Config::get(
            'blog',
            'default_post_image_url',
            'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80'
        );
    }

    public function publishedLabel(string $publishedAt): string
    {
        $normalized = $publishedAt !== '' ? $publishedAt : 'now';
        $timestamp = strtotime($normalized);

        return date('F j, Y', $timestamp !== false ? $timestamp : time());
    }
}
