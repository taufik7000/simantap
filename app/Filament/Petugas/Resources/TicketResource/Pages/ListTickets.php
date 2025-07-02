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
        return [];
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
                
            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['resolved', 'closed']))
                ->badge(fn () => $this->getModel()::whereIn('status', ['resolved', 'closed'])->count())
                ->badgeColor('success'),
        ];
    }
}
