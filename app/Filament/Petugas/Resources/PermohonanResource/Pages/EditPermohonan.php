<?php

namespace App\Filament\Petugas\Resources\PermohonanResource\Pages;

use App\Filament\Petugas\Resources\PermohonanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPermohonan extends EditRecord
{
    protected static string $resource = PermohonanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function afterSave(): void
    {
        $permohonan = $this->record;
        
        // Check if status was changed
        if ($this->record->wasChanged('status')) {
            $oldStatus = $this->record->getOriginal('status');
            $newStatus = $this->record->status;
            
            $this->sendStatusUpdateNotification($oldStatus, $newStatus);
        }
    }

    /**
     * Send status update notification to the user who submitted the request
     */
    protected function sendStatusUpdateNotification(string $oldStatus, string $newStatus): void
    {
        $permohonan = $this->record;
        $user = $permohonan->user;
        
        if (!$user) {
            return;
        }

        $statusLabels = [
            'baru' => 'Baru Diajukan',
            'sedang_ditinjau' => 'Sedang Ditinjau',
            'verifikasi_berkas' => 'Verifikasi Berkas',
            'diproses' => 'Sedang Diproses',
            'membutuhkan_revisi' => 'Membutuhkan Revisi',
            'butuh_perbaikan' => 'Butuh Perbaikan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
        ];

        $newStatusLabel = $statusLabels[$newStatus] ?? $newStatus;
        $notificationIcon = match($newStatus) {
            'disetujui' => 'heroicon-o-check-circle',
            'ditolak' => 'heroicon-o-x-circle',
            'membutuhkan_revisi', 'butuh_perbaikan' => 'heroicon-o-exclamation-triangle',
            'selesai' => 'heroicon-o-flag',
            default => 'heroicon-o-information-circle',
        };

        $notificationColor = match($newStatus) {
            'disetujui', 'selesai' => 'success',
            'ditolak' => 'danger',
            'membutuhkan_revisi', 'butuh_perbaikan' => 'warning',
            default => 'info',
        };

        // Create notification body
        $body = "Status permohonan Anda dengan kode {$permohonan->kode_permohonan} telah diperbarui menjadi: {$newStatusLabel}";
        
        if ($permohonan->catatan_petugas) {
            $body .= "\n\nCatatan: " . $permohonan->catatan_petugas;
        }

        // Send notification to user
        Notification::make()
            ->title('Status Permohonan Diperbarui')
            ->icon($notificationIcon)
            ->body($body)
            ->color($notificationColor)
            ->sendToDatabase($user);

        // Send notification to current user (petugas)
        Notification::make()
            ->title('Status berhasil diperbarui')
            ->body("Notifikasi perubahan status telah dikirim kepada {$user->name}")
            ->success()
            ->send();

        // Log the activity
        activity()
            ->performedOn($permohonan)
            ->causedBy(Auth::user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'catatan' => $permohonan->catatan_petugas,
            ])
            ->log("Status permohonan diubah dari {$oldStatus} ke {$newStatus}");
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Permohonan berhasil diperbarui';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Add timestamp when status is updated
        if (isset($data['status']) && $data['status'] !== $this->record->getOriginal('status')) {
            $data['status_updated_at'] = now();
            $data['status_updated_by'] = Auth::id();
        }

        return $data;
    }
}