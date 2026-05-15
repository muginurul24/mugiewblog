<?php

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('should render markdown and reading time when article is created', function () {
    $article = Article::factory()->published()->create([
        'content_md' => "## Judul\n\nKonten teknis dengan contoh implementasi produksi.",
        'content_html' => '',
        'reading_time' => 0,
    ]);

    expect($article->fresh())
        ->status->toBe(ArticleStatus::Published)
        ->content_html->toContain('<h2>Judul</h2>')
        ->reading_time->toBe(1);
});

it('should expose relationships when blog data exists', function () {
    $author = User::factory()->author()->create();
    $category = Category::factory()->create();
    $tags = Tag::factory()->count(3)->create();
    $article = Article::factory()
        ->for($author, 'author')
        ->for($category)
        ->published()
        ->create();

    $article->tags()->sync($tags->pluck('id'));
    Comment::factory()->for($article)->for($author, 'author')->create();

    $article->load(['author', 'category', 'tags', 'comments']);

    expect($article->author->isAuthor())->toBeTrue()
        ->and($article->category->is($category))->toBeTrue()
        ->and($article->tags)->toHaveCount(3)
        ->and($article->comments)->toHaveCount(1);
});

it('should seed production-like published content when database seeder runs', function () {
    $this->seed();

    expect(Article::published()->count())->toBeGreaterThanOrEqual(8)
        ->and(Category::query()->count())->toBeGreaterThanOrEqual(6)
        ->and(Tag::query()->count())->toBeGreaterThanOrEqual(12);
});
