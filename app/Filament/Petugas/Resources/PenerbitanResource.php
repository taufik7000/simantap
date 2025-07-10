<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PenerbitanResource\Pages;
use App\Models\Permohonan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;

class PenerbitanResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $slug = 'penerbitan-dokumen';
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Penerbitan';
    protected static ?string $navigationGroup = 'Manajemen Permohonan';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Penerbitan Dokumen';
    protected static ?string $pluralModelLabel = 'Penerbitan Dokumen';

    // Query ini memastikan hanya permohonan yang sudah disetujui yang muncul
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'disetujui');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    // Kita tidak ingin ada tombol "Create" di halaman ini
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenerbitans::route('/'),
            'view' => Pages\ViewPenerbitan::route('/{record:kode_permohonan}'),
        ];
    }
}