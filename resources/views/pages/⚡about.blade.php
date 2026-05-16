<?php

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    /**
     * @return array{articles: int, categories: int, authors: int, readers: int}
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'articles' => Article::published()->count(),
            'categories' => Category::query()
                ->whereHas('articles', fn (Builder $query): Builder => $query->published())
                ->count(),
            'authors' => User::query()
                ->whereHas('articles', fn (Builder $query): Builder => $query->published())
                ->count(),
            'readers' => (int) Article::published()->sum('view_count'),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function authors(): Collection
    {
        return User::query()
            ->whereIn('role', [
                UserRole::Admin->value,
                UserRole::Editor->value,
                UserRole::Author->value,
            ])
            ->whereHas('articles', fn (Builder $query): Builder => $query->published())
            ->withCount(['articles' => fn (Builder $query): Builder => $query->published()])
            ->orderByDesc('articles_count')
            ->orderBy('name')
            ->limit(4)
            ->get();
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->withCount(['articles' => fn (Builder $query): Builder => $query->published()])
            ->whereHas('articles', fn (Builder $query): Builder => $query->published())
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
    }

    public function formatCount(int $value): string
    {
        if ($value >= 1000) {
            $formatted = number_format($value / 1000, $value >= 10_000 ? 0 : 1, ',', '');

            return "{$formatted}rb+";
        }

        return (string) $value;
    }
};
?>

<div>
    <x-slot:title>Tentang MugiewBlog — Blog Teknologi dan Pemrograman</x-slot:title>
    <x-slot:metaDescription>MugiewBlog adalah blog teknologi untuk developer Indonesia yang membahas Laravel, cloud, DevOps, AI engineering, dan investasi teknologi dengan fokus praktik produksi.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('about') }}</x-slot:canonical>

    <section class="page-hero hero-grid">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <nav class="mb-8 flex items-center gap-2 text-sm text-surface-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Beranda</a>
                <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
                <span class="text-surface-600 dark:text-surface-300">Tentang</span>
            </nav>

            <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-end">
                <div class="max-w-3xl">
                    <p class="eyebrow mb-4">
                        <i class="fas fa-compass h-3.5 w-3.5" aria-hidden="true"></i>
                        Tentang MugiewBlog
                    </p>
                    <h1 class="font-display text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                        Catatan produksi untuk developer yang butuh keputusan jelas.
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-surface-600 dark:text-surface-300">
                        MugiewBlog merangkum pengalaman membangun aplikasi web, infrastruktur, workflow konten, dan sistem data dalam bentuk artikel yang bisa dipakai langsung oleh tim engineering.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
                        <p class="font-display text-3xl font-bold text-accent">{{ $this->formatCount($this->stats['articles']) }}</p>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Artikel</p>
                    </div>
                    <div class="rounded-lg border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
                        <p class="font-display text-3xl font-bold text-accent">{{ $this->formatCount($this->stats['categories']) }}</p>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Kategori</p>
                    </div>
                    <div class="rounded-lg border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
                        <p class="font-display text-3xl font-bold text-accent">{{ $this->formatCount($this->stats['authors']) }}</p>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Penulis</p>
                    </div>
                    <div class="rounded-lg border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
                        <p class="font-display text-3xl font-bold text-accent">{{ $this->formatCount($this->stats['readers']) }}</p>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Pembaca</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-lg border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-accent-muted text-accent">
                    <i class="fas fa-bullseye h-5 w-5" aria-hidden="true"></i>
                </div>
                <h2 class="font-display text-2xl font-bold">Visi</h2>
                <p class="mt-3 leading-7 text-surface-600 dark:text-surface-300">
                    Menjadi referensi teknologi yang ringkas tetapi tajam untuk developer Indonesia yang sedang mengirim fitur ke produksi, bukan hanya mengejar tren tooling.
                </p>
            </section>

            <section class="rounded-lg border border-surface-200 bg-white p-6 dark:border-surface-800 dark:bg-surface-900">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-accent-muted text-accent">
                    <i class="fas fa-rocket h-5 w-5" aria-hidden="true"></i>
                </div>
                <h2 class="font-display text-2xl font-bold">Misi</h2>
                <p class="mt-3 leading-7 text-surface-600 dark:text-surface-300">
                    Menulis artikel teknis yang mengutamakan konteks, tradeoff, pengujian, operasional, dan pola implementasi yang realistis untuk aplikasi Laravel modern.
                </p>
            </section>
        </div>
    </section>

    <section class="border-y border-surface-200 bg-white py-12 dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div>
                <p class="mb-3 text-sm font-semibold uppercase text-accent">Prinsip editorial</p>
                <h2 class="font-display text-3xl font-bold">Setiap artikel harus bisa dipakai untuk mengambil keputusan.</h2>
                <p class="mt-4 leading-7 text-surface-600 dark:text-surface-300">
                    Format tulisan dibuat untuk pembaca yang ingin memahami alasan teknis, risiko, dan langkah praktis tanpa kehilangan konteks produk.
                </p>
            </div>

            <div class="grid gap-3">
                @foreach ([
                    ['fa-microscope', 'Berbasis praktik', 'Topik ditulis dari sudut pandang implementasi, observability, rollback, dan maintenance.'],
                    ['fa-shield-halved', 'Aman untuk produksi', 'Setiap rekomendasi mempertimbangkan validasi, akses, data, performa, dan kegagalan operasional.'],
                    ['fa-layer-group', 'Mudah dipindai', 'Struktur heading, ringkasan, daftar, code block, dan tabel dibuat jelas untuk pembacaan cepat.'],
                ] as [$icon, $title, $description])
                    <div class="flex gap-4 rounded-lg border border-surface-200 bg-surface-50 p-4 dark:border-surface-800 dark:bg-surface-900">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-muted text-accent">
                            <i class="fas {{ $icon }} h-5 w-5" aria-hidden="true"></i>
                        </span>
                        <span>
                            <span class="block font-display text-lg font-bold">{{ $title }}</span>
                            <span class="mt-1 block text-sm leading-6 text-surface-500 dark:text-surface-400">{{ $description }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-display text-2xl font-bold">Tim Penulis</h2>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Kontributor yang menulis dari pengalaman membangun dan mengoperasikan sistem.</p>
            </div>
            <a href="{{ route('search') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-semibold text-accent hover:text-accent-hover">
                Cari topik
                <i class="fas fa-arrow-right h-4 w-4" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->authors as $author)
                <article class="rounded-lg border border-surface-200 bg-white p-5 dark:border-surface-800 dark:bg-surface-900">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-surface-100 text-xl font-bold text-accent dark:bg-surface-800">
                        {{ mb_strtoupper(mb_substr($author->name, 0, 1)) }}
                    </div>
                    <h3 class="font-display text-lg font-bold">{{ $author->name }}</h3>
                    <p class="mt-1 text-xs font-semibold uppercase text-accent">{{ $author->role->label() }}</p>
                    <p class="mt-3 line-clamp-4 text-sm leading-6 text-surface-500 dark:text-surface-400">
                        {{ $author->bio ?: 'Menulis catatan teknis seputar pengembangan aplikasi dan workflow produksi.' }}
                    </p>
                    <p class="mt-4 text-xs font-semibold text-surface-400">{{ $author->articles_count }} artikel published</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="border-t border-surface-200 bg-white py-12 dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="font-display text-2xl font-bold">Fokus Konten</h2>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Kategori utama yang membentuk arah editorial MugiewBlog.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->categories as $category)
                    <a href="{{ $category->url() }}" wire:navigate class="group rounded-lg border border-surface-200 bg-surface-50 p-5 transition hover:border-accent/40 hover:bg-white dark:border-surface-800 dark:bg-surface-900">
                        <span class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg text-white" style="background-color: {{ $category->color }}">
                            <i class="fas {{ $category->icon }} h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <span class="font-display text-lg font-bold transition group-hover:text-accent">{{ $category->name }}</span>
                        <span class="mt-2 block text-sm leading-6 text-surface-500 dark:text-surface-400">{{ $category->description }}</span>
                        <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-accent">
                            {{ $category->articles_count }} artikel
                            <i class="fas fa-arrow-right h-3 w-3" aria-hidden="true"></i>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="font-display text-2xl font-bold">Tech Stack</h2>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Stack utama yang dipakai untuk membangun blog dan workflow editorial.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Laravel 13', 'Backend framework', 'fab', 'fa-laravel'],
                ['Livewire 4', 'Interaksi server-driven', 'fas', 'fa-bolt'],
                ['Filament 5', 'Admin dan editorial workflow', 'fas', 'fa-table-columns'],
                ['Tailwind CSS v4', 'Design system utility-first', 'fab', 'fa-css'],
                ['FrankenPHP', 'Application server produksi', 'fas', 'fa-server'],
                ['Redis & Horizon', 'Queue, cache, dan monitoring', 'fas', 'fa-gauge-high'],
            ] as [$name, $description, $prefix, $icon])
                <div class="flex items-center gap-4 rounded-lg border border-surface-200 bg-white p-4 dark:border-surface-800 dark:bg-surface-900">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-muted text-accent">
                        <i class="{{ $prefix }} {{ $icon }} h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block font-semibold">{{ $name }}</span>
                        <span class="text-sm text-surface-500 dark:text-surface-400">{{ $description }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    </section>
</div>
