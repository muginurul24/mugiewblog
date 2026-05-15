<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('should render backoffice article resource when user is admin', function () {
    $this->seed();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertSuccessful()
        ->assertSeeText('Article');
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
