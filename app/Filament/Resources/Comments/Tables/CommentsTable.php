<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['article', 'author']))
            ->columns([
                TextColumn::make('article.title')
                    ->label('Artikel')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('guest_name')
                    ->label('Tamu')
                    ->searchable()
                    ->placeholder('Pengguna terdaftar'),
                TextColumn::make('author.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('content')
                    ->label('Komentar')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (CommentStatus $state): string => $state->label())
                    ->color(fn (CommentStatus $state): string => $state->color()),
                TextColumn::make('approved_at')
                    ->label('Disetujui')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(CommentStatus::options()),
                TrashedFilter::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status !== CommentStatus::Approved)
                    ->action(fn (Comment $record): bool => $record->approve()),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status === CommentStatus::Pending)
                    ->action(fn (Comment $record): bool => $record->reject()),
                Action::make('spam')
                    ->label('Spam')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status !== CommentStatus::Spam)
                    ->action(fn (Comment $record): bool => $record->markAsSpam()),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Setujui')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records): bool => $records->each->approve()->isNotEmpty()),
                    BulkAction::make('reject')
                        ->label('Tolak')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records): bool => $records->each->reject()->isNotEmpty()),
                    BulkAction::make('spam')
                        ->label('Spam')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records): bool => $records->each->markAsSpam()->isNotEmpty()),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
