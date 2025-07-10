<?php

namespace App\Filament\Kadis\Pages;
use App\Filament\Kadis\Widgets\DashboardStatsWidget;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.kadis.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }
}
