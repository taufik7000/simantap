<?php

namespace App\Filament\Warga\Pages;
use App\Models\Layanan;
use Illuminate\Database\Eloquent\Collection;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'filament.warga.pages.dashboard';

    public ?Collection $availableServices;

    public function mount(): void
    {
        // Ambil hingga 6 layanan yang aktif untuk ditampilkan
        $this->availableServices = Layanan::where('is_active', true)
            ->take(6)
            ->get();
    }
}
