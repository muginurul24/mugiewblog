<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected string $view = 'filament.resources.categories.pages.list-categories';

    /**
     * @return Collection<int, Category>
     */
    #[\NoDiscard]
    public function overviewCategories(): Collection
    {
        return Category::query()
            ->with('parent')
            ->withCount('articles')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array{total: int, roots: int, linked_articles: int}
     */
    #[\NoDiscard]
    public function overviewStats(): array
    {
        $categories = $this->overviewCategories();

        return [
            'total' => $categories->count(),
            'roots' => $categories->whereNull('parent_id')->count(),
            'linked_articles' => $categories->sum('articles_count'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
