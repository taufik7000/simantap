<?php

namespace App\Filament\Pages;

use App\Models\Layanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    public ?Collection $availableServices;
    public int $unreadMessagesCount = 0;

    public function mount(): void
    {
        $this->availableServices = Layanan::where('is_active', true)
            ->take(6)
            ->get();
        
        // Panggil metode yang sudah kita buat di model User
        $this->unreadMessagesCount = Auth::user()->getUnreadMessagesCount(); // <-- Tambahkan ini
    }
}
