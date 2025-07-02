<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPermohonans extends ListRecords
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(), // Uncomment if you want to allow manual creation
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Permohonan')
                ->badge(fn () => $this->getModel()::count()),
                
            'baru' => Tab::make('Baru Diajukan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'baru'))
                ->badge(fn () => $this->getModel()::where('status', 'baru')->count())
                ->badgeColor('gray'),
                
            'sedang_ditinjau' => Tab::make('Sedang Ditinjau')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sedang_ditinjau'))
                ->badge(fn () => $this->getModel()::where('status', 'sedang_ditinjau')->count())
                ->badgeColor('info'),
                
            'verifikasi_berkas' => Tab::make('Verifikasi Berkas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'verifikasi_berkas'))
                ->badge(fn () => $this->getModel()::where('status', 'verifikasi_berkas')->count())
                ->badgeColor('warning'),
                
            'diproses' => Tab::make('Sedang Diproses')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'diproses'))
                ->badge(fn () => $this->getModel()::where('status', 'diproses')->count())
                ->badgeColor('info'),
                
            'perlu_tindakan' => Tab::make('Perlu Tindakan')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['membutuhkan_revisi', 'butuh_perbaikan'])->count())
                ->badgeColor('danger'),
                
            'disetujui' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'disetujui'))
                ->badge(fn () => $this->getModel()::where('status', 'disetujui')->count())
                ->badgeColor('success'),
                
            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'selesai'))
                ->badge(fn () => $this->getModel()::where('status', 'selesai')->count())
                ->badgeColor('primary'),
                
            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ditolak'))
                ->badge(fn () => $this->getModel()::where('status', 'ditolak')->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['user', 'layanan']) // Eager load relationships
            ->latest('created_at'); // Default sorting
    }
}