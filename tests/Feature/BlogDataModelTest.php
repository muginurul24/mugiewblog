<?php

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Series;
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

it('should render safe github flavored markdown for technical articles', function () {
    $html = Article::renderMarkdown(<<<'MARKDOWN'
        ## Checklist

        - Item induk
          - Item anak

        1. Langkah pertama
        2. Langkah kedua

        | Area | Status |
        | --- | --- |
        | Code | Aman |

        Gunakan `inline_code` untuk istilah teknis.

        ```php
        echo "ok";
        ```

        <script>alert("xss")</script>

        [unsafe](javascript:alert("xss"))
        MARKDOWN);

    expect($html)
        ->toContain('<ul>')
        ->toContain('<ol>')
        ->toContain('<table>')
        ->toContain('<code>inline_code</code>')
        ->toContain('<pre><code class="hljs language-php">')
        ->toContain('hljs-keyword')
        ->not->toContain('<script')
        ->not->toContain('href="javascript:');
});

it('should highlight comments and caddy directives when rendering code fences', function () {
    $html = Article::renderMarkdown(<<<'MARKDOWN'
        ```caddyfile
        # reverse proxy untuk app
        example.test {
            encode zstd gzip
            reverse_proxy app:8000
        }
        ```
        MARKDOWN);

    expect($html)
        ->toContain('class="hljs language-caddyfile"')
        ->toContain('class="hljs-comment"')
        ->toContain('class="hljs-keyword">encode</span>')
        ->toContain('class="hljs-keyword">reverse_proxy</span>');
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

it('should expose media and series relationships when publishing content', function () {
    $author = User::factory()->author()->create();
    $article = Article::factory()->for($author, 'author')->published()->create();
    $media = Media::factory()->for($author, 'uploader')->create([
        'path' => 'articles/example.webp',
    ]);
    $series = Series::factory()->create();

    $series->articles()->attach($article->id, ['sort_order' => 1]);

    expect($media->uploader->is($author))->toBeTrue()
        ->and($media->url())->toContain('/storage/articles/example.webp')
        ->and($series->articles()->first()?->is($article))->toBeTrue()
        ->and($article->series()->first()?->is($series))->toBeTrue();
});

it('should seed production-like published content when database seeder runs', function () {
    $this->seed();

    expect(Article::published()->count())->toBeGreaterThanOrEqual(8)
        ->and(Category::query()->count())->toBeGreaterThanOrEqual(6)
        ->and(Tag::query()->count())->toBeGreaterThanOrEqual(12)
        ->and(Media::query()->count())->toBeGreaterThanOrEqual(8)
        ->and(Series::query()->count())->toBeGreaterThanOrEqual(1);
});
