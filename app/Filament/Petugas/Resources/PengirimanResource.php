<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PengirimanResource\Pages;
use App\Models\Permohonan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class PengirimanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $slug = 'pengiriman-dokumen';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Proses Pengiriman';
    protected static ?string $navigationGroup = 'Tugas Pengiriman';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Pengiriman Dokumen';
    protected static ?string $pluralModelLabel = 'Pengiriman Dokumen';

    // Query ini memastikan hanya permohonan yang statusnya 'dokumen_diterbitkan' yang muncul
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'dokumen_diterbitkan');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengirimans::route('/'),
            'view' => Pages\ViewPengiriman::route('/{record:kode_permohonan}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (!auth()->check()) { return null; }
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()->count() > 0 ? 'warning' : null;
    }
}