<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected string $view = 'filament.resources.media.pages.list-media';

    /**
     * @return Collection<int, Media>
     */
    #[\NoDiscard]
    public function recentMedia(): Collection
    {
        return Media::query()
            ->with('uploader')
            ->latest()
            ->limit(12)
            ->get();
    }

    /**
     * @return array{files: int, folders: int, bytes: int, images: int}
     */
    #[\NoDiscard]
    public function overviewStats(): array
    {
        return [
            'files' => Media::query()->count(),
            'folders' => Media::query()->distinct()->count('folder'),
            'bytes' => (int) Media::query()->sum('size'),
            'images' => Media::query()->where('mime_type', 'like', 'image/%')->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
