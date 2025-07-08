<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\VerifikasiBerkasResource\Pages;
use App\Models\Permohonan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VerifikasiBerkasResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $slug = 'verifikasi-berkas';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Verifikasi Berkas';
    protected static ?string $navigationGroup = 'Manajemen Permohonan';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Menunggu Verifikasi Berkas';
    protected static ?string $pluralModelLabel = 'Verifikasi Berkas';

    public static function getNavigationBadge(): ?string
    {
        // Panggil query yang sama dengan yang digunakan di halaman list
        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        // Tampilkan badge dengan warna 'warning' jika ada tugas
        return static::getEloquentQuery()->count() > 0 ? 'warning' : null;
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('assigned_to', Auth::id())
            ->whereIn('status', ['verifikasi_berkas', 'diperbaiki_warga', 'butuh_perbaikan']);
    }

    // Kita tidak ingin ada tombol "Create" di halaman ini
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifikasiBerkas::route('/'),
            // Tambahkan route untuk halaman view kustom kita
            'view' => Pages\ViewVerifikasi::route('/{record}/view'),
        ];
    }
}