<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should render public auth pages when visited by guests', function () {
    $this->get(route('login'))->assertOk()->assertSee('Masuk');
    $this->get(route('register'))->assertOk()->assertSee('Daftar');
    $this->get(route('password.request'))->assertOk()->assertSee('Reset password');
});

it('should create an unverified reader account when registration is submitted', function () {
    Notification::fake();

    Livewire::test('pages::auth.register')
        ->set('name', 'Rafi Reader')
        ->set('username', 'rafi-reader')
        ->set('email', 'reader@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('verification.notice'));

    $user = User::query()->where('email', 'reader@example.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeFalse();
    $this->assertAuthenticatedAs($user);
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('should authenticate active users when login credentials are valid', function () {
    $user = User::factory()->create([
        'email' => 'reader@example.com',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'reader@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('should send a password reset link when the email exists', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'reader@example.com',
    ]);

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'reader@example.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('should update profile details when an authenticated reader saves changes', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::auth.profile')
        ->set('name', 'Updated Reader')
        ->set('username', 'updated-reader')
        ->set('bio', 'Laravel reader and infrastructure notes collector.')
        ->call('saveProfile')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user)
        ->name->toBe('Updated Reader')
        ->username->toBe('updated-reader')
        ->bio->toBe('Laravel reader and infrastructure notes collector.');
});

it('should create or link a user when oauth callback succeeds', function () {
    Socialite::fake('github', (new SocialiteUser)->map([
        'id' => 'github-123',
        'nickname' => 'octo-reader',
        'name' => 'Octo Reader',
        'email' => 'octo@example.com',
        'avatar' => 'https://example.com/avatar.png',
    ]));

    $this->get(route('oauth.callback', 'github'))
        ->assertRedirect(route('home'));

    $user = User::query()->where('email', 'octo@example.com')->firstOrFail();

    expect($user)
        ->oauth_provider->toBe('github')
        ->oauth_provider_id->toBe('github-123')
        ->email_verified_at->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});
