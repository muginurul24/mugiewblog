<?php

namespace App\Models;

use App\Support\FontAwesomeIconCatalog;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'description', 'color', 'icon', 'parent_id', 'sort_order'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $category): void {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    #[\NoDiscard]
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<Category, $this>
     */
    #[\NoDiscard]
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<Article, $this>
     */
    #[\NoDiscard]
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    #[\NoDiscard]
    public function url(): string
    {
        return route('categories.show', $this);
    }

    #[\NoDiscard]
    public function getIconClassesAttribute(): string
    {
        return FontAwesomeIconCatalog::normalizeValue($this->icon);
    }

    #[\NoDiscard]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
