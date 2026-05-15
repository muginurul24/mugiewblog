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
    protected function getStats(): array
    {
        return [
            Stat::make('Total articles', Article::query()->count())
                ->description('All drafts, reviews, scheduled and published articles'),
            Stat::make('Published', Article::query()->where('status', ArticleStatus::Published)->count())
                ->color('success'),
            Stat::make('Pending comments', Comment::query()->where('status', CommentStatus::Pending)->count())
                ->color('warning'),
            Stat::make('Users', User::query()->count())
                ->color('info'),
        ];
    }
}
