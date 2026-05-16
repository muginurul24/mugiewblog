<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Support\BreadcrumbSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('should build ordered breadcrumb schema for public taxonomy pages', function () {
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();
    $categorySchema = BreadcrumbSchema::forCategory($category);
    $tagSchema = BreadcrumbSchema::forTag($tag);

    expect($categorySchema['@type'])->toBe('BreadcrumbList')
        ->and($categorySchema['itemListElement'][0]['name'])->toBe('Beranda')
        ->and($categorySchema['itemListElement'][1]['name'])->toBe($category->name)
        ->and($tagSchema['@type'])->toBe('BreadcrumbList')
        ->and($tagSchema['itemListElement'][0]['name'])->toBe('Beranda')
        ->and($tagSchema['itemListElement'][1]['name'])->toBe('Topik')
        ->and($tagSchema['itemListElement'][2]['name'])->toBe($tag->name);
});

it('should include category hierarchy before the article item', function () {
    $article = Article::factory()->published()->create();
    $schema = BreadcrumbSchema::forArticle($article->load('category'));

    expect($schema['itemListElement'])
        ->toHaveCount(3)
        ->and($schema['itemListElement'][1]['name'])->toBe($article->category->name)
        ->and($schema['itemListElement'][2]['name'])->toBe($article->title);
});
