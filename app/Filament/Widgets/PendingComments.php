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
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Komentar menunggu moderasi')
            ->query(fn (): Builder => Comment::query()
                ->where('status', 'pending')
                ->with(['article', 'author'])
                ->latest()
                ->limit(5))
            ->columns([
                TextColumn::make('article.title')
                    ->label('Artikel')
                    ->limit(28),
                TextColumn::make('commenter')
                    ->label('Komentator')
                    ->state(fn (Comment $record): string => $record->author?->name ?? $record->guest_name ?? '-'),
                TextColumn::make('content')
                    ->label('Isi')
                    ->limit(42),
                TextColumn::make('created_at')
                    ->label('Dibuat')
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
