<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function robots(): Response
    {
        return response(implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /livewire-*/',
            'Sitemap: '.route('sitemap'),
            '',
        ]))->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function rss(): Response
    {
        $articles = Article::query()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->limit(25)
            ->get();

        return response()
            ->view('feeds.rss', ['articles' => $articles])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function sitemap(): Response
    {
        $articles = Article::query()
            ->published()
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);
        $categories = Category::query()->orderBy('sort_order')->get(['slug', 'updated_at']);
        $tags = Tag::query()->orderBy('name')->get(['slug', 'updated_at']);

        return response()
            ->view('feeds.sitemap', [
                'articles' => $articles,
                'categories' => $categories,
                'tags' => $tags,
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
