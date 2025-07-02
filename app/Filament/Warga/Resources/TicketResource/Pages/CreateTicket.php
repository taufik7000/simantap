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
        
        // Jika ada permohonan_id, pastikan layanan_id juga terisi
        if (!empty($data['permohonan_id'])) {
            $permohonan = \App\Models\Permohonan::find($data['permohonan_id']);
            if ($permohonan) {
                $data['layanan_id'] = $permohonan->layanan_id;
            }
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $ticket = $this->record;

        // Tentukan pesan notifikasi berdasarkan jenis tiket
        $ticketType = $ticket->permohonan 
            ? "untuk permohonan {$ticket->permohonan->kode_permohonan}" 
            : ($ticket->layanan ? "tentang layanan {$ticket->layanan->name}" : "umum");

        // Kirim notifikasi ke user
        Notification::make()
            ->title('Tiket Berhasil Dibuat!')
            ->icon('heroicon-o-check-circle')
            ->body("Tiket Anda dengan kode {$ticket->kode_tiket} {$ticketType} telah berhasil dibuat. Tim support kami akan segera merespon.")
            ->success()
            ->sendToDatabase(Auth::user());

        // Kirim notifikasi ke petugas/admin (opsional - bisa dikonfigurasi)
        $this->notifyStaff($ticket);
    }

    protected function notifyStaff($ticket): void
    {
        // Ambil semua petugas dan admin
        $staffUsers = \App\Models\User::role(['petugas', 'admin'])->get();
        
        $ticketType = $ticket->permohonan 
            ? "terkait permohonan {$ticket->permohonan->kode_permohonan}" 
            : ($ticket->layanan ? "tentang {$ticket->layanan->name}" : "pertanyaan umum");

        foreach ($staffUsers as $staff) {
            Notification::make()
                ->title('Tiket Baru Dibuat')
                ->icon('heroicon-o-ticket')
                ->body("Tiket baru #{$ticket->kode_tiket} {$ticketType} dari {$ticket->user->name}")
                ->info()
                ->sendToDatabase($staff);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tiket berhasil dibuat';
    }
}