<?php

namespace App\Filament\Warga\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.warga.pages.profile';
    protected static ?string $title = 'Profil Saya';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(auth()->user()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        // Skema form Anda sudah benar, tidak perlu diubah
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Data Diri')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('nik')->label('NIK')->disabled(),
                                TextInput::make('name')->label('Nama Lengkap')->required(),
                                TextInput::make('email')->label('Alamat Email')->email()->required(),
                                TextInput::make('nomor_kk')->label('Nomor Kartu Keluarga')->required(),
                                TextInput::make('nomor_whatsapp')->label('Nomor Whatsapp')->required(),
                                Textarea::make('alamat')->label('Alamat Lengkap')->rows(3)->columnSpanFull(),
                            ])->columns(2),
                        
                        Tabs\Tab::make('Dokumen')
                            ->icon('heroicon-o-document-arrow-up')
                            ->schema([
                                FileUpload::make('foto_ktp')->label('Foto KTP')->image()->directory('dokumen_warga')->visibility('private'),
                                FileUpload::make('foto_kk')->label('Foto Kartu Keluarga')->image()->directory('dokumen_warga')->visibility('private'),
                                FileUpload::make('foto_tanda_tangan')->label('Foto Tanda Tangan')->image()->directory('dokumen_warga')->visibility('private'),
                                FileUpload::make('foto_selfie_ktp')->label('Foto Selfie dengan KTP')->image()->directory('dokumen_warga')->visibility('private'),
                            ])->columns(2),

                        Tabs\Tab::make('Ubah Password')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('password')->password()->label('Password Baru')->helperText('Biarkan kosong jika tidak ingin mengubah password.')->confirmed(),
                                TextInput::make('password_confirmation')->password()->label('Konfirmasi Password Baru'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function updateProfile(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // 1. Siapkan data non-file untuk diupdate
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'nomor_kk' => $data['nomor_kk'],
            'nomor_whatsapp' => $data['nomor_whatsapp'],
            'alamat' => $data['alamat'],
        ];

        // 2. Logika cerdas untuk menangani file
        $fileFields = ['foto_ktp', 'foto_kk', 'foto_tanda_tangan', 'foto_selfie_ktp'];
        foreach ($fileFields as $field) {
            // Periksa apakah ada file baru yang diunggah (bukan null dan merupakan array)
            if (isset($data[$field]) && is_array($data[$field]) && !empty($data[$field])) {
                // Hapus file lama jika ada
                if ($user->{$field}) {
                    Storage::disk('public')->delete($user->{$field});
                }
                // Tambahkan path file baru ke data update
                $updateData[$field] = head($data[$field]);
            }
        }
        
        // 3. Logika untuk password (tetap sama)
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // 4. Update pengguna dengan data yang sudah disiapkan
        $user->update($updateData);

        Notification::make()->title('Profil berhasil diperbarui')->success()->send();
            
        if (!empty($data['password'])) {
            $this->redirect(static::getUrl());
        }
    }
}