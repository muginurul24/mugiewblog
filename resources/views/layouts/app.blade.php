@php
    $pageTitle = trim((string) ($title ?? config('app.name', 'MugiewBlog')));
    $pageDescription = trim(
        (string) ($metaDescription ?? 'MugiewBlog membahas Laravel, pemrograman modern, DevOps, cloud, AI engineering, dan investasi teknologi untuk developer Indonesia.'),
    );
    $canonicalUrl = (string) ($canonical ?? url()->current());
    $shareImage = (string) ($ogImage ?? asset('favicon.ico'));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script>
        (function() {
            const stored = localStorage.getItem('theme') || 'system';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', stored === 'dark' || (stored === 'system' && prefersDark));
        })();
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">

    <title>{{ $pageTitle }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>

<body
    x-data="blogShell()"
    x-init="init()"
    class="min-h-screen bg-surface-50 text-surface-950 antialiased transition-colors duration-200 dark:bg-surface-950 dark:text-surface-50"
>
    <div class="flex min-h-screen flex-col">
        <header class="sticky top-0 z-50 border-b border-surface-200/80 bg-surface-50/90 backdrop-blur-xl dark:border-surface-800/80 dark:bg-surface-950/90">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3" aria-label="MugiewBlog home">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-bold text-white">
                        M
                    </span>
                    <span class="font-display text-lg font-bold">MugiewBlog</span>
                </a>

                <nav class="ml-4 hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
                    <a href="{{ route('home') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900">
                        Beranda
                    </a>
                    @foreach ($navigationCategories->take(4) as $category)
                        <a href="{{ $category->url() }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>

                <div class="ml-auto hidden items-center gap-2 lg:flex">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <label for="global-search" class="sr-only">Cari artikel</label>
                        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-400" aria-hidden="true"></i>
                        <input
                            id="global-search"
                            name="q"
                            value="{{ request('q') }}"
                            type="search"
                            placeholder="Cari artikel..."
                            class="h-10 w-72 rounded-lg border-surface-200 bg-white pl-9 pr-3 text-sm text-surface-900 placeholder:text-surface-400 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900 dark:text-surface-50"
                        >
                    </form>
                </div>

                <div class="flex items-center gap-1">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900"
                            aria-label="Pilih tema"
                            @click="open = ! open"
                        >
                            <i class="fas h-4 w-4" :class="themeIcon" aria-hidden="true"></i>
                        </button>
                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="animate__animated animate__fadeIn animate__faster"
                            class="absolute right-0 mt-2 w-40 overflow-hidden rounded-lg border border-surface-200 bg-white p-1 shadow-lg dark:border-surface-800 dark:bg-surface-900"
                        >
                            <button type="button" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800" @click="setTheme('light'); open = false">
                                <i class="fas fa-sun h-4 w-4 text-accent" aria-hidden="true"></i>
                                Light
                            </button>
                            <button type="button" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800" @click="setTheme('dark'); open = false">
                                <i class="fas fa-moon h-4 w-4 text-accent" aria-hidden="true"></i>
                                Dark
                            </button>
                            <button type="button" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm hover:bg-surface-100 dark:hover:bg-surface-800" @click="setTheme('system'); open = false">
                                <i class="fas fa-display h-4 w-4 text-accent" aria-hidden="true"></i>
                                System
                            </button>
                        </div>
                    </div>

                    <a href="{{ route('search') }}" wire:navigate class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900 lg:hidden" aria-label="Cari artikel">
                        <i class="fas fa-search h-4 w-4" aria-hidden="true"></i>
                    </a>

                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-surface-600 transition hover:bg-surface-100 hover:text-accent dark:text-surface-300 dark:hover:bg-surface-900 md:hidden"
                        aria-label="Buka menu"
                        @click="mobileMenuOpen = ! mobileMenuOpen"
                    >
                        <i class="fas h-4 w-4" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div x-cloak x-show="mobileMenuOpen" x-transition:enter="animate__animated animate__fadeIn animate__faster" class="border-t border-surface-200 bg-surface-50 px-4 py-4 dark:border-surface-800 dark:bg-surface-950 md:hidden">
                <form action="{{ route('search') }}" method="GET" class="mb-4">
                    <label for="mobile-search" class="sr-only">Cari artikel</label>
                    <input
                        id="mobile-search"
                        name="q"
                        value="{{ request('q') }}"
                        type="search"
                        placeholder="Cari artikel..."
                        class="h-11 w-full rounded-lg border-surface-200 bg-white text-sm focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900"
                    >
                </form>
                <nav class="grid gap-1" aria-label="Navigasi mobile">
                    <a href="{{ route('home') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900" @click="mobileMenuOpen = false">Beranda</a>
                    @foreach ($navigationCategories as $category)
                        <a href="{{ $category->url() }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-surface-100 dark:hover:bg-surface-900" @click="mobileMenuOpen = false">{{ $category->name }}</a>
                    @endforeach
                </nav>
            </div>
        </header>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="border-t border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-950">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.2fr_1fr_1fr] lg:px-8">
                <div>
                    <div class="mb-4 flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-accent text-sm font-bold text-white">M</span>
                        <span class="font-display text-lg font-bold">MugiewBlog</span>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-surface-500 dark:text-surface-400">
                        Artikel mendalam tentang pemrograman, infrastruktur cloud, DevOps, AI engineering, dan investasi teknologi.
                    </p>
                    <div class="mt-5 flex items-center gap-2">
                        <a href="https://github.com/muginurul24" class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300" aria-label="GitHub">
                            <i class="fab fa-github h-4 w-4" aria-hidden="true"></i>
                        </a>
                        <a href="{{ url('/feed.xml') }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300" aria-label="RSS feed">
                            <i class="fas fa-rss h-4 w-4" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Kategori</h2>
                    <ul class="grid gap-2">
                        @foreach ($navigationCategories as $category)
                            <li>
                                <a href="{{ $category->url() }}" wire:navigate class="text-sm text-surface-500 transition hover:text-accent dark:text-surface-400">
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
                            <a href="{{ $tag->url() }}" wire:navigate class="rounded-lg bg-surface-100 px-3 py-1 text-xs font-medium text-surface-600 transition hover:bg-accent-muted hover:text-accent dark:bg-surface-900 dark:text-surface-300">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="border-t border-surface-200 px-4 py-5 text-center text-xs text-surface-400 dark:border-surface-800">
                &copy; {{ now()->year }} MugiewBlog. Dibangun dengan Laravel, Livewire, dan Filament.
            </div>
        </footer>
    </div>

    @livewireScripts
    <script>
        function blogShell() {
            return {
                mobileMenuOpen: false,
                theme: localStorage.getItem('theme') || 'system',
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
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.classList.toggle('dark', this.theme === 'dark' || (this.theme === 'system' && prefersDark));
                },
            };
        }
    </script>
    @stack('scripts')
</body>

</html>
