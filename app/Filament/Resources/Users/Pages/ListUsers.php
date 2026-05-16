<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua pengguna'),
            'admins' => Tab::make('Admin')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('role', UserRole::Admin->value)),
            'editors' => Tab::make('Editor')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('role', UserRole::Editor->value)),
            'authors' => Tab::make('Author')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('role', UserRole::Author->value)),
            'readers' => Tab::make('Pembaca')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('role', UserRole::User->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
