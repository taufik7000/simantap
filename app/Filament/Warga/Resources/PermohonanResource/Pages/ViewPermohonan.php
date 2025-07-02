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
            // Action untuk membuat tiket bantuan terkait permohonan ini
            Actions\Action::make('create_ticket')
                ->label('Buat Tiket Bantuan')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('warning')
                ->url(fn () => TicketResource::getUrl('create', ['permohonan_id' => $this->record->id]))
                ->visible(fn () => !$this->record->hasActiveTickets())
                ->tooltip('Buat tiket bantuan jika ada masalah dengan permohonan ini')
                ->extraAttributes([
                    'class' => 'hover:scale-105 transition-transform duration-200'
                ]),

            // Action untuk melihat tiket yang sudah ada
            Actions\Action::make('view_tickets')
                ->label('Lihat Tiket Aktif')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn () => TicketResource::getUrl('index') . '?tableFilters[permohonan_id][value]=' . $this->record->id)
                ->visible(fn () => $this->record->hasActiveTickets())
                ->badge(fn () => $this->record->activeTickets()->count())
                ->badgeColor('warning')
                ->tooltip('Lihat tiket yang terkait dengan permohonan ini'),

            // Action untuk melihat semua tiket user (opsional)
            Actions\Action::make('all_tickets')
                ->label('Semua Tiket Saya')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('gray')
                ->url(fn () => TicketResource::getUrl('index'))
                ->outlined()
                ->tooltip('Lihat semua tiket bantuan yang pernah Anda buat'),
        ];
    }
}