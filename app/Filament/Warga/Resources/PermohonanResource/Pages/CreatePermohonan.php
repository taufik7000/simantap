<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\FormulirMaster;
use App\Models\Layanan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Mengimpor Collection untuk kejelasan

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;
    
    // 1. Tunjuk ke view custom kita
    protected static string $view = 'filament.warga.pages.create-permohonan';

    // Properti ini akan kita kirim ke view
    public Layanan $layanan;
    public array $jenisPermohonanData = [];

    public function mount(): void
    {
        $layananId = request()->query('layanan_id');
        abort_if(!$layananId, 404);
        
        $this->layanan = Layanan::findOrFail($layananId);

        // Siapkan data untuk dikirim ke view Blade
        if ($this->layanan->description && is_array($this->layanan->description)) {
            foreach ($this->layanan->description as $syarat) {
                // 'formulir_master_id' sekarang bisa berupa array ID atau ID tunggal
                $formulirIds = $syarat['formulir_master_id'] ?? null; 

                $namaFormulirList = [];
                // PERBAIKAN UTAMA: Periksa apakah ada ID formulir dan proses sebagai array
                if (!empty($formulirIds)) {
                    // Pastikan $formulirIds adalah array untuk whereIn(), bahkan jika hanya 1 ID
                    $formulirIds = (array) $formulirIds; 
                    
                    // Ambil semua formulir master yang terkait dalam satu query efisien
                    $formulirs = FormulirMaster::whereIn('id', $formulirIds)->get();

                    // Kumpulkan nama-nama formulir yang ditemukan
                    foreach ($formulirs as $formulir) {
                        $namaFormulirList[] = $formulir->nama_formulir;
                    }
                }

                $this->jenisPermohonanData[] = [
                    'nama' => $syarat['nama_syarat'],
                    'deskripsi' => $syarat['deskripsi_syarat'],
                    // Simpan ID formulir yang dipilih (bisa array atau null)
                    'formulir_master_id' => $formulirIds, 
                    // Kirim juga array nama formulir ke view (jika ada)
                    'nama_formulir' => !empty($namaFormulirList) ? $namaFormulirList : null,
                ];
            }
        }
        
        $this->form->fill();
    }

    // Metode ini tetap diperlukan untuk menambahkan user_id & layanan_id
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['layanan_id'] = $this->layanan->id;
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