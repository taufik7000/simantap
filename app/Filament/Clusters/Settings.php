<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Pengguna';

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?int $navigationSort = 10;
}