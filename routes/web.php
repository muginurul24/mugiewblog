<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('home');
Route::livewire('/articles/{article:slug}', 'pages::article-show')->name('articles.show');
Route::livewire('/categories/{category:slug}', 'pages::category-show')->name('categories.show');
Route::livewire('/tags/{tag:slug}', 'pages::tag-show')->name('tags.show');
Route::livewire('/search', 'pages::search')->middleware('throttle:30,1')->name('search');

Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/register', 'pages::auth.register')->name('register');
    Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset');
    Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('oauth.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::livewire('/profile', 'pages::auth.profile')->name('profile');
    Route::livewire('/email/verify', 'pages::auth.verify-email')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::get('/robots.txt', [FeedController::class, 'robots'])->name('robots');
Route::get('/feed.xml', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');
