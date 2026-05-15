<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
                return $query->where(function (Builder $query) use ($term): void {
                    if ($this->supportsFullText($term)) {
                        $query
                            ->whereFullText(['title', 'excerpt', 'content_md'], $term)
                            ->orWhere(fn (Builder $query): Builder => $this->whereLikeSearch($query, $term));

                        return;
                    }

                    $this->whereLikeSearch($query, $term);
                });
            });
    }

    private function supportsFullText(string $term): bool
    {
        return mb_strlen($term) >= 3
            && in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'], true);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    private function whereLikeSearch(Builder $query, string $term): Builder
    {
        return $query
            ->where('title', 'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%")
            ->orWhere('content_md', 'like', "%{$term}%")
            ->orWhereHas('category', function (Builder $query) use ($term): Builder {
                return $query->where('name', 'like', "%{$term}%");
            })
            ->orWhereHas('tags', function (Builder $query) use ($term): Builder {
                return $query->where('name', 'like', "%{$term}%");
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
        $terms = $this->highlightTerms($term);
        $excerpt = e($this->snippet($this->snippetSource($article, $terms), $terms));

        if ($terms->isEmpty()) {
            return $excerpt;
        }

        return preg_replace(
            '/('.$terms->map(fn (string $word): string => preg_quote($word, '/'))->implode('|').')/iu',
            '<mark class="rounded bg-accent-muted px-1 text-accent">$1</mark>',
            $excerpt,
        ) ?? $excerpt;
    }

    /**
     * @return Collection<int, string>
     */
    private function highlightTerms(string $term): Collection
    {
        return collect(preg_split('/\s+/', trim($term)) ?: [])
            ->map(fn (string $word): string => trim($word))
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3)
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, string>  $terms
     */
    private function snippetSource(Article $article, Collection $terms): string
    {
        $content = strip_tags($article->content_html);
        $fallback = $article->excerpt ?: $content ?: $article->title;

        return collect([$article->excerpt, $content, $article->title])
            ->filter()
            ->first(fn (string $source): bool => $this->containsAnyTerm($source, $terms)) ?? $fallback;
    }

    /**
     * @param  Collection<int, string>  $terms
     */
    private function snippet(string $text, Collection $terms): string
    {
        if ($terms->isEmpty() || mb_strlen($text) <= 180) {
            return Str::limit($text, 180);
        }

        $position = $terms
            ->map(fn (string $word): int|false => mb_stripos($text, $word))
            ->filter(fn (int|false $position): bool => $position !== false)
            ->min();

        if (! is_int($position) || $position <= 60) {
            return Str::limit($text, 180);
        }

        return '... '.Str::limit(mb_substr($text, max(0, $position - 70), 180), 180);
    }

    /**
     * @param  Collection<int, string>  $terms
     */
    private function containsAnyTerm(string $text, Collection $terms): bool
    {
        return $terms->contains(fn (string $word): bool => mb_stripos($text, $word) !== false);
    }
}
