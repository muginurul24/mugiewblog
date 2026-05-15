<?php

use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Support\ArticleContent;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public string $slug;

    #[Validate('required|string|max:80')]
    public string $guestName = '';

    #[Validate('required|email|max:255')]
    public string $guestEmail = '';

    #[Validate('required|string|min:8|max:2000')]
    public string $commentContent = '';

    public function mount(Article $article): void
    {
        abort_unless(
            $article->status === ArticleStatus::Published
                && $article->published_at !== null
                && $article->published_at->lte(now()),
            404,
        );

        $this->slug = $article->slug;
        $article->increment('view_count');
    }

    public function submitComment(): void
    {
        $validated = $this->validate();

        Comment::create([
            'article_id' => $this->article->id,
            'guest_name' => $validated['guestName'],
            'guest_email' => $validated['guestEmail'],
            'content' => $validated['commentContent'],
            'status' => CommentStatus::Pending,
        ]);

        $this->reset(['guestName', 'guestEmail', 'commentContent']);
        session()->flash('comment', 'Komentar terkirim dan menunggu moderasi.');
    }

    #[Computed]
    public function article(): Article
    {
        return Article::query()
            ->published()
            ->where('slug', $this->slug)
            ->with(['author', 'category', 'tags'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->firstOrFail();
    }

    #[Computed]
    public function preparedContent(): array
    {
        return ArticleContent::prepare($this->article->content_html);
    }

    #[Computed]
    public function comments()
    {
        return $this->article
            ->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with(['author', 'replies.author'])
            ->latest('approved_at')
            ->get();
    }

    #[Computed]
    public function relatedArticles()
    {
        return Article::query()
            ->published()
            ->whereKeyNot($this->article->id)
            ->where(function (Builder $query): void {
                $query
                    ->where('category_id', $this->article->category_id)
                    ->orWhereHas('tags', fn (Builder $tagQuery): Builder => $tagQuery->whereIn('tags.id', $this->article->tags->pluck('id')));
            })
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->limit(3)
            ->get();
    }
};
?>

<article
    x-data="{
        progress: 0,
        updateProgress() {
            const el = this.$refs.articleBody;
            if (!el) return;
            const start = el.offsetTop;
            const total = Math.max(1, el.offsetHeight - window.innerHeight);
            this.progress = Math.min(100, Math.max(0, ((window.scrollY - start + 120) / total) * 100));
        },
    }"
    x-init="
        updateProgress();
        const listener = () => updateProgress();
        window.addEventListener('scroll', listener, { passive: true });
        window.addEventListener('resize', listener);
        return () => {
            window.removeEventListener('scroll', listener);
            window.removeEventListener('resize', listener);
        };
    "
>
    <x-slot:title>{{ $this->article->meta_title ?: $this->article->title }} — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>{{ $this->article->meta_description ?: $this->article->excerpt }}</x-slot:metaDescription>
    <x-slot:canonical>{{ $this->article->url() }}</x-slot:canonical>
    <x-slot:ogImage>{{ $this->article->featured_image ?: asset('favicon.ico') }}</x-slot:ogImage>

    <div class="fixed left-0 top-16 z-40 h-1 w-full bg-transparent">
        <div class="h-full bg-accent transition-[width] duration-150" :style="`width: ${progress}%`"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <nav class="mb-8 flex flex-wrap items-center gap-2 text-sm text-surface-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Beranda</a>
            <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
            @if ($this->article->category)
                <a href="{{ $this->article->category->url() }}" wire:navigate class="hover:text-accent">{{ $this->article->category->name }}</a>
                <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
            @endif
            <span class="max-w-[16rem] truncate text-surface-600 dark:text-surface-300">{{ $this->article->title }}</span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_280px]">
            <div x-ref="articleBody" class="min-w-0">
                <header class="mb-8">
                    @if ($this->article->category)
                        <a href="{{ $this->article->category->url() }}" wire:navigate class="mb-5 inline-flex items-center gap-2 rounded-lg px-3 py-1 text-xs font-bold text-white" style="background-color: {{ $this->article->category->color }}">
                            <i class="fas {{ $this->article->category->icon }} h-3 w-3" aria-hidden="true"></i>
                            {{ $this->article->category->name }}
                        </a>
                    @endif

                    <h1 class="font-display text-4xl font-bold leading-tight sm:text-5xl">{{ $this->article->title }}</h1>
                    <p class="mt-5 text-lg leading-8 text-surface-600 dark:text-surface-300">{{ $this->article->excerpt }}</p>

                    <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-surface-500 dark:text-surface-400">
                        <span class="inline-flex items-center gap-2">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-100 text-sm font-bold text-accent dark:bg-surface-900">
                                {{ mb_strtoupper(mb_substr($this->article->author?->name ?? 'M', 0, 1)) }}
                            </span>
                            <span>
                                <span class="block font-semibold text-surface-800 dark:text-surface-100">{{ $this->article->author?->name ?? 'MugiewBlog' }}</span>
                                <span class="text-xs text-surface-400">{{ $this->article->published_at?->translatedFormat('d M Y') }}</span>
                            </span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="far fa-clock h-4 w-4" aria-hidden="true"></i>
                            {{ $this->article->reading_time }} menit baca
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="far fa-comment h-4 w-4" aria-hidden="true"></i>
                            {{ $this->article->comments_count }} komentar
                        </span>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-surface-200 pt-5 dark:border-surface-800">
                        <span class="mr-1 text-xs font-semibold uppercase text-surface-400">Bagikan</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($this->article->url()) }}&text={{ urlencode($this->article->title) }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300" aria-label="Bagikan ke X">
                            <i class="fab fa-x-twitter h-4 w-4" aria-hidden="true"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($this->article->url()) }}" target="_blank" rel="noopener noreferrer" class="flex h-9 w-9 items-center justify-center rounded-lg bg-surface-100 text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300" aria-label="Bagikan ke LinkedIn">
                            <i class="fab fa-linkedin h-4 w-4" aria-hidden="true"></i>
                        </a>
                        <button
                            type="button"
                            x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText(@js($this->article->url())); copied = true; setTimeout(() => copied = false, 1400)"
                            class="inline-flex h-9 items-center gap-2 rounded-lg bg-surface-100 px-3 text-sm font-semibold text-surface-600 transition hover:text-accent dark:bg-surface-900 dark:text-surface-300"
                        >
                            <i class="far fa-copy h-4 w-4" aria-hidden="true"></i>
                            <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                        </button>
                    </div>
                </header>

                @if ($this->article->featured_image)
                    <img
                        src="{{ $this->article->featured_image }}"
                        alt="{{ $this->article->featured_image_alt ?: $this->article->title }}"
                        class="mb-10 aspect-[16/9] w-full rounded-lg object-cover"
                        loading="eager"
                    >
                @endif

                <div class="article-prose">
                    {!! $this->preparedContent['html'] !!}
                </div>

                @if ($this->article->tags->isNotEmpty())
                    <div class="mt-10 flex flex-wrap gap-2 border-t border-surface-200 pt-8 dark:border-surface-800">
                        @foreach ($this->article->tags as $tag)
                            <a href="{{ $tag->url() }}" wire:navigate class="rounded-lg bg-surface-100 px-3 py-1.5 text-sm font-semibold text-surface-600 transition hover:bg-accent-muted hover:text-accent dark:bg-surface-900 dark:text-surface-300">
                                <i class="fas fa-tag mr-1.5 h-3 w-3" aria-hidden="true"></i>
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <section class="mt-12 rounded-lg border border-surface-200 bg-white p-5 dark:border-surface-800 dark:bg-surface-900">
                    <h2 class="font-display text-2xl font-bold">Komentar</h2>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Komentar baru akan masuk moderation queue sebelum tampil.</p>

                    @if (session('comment'))
                        <p class="mt-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('comment') }}</p>
                    @endif

                    <form wire:submit="submitComment" class="mt-5 grid gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="guest-name" class="mb-1 block text-sm font-semibold">Nama</label>
                                <input id="guest-name" wire:model="guestName" type="text" class="w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                                @error('guestName') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="guest-email" class="mb-1 block text-sm font-semibold">Email</label>
                                <input id="guest-email" wire:model="guestEmail" type="email" class="w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                                @error('guestEmail') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="comment-content" class="mb-1 block text-sm font-semibold">Komentar</label>
                            <textarea id="comment-content" wire:model="commentContent" rows="4" class="w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950"></textarea>
                            @error('commentContent') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-lg bg-accent px-4 py-2.5 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                                <i class="fas fa-paper-plane h-4 w-4" aria-hidden="true"></i>
                                Kirim Komentar
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 space-y-5">
                        @forelse ($this->comments as $comment)
                            <div class="border-t border-surface-200 pt-5 dark:border-surface-800">
                                <div class="flex gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-100 text-sm font-bold text-accent dark:bg-surface-800">
                                        {{ mb_strtoupper(mb_substr($comment->author?->name ?? $comment->guest_name ?? 'P', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold">{{ $comment->author?->name ?? $comment->guest_name }}</p>
                                        <p class="mt-1 text-sm leading-6 text-surface-600 dark:text-surface-300">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-lg bg-surface-100 px-4 py-3 text-sm text-surface-500 dark:bg-surface-950 dark:text-surface-400">Belum ada komentar approved.</p>
                        @endforelse
                    </div>
                </section>

                @if ($this->relatedArticles->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="mb-5 font-display text-2xl font-bold">Artikel Terkait</h2>
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($this->relatedArticles as $relatedArticle)
                                <x-article-card :article="$relatedArticle" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-6">
                    @if (count($this->preparedContent['toc']) > 0)
                        <section class="rounded-lg border border-surface-200 bg-white p-5 dark:border-surface-800 dark:bg-surface-900">
                            <h2 class="mb-4 text-sm font-semibold uppercase text-surface-400">Daftar Isi</h2>
                            <nav class="grid gap-1" aria-label="Daftar isi artikel">
                                @foreach ($this->preparedContent['toc'] as $item)
                                    <a href="#{{ $item['id'] }}" class="{{ $item['level'] === 3 ? 'pl-5 text-surface-400' : 'text-surface-600 dark:text-surface-300' }} rounded-md border-l-2 border-transparent py-1.5 pl-3 text-sm transition hover:border-accent hover:text-accent">
                                        {{ $item['title'] }}
                                    </a>
                                @endforeach
                            </nav>
                        </section>
                    @endif

                    <section class="rounded-lg border border-accent/25 bg-accent-muted p-5">
                        <h2 class="font-display text-lg font-bold">Baca lebih nyaman</h2>
                        <p class="mt-2 text-sm leading-6 text-surface-600 dark:text-surface-300">Progress baca ada di atas halaman. Gunakan ToC untuk lompat ke bagian penting.</p>
                    </section>
                </div>
            </aside>
        </div>
    </div>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $this->article->title,
            'description' => $this->article->excerpt,
            'datePublished' => $this->article->published_at?->toAtomString(),
            'dateModified' => $this->article->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name' => $this->article->author?->name ?? 'MugiewBlog',
            ],
            'image' => $this->article->featured_image,
            'mainEntityOfPage' => $this->article->url(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
</article>
