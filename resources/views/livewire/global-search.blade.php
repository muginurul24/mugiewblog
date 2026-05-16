<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false"
    class="relative">
    <form action="{{ route('search') }}" method="GET" @submit="open = false">
        <label for="global-search" class="sr-only">Cari artikel</label>
        <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-surface-400"
            aria-hidden="true"></i>
        <input id="global-search" name="q" wire:model.live.debounce.300ms="query"
            value="{{ request('q') }}" type="search" placeholder="Cari artikel..."
            autocomplete="off" role="combobox" aria-autocomplete="list"
            aria-controls="global-search-results" :aria-expanded="open.toString()"
            @focus="open = true" @input="open = true"
            class="h-10 w-72 rounded-lg border-surface-200 bg-white pl-9 pr-3 text-sm text-surface-900 placeholder:text-surface-400 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-900 dark:text-surface-50">
    </form>

    @if (mb_strlen(trim($query)) >= 2)
        <div id="global-search-results" x-cloak x-show="open"
            x-transition:enter="animate__animated animate__fadeIn animate__faster"
            class="absolute right-0 mt-2 w-[24rem] overflow-hidden rounded-lg border border-surface-200 bg-white shadow-lg dark:border-surface-800 dark:bg-surface-900"
            role="listbox" aria-label="Hasil pencarian cepat">
            <div class="border-b border-surface-200 px-4 py-3 text-xs font-semibold uppercase tracking-[0.18em] text-surface-400 dark:border-surface-800">
                Hasil cepat
            </div>

            @forelse ($this->articles as $article)
                <a href="{{ $article->url() }}" wire:navigate
                    class="block border-b border-surface-100 px-4 py-3 transition hover:bg-surface-50 focus-visible:bg-surface-50 focus-visible:outline-none dark:border-surface-800 dark:hover:bg-surface-800 dark:focus-visible:bg-surface-800"
                    role="option">
                    <span class="block truncate text-sm font-semibold text-surface-900 dark:text-surface-50">
                        {{ $article->title }}
                    </span>
                    <span class="mt-1 flex items-center gap-2 text-xs text-surface-500 dark:text-surface-400">
                        @if ($article->category)
                            <span>{{ $article->category->name }}</span>
                            <span aria-hidden="true">•</span>
                        @endif
                        <span>{{ $article->published_at?->translatedFormat('d M Y') }}</span>
                    </span>
                </a>
            @empty
                <p class="px-4 py-5 text-sm text-surface-500 dark:text-surface-400">
                    Tidak ada hasil untuk "{{ $query }}".
                </p>
            @endforelse

            <a href="{{ route('search', ['q' => $query]) }}" wire:navigate
                class="flex items-center justify-between gap-3 px-4 py-3 text-sm font-semibold text-accent transition hover:bg-accent-muted focus-visible:bg-accent-muted focus-visible:outline-none">
                Lihat semua hasil
                <i class="fas fa-arrow-right h-3.5 w-3.5" aria-hidden="true"></i>
            </a>
        </div>
    @endif
</div>
