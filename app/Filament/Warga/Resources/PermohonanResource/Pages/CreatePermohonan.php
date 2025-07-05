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
    
    protected static string $view = 'filament.warga.pages.create-permohonan';

    public Layanan $layanan;
    public array $jenisPermohonanData = [];

    public function mount(): void
    {
        $layananId = request()->query('layanan_id');
        abort_if(!$layananId, 404);
        
        $this->layanan = Layanan::findOrFail($layananId);

        if ($this->layanan->description && is_array($this->layanan->description)) {
            foreach ($this->layanan->description as $syarat) {
                $formulirIds = $syarat['formulir_master_id'] ?? null; 
                $namaFormulirList = [];

                if (!empty($formulirIds)) {
                    $formulirIds = (array) $formulirIds; 
                    $formulirs = FormulirMaster::whereIn('id', $formulirIds)->get();
                    foreach ($formulirs as $formulir) {
                        $namaFormulirList[] = $formulir->nama_formulir;
                    }
                }

                // [PERUBAHAN KUNCI] Menambahkan 'form_fields' ke data yang dikirim ke view
                $this->jenisPermohonanData[] = [
                    'nama' => $syarat['nama_syarat'],
                    'deskripsi' => $syarat['deskripsi_syarat'],
                    'formulir_master_id' => $formulirIds, 
                    'nama_formulir' => !empty($namaFormulirList) ? $namaFormulirList : null,
                    'form_fields' => $syarat['form_fields'] ?? [], // <-- TAMBAHKAN INI
                ];
            }
        }
        
        $this->form->fill();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['layanan_id'] = $this->layanan->id;
        $data['status'] = 'baru';

        // [PERUBAHAN KUNCI] Menggabungkan data dari form dinamis ke dalam 'data_pemohon'
        // Data isian dari form dinamis akan tersimpan di $data['data_pemohon_dinamis']
        if (isset($data['data_pemohon_dinamis']) && is_array($data['data_pemohon_dinamis'])) {
            foreach ($data['data_pemohon_dinamis'] as $key => $value) {
                // Semua data isian digabungkan ke 'data_pemohon' agar tersimpan dalam satu kolom JSON
                $data['data_pemohon'][$key] = $value;
            }
        }
        // Hapus key sementara agar tidak menyebabkan error
        unset($data['data_pemohon_dinamis']);

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