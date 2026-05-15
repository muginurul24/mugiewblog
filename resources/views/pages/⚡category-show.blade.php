<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $slug;

    public function mount(Category $category): void
    {
        $this->slug = $category->slug;
    }

    #[Computed]
    public function category(): Category
    {
        return Category::query()
            ->where('slug', $this->slug)
            ->withCount(['articles' => fn (Builder $query) => $query->published()])
            ->firstOrFail();
    }

    #[Computed]
    public function articles()
    {
        return Article::query()
            ->published()
            ->whereBelongsTo($this->category)
            ->with(['author', 'category'])
            ->withCount(['comments' => fn (Builder $query) => $query->approved()])
            ->latest('published_at')
            ->paginate(9);
    }
};
?>

<div>
    <x-slot:title>{{ $this->category->name }} — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>{{ $this->category->description }}</x-slot:metaDescription>
    <x-slot:canonical>{{ $this->category->url() }}</x-slot:canonical>

    <section class="border-b border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-950">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <nav class="mb-8 flex items-center gap-2 text-sm text-surface-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-accent">Beranda</a>
                <i class="fas fa-chevron-right h-3 w-3" aria-hidden="true"></i>
                <span class="text-surface-600 dark:text-surface-300">{{ $this->category->name }}</span>
            </nav>

            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg text-white" style="background-color: {{ $this->category->color }}">
                    <i class="fas {{ $this->category->icon }} h-5 w-5" aria-hidden="true"></i>
                </span>
                <div>
                    <h1 class="font-display text-4xl font-bold">{{ $this->category->name }}</h1>
                    <p class="mt-3 max-w-2xl leading-7 text-surface-600 dark:text-surface-300">{{ $this->category->description }}</p>
                    <p class="mt-3 text-sm font-semibold text-accent">{{ $this->category->articles_count }} artikel published</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($this->articles->isNotEmpty())
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($this->articles as $article)
                    <x-article-card :article="$article" wire:key="category-article-{{ $article->id }}" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $this->articles->links() }}
            </div>
        @else
            <x-empty-state
                title="Belum ada artikel"
                description="Kategori ini belum memiliki artikel published."
                icon="fa-folder-open"
            />
        @endif
    </section>
</div>
