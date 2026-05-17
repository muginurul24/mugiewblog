<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Support\FontAwesomeIconCatalog;
use Closure;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas kategori')
                    ->description('Nama publik, slug, hierarki, dan urutan tampil.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('parent_id')
                                    ->label('Kategori induk')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Presentasi')
                    ->description('Warna dan ikon untuk navigasi publik.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ColorPicker::make('color')
                                    ->label('Warna')
                                    ->required()
                                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')
                                    ->default('#D4943A'),
                                Select::make('icon')
                                    ->label('Ikon')
                                    ->helperText('Cari dan pilih ikon Font Awesome Free. Solid, Regular, dan Brands tersedia.')
                                    ->options(FontAwesomeIconCatalog::recommendedOptions())
                                    ->getSearchResultsUsing(fn (?string $search): array => FontAwesomeIconCatalog::search($search))
                                    ->getOptionLabelUsing(fn (?string $value): ?string => FontAwesomeIconCatalog::optionLabel($value))
                                    ->formatStateUsing(fn (?string $state): string => FontAwesomeIconCatalog::normalizeValue($state))
                                    ->dehydrateStateUsing(fn (?string $state): string => FontAwesomeIconCatalog::normalizeValue($state))
                                    ->searchable()
                                    ->native(false)
                                    ->allowHtml()
                                    ->required()
                                    ->rules([
                                        'string',
                                        'max:64',
                                        function (string $attribute, mixed $value, Closure $fail): void {
                                            if (! FontAwesomeIconCatalog::contains($value)) {
                                                $fail('Ikon yang dipilih tidak tersedia.');
                                            }
                                        },
                                    ])
                                    ->default('fa-solid fa-folder'),
                            ]),
                    ]),
            ]);
    }
}
