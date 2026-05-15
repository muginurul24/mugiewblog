<?php

namespace App\Filament\Resources\Media\Tables;

use App\Models\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('uploader'))
            ->columns([
                ImageColumn::make('url')
                    ->label('Preview')
                    ->state(fn (Media $record): string => $record->url())
                    ->square(),
                TextColumn::make('original_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('folder')
                    ->badge()
                    ->searchable(),
                TextColumn::make('uploader.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('size')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 1).' KB')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('folder')
                    ->options(fn (): array => Media::query()
                        ->distinct()
                        ->orderBy('folder')
                        ->pluck('folder', 'folder')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
