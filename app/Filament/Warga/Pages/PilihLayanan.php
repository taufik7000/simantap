<?php

namespace App\Filament\Warga\Pages;

use App\Models\KategoriLayanan;
use App\Models\Layanan;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification; // <-- Import Notifikasi
use App\Filament\Warga\Pages\Profile;    // <-- Import Halaman Profil

class PilihLayanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Menu Utama';
    protected static ?string $navigationLabel = 'Semua Layanan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.warga.pages.pilih-layanan';

    protected static ?string $title = '';

    public Collection $kategoriLayanans;

    /**
     * Dijalankan saat halaman dimuat.
     */
    public function mount(): void
    {
        // =======================================================
        // AWAL BLOK PEMERIKSAAN PROFIL
        // =======================================================
        if (!auth()->user()->isProfileComplete()) {
            // Beri notifikasi ke pengguna
            Notification::make()
                ->title('Profil Belum Lengkap')
                ->body('Anda harus melengkapi data profil Anda terlebih dahulu sebelum dapat mengajukan permohonan layanan.')
                ->warning()
                ->persistent() // Membuat notifikasi tetap muncul hingga ditutup
                ->send();
            
            // Alihkan pengguna ke halaman profil
            $this->redirect(Profile::getUrl());
            return; // Hentikan eksekusi lebih lanjut
        }
        // =======================================================
        // AKHIR BLOK PEMERIKSAAN PROFIL
        // =======================================================

        // Logika lama Anda untuk memuat layanan (ini sudah benar)
        $this->kategoriLayanans = KategoriLayanan::with(['layanans' => function ($query) {
                $query->where('is_active', true);
            }])
            ->has('layanans')
            ->get();
    }
}