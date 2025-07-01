<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\SubLayanan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;

    public SubLayanan $subLayanan;

    // Metode ini akan dijalankan saat halaman dimuat
    public function mount(): void
    {
        // Ambil 'sub_layanan_id' dari query string di URL
        $subLayananId = request()->query('sub_layanan_id');

        // Jika tidak ada ID, batalkan dan kembali
        if (!$subLayananId) {
            $this->redirect(PermohonanResource::getUrl('index'));
            return;
        }

        $this->subLayanan = SubLayanan::findOrFail($subLayananId);

        parent::mount();
    }

    // Metode ini untuk memanipulasi data sebelum disimpan
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tambahkan ID user yang sedang login
        $data['user_id'] = Auth::id();
        // Tambahkan ID sub layanan yang dipilih
        $data['sub_layanan_id'] = $this->subLayanan->id;

        return $data;
    }

    // Mengarahkan pengguna ke daftar permohonan setelah berhasil membuat
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}