<?php

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('should publish scheduled articles when their scheduled time has passed', function () {
    $readyArticle = Article::factory()->create([
        'status' => ArticleStatus::Scheduled,
        'published_at' => null,
        'scheduled_at' => now()->subMinutes(10),
    ]);

    $futureArticle = Article::factory()->create([
        'status' => ArticleStatus::Scheduled,
        'published_at' => null,
        'scheduled_at' => now()->addHour(),
    ]);

    $this->artisan('articles:publish-scheduled')
        ->expectsOutput('Published 1 scheduled articles.')
        ->assertExitCode(0);

    expect($readyArticle->refresh())
        ->status->toBe(ArticleStatus::Published)
        ->published_at->not->toBeNull();

    expect($futureArticle->refresh())
        ->status->toBe(ArticleStatus::Scheduled)
        ->published_at->toBeNull();
});

it('should expose crawler directives and sitemap location through robots txt', function () {
    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('User-agent: *', false)
        ->assertSee(route('sitemap'), false);
});
