<?php

use App\Enums\CommentStatus;
use App\Events\CommentCreated;
use App\Listeners\SendCommentNotifications;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\CommentApprovedNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should dispatch moderation event when a clean guest comment is submitted', function () {
    Event::fake([CommentCreated::class]);

    $article = Article::factory()->published()->create();
    $reader = User::factory()->create();

    Livewire::actingAs($reader)
        ->test('pages::article-show', ['article' => $article])
        ->set('commentContent', 'Komentar ini bersih dan cukup panjang untuk masuk moderasi.')
        ->call('submitComment')
        ->assertHasNoErrors();

    $comment = Comment::query()->firstOrFail();

    expect($comment->status)->toBe(CommentStatus::Pending);
    Event::assertDispatched(CommentCreated::class);
});

it('should mark suspicious comments as spam before moderation notifications are sent', function () {
    Event::fake([CommentCreated::class]);

    $article = Article::factory()->published()->create();
    $reader = User::factory()->create([
        'name' => 'Slot Reader',
        'email' => 'reader@example.com',
    ]);

    Livewire::actingAs($reader)
        ->test('pages::article-show', ['article' => $article])
        ->set('commentContent', 'slot gacor https://a.test https://b.test https://c.test')
        ->call('submitComment')
        ->assertHasNoErrors();

    $comment = Comment::query()->firstOrFail();

    expect($comment->status)->toBe(CommentStatus::Spam);
    Event::assertNotDispatched(CommentCreated::class);
});

it('should notify active moderators when the comment listener runs', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $editor = User::factory()->editor()->create();
    $reader = User::factory()->create();
    $comment = Comment::factory()->pending()->for(Article::factory()->published())->create();

    (new SendCommentNotifications)->handle(new CommentCreated($comment));

    Notification::assertSentTo([$admin, $editor], NewCommentNotification::class);
    Notification::assertNotSentTo($reader, NewCommentNotification::class);
});

it('should notify registered commenters when their comment is approved', function () {
    Notification::fake();

    $reader = User::factory()->create();
    $comment = Comment::factory()
        ->pending()
        ->for(Article::factory()->published())
        ->for($reader, 'author')
        ->create();

    expect($comment->approve())->toBeTrue();
    expect($comment->refresh()->status)->toBe(CommentStatus::Approved);

    Notification::assertSentTo($reader, CommentApprovedNotification::class);
});

it('should queue nested replies for moderation up to the third level', function () {
    Event::fake([CommentCreated::class]);

    $reader = User::factory()->create();
    $article = Article::factory()->published()->create();
    $root = Comment::factory()
        ->for($article)
        ->create();

    Livewire::actingAs($reader)
        ->test('pages::article-show', ['article' => $article])
        ->call('startReply', $root->id)
        ->assertSet('replyingTo', $root->id)
        ->set('replyContent', 'Balasan ini cukup panjang untuk moderation queue.')
        ->call('submitReply')
        ->assertHasNoErrors()
        ->assertSet('replyingTo', null);

    $reply = Comment::query()->where('parent_id', $root->id)->firstOrFail();

    expect($reply)
        ->user_id->toBe($reader->id)
        ->status->toBe(CommentStatus::Pending);

    Event::assertDispatched(CommentCreated::class);
});
