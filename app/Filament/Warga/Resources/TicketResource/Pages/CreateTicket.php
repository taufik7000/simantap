<?php

namespace App\Filament\Warga\Resources\TicketResource\Pages;

use App\Filament\Warga\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'open';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;

        // Kirim notifikasi ke user
        Notification::make()
            ->title('Tiket Berhasil Dibuat!')
            ->icon('heroicon-o-check-circle')
            ->body("Tiket Anda dengan kode {$ticket->kode_tiket} telah berhasil dibuat. Tim support kami akan segera merespon.")
            ->success()
            ->sendToDatabase(Auth::user());

        // Log aktivitas
        activity()
            ->performedOn($ticket)
            ->causedBy(Auth::user())
            ->log('Tiket baru dibuat dengan kode: ' . $ticket->kode_tiket);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}