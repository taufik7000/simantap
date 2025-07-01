<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\SubLayanan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;

    public SubLayanan $subLayanan;

    public function mount(): void
    {
        $subLayananId = request()->query('sub_layanan_id');

        if (!$subLayananId) {
            $this->redirect(PermohonanResource::getUrl('index'));
            return;
        }

        $this->subLayanan = SubLayanan::findOrFail($subLayananId);

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['sub_layanan_id'] = $this->subLayanan->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $permohonan = $this->record;
        $user = Auth::user();

        Notification::make()
            ->title('Permohonan Berhasil Diajukan!')
            ->icon('heroicon-o-check-circle')
            ->body("Permohonan Anda dengan kode {$permohonan->kode_permohonan} telah berhasil kami terima.")
            ->success()
            ->sendToDatabase($user);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}