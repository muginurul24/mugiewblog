<?php

use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Url(as: 'q')]
    public string $query = '';

    public function updatedQuery(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function articles()
    {
        $term = trim($this->query);

        return app(SearchService::class)
            ->publishedArticles($term)
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->paginate(9);
    }
};
?>

<div>
    <x-slot:title>Pencarian — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Cari artikel MugiewBlog berdasarkan judul, ringkasan, kategori, dan tag.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('search') }}</x-slot:canonical>

    <section class="border-b border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="font-display text-4xl font-bold">Cari Artikel</h1>
            <p class="mt-3 leading-7 text-surface-600 dark:text-surface-300">Temukan tulisan tentang Laravel, cloud, DevOps, AI engineering, dan investasi teknologi.</p>

            <div class="relative mt-6">
                <label for="search-query" class="sr-only">Kata kunci</label>
                <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-400" aria-hidden="true"></i>
                <input
                    id="search-query"
                    wire:model.live.debounce.300ms="query"
                    type="search"
                    placeholder="Contoh: Laravel, FrankenPHP, RAG..."
                    class="h-12 w-full rounded-lg border-surface-200 bg-surface-50 pl-11 text-base focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900"
                    autofocus
                >
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <h2 class="font-display text-2xl font-bold">Hasil</h2>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    @if (trim($query) !== '')
                        {{ $this->articles->total() }} hasil untuk "{{ $query }}"
                    @else
                        Artikel terbaru dari MugiewBlog.
                    @endif
                </p>
            </div>
            <div wire:loading class="text-sm font-medium text-accent">
                <i class="fas fa-spinner fa-spin h-4 w-4" aria-hidden="true"></i>
            </div>
        </div>

        @if ($this->articles->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" wire:loading.class="opacity-50">
                @foreach ($this->articles as $article)
                    <x-article-card :article="$article" wire:key="search-article-{{ $article->id }}" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $this->articles->links() }}
            </div>
        @else
            <x-empty-state
                title="Tidak ada hasil"
                description="Coba kata kunci lain atau cari berdasarkan kategori dan tag."
                icon="fa-magnifying-glass-minus"
            />
        @endif
    </section>
</div>
