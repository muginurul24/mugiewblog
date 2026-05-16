<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\NewsletterSubscriber;
use App\Models\Tag;
use App\Notifications\ConfirmNewsletterSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url]
    public ?string $category = null;

    #[Validate('required|email|max:255')]
    public string $email = '';

    public string $website = '';

    public function filterCategory(?string $slug): void
    {
        $this->category = $slug;
        $this->resetPage();
    }

    public function subscribe(): void
    {
        $this->email = Str::lower(trim($this->email));

        if (filled($this->website)) {
            $this->reset(['email', 'website']);

            return;
        }

        $this->validateOnly('email');
        $this->ensureSubscriptionIsNotRateLimited();

        $subscriber = NewsletterSubscriber::query()->updateOrCreate(
            ['email' => $this->email],
            [
                'status' => 'pending',
                'source' => 'homepage',
                'verified_at' => null,
                'subscribed_at' => null,
                'unsubscribed_at' => null,
            ],
        );

        $subscriber->notify(new ConfirmNewsletterSubscription($subscriber));

        $this->reset('email');
        session()->flash('newsletter', 'Cek inbox untuk konfirmasi newsletter.');
    }

    /**
     * @throws ValidationException
     */
    private function ensureSubscriptionIsNotRateLimited(): void
    {
        $key = 'newsletter:'.sha1((request()->ip() ?: 'unknown').'|'.$this->email);

        $executed = RateLimiter::attempt(
            $key,
            5,
            fn (): bool => true,
            600,
        );

        if (! $executed) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
            ->withCount(['articles' => fn (Builder $query) => $query->published()])
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function featuredArticle(): ?Article
    {
        return Article::query()
            ->published()
            ->featured()
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->first();
    }

    #[Computed]
    public function articles()
    {
        return Article::query()
            ->published()
            ->when($this->category, fn (Builder $query): Builder => $query->whereHas(
                'category',
                fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $this->category),
            ))
            ->when($this->featuredArticle, fn (Builder $query): Builder => $query->whereKeyNot($this->featuredArticle->id))
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->paginate(8);
    }

    #[Computed]
    public function popularArticles()
    {
        return Article::query()
            ->published()
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function popularTags()
    {
        return Tag::query()
            ->withCount(['articles' => fn (Builder $query) => $query->published()])
            ->orderByDesc('articles_count')
            ->limit(14)
            ->get();
    }
};
?>

<div>
    <x-slot:title>MugiewBlog — Blog Teknologi dan Pemrograman</x-slot:title>
    <x-slot:metaDescription>Artikel mendalam tentang Laravel, pemrograman, infrastruktur cloud, DevOps, AI engineering, dan investasi teknologi untuk developer Indonesia.</x-slot:metaDescription>

    <section class="page-hero hero-grid">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="eyebrow mb-5">
                    <i class="fas fa-bolt h-3.5 w-3.5" aria-hidden="true"></i>
                    Laravel, Cloud, DevOps, AI, Investment
                </p>
                <h1 class="font-display text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                    Engineering notes untuk developer yang mengirim fitur ke produksi.
                </h1>
                <p class="mx-auto mt-5 max-w-3xl text-lg leading-8 text-surface-600 dark:text-surface-300">
                    Artikel teknis mendalam tentang implementasi produksi, keputusan arsitektur, performa, dan workflow engineering yang bisa dipakai langsung oleh tim modern.
                </p>
            </div>

            @if ($this->featuredArticle)
                <div class="mt-10 motion-safe:animate__animated motion-safe:animate__fadeInUp motion-safe:animate__faster">
                    <x-article-card :article="$this->featuredArticle" featured />
                </div>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex gap-2 overflow-x-auto pb-2">
            <button
                type="button"
                wire:click="filterCategory(null)"
                class="shrink-0 rounded-lg border px-4 py-2 text-sm font-semibold transition {{ $category === null ? 'border-accent bg-accent text-white' : 'border-surface-200 bg-white text-surface-600 hover:border-accent/50 hover:text-accent dark:border-surface-800 dark:bg-surface-900 dark:text-surface-300' }}"
            >
                Semua
            </button>
            @foreach ($this->categories as $categoryItem)
                <button
                    type="button"
                    wire:click="filterCategory(@js($categoryItem->slug))"
                    class="shrink-0 rounded-lg border px-4 py-2 text-sm font-semibold transition {{ $category === $categoryItem->slug ? 'border-accent bg-accent text-white' : 'border-surface-200 bg-white text-surface-600 hover:border-accent/50 hover:text-accent dark:border-surface-800 dark:bg-surface-900 dark:text-surface-300' }}"
                >
                    {{ $categoryItem->name }}
                    <span class="ml-1 text-xs opacity-70">{{ $categoryItem->articles_count }}</span>
                </button>
            @endforeach
        </div>
    </section>

    <section class="mx-auto grid max-w-7xl gap-8 px-4 pb-14 sm:px-6 lg:grid-cols-[1fr_320px] lg:px-8">
        <div>
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="section-kicker">Terbaru</p>
                    <h2 class="mt-2 font-display text-2xl font-bold">Artikel Terbaru</h2>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Tulisan terbaru dari pipeline produksi MugiewBlog.</p>
                </div>
                <div wire:loading class="text-sm font-medium text-accent">
                    <i class="fas fa-spinner fa-spin h-4 w-4" aria-hidden="true"></i>
                </div>
            </div>

            @if ($this->articles->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2" wire:loading.class="opacity-50">
                    @foreach ($this->articles as $article)
                        <x-article-card :article="$article" wire:key="article-{{ $article->id }}" class="motion-safe:animate__animated motion-safe:animate__fadeInUp motion-safe:animate__faster" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $this->articles->links() }}
                </div>
            @else
                <x-empty-state
                    title="Belum ada artikel"
                    description="Kategori ini belum punya artikel published. Pilih kategori lain atau lihat semua artikel."
                    icon="fa-file-circle-xmark"
                />
            @endif
        </div>

        <aside class="space-y-6">
            <section class="surface-panel p-5">
                <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Populer</h2>
                <div class="space-y-4">
                    @foreach ($this->popularArticles as $popularArticle)
                        <a href="{{ $popularArticle->url() }}" wire:navigate class="group flex gap-3">
                            <span class="font-display text-xl font-bold text-accent/35 transition group-hover:text-accent">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="min-w-0">
                                <span class="line-clamp-2 text-sm font-semibold leading-5 transition group-hover:text-accent">{{ $popularArticle->title }}</span>
                                <span class="mt-1 block text-xs text-surface-400">{{ $popularArticle->reading_time }} menit baca</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="surface-panel p-5">
                <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Topik Populer</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($this->popularTags as $tag)
                        <a href="{{ $tag->url() }}" wire:navigate class="rounded-lg bg-surface-100 px-3 py-1 text-xs font-semibold text-surface-600 transition hover:bg-accent-muted hover:text-accent dark:bg-surface-800 dark:text-surface-300">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-accent/25 bg-accent-muted p-5">
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-white/70 text-accent dark:bg-surface-900">
                    <i class="far fa-envelope h-5 w-5" aria-hidden="true"></i>
                </div>
                <h2 class="font-display text-lg font-bold">Newsletter mingguan</h2>
                <p class="mt-2 text-sm leading-6 text-surface-600 dark:text-surface-300">Ringkasan artikel baru, insight produksi, dan catatan tooling tanpa spam.</p>

                @if (session('newsletter'))
                    <p class="mt-4 rounded-lg bg-white px-3 py-2 text-sm font-medium text-accent dark:bg-surface-900">{{ session('newsletter') }}</p>
                @endif

                <form wire:submit="subscribe" class="mt-4 space-y-3">
                    <div class="hidden" aria-hidden="true">
                        <label for="newsletter-website">Website</label>
                        <input id="newsletter-website" wire:model="website" type="text" tabindex="-1" autocomplete="off">
                    </div>
                    <label for="newsletter-email" class="sr-only">Email newsletter</label>
                    <input
                        id="newsletter-email"
                        wire:model="email"
                        type="email"
                        placeholder="email@anda.com"
                        class="w-full rounded-lg border-surface-200 bg-white text-sm focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900"
                    >
                    @error('email')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60" wire:loading.attr="disabled">
                        <i class="fas fa-paper-plane h-4 w-4" aria-hidden="true"></i>
                        Berlangganan
                    </button>
                </form>
            </section>
        </aside>
    </section>

    <section class="border-t border-surface-200 bg-white py-12 dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="font-display text-2xl font-bold">Jelajahi Kategori</h2>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Temukan tulisan berdasarkan fokus kerja Anda.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->categories as $categoryItem)
                    <a href="{{ $categoryItem->url() }}" wire:navigate class="group rounded-lg border border-surface-200 bg-surface-50 p-5 transition hover:border-accent/40 hover:bg-white dark:border-surface-800 dark:bg-surface-900 dark:hover:bg-surface-900">
                        <span class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg text-white" style="background-color: {{ $categoryItem->color }}">
                            <i class="fas {{ $categoryItem->icon }} h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <span class="font-display text-lg font-bold transition group-hover:text-accent">{{ $categoryItem->name }}</span>
                        <span class="mt-2 block text-sm leading-6 text-surface-500 dark:text-surface-400">{{ $categoryItem->description }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
