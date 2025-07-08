<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\EntriDataResource\Pages;
use App\Models\Permohonan;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EntriDataResource extends Resource
{
    protected static ?string $model = Permohonan::class;
    protected static ?string $slug = 'entri-data';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Menunggu Entri Data';
    protected static ?string $navigationGroup = 'Manajemen Permohonan'; 
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Entri Data';
    protected static ?string $pluralModelLabel = 'Entri Data';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()->count() > 0 ? 'warning' : null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('assigned_to', Auth::id())
            ->where('status', 'menunggu_entri_data');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntriData::route('/'),
            'view' => Pages\ViewEntriData::route('/{record}/view'),
        ];
    }
}