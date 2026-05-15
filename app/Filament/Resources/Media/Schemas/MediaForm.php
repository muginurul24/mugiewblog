<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('File')
                    ->schema([
                        FileUpload::make('path')
                            ->label('File')
                            ->disk('public')
                            ->directory('media')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('filename')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('original_name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('mime_type')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('size')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(2),
                Section::make('Ownership')
                    ->schema([
                        Select::make('user_id')
                            ->label('Uploader')
                            ->relationship('uploader', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('folder')
                            ->default('general')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('alt_text')
                            ->maxLength(255),
                    ]),
            ])
            ->columns(3);
    }
}
