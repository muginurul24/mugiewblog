<?php

use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\ConfirmNewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should subscribe a reader when newsletter form is submitted', function () {
    Notification::fake();

    Livewire::test('pages::index')
        ->set('email', ' Reader@Example.COM ')
        ->call('subscribe')
        ->assertHasNoErrors('email')
        ->assertSet('email', '');

    $subscriber = NewsletterSubscriber::query()->firstOrFail();

    expect($subscriber)
        ->email->toBe('reader@example.com')
        ->status->toBe('pending')
        ->source->toBe('homepage')
        ->verified_at->toBeNull();

    Notification::assertSentTo($subscriber, ConfirmNewsletterSubscription::class);
});

it('should confirm a newsletter subscription with a signed link', function () {
    $subscriber = NewsletterSubscriber::factory()->create([
        'status' => 'pending',
        'verified_at' => null,
        'subscribed_at' => null,
    ]);

    $this->get(URL::signedRoute('newsletter.confirm', [
        'subscriber' => $subscriber,
        'token' => $subscriber->verification_token,
    ]))->assertRedirect(route('home'));

    expect($subscriber->refresh())
        ->status->toBe('subscribed')
        ->verified_at->not->toBeNull()
        ->subscribed_at->not->toBeNull();
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

it('should queue verified reader comments for moderation when article form is submitted', function () {
    $article = Article::factory()->published()->create();
    $reader = User::factory()->create();

    Livewire::actingAs($reader)
        ->test('pages::article-show', ['article' => $article])
        ->set('commentContent', 'Komentar ini cukup panjang untuk melewati validasi.')
        ->call('submitComment')
        ->assertHasNoErrors()
        ->assertSet('commentContent', '');

    $comment = Comment::query()->firstOrFail();

    expect($comment)
        ->article_id->toBe($article->id)
        ->user_id->toBe($reader->id)
        ->content->toBe('Komentar ini cukup panjang untuk melewati validasi.')
        ->status->toBe(CommentStatus::Pending);
});

it('should redirect guests to login when comment form is submitted directly', function () {
    $article = Article::factory()->published()->create();

    Livewire::test('pages::article-show', ['article' => $article])
        ->set('commentContent', 'Komentar guest yang cukup panjang.')
        ->call('submitComment')
        ->assertRedirect(route('login'));

    expect(Comment::query()->count())->toBe(0);
});

it('should ignore comment honeypot submissions when spam field is filled', function () {
    $article = Article::factory()->published()->create();
    $reader = User::factory()->create();

    Livewire::actingAs($reader)
        ->test('pages::article-show', ['article' => $article])
        ->set('commentContent', 'Komentar spam yang cukup panjang.')
        ->set('website', 'https://spam.example')
        ->call('submitComment')
        ->assertHasNoErrors();

    expect(Comment::query()->count())->toBe(0);
});

it('should paginate root comments after twenty approved entries', function () {
    $article = Article::factory()->published()->create();
    Comment::factory()
        ->count(21)
        ->for($article)
        ->create();

    $component = Livewire::test('pages::article-show', ['article' => $article]);
    $comments = $component->instance()->comments;

    expect($comments->total())->toBe(21)
        ->and($comments->perPage())->toBe(20);
});
