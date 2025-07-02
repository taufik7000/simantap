<?php

namespace App\Filament\Petugas\Resources\TicketResource\Pages;

use App\Filament\Petugas\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('statistics')
                ->label('Statistik Tiket')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->action(function () {
                    // Bisa dikembangkan untuk menampilkan modal statistik
                    $this->js('alert("Fitur statistik sedang dalam pengembangan")');
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Tiket')
                ->badge(fn () => $this->getModel()::count()),
                
            'belum_ditugaskan' => Tab::make('Belum Ditugaskan')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('assigned_to'))
                ->badge(fn () => $this->getModel()::whereNull('assigned_to')->count())
                ->badgeColor('danger'),
                
            'saya' => Tab::make('Tiket Saya')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', auth()->id()))
                ->badge(fn () => $this->getModel()::where('assigned_to', auth()->id())->count())
                ->badgeColor('primary'),
                
            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['open', 'in_progress']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['open', 'in_progress'])->count())
                ->badgeColor('warning'),
                
            'urgent' => Tab::make('Prioritas Tinggi')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('priority', ['high', 'urgent'])
                    ->whereIn('status', ['open', 'in_progress']))
                ->badge(fn () => $this->getModel()::whereIn('priority', ['high', 'urgent'])
                    ->whereIn('status', ['open', 'in_progress'])->count())
                ->badgeColor('danger'),
                
            'permohonan_terkait' => Tab::make('Terkait Permohonan')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('permohonan_id'))
                ->badge(fn () => $this->getModel()::whereNotNull('permohonan_id')->count())
                ->badgeColor('info'),
                
            'pertanyaan_umum' => Tab::make('Pertanyaan Umum')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('permohonan_id'))
                ->badge(fn () => $this->getModel()::whereNull('permohonan_id')->count())
                ->badgeColor('gray'),
                
            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['resolved', 'closed']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['resolved', 'closed'])->count())
                ->badgeColor('success'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['user', 'layanan.kategoriLayanan', 'permohonan', 'assignedTo', 'messages'])
            ->withCount('messages')
            ->latest('created_at');
    }
}