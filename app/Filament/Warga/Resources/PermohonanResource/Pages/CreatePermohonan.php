<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\FormulirMaster;
use App\Models\Layanan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;
    
    // 1. Tunjuk ke view custom kita
    protected static string $view = 'filament.warga.pages.create-permohonan';

    // Properti ini akan kita kirim ke view
    public Layanan $layanan; // UBAH: Dari SubLayanan menjadi Layanan
    public array $jenisPermohonanData = [];

    public function mount(): void
    {
        $layananId = request()->query('layanan_id'); // UBAH: 'sub_layanan_id' menjadi 'layanan_id'
        abort_if(!$layananId, 404);
        
        $this->layanan = Layanan::findOrFail($layananId); // UBAH: Dari SubLayanan menjadi Layanan

        // Siapkan data untuk dikirim ke view Blade
        // Menggunakan properti 'description' dari model Layanan
       if ($this->layanan->description && is_array($this->layanan->description)) { // UBAH: $this->subLayanan menjadi $this->layanan
            foreach ($this->layanan->description as $syarat) { // UBAH: $this->subLayanan menjadi $this->layanan
                $formulirId = $syarat['formulir_master_id'] ?? null;
                // Cari formulir berdasarkan ID untuk mendapatkan namanya
                $formulir = $formulirId ? FormulirMaster::find($formulirId) : null;

                $this->jenisPermohonanData[] = [
                    'nama' => $syarat['nama_syarat'],
                    'deskripsi' => $syarat['deskripsi_syarat'],
                    'formulir_master_id' => $formulirId,
                    // Kirim juga nama formulir ke view
                    'nama_formulir' => $formulir ? $formulir->nama_formulir : null,
                ];
            }
        }
        
        $this->form->fill();
    }

    // Metode ini tetap diperlukan untuk menambahkan user_id & layanan_id
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['layanan_id'] = $this->layanan->id; // UBAH: 'sub_layanan_id' menjadi 'layanan_id' dan $this->subLayanan menjadi $this->layanan
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