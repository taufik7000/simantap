<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListPermohonans extends ListRecords
{
    protected static string $resource = PermohonanResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Permohonan'),
            
            'belum_diambil' => Tab::make('Belum Diambil')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('assigned_to'))
                ->badge(fn () => $this->getModel()::whereNull('assigned_to')->count())
                ->badgeColor('danger'),

            'tugas_saya' => Tab::make('Tugas Saya')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', Auth::id()))
                ->badge(fn () => $this->getModel()::where('assigned_to', Auth::id())->count())
                ->badgeColor('success'),
                
            'sedang_dikerjakan' => Tab::make('Sedang Dikerjakan')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('assigned_to')->whereNotIn('status', ['selesai', 'ditolak'])),
        ];
    }
}