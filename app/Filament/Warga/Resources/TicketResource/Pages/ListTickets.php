<?php

namespace App\Filament\Warga\Resources\TicketResource\Pages;

use App\Filament\Warga\Resources\TicketResource;
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
            Actions\CreateAction::make()
                ->label('Buat Tiket Baru')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Tiket')
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->count()),
                
            'aktif' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['open', 'in_progress']))
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->whereIn('status', ['open', 'in_progress'])->count())
                ->badgeColor('primary'),
                
            'selesai' => Tab::make('Selesai')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['resolved', 'closed']))
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->whereIn('status', ['resolved', 'closed'])->count())
                ->badgeColor('success'),
        ];
    }
}
