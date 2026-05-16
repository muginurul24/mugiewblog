<?php

use App\Livewire\GlobalSearch;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should show up to five quick search results when a global query matches articles', function () {
    Article::factory()
        ->count(6)
        ->published()
        ->sequence(fn ($sequence): array => [
            'title' => 'Laravel Search Result '.$sequence->index,
            'content_md' => 'Laravel content '.$sequence->index,
            'published_at' => now()->subMinutes($sequence->index),
        ])
        ->create();

    Livewire::test(GlobalSearch::class)
        ->set('query', 'Laravel')
        ->assertSeeText('Hasil cepat')
        ->assertSeeText('Lihat semua hasil')
        ->assertSeeText('Laravel Search Result 0')
        ->assertDontSeeText('Laravel Search Result 5');
});

it('should avoid searching until the query is meaningful', function () {
    Article::factory()->published()->create([
        'title' => 'Artikel Laravel',
        'content_md' => 'Laravel',
    ]);

    Livewire::test(GlobalSearch::class)
        ->set('query', 'L')
        ->assertDontSeeText('Hasil cepat');
});
