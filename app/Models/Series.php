<?php

namespace App\Models;

use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'description'])]

class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $series): void {
            if (blank($series->slug)) {
                $series->slug = Str::slug($series->name);
            }
        });
    }

    /**
     * @return BelongsToMany<Article, $this>
     */
    #[\NoDiscard]
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'series_articles')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    #[\NoDiscard]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
