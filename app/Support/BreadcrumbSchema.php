<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;

final class BreadcrumbSchema
{
    /**
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public static function forArticle(Article $article): array
    {
        $items = [
            self::item(1, 'Beranda', route('home')),
        ];

        if ($article->category !== null) {
            $items[] = self::item(2, $article->category->name, $article->category->url());
        }

        $items[] = self::item(count($items) + 1, $article->title, $article->url());

        return self::schema($items);
    }

    /**
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public static function forCategory(Category $category): array
    {
        return self::schema([
            self::item(1, 'Beranda', route('home')),
            self::item(2, $category->name, $category->url()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public static function forTag(Tag $tag): array
    {
        return self::schema([
            self::item(1, 'Beranda', route('home')),
            self::item(2, 'Topik', route('home')),
            self::item(3, $tag->name, $tag->url()),
        ]);
    }

    /**
     * @param  array<int, array<string, int|string>>  $items
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    private static function schema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, int|string>
     */
    #[\NoDiscard]
    private static function item(int $position, string $name, string $url): array
    {
        return [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $name,
            'item' => $url,
        ];
    }
}
