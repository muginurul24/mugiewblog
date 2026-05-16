<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Enums\CommentStatus;
use App\Filament\Resources\Comments\CommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua komentar'),
            'pending' => Tab::make('Menunggu')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', CommentStatus::Pending->value)),
            'approved' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', CommentStatus::Approved->value)),
            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', CommentStatus::Rejected->value)),
            'spam' => Tab::make('Spam')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', CommentStatus::Spam->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
