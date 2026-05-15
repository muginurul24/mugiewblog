<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts::app'], function ($view): void {
            $view->with([
                'navigationCategories' => Category::query()
                    ->withCount(['articles' => fn ($query) => $query->published()])
                    ->orderBy('sort_order')
                    ->limit(8)
                    ->get(),
                'navigationTags' => Tag::query()
                    ->withCount(['articles' => fn ($query) => $query->published()])
                    ->orderByDesc('articles_count')
                    ->limit(12)
                    ->get(),
            ]);
        });
    }
}
