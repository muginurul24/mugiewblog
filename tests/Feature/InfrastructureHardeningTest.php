<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

it('should attach security headers to public responses', function () {
    $response = $this->get(route('home'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Content-Security-Policy');

    expect($response->headers->get('Content-Security-Policy'))
        ->toContain("script-src 'self' 'unsafe-inline' 'unsafe-eval'")
        ->toContain("style-src 'self' 'unsafe-inline' https://fonts.bunny.net")
        ->toContain("font-src 'self' data: https://fonts.bunny.net");
});

it('should restrict horizon access to admin users', function () {
    $admin = User::factory()->admin()->create();
    $editor = User::factory()->editor()->create();
    $reader = User::factory()->create();

    expect(Gate::forUser($admin)->allows('viewHorizon'))->toBeTrue()
        ->and(Gate::forUser($editor)->allows('viewHorizon'))->toBeFalse()
        ->and(Gate::forUser($reader)->allows('viewHorizon'))->toBeFalse();
});

it('should keep production queue workers on redis queues', function () {
    config(['queue.default' => 'redis']);

    expect(config('queue.default'))->toBe('redis')
        ->and(config('horizon.defaults.supervisor-default.queue'))->toBe(['default'])
        ->and(config('horizon.defaults.supervisor-notifications.queue'))->toBe(['notifications', 'emails'])
        ->and(config('horizon.defaults.supervisor-images.queue'))->toBe(['images']);
});
