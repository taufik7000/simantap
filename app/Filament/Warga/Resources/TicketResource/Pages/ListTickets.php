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
    protected static string $view = 'filament.warga.pages.list-tickets';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Tiket Baru')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->size('lg'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua Tiket')
                ->badge(fn () => $this->getModel()::where('user_id', auth()->id())->count())
                ->badgeColor('gray'),
                
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

    /**
     * Override untuk menangani query berdasarkan filter custom
     */
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        // Handle custom status filter dari URL
        if (request('status') == 'active') {
            $query->whereIn('status', ['open', 'in_progress']);
        } elseif (request('status') == 'resolved') {
            $query->whereIn('status', ['resolved', 'closed']);
        }
        
        return $query;
    }

    /**
     * Disable default table karena kita menggunakan custom view
     */
    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([]) // Empty columns karena tidak digunakan
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->emptyStateHeading('Tidak ada tiket')
            ->emptyStateDescription('Belum ada tiket bantuan yang dibuat.')
            ->emptyStateIcon('heroicon-o-ticket');
    }

    /**
     * Data tambahan untuk view
     */
    protected function getViewData(): array
    {
        return [
            'tickets' => $this->getTableQuery()->with(['layanan', 'permohonan', 'messages', 'assignedTo'])->get(),
            'totalTickets' => auth()->user()->tickets()->count(),
            'activeTickets' => auth()->user()->tickets()->whereIn('status', ['open', 'in_progress'])->count(),
            'resolvedTickets' => auth()->user()->tickets()->whereIn('status', ['resolved', 'closed'])->count(),
            'unreadMessages' => auth()->user()->getUnreadMessagesCount(),
        ];
    }
}