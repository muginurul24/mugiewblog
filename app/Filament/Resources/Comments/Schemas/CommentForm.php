<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Enums\CommentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Percakapan')
                    ->description('Relasi artikel, penulis komentar, dan isi balasan.')
                    ->schema([
                        Select::make('article_id')
                            ->label('Artikel')
                            ->relationship('article', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('user_id')
                            ->label('Pengguna terdaftar')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('parent_id')
                            ->label('Komentar induk')
                            ->relationship('parent', 'id')
                            ->searchable(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('guest_name')
                                    ->label('Nama tamu')
                                    ->maxLength(255),
                                TextInput::make('guest_email')
                                    ->label('Email tamu')
                                    ->email()
                                    ->maxLength(255),
                            ])
                            ->columnSpanFull(),
                        Textarea::make('content')
                            ->label('Isi komentar')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),
                Section::make('Moderasi')
                    ->schema([
                        Select::make('status')
                            ->options(CommentStatus::options())
                            ->default(CommentStatus::Pending->value)
                            ->required(),
                        DateTimePicker::make('approved_at')
                            ->label('Disetujui pada')
                            ->seconds(false),
                        TextInput::make('ip_address')
                            ->label('Alamat IP')
                            ->maxLength(45),
                        Textarea::make('user_agent')
                            ->label('User agent')
                            ->rows(2)
                            ->maxLength(1000),
                    ]),
            ])
            ->columns(3);
    }
}
