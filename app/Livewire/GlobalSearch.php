<?php

namespace App\Livewire;

use App\Models\Article;
use App\Services\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    /**
     * @return Collection<int, Article>
     */
    #[Computed]
    public function articles(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < 2) {
            return Article::query()->whereRaw('1 = 0')->get();
        }

        return app(SearchService::class)
            ->cachedPublishedArticles($term)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->limit(5)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.global-search');
    }
}
