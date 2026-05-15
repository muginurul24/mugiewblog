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
use Filament\Schemas\Components\Grid;
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
                Section::make('Content')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        MarkdownEditor::make('content_md')
                            ->label('Content')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),
                Section::make('Publishing')
                    ->schema([
                        Select::make('user_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
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
                            ->options(ArticleStatus::options())
                            ->default(ArticleStatus::Draft->value)
                            ->required()
                            ->live(),
                        DateTimePicker::make('published_at')
                            ->seconds(false)
                            ->visible(fn (Get $get): bool => $get('status') === ArticleStatus::Published->value),
                        DateTimePicker::make('scheduled_at')
                            ->seconds(false)
                            ->visible(fn (Get $get): bool => $get('status') === ArticleStatus::Scheduled->value),
                        Toggle::make('is_featured')
                            ->label('Featured'),
                    ]),
                Section::make('Media & SEO')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->image()
                            ->disk('public')
                            ->directory('articles')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        TextInput::make('featured_image_alt')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('meta_title')
                            ->maxLength(60),
                        Textarea::make('meta_description')
                            ->rows(3)
                            ->maxLength(160)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        TextInput::make('reading_time')
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        TextInput::make('view_count')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
