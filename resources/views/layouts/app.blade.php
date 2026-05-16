@php
    $pageTitle = trim((string) ($title ?? $siteSettings->site_name));
    $pageDescription = trim(
        (string) ($metaDescription ?? $siteSettings->site_description),
    );
    $canonicalUrl = (string) ($canonical ?? url()->current());
    $shareImage = (string) ($ogImage ?? ($siteSettings->default_og_image ?: asset('favicon.ico')));
    $isHomeRoute = request()->routeIs('home');
    $isCategoryRoute = request()->routeIs('categories.show');
    $isAboutRoute = request()->routeIs('about');
    $currentCategory = request()->route('category');
    $activeCategoryId = $currentCategory instanceof \App\Models\Category ? $currentCategory->getKey() : null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script>
        (() => {
            const storedTheme = localStorage.getItem('theme') ?? 'system';
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.classList.toggle(
                'dark',
                storedTheme === 'dark' || (storedTheme === 'system' && systemPrefersDark),
            );
        })();
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <meta name="theme-color" content="#d4943a">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if ($siteSettings->rss_enabled)
        <link rel="alternate" type="application/rss+xml" title="{{ $siteSettings->site_name }} RSS" href="{{ route('feed') }}">
    @endif

    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:site_name" content="{{ $siteSettings->site_name }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">

    <title>{{ $pageTitle }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=dm-sans:400,400i,500,500i,600,600i,700,700i|jetbrains-mono:400,400i,500,500i,600,600i,700,700i|space-grotesk:500,600,700"
        rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script type="application/ld+json">
        {!! json_encode([
            '@@context' => 'https://schema.org',
            '@@type' => 'WebSite',
            'name' => $siteSettings->site_name,
            'url' => route('home'),
            'description' => $pageDescription,
            'potentialAction' => [
                '@@type' => 'SearchAction',
                'target' => route('search').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>

<body x-data="blogShell()" x-init="init()"
    class="min-h-screen bg-surface-50 font-sans text-surface-950 antialiased transition-colors duration-200 dark:bg-surface-950 dark:text-surface-50">
    <a href="#main-content"
        class="sr-only z-[60] rounded-lg bg-accent px-4 py-2 font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Lewati ke konten utama
    </a>
    <div class="flex min-h-screen flex-col">
        <header
            class="sticky top-0 z-50 border-b border-surface-200/80 bg-surface-50/90 backdrop-blur-xl dark:border-surface-800/80 dark:bg-surface-950/90">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3"
                    aria-label="{{ $siteSettings->site_name }} home">
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-bold text-white">
                        M
                    </span>
                    <span class="font-display text-lg font-bold">{{ $siteSettings->site_name }}</span>
                </a>

                <nav class="ml-4 hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                    <a href="{{ route('home') }}" wire:navigate
                        data-nav="home"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                            'bg-surface-100 text-accent dark:bg-surface-900' => $isHomeRoute,
                            'text-surface-600 hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900' => ! $isHomeRoute,
                        ])
                        @if ($isHomeRoute) aria-current="page" @endif>
                        Beranda
                    </a>

                    @if ($navigationCategories->isNotEmpty())
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false"
                            @keydown.escape.window="open = false">
                            <button type="button" data-nav="categories" aria-controls="desktop-category-menu"
                                aria-haspopup="menu" :aria-expanded="open.toString()"
                                @click="open = ! open"
                                @keydown.arrow-down.prevent="open = true; $nextTick(() => $refs.firstDesktopCategory?.focus())"
                                @class([
                                    'flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                                    'bg-surface-100 text-accent dark:bg-surface-900' => $isCategoryRoute,
                                    'text-surface-600 hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900' => ! $isCategoryRoute,
                                ])>
                                Kategori
                                <i class="fas fa-chevron-down h-3 w-3 transition" :class="{ 'rotate-180': open }"
                                    aria-hidden="true"></i>
                            </button>

                            <div id="desktop-category-menu" x-cloak x-show="open" role="menu"
                                x-transition:enter="animate__animated animate__fadeIn animate__faster"
                                class="absolute left-0 mt-2 min-w-56 overflow-hidden rounded-lg border border-surface-200 bg-white p-1 shadow-lg dark:border-surface-800 dark:bg-surface-900">
                                @foreach ($navigationCategories as $category)
                                    @php($isActiveCategory = $activeCategoryId === $category->getKey())
                                    <a href="{{ $category->url() }}" wire:navigate role="menuitem"
                                        data-category-nav="{{ $category->slug }}"
                                        @if ($loop->first) x-ref="firstDesktopCategory" @endif
                                        @click="open = false"
                                        @class([
                                            'flex items-center justify-between gap-4 rounded-md px-3 py-2 text-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                                            'bg-surface-100 font-semibold text-accent dark:bg-surface-800' => $isActiveCategory,
                                            'text-surface-700 hover:bg-surface-100 hover:text-accent dark:text-surface-200 dark:hover:bg-surface-800' => ! $isActiveCategory,
                                        ])
                                        @if ($isActiveCategory) aria-current="page" @endif>
                                        <span>{{ $category->name }}</span>
                                        <span class="text-xs text-surface-400">{{ $category->articles_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('about') }}" wire:navigate
                        data-nav="about"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                            'bg-surface-100 text-accent dark:bg-surface-900' => $isAboutRoute,
                            'text-surface-600 hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900' => ! $isAboutRoute,
                        ])
                        @if ($isAboutRoute) aria-current="page" @endif>
                        Tentang
                    </a>
                </nav>

                <div class="ml-auto hidden items-center gap-2 lg:flex">
                    <livewire:global-search />
                </div>

                <div class="ms-auto flex items-center gap-1">
                    @auth
                        <div class="relative hidden sm:block" x-data="{ open: false }" @click.outside="open = false">
                            <button type="button"
                                class="flex h-10 items-center gap-2 rounded-lg px-2 text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900"
                                aria-label="Menu akun" @click="open = ! open">
                                <img
                                    src="{{ auth()->user()->avatar_url }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="h-8 w-8 rounded-lg object-cover"
                                >
                                <i class="fas fa-chevron-down h-3 w-3" aria-hidden="true"></i>
                            </button>
                            <div x-cloak x-show="open"
                                x-transition:enter="animate__animated animate__fadeIn animate__faster"
                                class="absolute right-0 mt-2 w-48 overflow-hidden rounded-lg border border-surface-200 bg-white p-1 shadow-lg dark:border-surface-800 dark:bg-surface-900">
                                <a href="{{ route('profile') }}" wire:navigate
                                    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-surface-100 dark:hover:bg-surface-800">
                                    <i class="fas fa-user h-4 w-4 text-accent" aria-hidden="true"></i>
                                    Profil
                                </a>
                                @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                                    <a href="{{ route('filament.backoffice.pages.dashboard') }}"
                                        class="flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-surface-100 dark:hover:bg-surface-800">
                                        <i class="fas fa-gauge-high h-4 w-4 text-accent" aria-hidden="true"></i>
                                        Admin
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800">
                                        <i class="fas fa-right-from-bracket h-4 w-4 text-accent" aria-hidden="true"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="hidden h-10 items-center gap-2 rounded-lg px-3 text-sm font-semibold text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900 sm:inline-flex">
                            <i class="fas fa-right-to-bracket h-4 w-4" aria-hidden="true"></i>
                            Masuk
                        </a>
                    @endauth

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900"
                            aria-label="Pilih tema" @click="open = ! open">
                            <i class="fas h-4 w-4" :class="themeIcon" aria-hidden="true"></i>
                        </button>
                        <div x-cloak x-show="open"
                            x-transition:enter="animate__animated animate__fadeIn animate__faster"
                            class="absolute right-0 mt-2 w-40 overflow-hidden rounded-lg border border-surface-200 bg-white p-1 shadow-lg dark:border-surface-800 dark:bg-surface-900">
                            <button type="button"
                                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800"
                                @click="setTheme('light'); open = false">
                                <i class="fas fa-sun h-4 w-4 text-accent" aria-hidden="true"></i>
                                Light
                            </button>
                            <button type="button"
                                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800"
                                @click="setTheme('dark'); open = false">
                                <i class="fas fa-moon h-4 w-4 text-accent" aria-hidden="true"></i>
                                Dark
                            </button>
                            <button type="button"
                                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800"
                                @click="setTheme('system'); open = false">
                                <i class="fas fa-display h-4 w-4 text-accent" aria-hidden="true"></i>
                                System
                            </button>
                        </div>
                    </div>

                    <a href="{{ route('search') }}" wire:navigate
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900 lg:hidden"
                        aria-label="Cari artikel">
                        <i class="fas fa-search h-4 w-4" aria-hidden="true"></i>
                    </a>

                    <button type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900 md:hidden"
                        aria-label="Buka menu" @click="mobileMenuOpen = ! mobileMenuOpen">
                        <i class="fas h-4 w-4" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"
                            aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div x-cloak x-show="mobileMenuOpen"
                x-transition:enter="animate__animated animate__fadeIn animate__faster"
                class="border-t border-surface-200 bg-surface-50 px-4 py-4 dark:border-surface-800 dark:bg-surface-950 md:hidden">
                <form action="{{ route('search') }}" method="GET" class="mb-4">
                    <label for="mobile-search" class="sr-only">Cari artikel</label>
                    <input id="mobile-search" name="q" value="{{ request('q') }}" type="search"
                        placeholder="Cari artikel..."
                        class="h-11 w-full rounded-lg border-surface-200 bg-white text-sm focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900">
                </form>
                <nav class="grid gap-1" aria-label="Navigasi mobile">
                    <a href="{{ route('home') }}" wire:navigate
                        data-nav="mobile-home"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                            'bg-surface-100 text-accent dark:bg-surface-900' => $isHomeRoute,
                            'hover:bg-surface-100 dark:hover:bg-surface-900' => ! $isHomeRoute,
                        ])
                        @if ($isHomeRoute) aria-current="page" @endif
                        @click="mobileMenuOpen = false">Beranda</a>

                    @if ($navigationCategories->isNotEmpty())
                        <div x-data="{ categoriesOpen: @js($isCategoryRoute) }" class="grid gap-1">
                            <button type="button" data-nav="mobile-categories" aria-controls="mobile-category-menu"
                                :aria-expanded="categoriesOpen.toString()" @click="categoriesOpen = ! categoriesOpen"
                                @class([
                                    'flex items-center justify-between rounded-lg px-3 py-2 text-left text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                                    'bg-surface-100 text-accent dark:bg-surface-900' => $isCategoryRoute,
                                    'hover:bg-surface-100 dark:hover:bg-surface-900' => ! $isCategoryRoute,
                                ])>
                                <span>Kategori</span>
                                <i class="fas fa-chevron-down h-3 w-3 transition"
                                    :class="{ 'rotate-180': categoriesOpen }" aria-hidden="true"></i>
                            </button>

                            <div id="mobile-category-menu" x-cloak x-show="categoriesOpen"
                                x-transition:enter="animate__animated animate__fadeIn animate__faster"
                                class="grid gap-1 pl-3">
                                @foreach ($navigationCategories as $category)
                                    @php($isActiveCategory = $activeCategoryId === $category->getKey())
                                    <a href="{{ $category->url() }}" wire:navigate
                                        data-category-nav="{{ $category->slug }}"
                                        @class([
                                            'rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                                            'bg-surface-100 text-accent dark:bg-surface-900' => $isActiveCategory,
                                            'hover:bg-surface-100 dark:hover:bg-surface-900' => ! $isActiveCategory,
                                        ])
                                        @if ($isActiveCategory) aria-current="page" @endif
                                        @click="mobileMenuOpen = false">{{ $category->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('about') }}" wire:navigate
                        data-nav="mobile-about"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/40',
                            'bg-surface-100 text-accent dark:bg-surface-900' => $isAboutRoute,
                            'hover:bg-surface-100 dark:hover:bg-surface-900' => ! $isAboutRoute,
                        ])
                        @if ($isAboutRoute) aria-current="page" @endif
                        @click="mobileMenuOpen = false">Tentang</a>
                    @auth
                        <a href="{{ route('profile') }}" wire:navigate
                            class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900"
                            @click="mobileMenuOpen = false">Profil</a>
                        @if (auth()->user()->isAdmin() || auth()->user()->isEditor())
                            <a href="{{ route('filament.backoffice.pages.dashboard') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" wire:navigate
                            class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900"
                            @click="mobileMenuOpen = false">Masuk</a>
                        <a href="{{ route('register') }}" wire:navigate
                            class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900"
                            @click="mobileMenuOpen = false">Daftar</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main id="main-content" class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-950">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.2fr_1fr_1fr] lg:px-8">
                <div>
                    <div class="mb-4 flex items-center gap-3">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-bold text-white">M</span>
                        <span class="font-display text-lg font-bold">Mugiew<span class="text-accent">Blog</span></span>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-surface-500 dark:text-surface-400">
                        Artikel mendalam tentang pemrograman, infrastruktur cloud, DevOps, AI engineering, dan investasi
                        teknologi.
                    </p>
                    <div class="mt-5 flex items-center gap-2">
                        <a href="{{ route('about') }}" wire:navigate
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300"
                            aria-label="Tentang MugiewBlog">
                            <i class="fas fa-circle-info h-4 w-4" aria-hidden="true"></i>
                        </a>
                        <a href="https://github.com/muginurul24"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300"
                            aria-label="GitHub">
                            <i class="fab fa-github h-4 w-4" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/feed.xml') }}"
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300"
                            aria-label="RSS feed">
                            <i class="fas fa-rss h-4 w-4" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Kategori</h2>
                    <ul class="grid gap-2">
                        @foreach ($navigationCategories as $category)
                            <li>
                                <a href="{{ $category->url() }}" wire:navigate
                                    class="text-sm text-surface-500 transition hover:text-accent dark:text-surface-400">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Topik</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($navigationTags as $tag)
                            <a href="{{ $tag->url() }}" wire:navigate
                                class="rounded-lg bg-surface-100 px-3 py-1 text-xs font-medium text-surface-600 transition hover:bg-accent-muted hover:text-accent dark:bg-surface-900 dark:text-surface-300">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div
                class="border-t border-surface-200 px-4 py-5 text-center text-xs text-surface-400 dark:border-surface-800">
                &copy; {{ now()->year }} {{ $siteSettings->site_name }}. Dibangun dengan Laravel, Livewire, dan Filament.
            </div>
        </footer>
    </div>

    @livewireScripts
    <script>
        function blogShell() {
            return {
                mobileMenuOpen: false,
                theme: localStorage.getItem('theme') ?? 'system',
                get themeIcon() {
                    if (this.theme === 'light') {
                        return 'fa-sun';
                    }

                    if (this.theme === 'dark') {
                        return 'fa-moon';
                    }

                    return 'fa-display';
                },
                init() {
                    this.applyTheme();

                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                        if (this.theme === 'system') {
                            this.applyTheme();
                        }
                    });
                },
                setTheme(theme) {
                    this.theme = theme;
                    localStorage.setItem('theme', theme);
                    this.applyTheme();
                },
                applyTheme() {
                    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    document.documentElement.classList.toggle(
                        'dark',
                        this.theme === 'dark' || (this.theme === 'system' && systemPrefersDark),
                    );
                },
            };
        }
    </script>
    @stack('scripts')
</body>

</html>
