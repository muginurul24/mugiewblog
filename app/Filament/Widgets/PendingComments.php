<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingComments extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Comment::query()
                ->where('status', 'pending')
                ->with(['article', 'author'])
                ->latest()
                ->limit(5))
            ->columns([
                TextColumn::make('article.title')
                    ->label('Article')
                    ->limit(40),
                TextColumn::make('guest_name')
                    ->label('Guest')
                    ->placeholder('Registered user'),
                TextColumn::make('author.name')
                    ->label('User')
                    ->placeholder('-'),
                TextColumn::make('content')
                    ->limit(70),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->color('success')
                    ->action(fn (Comment $record): bool => $record->update([
                        'status' => 'approved',
                        'approved_at' => now(),
                    ])),
            ]);
    }
}
