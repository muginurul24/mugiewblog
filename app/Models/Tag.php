<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'description'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $tag): void {
            if (blank($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * @return MorphToMany<Article, $this>
     */
    #[\NoDiscard]
    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable')->withTimestamps();
    }

    #[\NoDiscard]
    public function url(): string
    {
        return route('tags.show', $this);
    }

    #[\NoDiscard]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
