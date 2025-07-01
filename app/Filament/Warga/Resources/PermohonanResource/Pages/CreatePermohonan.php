<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\SubLayanan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;
    
    // 1. Tunjuk ke view custom kita
    protected static string $view = 'filament.warga.pages.create-permohonan';

    // Properti ini akan kita kirim ke view
    public SubLayanan $subLayanan;
    public array $jenisPermohonanData = [];

    public function mount(): void
    {
        $subLayananId = request()->query('sub_layanan_id');
        abort_if(!$subLayananId, 404);
        
        $this->subLayanan = SubLayanan::findOrFail($subLayananId);

        // Siapkan data untuk dikirim ke view Blade
        if ($this->subLayanan->description && is_array($this->subLayanan->description)) {
            foreach ($this->subLayanan->description as $syarat) {
                $this->jenisPermohonanData[] = [
                    'nama' => $syarat['nama_syarat'],
                    'deskripsi' => $syarat['deskripsi_syarat'],
                ];
            }
        }
        
        $this->form->fill();
    }

    // Metode ini tetap diperlukan untuk menambahkan user_id & sub_layanan_id
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