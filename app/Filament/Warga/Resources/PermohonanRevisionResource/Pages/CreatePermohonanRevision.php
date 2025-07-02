<?php

namespace App\Filament\Warga\Resources\PermohonanRevisionResource\Pages;

use App\Filament\Warga\Resources\PermohonanRevisionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreatePermohonanRevision extends CreateRecord
{
    protected static string $resource = PermohonanRevisionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $revision = $this->record;
        $permohonan = $revision->permohonan;

        // Update status permohonan ke "sedang_ditinjau" 
        $permohonan->update([
            'status' => 'sedang_ditinjau',
            'catatan_petugas' => 'Warga telah mengirim revisi dokumen. Menunggu review petugas.',
        ]);

        Notification::make()
            ->title('Revisi Berhasil Dikirim!')
            ->icon('heroicon-o-check-circle')
            ->body("Revisi ke-{$revision->revision_number} untuk permohonan {$permohonan->kode_permohonan} telah berhasil dikirim.")
            ->success()
            ->sendToDatabase(Auth::user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}