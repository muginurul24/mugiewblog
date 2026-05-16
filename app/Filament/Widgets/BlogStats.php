<?php

namespace App\Filament\Widgets;

use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan editorial';

    protected ?string $description = 'Status konten dan moderasi terbaru.';

    protected function getStats(): array
    {
        return [
            Stat::make('Total artikel', Article::query()->count())
                ->description('Draft, review, terjadwal, dan terbit')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($this->articleCreationTrend()),
            Stat::make('Sudah terbit', Article::query()->where('status', ArticleStatus::Published)->count())
                ->description('Artikel siap dibaca publik')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart($this->publishedArticleTrend())
                ->color('success'),
            Stat::make('Komentar menunggu', Comment::query()->where('status', CommentStatus::Pending)->count())
                ->description('Perlu moderasi')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
            Stat::make('Pengguna', User::query()->count())
                ->description('Akun terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }

    /**
     * @return array<int, int>
     */
    private function articleCreationTrend(): array
    {
        return $this->monthlyArticleCounts();
    }

    /**
     * @return array<int, int>
     */
    private function publishedArticleTrend(): array
    {
        return $this->monthlyArticleCounts(onlyPublished: true);
    }

    /**
     * @return array<int, int>
     */
    private function monthlyArticleCounts(bool $onlyPublished = false): array
    {
        $months = collect(range(5, 0))
            ->map(fn (int $monthsAgo) => now()->subMonths($monthsAgo)->startOfMonth());

        $articles = Article::query()
            ->where('created_at', '>=', $months->first())
            ->when($onlyPublished, fn ($query) => $query->where('status', ArticleStatus::Published))
            ->get(['created_at']);

        $counts = $articles
            ->groupBy(fn (Article $article): string => $article->created_at->format('Y-m'))
            ->map
            ->count();

        return $months
            ->map(fn ($month): int => $counts->get($month->format('Y-m'), 0))
            ->values()
            ->all();
    }
}
