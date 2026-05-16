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
                    ->description('Unggah aset yang siap dipakai di konten publik.')
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
                                    ->label('Nama file')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('original_name')
                                    ->label('Nama asli')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('mime_type')
                                    ->label('MIME type')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('size')
                                    ->label('Ukuran byte')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(2),
                Section::make('Kepemilikan')
                    ->description('Pemilik aset, folder, dan alt text publik.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Pengunggah')
                            ->relationship('uploader', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('folder')
                            ->default('general')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('alt_text')
                            ->label('Alt text')
                            ->maxLength(255),
                    ]),
            ])
            ->columns(3);
    }
}
