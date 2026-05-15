@props([
    'article',
    'featured' => false,
])

@php
    $category = $article->category;
    $author = $article->author;
    $authorInitial = mb_strtoupper(mb_substr($author?->name ?? 'M', 0, 1));
@endphp

<article {{ $attributes->class([
    'article-card group overflow-hidden',
    'grid md:grid-cols-2' => $featured,
    'flex h-full flex-col' => ! $featured,
]) }}>
    <a href="{{ $article->url() }}" wire:navigate class="{{ $featured ? 'relative block min-h-72 overflow-hidden' : 'relative block aspect-[16/10] overflow-hidden' }}" aria-label="Baca {{ $article->title }}">
        @if ($article->featured_image_url)
            <img
                src="{{ $article->featured_image_url }}"
                alt="{{ $article->featured_image_alt ?: $article->title }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                loading="{{ $featured ? 'eager' : 'lazy' }}"
            >
        @else
            <div class="hero-grid h-full w-full bg-surface-100 dark:bg-surface-900"></div>
        @endif

        @if ($category)
            <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $category->color }}">
                <i class="fas {{ $category->icon }} h-3 w-3" aria-hidden="true"></i>
                {{ $category->name }}
            </span>
        @endif
    </a>

    <div class="{{ $featured ? 'flex flex-col justify-center p-6 sm:p-8' : 'flex flex-1 flex-col p-5' }}">
        <div class="mb-4 flex items-center gap-3 text-sm text-surface-500 dark:text-surface-400">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-surface-100 text-xs font-bold text-accent dark:bg-surface-800">
                {{ $authorInitial }}
            </span>
            <div class="min-w-0">
                <p class="truncate font-medium text-surface-800 dark:text-surface-100">{{ $author?->name ?? 'MugiewBlog' }}</p>
                <p class="text-xs text-surface-400">{{ $article->published_at?->translatedFormat('d M Y') ?? 'Draft' }}</p>
            </div>
        </div>

        <h2 class="{{ $featured ? 'text-2xl sm:text-3xl' : 'text-lg' }} font-display font-bold leading-tight">
            <a href="{{ $article->url() }}" wire:navigate class="transition hover:text-accent">
                {{ $article->title }}
            </a>
        </h2>

        <p class="{{ $featured ? 'mt-4 text-base' : 'mt-3 text-sm' }} line-clamp-3 leading-6 text-surface-500 dark:text-surface-400">
            {{ $article->excerpt }}
        </p>

        <div class="mt-5 flex flex-wrap items-center gap-4 text-xs font-medium text-surface-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="far fa-clock h-3.5 w-3.5" aria-hidden="true"></i>
                {{ $article->reading_time }} menit
            </span>
            <span class="inline-flex items-center gap-1.5">
                <i class="far fa-comment h-3.5 w-3.5" aria-hidden="true"></i>
                {{ $article->comments_count ?? 0 }} komentar
            </span>
            <span class="ml-auto inline-flex items-center gap-1.5 text-accent">
                Baca
                <i class="fas fa-arrow-right h-3 w-3" aria-hidden="true"></i>
            </span>
        </div>
    </div>
</article>
