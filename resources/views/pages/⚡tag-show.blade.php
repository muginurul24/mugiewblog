<?php

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $slug;

    public function mount(Tag $tag): void
    {
        $this->slug = $tag->slug;
    }

    #[Computed]
    public function tag(): Tag
    {
        return Tag::query()
            ->where('slug', $this->slug)
            ->withCount(['articles' => fn (Builder $query) => $query->published()])
            ->firstOrFail();
    }

    #[Computed]
    public function articles()
    {
        return Article::query()
            ->published()
            ->whereHas('tags', fn (Builder $query): Builder => $query->whereKey($this->tag->id))
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->paginate(9);
    }
};
?>

<div>
    <x-slot:title>{{ $this->tag->name }} — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Artikel MugiewBlog dengan topik {{ $this->tag->name }}.</x-slot:metaDescription>
    <x-slot:canonical>{{ $this->tag->url() }}</x-slot:canonical>

    <section class="border-b border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <nav class="mb-8 flex items-center gap-2 text-sm text-surface-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Beranda</a>
                <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
                <span class="text-surface-600 dark:text-surface-300">Topik</span>
                <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
                <span class="text-surface-600 dark:text-surface-300">{{ $this->tag->name }}</span>
            </nav>

            <p class="mb-3 inline-flex items-center gap-2 rounded-lg bg-accent-muted px-3 py-1 text-sm font-semibold text-accent">
                <i class="fas fa-tag h-3.5 w-3.5" aria-hidden="true"></i>
                Topik
            </p>
            <h1 class="font-display text-4xl font-bold">{{ $this->tag->name }}</h1>
            <p class="mt-3 text-sm font-semibold text-accent">{{ $this->tag->articles_count }} artikel published</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($this->articles->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->articles as $article)
                    <x-article-card :article="$article" wire:key="tag-article-{{ $article->id }}" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $this->articles->links() }}
            </div>
        @else
            <x-empty-state
                title="Belum ada artikel"
                description="Topik ini belum memiliki artikel published."
                icon="fa-tags"
            />
        @endif
    </section>
</div>
