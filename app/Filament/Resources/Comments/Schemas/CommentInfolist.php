<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Enums\CommentStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Komentar')
                    ->schema([
                        TextEntry::make('article.title')
                            ->label('Artikel'),
                        TextEntry::make('author.name')
                            ->label('Pengguna terdaftar')
                            ->placeholder('-'),
                        TextEntry::make('guest_name')
                            ->label('Nama tamu')
                            ->placeholder('-'),
                        TextEntry::make('guest_email')
                            ->label('Email tamu')
                            ->placeholder('-'),
                        TextEntry::make('content')
                            ->label('Isi komentar')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Moderasi')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (CommentStatus $state): string => $state->label())
                            ->color(fn (CommentStatus $state): string => $state->color()),
                        TextEntry::make('approved_at')
                            ->label('Disetujui pada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->dateTime(),
                    ])
                    ->columns(3),
                Section::make('Jejak teknis')
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label('Alamat IP')
                            ->placeholder('-'),
                        TextEntry::make('user_agent')
                            ->label('User agent')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
