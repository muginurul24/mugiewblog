<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
}
