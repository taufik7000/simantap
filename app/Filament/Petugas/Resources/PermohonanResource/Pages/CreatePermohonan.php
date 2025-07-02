<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $permohonan = $this->record;
        $user = $permohonan->user; // Mengambil user yang mengajukan permohonan
        
        // Kirim notifikasi ke user yang mengajukan permohonan
        Notification::make()
            ->title('Permohonan Berhasil Diajukan!')
            ->icon('heroicon-o-check-circle')
            ->body("Permohonan Anda dengan kode {$permohonan->kode_permohonan} telah berhasil kami terima dan akan segera diproses.")
            ->success()
            ->sendToDatabase($user);

        // Notifikasi untuk petugas yang membuat (jika berbeda)
        if (Auth::id() !== $user->id) {
            Notification::make()
                ->title('Permohonan Baru Dibuat')
                ->icon('heroicon-o-document-plus')
                ->body("Permohonan baru dengan kode {$permohonan->kode_permohonan} telah dibuat untuk {$user->name}.")
                ->success()
                ->send();
        }

        // Log aktivitas
        activity()
            ->performedOn($permohonan)
            ->causedBy(Auth::user())
            ->log('Permohonan baru dibuat dengan kode: ' . $permohonan->kode_permohonan);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Permohonan berhasil dibuat';
    }
}