<?php

namespace App\Filament\Kadis\Resources;

use App\Filament\Kadis\Resources\PersetujuanResource\Pages;
use App\Models\Permohonan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class PersetujuanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $slug = 'persetujuan-permohonan';
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Menunggu Persetujuan';
    protected static ?string $navigationGroup = 'Manajemen Permohonan';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Persetujuan Permohonan';
    protected static ?string $pluralModelLabel = 'Persetujuan Permohonan';

    // Query ini memastikan hanya permohonan yang menunggu persetujuan yang muncul
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'menunggu_persetujuan');
    }

    // Kita tidak ingin ada tombol "Create" di halaman ini
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersetujuans::route('/'),
            'view' => Pages\ViewPersetujuan::route('/{record:kode_permohonan}'),
        ];
    }
}