<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'category_id',
    'title',
    'slug',
    'excerpt',
    'content_md',
    'content_html',
    'featured_image',
    'featured_image_alt',
    'status',
    'published_at',
    'scheduled_at',
    'reading_time',
    'meta_title',
    'meta_description',
    'view_count',
    'is_featured',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (self $article): void {
            if (blank($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            if ($article->isDirty('content_md') || blank($article->content_html)) {
                $article->content_html = self::renderMarkdown($article->content_md);
            }

            if ($article->isDirty('content_md') || blank($article->reading_time)) {
                $article->reading_time = self::estimateReadingTime($article->content_md);
            }

            $article->meta_title ??= Str::limit($article->title, 60, '');
            $article->meta_description ??= Str::limit($article->excerpt ?? strip_tags($article->content_html), 155, '');
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'is_featured' => 'boolean',
            'view_count' => 'integer',
            'reading_time' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    #[\NoDiscard]
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    #[\NoDiscard]
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return MorphToMany<Tag, $this>
     */
    #[\NoDiscard]
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Series, $this>
     */
    #[\NoDiscard]
    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class, 'series_articles')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    #[\NoDiscard]
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany<Bookmark, $this>
     */
    #[\NoDiscard]
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', ArticleStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[\NoDiscard]
    public function url(): string
    {
        return route('articles.show', $this);
    }

    #[\NoDiscard]
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (blank($this->featured_image)) {
            return null;
        }

        if (Str::startsWith($this->featured_image, ['http://', 'https://'])) {
            return $this->featured_image;
        }

        return Storage::disk('public')->url($this->featured_image);
    }

    #[\NoDiscard]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[\NoDiscard]
    public static function renderMarkdown(string $markdown): string
    {
        return (string) Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    #[\NoDiscard]
    public static function estimateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));

        return max(1, (int) ceil($wordCount / 220));
    }
}
