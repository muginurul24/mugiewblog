<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan')
                    ->schema([
                        TextEntry::make('author.name')
                            ->label('Penulis'),
                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->placeholder('-'),
                        TextEntry::make('title')
                            ->label('Judul'),
                        TextEntry::make('slug'),
                        TextEntry::make('excerpt')
                            ->label('Ringkasan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Konten')
                    ->schema([
                        TextEntry::make('content_md')
                            ->label('Markdown')
                            ->columnSpanFull(),
                        TextEntry::make('content_html')
                            ->label('Render HTML')
                            ->html()
                            ->columnSpanFull(),
                    ]),
                Section::make('Publikasi')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ArticleStatus $state): string => $state->label())
                            ->color(fn (ArticleStatus $state): string => $state->color()),
                        TextEntry::make('published_at')
                            ->label('Terbit pada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('scheduled_at')
                            ->label('Jadwal tayang')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('reading_time')
                            ->label('Waktu baca')
                            ->suffix(' menit')
                            ->numeric(),
                        TextEntry::make('view_count')
                            ->label('Views')
                            ->numeric(),
                        IconEntry::make('is_featured')
                            ->label('Unggulan')
                            ->boolean(),
                    ])
                    ->columns(3),
                Section::make('Media & SEO')
                    ->schema([
                        ImageEntry::make('featured_image_url')
                            ->label('Gambar utama')
                            ->placeholder('-'),
                        TextEntry::make('featured_image_alt')
                            ->label('Alt text gambar utama')
                            ->placeholder('-'),
                        TextEntry::make('meta_title')
                            ->placeholder('-'),
                        TextEntry::make('meta_description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Audit')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime()
                            ->visible(fn (Article $record): bool => $record->trashed()),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
