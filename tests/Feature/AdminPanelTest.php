<?php

use App\Filament\Widgets\ArticleCategoriesChart;
use App\Filament\Widgets\ArticleViewsChart;
use App\Filament\Widgets\BlogStats;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should render backoffice article resource when user is admin', function () {
    $this->seed();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertSuccessful()
        ->assertSeeText('Article');
});

it('should render admin dashboard at the production admin path', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertSuccessful()
        ->assertSeeText('MugiewBlog Admin')
        ->assertSee('data-backoffice-logo', false);
});

it('should render custom dashboard widgets for admin users', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(BlogStats::class)
        ->assertSeeText('Ringkasan editorial');

    Livewire::test(ArticleViewsChart::class)
        ->assertSeeText('Views per bulan');

    Livewire::test(ArticleCategoriesChart::class)
        ->assertSeeText('Distribusi kategori');
});

it('should render backoffice article creation page when user is admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.create'))
        ->assertSuccessful();
});

it('should render backoffice article detail page when user is admin', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->published()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.view', $article))
        ->assertSuccessful()
        ->assertSeeText($article->title);
});

it('should protect backoffice resources when user is guest', function () {
    $this->get(route('filament.backoffice.resources.articles.index'))
        ->assertRedirect();
});

it('should allow editors into content management while blocking regular users', function () {
    $editor = User::factory()->editor()->create();
    $user = User::factory()->create();

    $this->actingAs($editor)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertSuccessful();

    $this->actingAs($user)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertForbidden();
});

it('should render system resources when user is admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.users.index'))
        ->assertSuccessful()
        ->assertSeeText('User');

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.media.index'))
        ->assertSuccessful()
        ->assertSeeText('Media');
});
