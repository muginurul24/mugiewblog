<?php

use App\Filament\Pages\CacheQueue;
use App\Filament\Pages\Settings;
use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use App\Filament\Resources\Tags\TagResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\ArticleCategoriesChart;
use App\Filament\Widgets\ArticleViewsChart;
use App\Filament\Widgets\BlogStats;
use App\Models\Article;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('should render backoffice article resource when user is admin', function () {
    $this->seed();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertSuccessful()
        ->assertSeeText('Artikel')
        ->assertSeeText('Semua artikel')
        ->assertSeeText('Terbit');
});

it('should render admin dashboard at the production admin path', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertSuccessful()
        ->assertSeeText('MugiewBlog Admin')
        ->assertSee('data-backoffice-logo', false);
});

it('should render custom dashboard widgets for admin users', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(BlogStats::class)
        ->assertSeeText('Ringkasan editorial');

    Livewire::test(ArticleViewsChart::class)
        ->assertSeeText('Views per bulan');

    Livewire::test(ArticleCategoriesChart::class)
        ->assertSeeText('Distribusi kategori');
});

it('should render backoffice article creation page when user is admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.create'))
        ->assertSuccessful();
});

it('should render backoffice article detail page when user is admin', function () {
    $admin = User::factory()->admin()->create();
    $article = Article::factory()->published()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.articles.view', $article))
        ->assertSuccessful()
        ->assertSeeText($article->title);
});

it('should protect backoffice resources when user is guest', function () {
    $this->get(route('filament.backoffice.resources.articles.index'))
        ->assertRedirect();
});

it('should allow editors into content management while blocking regular users', function () {
    $editor = User::factory()->editor()->create();
    $user = User::factory()->create();

    $this->actingAs($editor)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertSuccessful();

    $this->actingAs($user)
        ->get(route('filament.backoffice.resources.articles.index'))
        ->assertForbidden();
});

it('should render system resources when user is admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.users.index'))
        ->assertSuccessful()
        ->assertSeeText('Pengguna')
        ->assertSeeText('Semua pengguna')
        ->assertSeeText('Pembaca');

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.media.index'))
        ->assertSuccessful()
        ->assertSeeText('Media');
});

it('should render polished management pages when user is admin', function () {
    $this->seed();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.categories.index'))
        ->assertSuccessful()
        ->assertSeeText('Peta kategori');

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.media.index'))
        ->assertSuccessful()
        ->assertSeeText('Media terbaru');

    $this->actingAs($admin)
        ->get(route('filament.backoffice.resources.newsletter-subscribers.index'))
        ->assertSuccessful()
        ->assertSeeText('Status subscriber');
});

it('should save working site settings when admin submits the settings page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test(Settings::class)
        ->fillForm([
            'site_name' => 'DevPulse',
            'tagline' => 'Catatan engineering',
            'site_url' => 'https://devpulse.test',
            'site_description' => 'Blog untuk tim engineering.',
            'default_og_image' => 'https://devpulse.test/og.webp',
            'contact_email' => 'hello@devpulse.test',
            'sitemap_enabled' => true,
            'rss_enabled' => false,
            'newsletter_enabled' => false,
            'articles_per_page' => 12,
        ])
        ->call('save')
        ->assertNotified();

    expect(SiteSetting::current())
        ->site_name->toBe('DevPulse')
        ->rss_enabled->toBeFalse()
        ->newsletter_enabled->toBeFalse()
        ->articles_per_page->toBe(12);
});

it('should cache only the current site settings identifier', function () {
    $siteSetting = SiteSetting::factory()->create();

    Cache::forever('site-settings.current', (object) ['legacy' => true]);

    expect(SiteSetting::current()->is($siteSetting))->toBeTrue()
        ->and(Cache::get('site-settings.current'))->toBe($siteSetting->id);
});

it('should expose cache management only to admins', function () {
    $admin = User::factory()->admin()->create();
    $editor = User::factory()->editor()->create();

    $this->actingAs($admin)
        ->get(route('filament.backoffice.pages.cache'))
        ->assertSuccessful()
        ->assertSeeText('Cache management');

    $this->actingAs($editor)
        ->get(route('filament.backoffice.pages.cache'))
        ->assertForbidden();

    $this->actingAs($admin);

    Livewire::test(CacheQueue::class)
        ->assertSeeText('Cache management');
});

it('should expose meaningful backoffice navigation icons', function () {
    expect(ArticleResource::getNavigationIcon())->toBe(Heroicon::OutlinedDocumentText)
        ->and(CategoryResource::getNavigationIcon())->toBe(Heroicon::OutlinedFolder)
        ->and(TagResource::getNavigationIcon())->toBe(Heroicon::OutlinedHashtag)
        ->and(CommentResource::getNavigationIcon())->toBe(Heroicon::OutlinedChatBubbleLeftRight)
        ->and(NewsletterSubscriberResource::getNavigationIcon())->toBe(Heroicon::OutlinedEnvelope)
        ->and(UserResource::getNavigationIcon())->toBe(Heroicon::OutlinedUserGroup)
        ->and(MediaResource::getNavigationIcon())->toBe(Heroicon::OutlinedPhoto);
});

it('should expose uploaded and fallback avatars for users', function () {
    $uploaded = User::factory()->create(['avatar' => 'avatars/rafi.webp']);
    $fallback = User::factory()->create(['avatar' => null]);

    expect($uploaded->avatar_url)
        ->toContain('/storage/avatars/rafi.webp')
        ->and($fallback->avatar_url)
        ->toBe("https://picsum.photos/id/{$fallback->id}/200/200")
        ->and($fallback->getFilamentAvatarUrl())
        ->toBe($fallback->avatar_url);
});
