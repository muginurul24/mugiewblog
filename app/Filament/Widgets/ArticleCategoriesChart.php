<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class ArticleCategoriesChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected ?string $heading = 'Distribusi kategori';

    protected ?string $description = 'Artikel terbit per kategori.';

    protected ?string $maxHeight = '20rem';

    protected function getData(): array
    {
        $categories = Category::query()
            ->whereHas('articles', fn (Builder $query): Builder => $query->published())
            ->withCount([
                'articles as published_articles_count' => fn (Builder $query): Builder => $query->published(),
            ])
            ->orderByDesc('published_articles_count')
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        return [
            'datasets' => [
                [
                    'label' => 'Artikel',
                    'data' => $categories->pluck('published_articles_count')->all(),
                    'backgroundColor' => $categories->pluck('color')->all(),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $categories->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'cutout' => '68%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
