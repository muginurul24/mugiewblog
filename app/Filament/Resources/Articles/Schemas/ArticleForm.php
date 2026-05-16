<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Naskah')
                    ->description('Judul, slug, ringkasan, dan isi utama artikel.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        MarkdownEditor::make('content_md')
                            ->label('Isi artikel')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
                Section::make('Publikasi')
                    ->description('Atur pemilik, taksonomi, status, dan jadwal tayang.')
                    ->schema([
                        Select::make('user_id')
                            ->label('Penulis')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('categories', 'slug'),
                                Textarea::make('description')
                                    ->rows(3)
                                    ->maxLength(500),
                            ]),
                        Select::make('tags')
                            ->label('Tag')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('tags', 'slug'),
                                Textarea::make('description')
                                    ->rows(3)
                                    ->maxLength(500),
                            ]),
                        Select::make('status')
                            ->label('Status')
                            ->options(ArticleStatus::options())
                            ->default(ArticleStatus::Draft->value)
                            ->required()
                            ->live(),
                        DateTimePicker::make('published_at')
                            ->label('Terbit pada')
                            ->seconds(false)
                            ->visible(fn (Get $get): bool => $get('status') === ArticleStatus::Published->value),
                        DateTimePicker::make('scheduled_at')
                            ->label('Jadwal tayang')
                            ->seconds(false)
                            ->visible(fn (Get $get): bool => $get('status') === ArticleStatus::Scheduled->value),
                        Toggle::make('is_featured')
                            ->label('Artikel unggulan'),
                    ]),
                Section::make('Media & SEO')
                    ->description('Gambar utama dan metadata mesin pencari.')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->image()
                            ->disk('public')
                            ->directory('articles')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('featured_image_alt')
                            ->label('Alt text gambar utama')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(60),
                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(160)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
