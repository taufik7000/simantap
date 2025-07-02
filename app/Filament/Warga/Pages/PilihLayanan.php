<?php

namespace App\Filament\Warga\Pages;

use App\Models\KategoriLayanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class PilihLayanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Semua Layanan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.warga.pages.pilih-layanan';

    protected static ?string $title = '';

    public Collection $kategoriLayanans;

    public function mount(): void
    {
        $this->kategoriLayanans = KategoriLayanan::with(['layanans' => function ($query) {
                // Filter hanya layanan (yang dulunya sub-layanan) yang aktif
                $query->where('is_active', true);
            }])
            ->has('layanans')
            ->get();
    }
}