<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ArticleCategoriesChart;
use App\Filament\Widgets\ArticleViewsChart;
use App\Filament\Widgets\BlogStats;
use App\Filament\Widgets\PendingComments;
use App\Filament\Widgets\RecentArticles;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class BackofficePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('backoffice')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->brandName('MugiewBlog Admin')
                    ->recoverable(),
            ], isRequired: app()->isProduction())
            ->brandName('MugiewBlog Admin')
            ->brandLogo(fn () => view('filament.backoffice.logo'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
            ->font('DM Sans')
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Taupe,
                'info' => Color::Sky,
                'primary' => Color::hex('#D4943A'),
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->viteTheme('resources/css/filament/backoffice/theme.css')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Editorial',
                'Interaksi',
                'Sistem',
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('20s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                BlogStats::class,
                ArticleViewsChart::class,
                ArticleCategoriesChart::class,
                RecentArticles::class,
                PendingComments::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa();
    }
}
