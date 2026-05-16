<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'site_name',
    'tagline',
    'site_url',
    'site_description',
    'default_og_image',
    'contact_email',
    'sitemap_enabled',
    'rss_enabled',
    'newsletter_enabled',
    'articles_per_page',
])]
class SiteSetting extends Model
{
    private const CACHE_KEY = 'site-settings.current';

    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn (): bool => Cache::forget(self::CACHE_KEY));
        static::deleted(fn (): bool => Cache::forget(self::CACHE_KEY));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sitemap_enabled' => 'boolean',
            'rss_enabled' => 'boolean',
            'newsletter_enabled' => 'boolean',
            'articles_per_page' => 'integer',
        ];
    }

    #[\NoDiscard]
    public static function current(): self
    {
        $id = Cache::get(self::CACHE_KEY);

        if (! is_int($id)) {
            return tap(self::query()->firstOrCreate([], self::defaults()), function (self $siteSetting): void {
                Cache::forever(self::CACHE_KEY, $siteSetting->getKey());
            });
        }

        return self::query()->find($id) ?? tap(self::query()->firstOrCreate([], self::defaults()), function (self $siteSetting): void {
            Cache::forever(self::CACHE_KEY, $siteSetting->getKey());
        });
    }

    public static function forgetCurrent(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    #[\NoDiscard]
    public static function defaults(): array
    {
        return [
            'site_name' => 'MugiewBlog',
            'tagline' => 'Engineering notes untuk developer yang mengirim fitur ke produksi.',
            'site_url' => config('app.url'),
            'site_description' => 'MugiewBlog membahas Laravel, pemrograman modern, DevOps, cloud, AI engineering, dan investasi teknologi untuk developer Indonesia.',
            'default_og_image' => null,
            'contact_email' => null,
            'sitemap_enabled' => true,
            'rss_enabled' => true,
            'newsletter_enabled' => true,
            'articles_per_page' => 11,
        ];
    }
}
