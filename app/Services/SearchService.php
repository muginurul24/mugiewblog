<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchService
{
    /**
     * @return Builder<Article>
     */
    #[\NoDiscard]
    public function publishedArticles(string $term): Builder
    {
        $term = trim($term);

        return Article::query()
            ->published()
            ->when($term !== '', function (Builder $query) use ($term): Builder {
                if (in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'], true) && mb_strlen($term) >= 3) {
                    return $query->whereFullText(['title', 'excerpt', 'content_md'], $term);
                }

                return $query->where(function (Builder $query) use ($term): void {
                    $query
                        ->where('title', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('tags', fn (Builder $tagQuery): Builder => $tagQuery->where('name', 'like', "%{$term}%"));
                });
            });
    }

    /**
     * @return Builder<Article>
     */
    #[\NoDiscard]
    public function cachedPublishedArticles(string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $this->publishedArticles($term);
        }

        $ids = Cache::remember(
            'search:articles:'.sha1(Str::lower($term)),
            now()->addHour(),
            fn (): array => $this->publishedArticles($term)
                ->latest('published_at')
                ->limit(120)
                ->pluck('id')
                ->all(),
        );

        return Article::query()
            ->published()
            ->whereKey($ids);
    }

    #[\NoDiscard]
    public function highlightedExcerpt(Article $article, string $term): string
    {
        $text = strip_tags($article->excerpt ?: $article->content_html);
        $excerpt = e(Str::limit($text, 180));
        $terms = collect(preg_split('/\s+/', trim($term)) ?: [])
            ->map(fn (string $word): string => trim($word))
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3)
            ->unique()
            ->map(fn (string $word): string => preg_quote($word, '/'))
            ->values();

        if ($terms->isEmpty()) {
            return $excerpt;
        }

        return preg_replace(
            '/('.$terms->implode('|').')/iu',
            '<mark class="rounded bg-accent-muted px-1 text-accent">$1</mark>',
            $excerpt,
        ) ?? $excerpt;
    }
}
