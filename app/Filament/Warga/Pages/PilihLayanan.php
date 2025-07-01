<?php

namespace App\Filament\Warga\Pages;

use App\Models\KategoriLayanan; // UBAH: Gunakan model KategoriLayanan
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class PilihLayanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Ajukan Permohonan';
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
            // OPSIONAL: Jika Anda hanya ingin kategori yang memiliki layanan aktif
            // Jika Anda menambahkan ini, pastikan tanpa semicolon di akhir baris sebelumnya
            ->has('layanans') // <-- Pastikan ini menempel ke KategoriLayanan::with(...)
            ->get();
    }
}