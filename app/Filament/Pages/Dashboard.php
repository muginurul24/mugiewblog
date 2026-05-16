<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    /**
     * @return array<string, int>
     */
    public function getColumns(): array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }
}
