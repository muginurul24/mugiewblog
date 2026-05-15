<?php

namespace App\Filament\Resources\Comments\Schemas;

use App\Enums\CommentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('article_id')
                    ->relationship('article', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->label('Registered author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('parent_id')
                    ->relationship('parent', 'id')
                    ->searchable(),
                Grid::make(2)
                    ->schema([
                        TextInput::make('guest_name')
                            ->maxLength(255),
                        TextInput::make('guest_email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(CommentStatus::options())
                    ->default(CommentStatus::Pending->value)
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->seconds(false),
                Grid::make(2)
                    ->schema([
                        TextInput::make('ip_address')
                            ->maxLength(45),
                        Textarea::make('user_agent')
                            ->rows(2)
                            ->maxLength(1000),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
