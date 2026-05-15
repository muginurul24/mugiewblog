<?php

use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should subscribe a reader when newsletter form is submitted', function () {
    Livewire::test('pages::index')
        ->set('email', ' Reader@Example.COM ')
        ->call('subscribe')
        ->assertHasNoErrors('email')
        ->assertSet('email', '');

    $subscriber = NewsletterSubscriber::query()->firstOrFail();

    expect($subscriber)
        ->email->toBe('reader@example.com')
        ->status->toBe('subscribed')
        ->source->toBe('homepage')
        ->verified_at->not->toBeNull();
});

it('should ignore newsletter honeypot submissions when spam field is filled', function () {
    Livewire::test('pages::index')
        ->set('email', 'spam@example.com')
        ->set('website', 'https://spam.example')
        ->call('subscribe')
        ->assertHasNoErrors();

    expect(NewsletterSubscriber::query()->count())->toBe(0);
});

it('should block newsletter submissions when rate limit is exceeded', function () {
    $email = 'limited@example.com';
    $key = 'newsletter:'.sha1('127.0.0.1|'.$email);

    RateLimiter::clear($key);

    foreach (range(1, 5) as $attempt) {
        RateLimiter::hit($key, 600);
    }

    Livewire::test('pages::index')
        ->set('email', $email)
        ->call('subscribe')
        ->assertHasErrors('email');

    expect(NewsletterSubscriber::query()->where('email', $email)->exists())->toBeFalse();
});

it('should queue guest comments for moderation when article form is submitted', function () {
    $article = Article::factory()->published()->create();

    Livewire::test('pages::article-show', ['article' => $article])
        ->set('guestName', '  Rafi  ')
        ->set('guestEmail', ' READER@Example.COM ')
        ->set('commentContent', 'Komentar ini cukup panjang untuk melewati validasi.')
        ->call('submitComment')
        ->assertHasNoErrors()
        ->assertSet('guestName', '')
        ->assertSet('guestEmail', '')
        ->assertSet('commentContent', '');

    $comment = Comment::query()->firstOrFail();

    expect($comment)
        ->article_id->toBe($article->id)
        ->guest_name->toBe('Rafi')
        ->guest_email->toBe('reader@example.com')
        ->content->toBe('Komentar ini cukup panjang untuk melewati validasi.')
        ->status->toBe(CommentStatus::Pending);
});

it('should ignore comment honeypot submissions when spam field is filled', function () {
    $article = Article::factory()->published()->create();

    Livewire::test('pages::article-show', ['article' => $article])
        ->set('guestName', 'Bot')
        ->set('guestEmail', 'bot@example.com')
        ->set('commentContent', 'Komentar spam yang cukup panjang.')
        ->set('website', 'https://spam.example')
        ->call('submitComment')
        ->assertHasNoErrors();

    expect(Comment::query()->count())->toBe(0);
});
