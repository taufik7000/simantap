<?php

namespace App\Filament\Warga\Pages;

use App\Models\Layanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Dashboard';

    protected static string $view = 'filament.warga.pages.dashboard';

    public ?Collection $availableServices;
    public int $unreadMessagesCount = 0;

    public function mount(): void
    {
        $this->availableServices = Layanan::where('is_active', true)
            ->take(6)
            ->get();
        
        $this->unreadMessagesCount = Auth::user()->getUnreadMessagesCount();
    }
}