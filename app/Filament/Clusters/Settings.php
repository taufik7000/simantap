<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    // Ikon untuk menu utama cluster
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    // (Opsional) Mengatur label yang akan ditampilkan di navigasi
    // Jika tidak diatur, akan menggunakan "Settings"
    protected static ?string $navigationLabel = 'Pengaturan';

    // (Opsional) Mengelompokkan cluster ini dengan item navigasi lain
    // Berguna jika Anda punya banyak cluster atau resource tingkat atas
    protected static ?string $navigationGroup = 'Manajemen Sistem';

    // (Opsional) Mengatur urutan menu di sidebar
    protected static ?int $navigationSort = 3;
}