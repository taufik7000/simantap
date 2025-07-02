<?php

namespace App\Filament\Petugas\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Petugas\Resources\PermohonanRevisionResource;
use App\Models\PermohonanRevision;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPermohonanRevision extends EditRecord
{
    protected static string $resource = PermohonanRevisionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['status']) && $data['status'] !== 'pending') {
            $data['reviewed_at'] = now();
            $data['reviewed_by'] = Auth::id();
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        $revision = $this->record;
        $permohonan = $revision->permohonan;

        // Update status permohonan berdasarkan status revisi
        match ($revision->status) {
            'accepted' => $permohonan->update([
                'status' => 'sedang_diproses',
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} diterima. Permohonan akan diproses lebih lanjut.",
            ]),
            'rejected' => $permohonan->update([
                'status' => 'membutuhkan_revisi',
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} ditolak. " . $revision->catatan_petugas,
            ]),
            'reviewed' => $permohonan->update([
                'catatan_petugas' => "Revisi ke-{$revision->revision_number} telah direview. " . $revision->catatan_petugas,
            ]),
            default => null,
        };

        // Kirim notifikasi ke warga
        Notification::make()
            ->title('Revisi Anda Telah Direview')
            ->icon('heroicon-o-eye')
            ->body("Revisi ke-{$revision->revision_number} untuk permohonan {$permohonan->kode_permohonan} telah direview oleh petugas.")
            ->success()
            ->sendToDatabase($revision->user);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}