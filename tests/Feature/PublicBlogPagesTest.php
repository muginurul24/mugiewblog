<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('should render homepage when published articles exist', function () {
    $this->seed();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertHeader('x-content-type-options', 'nosniff')
        ->assertHeader('x-frame-options', 'SAMEORIGIN')
        ->assertSeeText('Byte-sized insight')
        ->assertSeeText('Artikel Terbaru')
        ->assertSee('application/rss+xml', false);
});

it('should render article page when article is published', function () {
    $this->seed();

    $article = Article::published()->firstOrFail();

    $this->get($article->url())
        ->assertSuccessful()
        ->assertSeeText($article->title)
        ->assertSeeText('Daftar Isi')
        ->assertSeeText('Komentar');
});

it('should render category and tag pages when taxonomy exists', function () {
    $this->seed();

    $category = Category::query()->whereHas('articles', fn ($query) => $query->published())->firstOrFail();
    $tag = Tag::query()->whereHas('articles', fn ($query) => $query->published())->firstOrFail();

    $this->get($category->url())
        ->assertSuccessful()
        ->assertSeeText($category->name);

    $this->get($tag->url())
        ->assertSuccessful()
        ->assertSeeText($tag->name);
});

it('should render search results when query matches content', function () {
    $this->seed();

    $this->get(route('search', ['q' => 'Laravel']))
        ->assertSuccessful()
        ->assertSeeText('Hasil')
        ->assertSeeText('Laravel');
});

it('should render rss feed and sitemap when published content exists', function () {
    $this->seed();

    $this->get(route('feed'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('<rss', false);

    $this->get(route('sitemap'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->assertSee('<urlset', false);
});
