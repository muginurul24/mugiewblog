<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['article', 'author']))
            ->columns([
                TextColumn::make('article.title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('guest_name')
                    ->label('Guest')
                    ->searchable()
                    ->placeholder('Registered user'),
                TextColumn::make('author.name')
                    ->label('User')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('content')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CommentStatus $state): string => $state->label())
                    ->color(fn (CommentStatus $state): string => $state->color()),
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CommentStatus::options()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status !== CommentStatus::Approved)
                    ->action(fn (Comment $record): bool => $record->update([
                        'status' => CommentStatus::Approved,
                        'approved_at' => now(),
                    ])),
                Action::make('reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Comment $record): bool => $record->status === CommentStatus::Pending)
                    ->action(fn (Comment $record): bool => $record->update([
                        'status' => CommentStatus::Rejected,
                        'approved_at' => null,
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
