<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Enums\ArticleStatus;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua artikel'),
            'published' => Tab::make('Terbit')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ArticleStatus::Published->value)),
            'review' => Tab::make('Review')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ArticleStatus::Review->value)),
            'scheduled' => Tab::make('Terjadwal')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ArticleStatus::Scheduled->value)),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ArticleStatus::Draft->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
