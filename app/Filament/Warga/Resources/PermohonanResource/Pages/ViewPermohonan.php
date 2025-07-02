<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Filament\Warga\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermohonan extends ViewRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_ticket')
                ->label('Buat Tiket Bantuan')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->url(fn () => TicketResource::getUrl('create', ['permohonan_id' => $this->record->id]))
                ->visible(fn () => !$this->record->hasActiveTickets())
                ->tooltip('Buat tiket bantuan jika ada masalah dengan permohonan ini'),

            Actions\Action::make('view_tickets')
                ->label('Lihat Tiket')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn () => TicketResource::getUrl('index'))
                ->visible(fn () => $this->record->hasActiveTickets())
                ->badge(fn () => $this->record->activeTickets()->count())
                ->tooltip('Lihat tiket yang terkait dengan permohonan ini'),
        ];
    }
}