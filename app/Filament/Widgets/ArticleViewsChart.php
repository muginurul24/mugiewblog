<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ArticleViewsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    protected ?string $heading = 'Views per bulan';

    protected ?string $description = 'Akumulasi views artikel terbit pada enam bulan terakhir.';

    protected ?string $maxHeight = '20rem';

    protected function getData(): array
    {
        $months = $this->months();
        $views = Article::published()
            ->where('published_at', '>=', $months->first())
            ->get(['published_at', 'view_count'])
            ->groupBy(fn (Article $article): string => $article->published_at->format('Y-m'))
            ->map
            ->sum('view_count');

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $months
                        ->map(fn ($month): int => $views->get($month->format('Y-m'), 0))
                        ->all(),
                    'borderColor' => '#D4943A',
                    'backgroundColor' => 'rgba(212, 148, 58, 0.14)',
                    'fill' => true,
                    'tension' => 0.36,
                ],
            ],
            'labels' => $months
                ->map(fn ($month): string => $month->translatedFormat('M'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    /**
     * @return Collection<int, Carbon>
     */
    private function months(): Collection
    {
        return collect(range(5, 0))
            ->map(fn (int $monthsAgo) => now()->subMonths($monthsAgo)->startOfMonth());
    }
}
