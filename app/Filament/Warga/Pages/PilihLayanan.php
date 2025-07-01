<?php

namespace App\Filament\Warga\Pages;

use App\Models\Layanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class PilihLayanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Ajukan Permohonan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.warga.pages.pilih-layanan';

    protected static ?string $title = '';

    public Collection $layanans;

    public function mount(): void
    {
      $this->layanans = Layanan::with(['subLayanans' => function ($query) {
                // Filter hanya sub-layanan yang aktif
                $query->where('is_active', true);
            }])
            ->get();
    }
}