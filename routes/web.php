<?php

use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('home');
Route::livewire('/articles/{article:slug}', 'pages::article-show')->name('articles.show');
Route::livewire('/categories/{category:slug}', 'pages::category-show')->name('categories.show');
Route::livewire('/tags/{tag:slug}', 'pages::tag-show')->name('tags.show');
Route::livewire('/search', 'pages::search')->name('search');

Route::get('/feed.xml', [FeedController::class, 'rss'])->name('feed');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');
