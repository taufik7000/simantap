<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\Layanan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;

    // Hapus baris view kustom, kita akan gunakan view default dari Filament
    // protected static string $view = 'filament.warga.pages.create-permohonan';

    public function mount(): void
    {
        $layananId = request()->query('layanan_id');
        abort_if(!$layananId, 404, 'Layanan tidak ditemukan.');
        
        $layanan = Layanan::with('kategoriLayanan')->findOrFail($layananId);

        // Teruskan data yang dibutuhkan untuk membangun form ke dalam schema
        $this->form->fill([
            'layanan_id' => $layanan->id,
            // 'layanan_data' adalah field sementara untuk menyimpan definisi form dari Kadis
            'layanan_data' => $layanan->description, 
            'layanan_info' => [
                'nama_layanan' => $layanan->name,
                'nama_kategori' => $layanan->kategoriLayanan->name,
            ],
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'baru';

        // Hapus data sementara yang tidak perlu disimpan ke database
        unset($data['layanan_data']);
        unset($data['layanan_info']);

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