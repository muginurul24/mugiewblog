<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
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
