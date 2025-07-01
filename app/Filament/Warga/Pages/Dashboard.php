<?php

namespace App\Filament\Warga\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'filament.warga.pages.dashboard';
}
