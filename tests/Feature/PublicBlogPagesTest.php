<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Support\ArticleContent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('should render homepage when published articles exist', function () {
    $this->seed();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertHeader('x-content-type-options', 'nosniff')
        ->assertHeader('x-frame-options', 'SAMEORIGIN')
        ->assertHeaderMissing('x-powered-by')
        ->assertSeeText('Engineering notes')
        ->assertSeeText('Artikel Terbaru')
        ->assertSee('application/rss+xml', false);
});

it('should hide category navigation when no categories exist', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('data-nav="categories"', false)
        ->assertDontSee('data-nav="mobile-categories"', false);
});

it('should render accessible category navigation when categories exist', function () {
    $this->seed();

    $category = Category::query()
        ->whereHas('articles', fn ($query) => $query->published())
        ->firstOrFail();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('data-nav="categories"', false)
        ->assertSee('aria-controls="desktop-category-menu"', false)
        ->assertSee('aria-haspopup="menu"', false)
        ->assertSee('data-nav="mobile-categories"', false)
        ->assertSee('data-category-nav="'.$category->slug.'"', false);
});

it('should expose the active public navigation item to assistive technology', function () {
    $this->seed();

    $category = Category::query()
        ->whereHas('articles', fn ($query) => $query->published())
        ->firstOrFail();

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'data-nav="home"',
            'aria-current="page"',
            'Beranda',
        ], false);

    $this->get(route('about'))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'data-nav="about"',
            'aria-current="page"',
            'Tentang',
        ], false);

    $this->get($category->url())
        ->assertSuccessful()
        ->assertSeeInOrder([
            'data-nav="categories"',
            'Kategori',
        ], false)
        ->assertSeeInOrder([
            'data-category-nav="'.$category->slug.'"',
            'aria-current="page"',
            $category->name,
        ], false);
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

it('should render about page with editorial context and real blog stats', function () {
    $this->seed();

    $this->get(route('about'))
        ->assertSuccessful()
        ->assertSeeText('Tentang MugiewBlog')
        ->assertSeeText('Visi')
        ->assertSeeText('Misi')
        ->assertSeeText('Tim Penulis')
        ->assertSeeText('Tech Stack')
        ->assertSeeText('Artikel')
        ->assertSeeText('Pembaca');
});

it('should prepare responsive article markdown content when rich markdown exists', function () {
    $html = Article::renderMarkdown(<<<'MARKDOWN'
        ## Bagian Utama

        Paragraf dengan `inline_code`, daftar, gambar, dan tabel.

        - Satu
          - Dua

        ```php
        echo "ok";
        ```

        ![Diagram arsitektur](https://example.com/diagram.png)

        | Area | Status |
        | --- | --- |
        | Render | Aman |
        MARKDOWN);

    $prepared = ArticleContent::prepare($html);

    expect($prepared['toc'])->toBe([
        ['id' => 'bagian-utama', 'title' => 'Bagian Utama', 'level' => 2],
    ])
        ->and($prepared['html'])->toContain('id="bagian-utama"')
        ->and($prepared['html'])->toContain('loading="lazy"')
        ->and($prepared['html'])->toContain('decoding="async"')
        ->and($prepared['html'])->toContain('class="article-table-scroll"');
});

it('should render article markdown patterns on public article pages', function () {
    $article = Article::factory()->published()->create([
        'title' => 'Markdown Rendering Guide',
        'slug' => 'markdown-rendering-guide',
        'content_md' => <<<'MARKDOWN'
        ## Checklist Produksi

        - List utama
          - List anak

        Gunakan `inline_code` untuk nama fungsi.

        ```php
        echo "ok";
        ```

        | Elemen | Status |
        | --- | --- |
        | Tabel | Responsive |
        MARKDOWN,
        'content_html' => '',
    ]);

    $this->get($article->url())
        ->assertSuccessful()
        ->assertSee('class="article-prose"', false)
        ->assertSee('<ul>', false)
        ->assertSee('<pre><code class="language-php">', false)
        ->assertSee('class="article-table-scroll"', false);
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
        ->assertSeeText('Laravel')
        ->assertSee('<mark', false);
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
        ->assertSee('<urlset', false)
        ->assertSee(route('about'), false);
});
