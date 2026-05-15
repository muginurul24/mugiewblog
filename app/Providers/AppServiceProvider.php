<?php

namespace App\Providers;

use App\Events\CommentCreated;
use App\Listeners\SendCommentNotifications;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(CommentCreated::class, SendCommentNotifications::class);

        VerifyEmail::toMailUsing(fn (object $notifiable, string $url): MailMessage => (new MailMessage)
            ->subject('Verifikasi email MugiewBlog')
            ->line('Klik tombol di bawah untuk mengaktifkan akun MugiewBlog Anda.')
            ->action('Verifikasi email', $url));

        View::composer(['layouts.app', 'layouts::app'], function ($view): void {
            $view->with([
                'navigationCategories' => Category::query()
                    ->withCount(['articles' => fn ($query) => $query->published()])
                    ->orderBy('sort_order')
                    ->limit(8)
                    ->get(),
                'navigationTags' => Tag::query()
                    ->withCount(['articles' => fn ($query) => $query->published()])
                    ->orderByDesc('articles_count')
                    ->limit(12)
                    ->get(),
            ]);
        });
    }
}
