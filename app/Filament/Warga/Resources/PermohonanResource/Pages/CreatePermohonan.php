<?php

namespace App\Filament\Warga\Resources\PermohonanResource\Pages;

use App\Filament\Warga\Resources\PermohonanResource;
use App\Models\Layanan;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use App\Filament\Warga\Pages\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class CreatePermohonan extends CreateRecord
{
    protected static string $resource = PermohonanResource::class;

    /**
     * PERUBAHAN UTAMA: Menambahkan metode mount() untuk inisialisasi data.
     */
    public function mount(): void
    {

        if (!auth()->user()->isProfileComplete()) {
            // Beri notifikasi ke pengguna
            Notification::make()
                ->title('Profil Belum Lengkap')
                ->body('Anda harus melengkapi data profil Anda terlebih dahulu sebelum dapat mengajukan permohonan.')
                ->warning()
                ->persistent() // Membuat notifikasi tetap muncul hingga ditutup
                ->send();

            // Alihkan pengguna ke halaman profil
            $this->redirect(Profile::getUrl());
            return; // Hentikan eksekusi lebih lanjut agar form tidak dimuat
        }

        // Ambil ID layanan dari URL
        $layananId = request()->query('layanan_id');
        abort_if(!$layananId, 404, 'Layanan tidak ditemukan.');
        
        $layanan = Layanan::with('kategoriLayanan')->findOrFail($layananId);

        // Siapkan array data awal
        $initialData = [
            'layanan_id' => $layanan->id,
            'layanan_data' => $layanan->description, 
            'layanan_info' => [
                'nama_layanan' => $layanan->name,
                'nama_kategori' => $layanan->kategoriLayanan->name,
            ],
            'berkas_pemohon' => [], // Inisialisasi 'berkas_pemohon' sebagai array kosong
        ];

        // --- INI BAGIAN PENTINGNYA ---
        // Kumpulkan semua kemungkinan 'file_key' dari semua jenis permohonan dalam layanan ini
        if (is_array($layanan->description)) {
            $allFileKeys = collect($layanan->description)
                ->pluck('file_requirements')
                ->flatten(1)
                ->whereNotNull('file_key')
                ->pluck('file_key')
                ->unique();

            // Inisialisasi setiap key di dalam 'berkas_pemohon' dengan nilai null
            foreach ($allFileKeys as $key) {
                $initialData['berkas_pemohon'][$key] = null;
            }
        }

        // Isi form dengan data yang sudah diinisialisasi
        $this->form->fill($initialData);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'baru';
        
        // Bersihkan data yang tidak perlu disimpan ke database
        unset($data['layanan_data']);
        unset($data['layanan_info']);

        // Pastikan hanya file yang diisi yang disimpan
        if (isset($data['berkas_pemohon'])) {
            $data['berkas_pemohon'] = array_filter($data['berkas_pemohon']);
        }

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
    protected function getFormActions(): array
    {
        return [];
    }
}